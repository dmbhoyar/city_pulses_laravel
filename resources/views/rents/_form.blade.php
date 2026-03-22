@php
  $rent = $listing ?? $rent;
  $isEdit = isset($rent) && $rent->exists;
  $previewPhotos = old('existing_photos', $rent->photos ?? []);
@endphp

<style>
  .rent-form-grid{display:grid;grid-template-columns:2fr 1fr;gap:14px}
  .rent-form-card{background:#fff;border:1px solid #dbe7f8;border-radius:12px;padding:14px}
  .rent-form-card h3{margin:0 0 10px;color:#2f4e74}
  .rent-form-note{font-size:12px;color:#5c7698;line-height:1.6;margin:0 0 10px}
  .rent-form-field{margin-bottom:10px}
  .rent-form-field label{display:block;font-size:12px;font-weight:700;color:#46648a;margin-bottom:4px}
  .rent-form-field input,.rent-form-field select,.rent-form-field textarea{width:100%;border:1px solid #d3e2f4;border-radius:8px;padding:9px 10px;font-size:13px;background:#f8fbff;color:#223b5b}
  .rent-form-field textarea{resize:vertical;min-height:90px}
  .rent-form-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
  .rent-photo-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}
  .rent-photo-grid img{width:100%;height:88px;border-radius:8px;object-fit:cover;border:1px solid #dbe7f8}
  .rent-qr-wrap{background:#f8fbff;border:1px solid #dbe7f8;border-radius:10px;padding:10px;text-align:center}
  .rent-qr-row{display:flex;gap:8px;justify-content:center;flex-wrap:wrap}
  .rent-qr-row img{width:min(100%,165px);height:auto;border-radius:8px;border:1px solid #dbe7f8;background:#fff}
  .rent-fee{font-size:28px;font-weight:900;color:#2f4e74;margin:8px 0 2px}
  .rent-submit{width:100%;padding:12px;border:none;border-radius:10px;background:#f5c518;color:#2b1f00;font-weight:800;cursor:pointer}
  @media (max-width: 900px){
    .rent-form-grid{grid-template-columns:1fr}
  }
  @media (max-width: 700px){
    .rent-form-row{grid-template-columns:1fr}
    .rent-photo-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
  }
</style>

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
  @csrf
  @if(isset($method) && $method !== 'POST')
    @method($method)
  @endif

  <div class="rent-form-grid">
    <div class="rent-form-card">
      <h3>{{ $isEdit ? 'Rental Listing Details' : 'Post Rental Property' }}</h3>
      <p class="rent-form-note">Fill complete rental details. {{ $isEdit ? 'You can update listing information and add more photos.' : 'After submission, your listing stays in review until superadmin approval.' }}</p>

      <div class="rent-form-field">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', $rent->title ?? '') }}" required>
        @error('title')<div style="color:#c34141;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
      </div>

      <div class="rent-form-row">
        <div class="rent-form-field">
          <label for="subcategory">Property Type</label>
          <select id="subcategory" name="subcategory" required>
            <option value="">Select type</option>
            @foreach(['house' => 'House', 'flat' => 'Flat / Apartment', 'shop' => 'Shop', 'office' => 'Office', 'land' => 'Land'] as $catKey => $catLabel)
              <option value="{{ $catKey }}" {{ old('subcategory', $rent->subcategory ?? '') === $catKey ? 'selected' : '' }}>{{ $catLabel }}</option>
            @endforeach
          </select>
          @error('subcategory')<div style="color:#c34141;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
        </div>
        <div class="rent-form-field">
          <label for="price">Monthly Rent (₹)</label>
          <input type="number" id="price" name="price" value="{{ old('price', $rent->price ?? '') }}" min="0" step="1">
          @error('price')<div style="color:#c34141;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="rent-form-row">
        <div class="rent-form-field">
          <label for="city_id">City</label>
          <select id="city_id" name="city_id">
            <option value="">Select city</option>
            @foreach(($cities ?? []) as $city)
              <option value="{{ $city->id }}" {{ (string) old('city_id', $rent->city_id ?? '') === (string) $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
            @endforeach
          </select>
          @error('city_id')<div style="color:#c34141;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
        </div>
        <div class="rent-form-field">
          <label for="location">Location</label>
          <input type="text" id="location" name="location" value="{{ old('location', $rent->location ?? '') }}" placeholder="Area / locality">
          @error('location')<div style="color:#c34141;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="rent-form-field">
        <label for="description">Description</label>
        <textarea id="description" name="description">{{ old('description', $rent->description ?? '') }}</textarea>
        @error('description')<div style="color:#c34141;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
      </div>

      <div class="rent-form-field">
        <label for="contact_number">Contact Number</label>
        <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number', $rent->contact_number ?? auth()->user()->mobile_number ?? '') }}">
        @error('contact_number')<div style="color:#c34141;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
      </div>

      <div class="rent-form-field">
        <label for="photos">Photos (multiple)</label>
        <input type="file" id="photos" name="photos[]" accept="image/*" multiple>
        <div style="font-size:11px;color:#6d84a5;margin-top:4px">Upload related photos (JPG/PNG/WEBP).</div>
        @error('photos')<div style="color:#c34141;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
        @error('photos.*')<div style="color:#c34141;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
      </div>

      @if(is_array($previewPhotos) && count($previewPhotos))
        <div class="rent-form-field">
          <label>Uploaded Photos</label>
          <div class="rent-photo-grid">
            @foreach($previewPhotos as $photoPath)
              <img src="{{ \Illuminate\Support\Facades\Storage::url($photoPath) }}" alt="Listing Photo">
            @endforeach
          </div>
        </div>
      @endif
    </div>

    <div class="rent-form-card">
      @if(!$isEdit)
        <h3>Payment & Review</h3>
        <p class="rent-form-note">Posting fee is <strong>₹{{ number_format((float) ($listingFee ?? 10), 0) }}</strong>. Scan QR and upload proof. Your listing status will be <strong>In Review</strong>.</p>
        <div class="rent-qr-wrap">
          @if(!empty($paymentQrUrl ?? '') || !empty($paymentBarcodeUrl ?? ''))
            <div class="rent-qr-row">
              @if(!empty($paymentQrUrl ?? ''))
                <img src="{{ $paymentQrUrl }}" alt="Payment QR">
              @endif
              @if(!empty($paymentBarcodeUrl ?? ''))
                <img src="{{ $paymentBarcodeUrl }}" alt="Payment Barcode">
              @endif
            </div>
          @else
            <div style="font-size:12px;color:#9a5800">Payment QR not available right now. Contact admin.</div>
          @endif
          <div class="rent-fee">₹{{ number_format((float) ($listingFee ?? 10), 0) }}</div>
          <div style="font-size:12px;color:#6d84a5">Pay and submit transaction ID or screenshot</div>
        </div>

        <div class="rent-form-field" style="margin-top:10px">
          <label for="payment_transaction_id">Transaction ID</label>
          <input type="text" id="payment_transaction_id" name="payment_transaction_id" value="{{ old('payment_transaction_id') }}" placeholder="UPI / bank ref no.">
          @error('payment_transaction_id')<div style="color:#c34141;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
        </div>

        <div class="rent-form-field">
          <label for="payment_screenshot">Payment Screenshot</label>
          <input type="file" id="payment_screenshot" name="payment_screenshot" accept="image/*">
          @error('payment_screenshot')<div style="color:#c34141;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
        </div>

      @else
        <h3>Update Status</h3>
        <p class="rent-form-note">Editing updates listing details only. Review status is managed by superadmin.</p>
        <div style="font-size:13px;color:#425e82;background:#f8fbff;border:1px solid #dbe7f8;border-radius:10px;padding:10px">
          Current Status: <strong>{{ ucfirst($rent->status ?? 'pending') }}</strong>
        </div>
      @endif

      <div style="margin-top:14px">
        <button type="submit" class="rent-submit">{{ $isEdit ? 'Update Listing' : 'Submit for Review' }}</button>
      </div>
    </div>
  </div>
</form>
