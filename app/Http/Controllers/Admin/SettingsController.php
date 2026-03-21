<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $settings = [
            'yearly_base_plan_price' => (float) AdminSetting::getValue('yearly_base_plan_price', '999'),
            'astro_dynamic_template_price' => (float) AdminSetting::getValue('astro_dynamic_template_price', '499'),
            'template_unlock_sla_hours' => (int) AdminSetting::getValue('template_unlock_sla_hours', '24'),
            'payment_qr_image_path' => (string) AdminSetting::getValue('payment_qr_image_path', ''),
            'payment_barcode_image_path' => (string) AdminSetting::getValue('payment_barcode_image_path', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'yearly_base_plan_price' => 'required|numeric|min:1|max:999999',
            'astro_dynamic_template_price' => 'required|numeric|min:1|max:999999',
            'template_unlock_sla_hours' => 'required|integer|min:1|max:720',
            'payment_qr_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'payment_barcode_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        AdminSetting::setValue('yearly_base_plan_price', number_format((float) $validated['yearly_base_plan_price'], 2, '.', ''));
        AdminSetting::setValue('astro_dynamic_template_price', number_format((float) $validated['astro_dynamic_template_price'], 2, '.', ''));
        AdminSetting::setValue('template_unlock_sla_hours', (int) $validated['template_unlock_sla_hours']);

        if ($request->hasFile('payment_qr_image')) {
            $oldQrPath = (string) AdminSetting::getValue('payment_qr_image_path', '');
            if ($oldQrPath !== '') {
                Storage::disk('public')->delete($oldQrPath);
            }
            $newQrPath = $request->file('payment_qr_image')->store('admin_settings/payments', 'public');
            AdminSetting::setValue('payment_qr_image_path', $newQrPath);
        }

        if ($request->hasFile('payment_barcode_image')) {
            $oldBarcodePath = (string) AdminSetting::getValue('payment_barcode_image_path', '');
            if ($oldBarcodePath !== '') {
                Storage::disk('public')->delete($oldBarcodePath);
            }
            $newBarcodePath = $request->file('payment_barcode_image')->store('admin_settings/payments', 'public');
            AdminSetting::setValue('payment_barcode_image_path', $newBarcodePath);
        }

        return back()->with('notice', 'Admin settings updated successfully.');
    }
}
