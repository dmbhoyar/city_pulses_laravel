<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f7f7f9; margin: 0; padding: 0; }
        .container { max-width: 420px; margin: 40px auto; background: #fff; border-radius: 10px; box-shadow: 0 2px 8px #e0e0e0; padding: 32px 28px; }
        .logo { text-align: center; margin-bottom: 18px; }
        .logo img { max-width: 120px; }
        .brand { text-align: center; font-size: 1.3em; color: #1a7a3c; font-weight: 600; margin-bottom: 18px; }
        .otp-box { background: #f2f8f3; border-radius: 8px; padding: 18px 0; text-align: center; font-size: 2.2em; letter-spacing: 8px; color: #1a7a3c; font-weight: bold; margin: 18px 0; }
        .info { text-align: center; color: #444; font-size: 1.1em; margin-bottom: 18px; }
        .footer { text-align: center; color: #aaa; font-size: 0.95em; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="https://aajchaoffer.com/assets/logo.png" alt="AajchaOffer Logo">
        </div>
        <div class="brand">AajchaOffer</div>
        <div class="info">Use the following OTP to complete your action:</div>
        <div class="otp-box">{{ $otp }}</div>
        <div class="info">This OTP is valid for {{ $ttl }} minutes.<br>If you did not request this, please ignore this email.</div>
        <div class="footer">&copy; {{ date('Y') }} AajchaOffer. All rights reserved.</div>
    </div>
</body>
</html>
