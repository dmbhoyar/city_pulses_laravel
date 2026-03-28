<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailOtpService
{
    public const OTP_TTL_MINUTES = 10;

    public function sendOtp(string $recipient, string $purpose, array $meta = []): void
    {
        $recipient = $this->normalizeRecipient($recipient);
        $code = (string) random_int(100000, 999999);

        DB::table('otp_verifications')
            ->where('email', $recipient)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->delete();

        DB::table('otp_verifications')->insert([
            'email' => $recipient,
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'meta' => json_encode($meta),
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes(self::OTP_TTL_MINUTES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $subject = 'Your AajchaOffer OTP Code';
            try {
                Mail::send('emails.otp', [
                    'otp' => $code,
                    'ttl' => self::OTP_TTL_MINUTES,
                ], function ($message) use ($recipient, $subject) {
                    $message->to($recipient)
                            ->from('noreply@aajchaoffer.com', 'AajchaOffer')
                            ->subject($subject);
                });
            } catch (\Exception $e) {
                \Log::error('Failed to send OTP email: ' . $e->getMessage());
                throw $e;
            }
            return;
        }

        $this->sendSmsOtp($recipient, $code);
    }

    public function verifyOtp(string $recipient, string $purpose, string $otp, ?array &$meta = null): bool
    {
        $recipient = $this->normalizeRecipient($recipient);

        $record = DB::table('otp_verifications')
            ->where('email', $recipient)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->orderByDesc('id')
            ->first();

        if (!$record) {
            return false;
        }

        if (Carbon::parse($record->expires_at)->isPast()) {
            return false;
        }

        if (!Hash::check($otp, $record->code_hash)) {
            DB::table('otp_verifications')->where('id', $record->id)->update([
                'attempts' => (int) $record->attempts + 1,
                'updated_at' => now(),
            ]);
            return false;
        }

        DB::table('otp_verifications')->where('id', $record->id)->update([
            'consumed_at' => now(),
            'updated_at' => now(),
        ]);

        $metaArr = [];
        if (!empty($record->meta)) {
            $decoded = json_decode((string) $record->meta, true);
            if (is_array($decoded)) {
                $metaArr = $decoded;
            }
        }
        $meta = $metaArr;

        return true;
    }

    private function normalizeRecipient(string $recipient): string
    {
        $recipient = trim($recipient);
        if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            return strtolower($recipient);
        }

        return preg_replace('/\D+/', '', $recipient) ?? '';
    }

    private function sendSmsOtp(string $mobileNumber, string $code): void
    {
        $apiUrl = (string) config('services.sms.api_url', '');
        $apiKey = (string) config('services.sms.api_key', '');
        $sender = (string) config('services.sms.sender', 'CITYPL');

        $message = "Your OTP is {$code}. It expires in " . self::OTP_TTL_MINUTES . ' minutes.';

        if ($apiUrl !== '' && $apiKey !== '') {
            $response = Http::asForm()->post($apiUrl, [
                'apikey' => $apiKey,
                'sender' => $sender,
                'number' => $mobileNumber,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return;
            }

            Log::warning('SMS OTP provider request failed.', [
                'mobile' => $mobileNumber,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return;
        }

        // Fallback for local environments without SMS gateway integration.
        Log::info('SMS gateway not configured, OTP generated for mobile recipient.', [
            'mobile' => $mobileNumber,
            'otp' => $code,
        ]);
    }
}
