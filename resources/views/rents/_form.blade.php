<div class="panel" style="max-width:760px;margin:0 auto;">
  <div class="card" style="padding:18px;">
    <form action="{{ $formAction }}" method="POST">
      @csrf
      @if(isset($method) && $method !== 'POST')
        @method($method)
      @endif
      <div class="field">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', $rent->title ?? '') }}">
      </div>
      <div class="field">
        <label for="city_id">City</label>
        <select id="city_id" name="city_id">
          @foreach(\App\Models\City::all() as $city)
            <option value="{{ $city->id }}" {{ old('city_id', $rent->city_id ?? '') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="6">{{ old('description', $rent->description ?? '') }}</textarea>
      </div>
      <div class="actions">
        <button type="submit" class="button primary">Save</button>
      </div>
    </form>
  </div>
</div>
