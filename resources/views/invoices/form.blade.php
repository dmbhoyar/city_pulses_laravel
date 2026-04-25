@extends('layouts.app')

@php
  $editing    = isset($invoice);
  $pageTitle  = $editing ? 'Edit Invoice '.$invoice->invoice_number : 'New Invoice';
  $formAction = $editing ? route('invoices.update', $invoice) : route('invoices.store');
  $method     = $editing ? 'PUT' : 'POST';

  // Business info: snapshot on invoice if editing, else from business profile + shop fallback
  $defaults   = \App\Http\Controllers\InvoiceController::resolveBusinessDefaults($profile, $shop);
  $bizName    = $editing ? ($invoice->business_name    ?? '') : $defaults['biz_name'];
  $bizPhone   = $editing ? ($invoice->business_phone   ?? '') : $defaults['biz_phone'];
  $bizAddr    = $editing ? ($invoice->business_address ?? '') : $defaults['biz_address'];
  $bizGstin   = $editing ? ($invoice->business_gstin   ?? '') : $defaults['biz_gstin'];
  $existItems = $editing ? $invoice->items->toArray() : [];

  // Profile completeness — warn if bank/QR not configured
  $profileOk  = $profile && ($profile->bank_name || $profile->payment_qr);
@endphp

@section('title', $pageTitle)

@section('content')
<style>
.inv-form-wrap{max-width:860px;margin:0 auto;padding:1.2rem 1rem 3rem}
.inv-form-head{display:flex;align-items:center;gap:1rem;margin-bottom:1.4rem;flex-wrap:wrap}
.inv-form-title{font-family:'Playfair Display',serif;font-size:1.6rem;color:#1a1208;margin:0}
.inv-back{font-size:.82rem;color:#b91c1c;text-decoration:none}
.inv-back:hover{text-decoration:underline}
.inv-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem 1.4rem;margin-bottom:1.1rem}
.inv-section-title{font-size:.7rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#9ca3af;margin:0 0 .9rem;padding-bottom:.5rem;border-bottom:1px solid #f3f4f6}
.inv-grid{display:grid;grid-template-columns:1fr 1fr;gap:.75rem}
.inv-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:.75rem}
.inv-field{display:flex;flex-direction:column;gap:.3rem}
.inv-field label{font-size:.78rem;font-weight:700;color:#374151}
.inv-field input,.inv-field select,.inv-field textarea{border:1px solid #d1d5db;border-radius:7px;padding:.5rem .75rem;font-size:.875rem;outline:none;width:100%;box-sizing:border-box;font-family:inherit}
.inv-field input:focus,.inv-field select:focus,.inv-field textarea:focus{border-color:#1a1208;box-shadow:0 0 0 2px rgba(26,18,8,.08)}
.inv-field textarea{resize:vertical;min-height:60px}
.inv-span2{grid-column:span 2}
.inv-span3{grid-column:span 3}

/* Items table */
.inv-items-wrap{overflow-x:auto}
.inv-items-table{width:100%;border-collapse:collapse;min-width:640px}
.inv-items-table th{background:#f9fafb;padding:.5rem .6rem;font-size:.72rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;text-align:left;border:1px solid #e5e7eb}
.inv-items-table td{padding:.4rem .5rem;border:1px solid #e5e7eb;vertical-align:middle}
.inv-items-table input,.inv-items-table select{border:1px solid #d1d5db;border-radius:5px;padding:.35rem .5rem;font-size:.82rem;width:100%;box-sizing:border-box;outline:none}
.inv-items-table input:focus,.inv-items-table select:focus{border-color:#1a1208}
.inv-row-del{background:none;border:none;color:#dc2626;cursor:pointer;font-size:1rem;padding:.2rem .4rem;border-radius:4px}
.inv-row-del:hover{background:#fef2f2}
.inv-add-row{background:#f9fafb;border:1px dashed #d1d5db;border-radius:7px;padding:.5rem;width:100%;cursor:pointer;font-size:.82rem;color:#6b7280;font-weight:600;margin-top:.5rem}
.inv-add-row:hover{background:#f3f4f6;color:#1a1208;border-color:#9ca3af}

/* Totals */
.inv-totals{margin-left:auto;max-width:280px;margin-top:.75rem}
.inv-totals-row{display:flex;justify-content:space-between;align-items:center;padding:.35rem 0;font-size:.875rem;border-bottom:1px solid #f3f4f6}
.inv-totals-row:last-child{border-bottom:none;font-size:1rem;font-weight:800;padding-top:.5rem}
.inv-totals-row span:first-child{color:#6b7280}
.inv-totals-row span:last-child{font-weight:700;color:#1a1208}

/* Submit */
.inv-submit-bar{display:flex;gap:.75rem;justify-content:flex-end;flex-wrap:wrap}
.inv-btn{padding:.6rem 1.4rem;border-radius:7px;font-size:.875rem;font-weight:700;cursor:pointer;text-decoration:none;border:none}
.inv-btn-primary{background:#1a1208;color:#fff}
.inv-btn-primary:hover{background:#b91c1c}
.inv-btn-secondary{background:#f3f4f6;color:#374151;border:1px solid #d1d5db}
.inv-btn-secondary:hover{background:#e5e7eb}

@media(max-width:600px){
  .inv-grid,.inv-grid-3{grid-template-columns:1fr}
  .inv-span2,.inv-span3{grid-column:span 1}
}
</style>

<div class="inv-form-wrap">
    <div class="inv-form-head">
        <a href="{{ route('invoices.index') }}" class="inv-back">← Back to Invoices</a>
        <h1 class="inv-form-title">{{ $pageTitle }}</h1>
    </div>

    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem">
            <strong>Please fix these errors:</strong>
            <ul style="margin:.35rem 0 0 1.1rem;padding:0">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $formAction }}" id="inv-form" enctype="multipart/form-data">
        @csrf
        @method($method)

        {{-- ── Business Info (snapshot, editable) ── --}}
        <div class="inv-card">
            <div class="inv-section-title">Your Business Info
                <a href="{{ route('invoices.settings') }}" style="font-size:.7rem;font-weight:600;color:#2563eb;margin-left:auto;text-decoration:none">⚙ Manage Settings →</a>
            </div>

            @if(!$profileOk)
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:7px;padding:.6rem .9rem;font-size:.8rem;color:#92400e;margin-bottom:.85rem;display:flex;gap:.5rem;align-items:center">
                ⚠️ <span>Bank details, payment QR & signature not configured yet.
                <a href="{{ route('invoices.settings') }}" style="color:#92400e;font-weight:700;text-decoration:underline">Set up Invoice Settings →</a></span>
            </div>
            @endif

            <div class="inv-grid">
                <div class="inv-field">
                    <label>Business Name</label>
                    <input type="text" name="business_name" value="{{ old('business_name', $bizName) }}" placeholder="Your shop / service name">
                </div>
                <div class="inv-field">
                    <label>Business Phone</label>
                    <input type="text" name="business_phone" value="{{ old('business_phone', $bizPhone) }}" placeholder="Phone number">
                </div>
                <div class="inv-field inv-span2">
                    <label>Business Address</label>
                    <textarea name="business_address" rows="2" placeholder="Full address">{{ old('business_address', $bizAddr) }}</textarea>
                </div>
                <div class="inv-field">
                    <label>GSTIN</label>
                    <input type="text" name="business_gstin" value="{{ old('business_gstin', $bizGstin) }}" placeholder="27XXXXX1234X1Z5" style="font-family:monospace">
                </div>
                <div class="inv-field" style="align-self:end">
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:7px;padding:.5rem .75rem;font-size:.76rem;color:#166534">
                        ✓ Bank, QR & signature from
                        <a href="{{ route('invoices.settings') }}" style="color:#166534;font-weight:700">Invoice Settings</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Client Info ── --}}
        <div class="inv-card">
            <div class="inv-section-title">Client / Customer</div>
            <div class="inv-grid">
                <div class="inv-field">
                    <label>Client Name <span style="color:#dc2626">*</span></label>
                    <input type="text" name="client_name" value="{{ old('client_name', $editing ? $invoice->client_name : '') }}" placeholder="Full name" required>
                </div>
                <div class="inv-field">
                    <label>Phone</label>
                    <input type="text" name="client_phone" value="{{ old('client_phone', $editing ? $invoice->client_phone : '') }}" placeholder="Mobile number">
                </div>
                <div class="inv-field">
                    <label>Email</label>
                    <input type="email" name="client_email" value="{{ old('client_email', $editing ? $invoice->client_email : '') }}" placeholder="client@email.com">
                </div>
                <div class="inv-field">
                    <label>Address</label>
                    <input type="text" name="client_address" value="{{ old('client_address', $editing ? $invoice->client_address : '') }}" placeholder="Client address">
                </div>
            </div>
        </div>

        {{-- ── Invoice Meta ── --}}
        <div class="inv-card">
            <div class="inv-section-title">Invoice Details</div>
            <div class="inv-grid-3">
                <div class="inv-field">
                    <label>Invoice Date <span style="color:#dc2626">*</span></label>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', $editing ? $invoice->invoice_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                </div>
                <div class="inv-field">
                    <label>Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $editing ? $invoice->due_date?->format('Y-m-d') : '') }}">
                </div>
                <div class="inv-field">
                    <label>Currency</label>
                    <select name="currency" disabled style="background:#f9fafb;color:#9ca3af">
                        <option selected>INR (₹)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ── Line Items ── --}}
        <div class="inv-card">
            <div class="inv-section-title">Items / Services</div>
            <div class="inv-items-wrap">
                <table class="inv-items-table" id="items-table">
                    <thead>
                        <tr>
                            <th style="width:32%">Description</th>
                            <th style="width:11%">HSN/SAC</th>
                            <th style="width:9%">Qty</th>
                            <th style="width:9%">Unit</th>
                            <th style="width:13%">Price (₹)</th>
                            <th style="width:10%">GST %</th>
                            <th style="width:13%">Amount (₹)</th>
                            <th style="width:3%"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        {{-- filled by JS --}}
                    </tbody>
                </table>
            </div>
            <button type="button" class="inv-add-row" onclick="addRow()">+ Add Item</button>

            {{-- Totals --}}
            <div class="inv-totals">
                <div class="inv-totals-row">
                    <span>Subtotal</span>
                    <span id="disp-subtotal">₹0.00</span>
                </div>
                <div class="inv-totals-row">
                    <span>GST / Tax</span>
                    <span id="disp-tax">₹0.00</span>
                </div>
                <div class="inv-totals-row">
                    <span>Total</span>
                    <span id="disp-total">₹0.00</span>
                </div>
            </div>
        </div>

        {{-- ── Notes & Terms ── --}}
        <div class="inv-card">
            <div class="inv-section-title">Notes & Terms</div>
            <div class="inv-grid">
                <div class="inv-field">
                    <label>Notes (shown on invoice)</label>
                    <textarea name="notes" rows="3" placeholder="Thank you for your business!">{{ old('notes', $editing ? $invoice->notes : $profile->default_notes) }}</textarea>
                </div>
                <div class="inv-field">
                    <label>Terms & Conditions</label>
                    <textarea name="terms" rows="3" placeholder="1. Payment within 15 days…">{{ old('terms', $editing ? $invoice->terms : $profile->default_terms) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="inv-submit-bar">
            <a href="{{ route('invoices.index') }}" class="inv-btn inv-btn-secondary">Cancel</a>
            <button type="submit" class="inv-btn inv-btn-primary">{{ $editing ? '💾 Update Invoice' : '✓ Create Invoice' }}</button>
        </div>
    </form>
</div>

<script>
const existingItems = @json($existItems);
let rowCount = 0;

function addRow(data = {}) {
  rowCount++;
  const i = rowCount;
  const desc  = data.description || '';
  const hsn   = data.hsn_sac || '';
  const qty   = data.quantity || '1';
  const unit  = data.unit || '';
  const price = data.unit_price || '';
  const tax   = data.tax_rate || '0';

  const tr = document.createElement('tr');
  tr.dataset.row = i;
  tr.innerHTML = `
    <td><input type="text" name="items[${i}][description]" value="${escHtml(desc)}" placeholder="Item / service description" required oninput="recalc()"></td>
    <td><input type="text" name="items[${i}][hsn_sac]" value="${escHtml(hsn)}" placeholder="HSN"></td>
    <td><input type="number" name="items[${i}][quantity]" value="${qty}" min="0.001" step="0.001" class="qty" oninput="recalc()" style="text-align:right"></td>
    <td><input type="text" name="items[${i}][unit]" value="${escHtml(unit)}" placeholder="pcs"></td>
    <td><input type="number" name="items[${i}][unit_price]" value="${price}" min="0" step="0.01" class="price" oninput="recalc()" style="text-align:right"></td>
    <td>
      <select name="items[${i}][tax_rate]" class="tax-rate" onchange="recalc()">
        ${[0,5,9,12,18,28].map(r=>`<option value="${r}" ${parseFloat(tax)===r?'selected':''}>${r}%</option>`).join('')}
      </select>
    </td>
    <td><input type="text" name="items[${i}][amount]" class="row-amount" readonly style="background:#f9fafb;text-align:right;font-weight:700;color:#1a1208"></td>
    <td><button type="button" class="inv-row-del" onclick="removeRow(this)" title="Remove">×</button></td>
  `;
  document.getElementById('items-body').appendChild(tr);
  recalc();
}

function removeRow(btn) {
  const tbody = document.getElementById('items-body');
  if (tbody.rows.length <= 1) { alert('At least one item is required.'); return; }
  btn.closest('tr').remove();
  recalc();
}

function recalc() {
  let subtotal = 0, taxTotal = 0;
  document.querySelectorAll('#items-body tr').forEach(tr => {
    const qty   = parseFloat(tr.querySelector('.qty')?.value) || 0;
    const price = parseFloat(tr.querySelector('.price')?.value) || 0;
    const rate  = parseFloat(tr.querySelector('.tax-rate')?.value) || 0;
    const base  = qty * price;
    const tax   = base * rate / 100;
    const total = base + tax;
    const amtEl = tr.querySelector('.row-amount');
    if (amtEl) amtEl.value = total.toFixed(2);
    subtotal += base;
    taxTotal += tax;
  });
  document.getElementById('disp-subtotal').textContent = '₹' + subtotal.toFixed(2);
  document.getElementById('disp-tax').textContent = '₹' + taxTotal.toFixed(2);
  document.getElementById('disp-total').textContent = '₹' + (subtotal + taxTotal).toFixed(2);
}

function escHtml(str) {
  return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function previewQr(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = function(e) {
    const box = document.getElementById('qr-preview-box');
    const ph  = document.getElementById('qr-placeholder');
    let img = document.getElementById('qr-preview-img');
    if (!img) {
      img = document.createElement('img');
      img.id = 'qr-preview-img';
      img.style.cssText = 'width:100%;height:100%;object-fit:contain';
      box.innerHTML = '';
      box.appendChild(img);
    }
    if (ph) ph.style.display = 'none';
    img.src = e.target.result;
  };
  reader.readAsDataURL(input.files[0]);
}

// Init with existing items or one blank row
document.addEventListener('DOMContentLoaded', () => {
  if (existingItems.length > 0) {
    existingItems.forEach(item => addRow(item));
  } else {
    addRow();
  }
});
</script>
@endsection
