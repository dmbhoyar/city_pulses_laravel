<div class="panel" style="max-width:760px;margin:0 auto;">
  <div class="card" style="padding:18px;">
    <form action="{{ $formAction }}" method="POST">
      @csrf
      @if(isset($method) && $method !== 'POST')
        @method($method)
      @endif

      <div class="field">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', $job->title ?? '') }}">
      </div>
      <div class="field">
        <label for="company">Company</label>
        <input type="text" id="company" name="company" value="{{ old('company', $job->company ?? '') }}">
      </div>
      <div class="field">
        <label for="city_id">City</label>
        <select id="city_id" name="city_id">
          <option value="">Select city</option>
          @foreach(($cities ?? []) as $city)
            <option value="{{ $city->id }}" {{ (string) old('city_id', $job->city_id ?? '') === (string) $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label for="location">Location</label>
        <input type="text" id="location" name="location" value="{{ old('location', $job->location ?? '') }}">
      </div>
      <div class="field">
        <label for="category">Category</label>
        <select id="category" name="category">
          <option value="">Select category</option>
          @foreach(['IT', 'Government', 'Sales', 'Marketing', 'Support', 'Delivery', 'Operations', 'Finance', 'Healthcare', 'Education'] as $cat)
            <option value="{{ $cat }}" {{ old('category', $job->category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label for="external_url">External URL (optional) - shop/government apply link</label>
        <input type="text" id="external_url" name="external_url" value="{{ old('external_url', $job->external_url ?? '') }}">
      </div>
      <div class="field">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="6">{{ old('description', $job->description ?? '') }}</textarea>
      </div>
      <div class="actions">
        <button type="submit" class="button primary">Save Job</button>
      </div>
    </form>
  </div>
</div>
