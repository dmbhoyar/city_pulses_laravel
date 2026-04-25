@extends('layouts.app')

@section('title', 'Invoice & Business Settings')

@section('content')
<style>
.is-wrap { max-width: 860px; margin: 0 auto; padding: 1.2rem 1rem 3rem }
.is-head { display: flex; align-items: center; gap: .6rem; margin-bottom: 1.2rem; flex-wrap: wrap }
.is-title { font-size: 1.35rem; font-weight: 800; color: #1a1208; margin: 0 }
.is-back  { font-size: .82rem; color: #b91c1c; text-decoration: none; white-space: nowrap }
.is-back:hover { text-decoration: underline }

.is-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.1rem 1.25rem; margin-bottom: .9rem }
.is-sect  { font-size: .67rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #9ca3af; margin: 0 0 .85rem; padding-bottom: .45rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: .4rem; flex-wrap: wrap }
.is-grid  { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem }
.is-grid3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: .75rem }
.is-span2 { grid-column: span 2 }
.is-span3 { grid-column: span 3 }
.is-field { display: flex; flex-direction: column; gap: .26rem }
.is-field label { font-size: .78rem; font-weight: 700; color: #374151 }
.is-field input, .is-field textarea { border: 1px solid #d1d5db; border-radius: 7px; padding: .52rem .75rem; font-size: .9rem; outline: none; width: 100%; box-sizing: border-box; font-family: inherit }
.is-field input:focus, .is-field textarea:focus { border-color: #1a1208; box-shadow: 0 0 0 2px rgba(26,18,8,.07) }
.is-field textarea { resize: vertical; min-height: 75px }
.is-hint { font-size: .72rem; color: #9ca3af; margin-top: .15rem }

/* Business identity row: form + logo side by side on desktop, stacked on mobile */
.is-biz-row { display: grid; grid-template-columns: 1fr auto; gap: 1.25rem; align-items: start }

/* Upload boxes */
.is-upload-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem }
.is-upload-box { display: flex; flex-direction: column; align-items: center; gap: .45rem }
.is-upload-lbl { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #6b7280; text-align: center }
.is-dropzone {
  width: 100%; max-width: 160px; height: 130px;
  border: 2px dashed #d1d5db; border-radius: 10px;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  cursor: pointer; background: #f9fafb; transition: all .15s; overflow: hidden;
}
.is-dropzone:hover { border-color: #1a1208; background: #f3f4f6 }
.is-dropzone.has-img { border-style: solid }
.is-dropzone img { width: 100%; height: 100%; object-fit: contain; padding: 6px }
.is-dz-ph { text-align: center; color: #9ca3af; padding: .6rem; pointer-events: none }
.is-dz-ph .icon { font-size: 1.6rem; margin-bottom: .25rem }
.is-dz-ph p { font-size: .67rem; margin: 0; line-height: 1.4 }
.is-dz-ph strong { display: block; font-size: .72rem; color: #6b7280; margin-bottom: .12rem }
.is-remove { display: flex; align-items: center; gap: .3rem; font-size: .74rem; color: #dc2626; cursor: pointer }
.is-remove input { width: auto; margin: 0 }
.is-upload-hint { font-size: .67rem; color: #9ca3af; text-align: center; line-height: 1.4 }

/* Progress steps */
.is-steps { display: flex; gap: 0; margin-bottom: 1.2rem; border-radius: 9px; overflow: hidden; border: 1px solid #e5e7eb }
.is-step { flex: 1; text-align: center; padding: .55rem .3rem; background: #f9fafb; border-right: 1px solid #e5e7eb; font-size: .7rem; font-weight: 600; color: #9ca3af; min-width: 0 }
.is-step:last-child { border-right: none }
.is-step.done { background: #f0fdf4; color: #15803d }
.is-step.active { background: #1a1208; color: #fff }
.is-step .num { display: block; font-size: .95rem; font-weight: 800; margin-bottom: .06rem }

/* Submit */
.is-submit-bar { display: flex; gap: .75rem; justify-content: flex-end; flex-wrap: wrap; margin-top: 1rem }
.is-btn { padding: .65rem 1.5rem; border-radius: 7px; font-size: .9rem; font-weight: 700; cursor: pointer; text-decoration: none; border: none; display: inline-block; text-align: center }
.is-btn-primary { background: #1a1208; color: #fff }
.is-btn-primary:hover { background: #b91c1c }
.is-btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db }

@media(max-width: 640px) {
  .is-wrap { padding: .7rem .65rem 2.5rem }
  .is-card { padding: .85rem .9rem }
  .is-title { font-size: 1.15rem }
  .is-grid { grid-template-columns: 1fr }
  .is-grid3 { grid-template-columns: 1fr 1fr }
  .is-span2, .is-span3 { grid-column: span 1 }
  .is-biz-row { grid-template-columns: 1fr }
  .is-upload-row { grid-template-columns: 1fr 1fr }
  .is-upload-box:last-child { grid-column: span 2 }
  .is-dropzone { max-width: 100%; height: 110px }
  .is-step .num { font-size: .8rem }
  .is-step { font-size: .6rem; padding: .45rem .15rem }
  .is-submit-bar { flex-direction: column-reverse }
  .is-btn { width: 100%; text-align: center }
}
@media(max-width: 400px) {
  .is-grid3 { grid-template-columns: 1fr }
  .is-upload-row { grid-template-columns: 1fr }
  .is-upload-box:last-child { grid-column: span 1 }
}
</style>

<div class="is-wrap">
    <div class="is-head">
        <a href="{{ route('invoices.index') }}" class="is-back">← Invoices</a>
        <h1 class="is-title">⚙ Business & Invoice Settings</h1>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem;font-weight:600">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- Setup progress --}}
    @php
        $steps = [
            ['label' => '1. Business Identity', 'done' => (bool)$profile->business_name],
            ['label' => '2. Tax / GSTIN',        'done' => (bool)$profile->gstin],
            ['label' => '3. Bank Details',        'done' => (bool)$profile->bank_name],
            ['label' => '4. QR & Signature',      'done' => (bool)($profile->payment_qr || $profile->signature)],
            ['label' => '5. Default Terms',        'done' => (bool)$profile->default_terms],
        ];
        $activeStep = collect($steps)->search(fn($s) => !$s['done']);
        $activeStep = $activeStep === false ? 4 : $activeStep;
    @endphp
    <div class="is-steps">
        @foreach($steps as $i => $step)
            <div class="is-step {{ $step['done'] ? 'done' : ($i === $activeStep ? 'active' : '') }}">
                <span class="num">{{ $step['done'] ? '✓' : ($i + 1) }}</span>
                {{ $step['label'] }}
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('invoices.settings.save') }}" enctype="multipart/form-data">
        @csrf

        @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem">
                @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
            </div>
        @endif

        {{-- ── STEP 1: Business Identity ── --}}
        <div class="is-card">
            <div class="is-sect">🏢 Step 1 — Business Identity
                <span style="font-size:.7rem;color:#9ca3af;font-weight:400;text-transform:none;letter-spacing:0">(shown on all invoices — fill once, reused everywhere)</span>
            </div>
            <div class="is-biz-row">
                <div class="is-grid">
                    <div class="is-field is-span2">
                        <label>Business / Shop Name</label>
                        <input type="text" name="business_name" value="{{ old('business_name', $profile->business_name ?: optional($shop)->name) }}" placeholder="e.g. Apex Automation">
                    </div>
                    <div class="is-field">
                        <label>Phone</label>
                        <input type="text" name="business_phone" value="{{ old('business_phone', $profile->business_phone ?: optional($shop)->phone) }}" placeholder="Contact number">
                    </div>
                    <div class="is-field">
                        <label>Email</label>
                        <input type="email" name="business_email" value="{{ old('business_email', $profile->business_email) }}" placeholder="business@email.com">
                    </div>
                    <div class="is-field is-span2">
                        <label>Full Address</label>
                        <textarea name="business_address" rows="2" placeholder="Door no., Street, City, PIN">{{ old('business_address', $profile->business_address ?: optional($shop)->address) }}</textarea>
                    </div>
                </div>

                {{-- Business Logo --}}
                <div class="is-upload-box">
                    <div class="is-upload-lbl">🏷 Business Logo</div>
                    <div class="is-dropzone {{ $profile->business_logo ? 'has-img' : '' }}"
                         id="logo-dz" onclick="document.getElementById('logo-input').click()">
                        @if($profile->business_logo)
                            <img src="{{ asset('storage/'.$profile->business_logo) }}" alt="Logo">
                        @else
                            <div class="is-dz-ph" id="logo-ph">
                                <div class="icon">🏢</div>
                                <strong>Click to upload</strong>
                                <p>Your business logo</p>
                            </div>
                        @endif
                    </div>
                    <input type="file" id="logo-input" name="business_logo" accept="image/*" style="display:none" onchange="previewFile(this,'logo-dz','logo-ph')">
                    <div class="is-upload-hint">PNG/JPG · max 2MB</div>
                    @if($profile->business_logo)
                        <label class="is-remove"><input type="checkbox" name="remove_logo" value="1"> Remove Logo</label>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── STEP 2: Tax / GSTIN ── --}}
        <div class="is-card">
            <div class="is-sect">🏛 Step 2 — Tax & Legal</div>
            <div class="is-grid">
                <div class="is-field">
                    <label>GSTIN</label>
                    <input type="text" name="gstin" value="{{ old('gstin', $profile->gstin) }}" placeholder="27XXXXX1234X1Z5" style="font-family:monospace;letter-spacing:.5px">
                    <span class="is-hint">GST Registration Number — leave blank if not GST registered.</span>
                </div>
                <div class="is-field" style="justify-content:flex-end">
                    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:7px;padding:.55rem .75rem;font-size:.76rem;color:#92400e">
                        💡 Your GSTIN appears on invoice header. CGST+SGST breakdown is auto-calculated per item.
                    </div>
                </div>
            </div>
        </div>

        {{-- ── STEP 3: Bank Details ── --}}
        <div class="is-card">
            <div class="is-sect">🏦 Step 3 — Bank Details</div>
            <div class="is-grid3">
                <div class="is-field">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $profile->bank_name) }}" placeholder="e.g. SBI, HDFC, Indian Bank">
                </div>
                <div class="is-field">
                    <label>Account Number</label>
                    <input type="text" name="bank_account" value="{{ old('bank_account', $profile->bank_account) }}" placeholder="Account number">
                </div>
                <div class="is-field">
                    <label>IFSC Code</label>
                    <input type="text" name="bank_ifsc" value="{{ old('bank_ifsc', $profile->bank_ifsc) }}" placeholder="e.g. IDIB000D605" style="font-family:monospace">
                </div>
                <div class="is-field is-span3">
                    <label>Account Holder Name</label>
                    <input type="text" name="bank_holder" value="{{ old('bank_holder', $profile->bank_holder) }}" placeholder="Name as on bank account">
                </div>
            </div>
        </div>

        {{-- ── STEP 4: QR + Signature ── --}}
        <div class="is-card">
            <div class="is-sect">📸 Step 4 — Payment QR & Authorized Signature</div>
            <div class="is-upload-row">

                {{-- Payment QR --}}
                <div class="is-upload-box">
                    <div class="is-upload-lbl">📱 Payment QR Code</div>
                    <div class="is-dropzone {{ $profile->payment_qr ? 'has-img' : '' }}"
                         id="qr-dz" onclick="document.getElementById('qr-input').click()">
                        @if($profile->payment_qr)
                            <img src="{{ asset('storage/'.$profile->payment_qr) }}" alt="QR">
                        @else
                            <div class="is-dz-ph" id="qr-ph">
                                <div class="icon">📱</div>
                                <strong>Click to upload</strong>
                                <p>UPI / GPay / PhonePe</p>
                            </div>
                        @endif
                    </div>
                    <input type="file" id="qr-input" name="payment_qr" accept="image/*" style="display:none" onchange="previewFile(this,'qr-dz','qr-ph')">
                    <div class="is-upload-hint">Scan-to-pay QR image<br>JPG/PNG · max 2MB</div>
                    @if($profile->payment_qr)
                        <label class="is-remove"><input type="checkbox" name="remove_qr" value="1"> Remove QR</label>
                    @endif
                </div>

                {{-- Signature --}}
                <div class="is-upload-box">
                    <div class="is-upload-lbl">✍️ Authorized Signature</div>
                    <div class="is-dropzone {{ $profile->signature ? 'has-img' : '' }}"
                         id="sig-dz" onclick="document.getElementById('sig-input').click()">
                        @if($profile->signature)
                            <img src="{{ asset('storage/'.$profile->signature) }}" alt="Signature" style="object-fit:contain;padding:12px">
                        @else
                            <div class="is-dz-ph" id="sig-ph">
                                <div class="icon">✍️</div>
                                <strong>Click to upload</strong>
                                <p>Signature / stamp image</p>
                            </div>
                        @endif
                    </div>
                    <input type="file" id="sig-input" name="signature" accept="image/*" style="display:none" onchange="previewFile(this,'sig-dz','sig-ph')">
                    <div class="is-upload-hint">Transparent PNG preferred<br>max 2MB</div>
                    @if($profile->signature)
                        <label class="is-remove"><input type="checkbox" name="remove_signature" value="1"> Remove</label>
                    @endif
                </div>

                {{-- Preview of how footer looks --}}
                <div style="display:flex;flex-direction:column;gap:.5rem">
                    <div class="is-upload-lbl">📄 Footer Preview</div>
                    <div style="border:1px solid #e5e7eb;border-radius:8px;padding:.75rem;background:#f9fafb;font-size:.72rem;color:#374151;min-height:140px">
                        @if($profile->bank_name || $profile->payment_qr || $profile->signature)
                            @if($profile->bank_name)
                                <strong style="font-size:.65rem;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af">Bank</strong><br>
                                {{ $profile->bank_name }}<br>
                                {{ $profile->bank_account }}<br>
                                {{ $profile->bank_ifsc }}<br>
                            @endif
                            @if($profile->payment_qr)
                                <img src="{{ asset('storage/'.$profile->payment_qr) }}" style="width:40px;height:40px;object-fit:contain;border:1px solid #ddd;vertical-align:middle"> UPI QR<br>
                            @endif
                            @if($profile->signature)
                                <img src="{{ asset('storage/'.$profile->signature) }}" style="height:30px;object-fit:contain;vertical-align:middle"> Signature
                            @endif
                        @else
                            <div style="color:#9ca3af;text-align:center;padding-top:2rem">
                                Fill in the details<br>to see preview
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- ── STEP 5: Default Invoice Text ── --}}
        <div class="is-card">
            <div class="is-sect">📝 Step 5 — Default Invoice Text</div>
            <div class="is-grid">
                <div class="is-field">
                    <label>Default Notes</label>
                    <textarea name="default_notes" rows="4" placeholder="e.g. Thank you for your business!">{{ old('default_notes', $profile->default_notes) }}</textarea>
                    <span class="is-hint">Pre-fills the Notes field on every new invoice.</span>
                </div>
                <div class="is-field">
                    <label>Default Terms & Conditions</label>
                    <textarea name="default_terms" rows="4" placeholder="1. Payment within 15 days&#10;2. Goods once sold not returnable&#10;3. GST as applicable">{{ old('default_terms', $profile->default_terms) }}</textarea>
                    <span class="is-hint">Pre-fills Terms on every new invoice.</span>
                </div>
            </div>
        </div>

        <div class="is-submit-bar">
            <a href="{{ route('invoices.index') }}" class="is-btn is-btn-secondary">Cancel</a>
            <button type="submit" class="is-btn is-btn-primary">💾 Save Settings</button>
        </div>
    </form>
</div>

<script>
function previewFile(input, dzId, phId) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const dz = document.getElementById(dzId);
        const ph = document.getElementById(phId);
        if (ph) ph.style.display = 'none';
        dz.classList.add('has-img');
        let img = dz.querySelector('img');
        if (!img) { img = document.createElement('img'); img.style.cssText = 'width:100%;height:100%;object-fit:contain;padding:6px'; dz.appendChild(img); }
        img.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endsection
