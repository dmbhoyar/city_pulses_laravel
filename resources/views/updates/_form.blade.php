<div class="panel" style="max-width:760px;margin:0 auto;">
  <div class="card" style="padding:18px;">
    <form action="{{ $formAction }}" method="POST">
      @csrf
      @if(isset($method) && $method !== 'POST')
        @method($method)
      @endif
      <div class="field">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', $update->title ?? '') }}">
      </div>
      <div class="field">
        <label for="city_id">City</label>
        <select id="city_id" name="city_id">
          <option value="">-- All Cities --</option>
          @foreach(\App\Models\City::all() as $city)
            <option value="{{ $city->id }}" {{ old('city_id', $update->city_id ?? '') == $city->id ? 'selected' : '' }}>{{ city_display_name($city->name) }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label for="update_type">Type</label>
        <select id="update_type" name="update_type">
          @foreach(['general','offer','event'] as $type)
            <option value="{{ $type }}" {{ old('update_type', $update->update_type ?? 'general') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label for="content">Content</label>
        <textarea id="content" name="content" rows="6">{{ old('content', $update->content ?? '') }}</textarea>
      </div>
      <div class="actions">
        <button type="submit" class="button primary">Save</button>
      </div>
    </form>
  </div>
</div>
