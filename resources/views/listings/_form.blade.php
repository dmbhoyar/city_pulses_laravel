<div class="panel" style="max-width:760px;margin:0 auto;">
  <div class="card" style="padding:18px;">
    <form action="{{ $formAction }}" method="POST">
      @csrf
      @if(isset($method) && $method !== 'POST')
        @method($method)
      @endif
      <div class="field">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', $listing->title ?? '') }}">
      </div>
      <div class="field">
        <label for="category">Category</label>
        <select id="category" name="category">
          @foreach(['sell','rent','service','vehicle','land'] as $cat)
            <option value="{{ $cat }}" {{ old('category', $listing->category ?? '') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label for="price">Price</label>
        <input type="number" step="0.01" id="price" name="price" value="{{ old('price', $listing->price ?? '') }}">
      </div>
      <div class="field">
        <label for="location">Location</label>
        <input type="text" id="location" name="location" value="{{ old('location', $listing->location ?? '') }}">
      </div>
      <div class="field">
        <label for="contact_number">Contact Number</label>
        <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number', $listing->contact_number ?? '') }}">
      </div>
      <div class="field">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="6">{{ old('description', $listing->description ?? '') }}</textarea>
      </div>
      <div class="actions">
        <button type="submit" class="button primary">Save Listing</button>
      </div>
    </form>
  </div>
</div>
