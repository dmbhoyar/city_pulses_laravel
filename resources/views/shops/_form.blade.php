<div class="panel" style="max-width:760px;margin:0 auto;">
  <div class="card" style="padding:18px;">
    <form action="{{ $formAction }}" method="POST">
      @csrf
      @if(isset($method) && $method !== 'POST')
        @method($method)
      @endif
      <div class="field">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $shop->name ?? '') }}">
      </div>
      <div class="field">
        <label for="address">Address</label>
        <input type="text" id="address" name="address" value="{{ old('address', $shop->address ?? '') }}">
      </div>
      <div class="field">
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" value="{{ old('phone', $shop->phone ?? '') }}">
      </div>
      <div class="field">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="6">{{ old('description', $shop->description ?? '') }}</textarea>
      </div>
      <div class="actions">
        <button type="submit" class="button primary">Save Shop</button>
      </div>
    </form>
  </div>
</div>
