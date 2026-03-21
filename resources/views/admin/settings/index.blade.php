@extends('layouts.app')

@section('content')
<div class="panel">
  <h1>Admin Settings</h1>
  <p style="margin-top:6px;color:#5d7698">Manage global payment images, template pricing, and unlock approval SLA.</p>

  <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" style="margin-top:12px;display:grid;gap:12px">
    @csrf
    @method('PATCH')

    <div class="card" style="padding:14px">
      <h3 style="margin-top:0">Pricing</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div>
          <label for="yearly_base_plan_price">Yearly Base Plan Price (₹)</label>
          <input id="yearly_base_plan_price" type="number" step="0.01" min="1" name="yearly_base_plan_price" value="{{ old('yearly_base_plan_price', $settings['yearly_base_plan_price']) }}" style="width:100%">
        </div>
        <div>
          <label for="astro_dynamic_template_price">Astro Dynamic Unlock Price (₹)</label>
          <input id="astro_dynamic_template_price" type="number" step="0.01" min="1" name="astro_dynamic_template_price" value="{{ old('astro_dynamic_template_price', $settings['astro_dynamic_template_price']) }}" style="width:100%">
        </div>
      </div>
    </div>

    <div class="card" style="padding:14px">
      <h3 style="margin-top:0">Approval SLA</h3>
      <div>
        <label for="template_unlock_sla_hours">Template Unlock SLA (hours)</label>
        <input id="template_unlock_sla_hours" type="number" step="1" min="1" max="720" name="template_unlock_sla_hours" value="{{ old('template_unlock_sla_hours', $settings['template_unlock_sla_hours']) }}" style="width:100%">
      </div>
    </div>

    <div class="card" style="padding:14px">
      <h3 style="margin-top:0">Payment Images</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div>
          <label for="payment_qr_image">Global QR Image</label>
          <input id="payment_qr_image" type="file" name="payment_qr_image" accept="image/*">
          @if(!empty($settings['payment_qr_image_path']))
            <div style="margin-top:8px">
              <img src="{{ Storage::url($settings['payment_qr_image_path']) }}" alt="QR" style="width:140px;height:140px;object-fit:cover;border:1px solid #dbe7f8;border-radius:8px">
            </div>
          @endif
        </div>
        <div>
          <label for="payment_barcode_image">Global Barcode Image</label>
          <input id="payment_barcode_image" type="file" name="payment_barcode_image" accept="image/*">
          @if(!empty($settings['payment_barcode_image_path']))
            <div style="margin-top:8px">
              <img src="{{ Storage::url($settings['payment_barcode_image_path']) }}" alt="Barcode" style="width:220px;height:140px;object-fit:cover;border:1px solid #dbe7f8;border-radius:8px">
            </div>
          @endif
        </div>
      </div>
      <p style="margin-top:8px;color:#6d84a5;font-size:12px">Configure page will use QR image first; if QR is not set, barcode image will be shown.</p>
    </div>

    <div>
      <button type="submit" class="toggle-btn">Save Settings</button>
      <a href="{{ route('admin.dashboard') }}" class="button">Back to Admin Dashboard</a>
    </div>
  </form>
</div>
@endsection
