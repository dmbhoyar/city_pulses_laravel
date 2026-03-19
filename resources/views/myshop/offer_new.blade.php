@extends('layouts.app')

@section('content')
<div class="panel">
  <h1 style="margin:0">Add Offer</h1>
  <div style="margin-top:12px">
    <form action="{{ route('myshop_offer_create') }}" method="POST">
      @csrf
      <div class="form-row">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title') }}">
      </div>
      <div class="form-row">
        <label for="content">Details</label>
        <textarea id="content" name="content">{{ old('content') }}</textarea>
      </div>
      <div class="form-row">
        <button type="submit" class="toggle-btn">Publish Offer</button>
      </div>
    </form>
  </div>
</div>
@endsection
