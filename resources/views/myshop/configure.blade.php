@extends('layouts.app')

@section('content')
<div class="panel">
  <h1 style="margin:0">Configure Shop</h1>
  <div style="margin-top:12px">
    <form action="{{ route('configure_myshop') }}" method="POST">
      @csrf
      @method('PATCH')
      <div class="form-row">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $shop->name ?? '') }}">
      </div>
      <div class="form-row">
        <label for="description">Description</label>
        <textarea id="description" name="description">{{ old('description', $shop->description ?? '') }}</textarea>
      </div>
      <div class="form-row">
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" value="{{ old('phone', $shop->phone ?? '') }}">
      </div>
      <div class="form-row">
        <label for="address">Address</label>
        <input type="text" id="address" name="address" value="{{ old('address', $shop->address ?? '') }}">
      </div>
      <div class="form-row">
        <button type="submit" class="toggle-btn">Save</button>
      </div>
    </form>
    @if(isset($shop) && $shop)
      @include('shared.page_config_form')
    @endif
  </div>
</div>
@endsection
