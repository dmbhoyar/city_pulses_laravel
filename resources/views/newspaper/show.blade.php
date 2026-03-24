@extends('layouts.app')

@section('content')
<div class="panel">
  <h1 style="margin:0;text-align:center">AajchaOffer - {{ isset($city) ? city_display_name($city->name) : __('ui.all_cities') }} Newspaper</h1>
  <div style="margin-top:12px">
    @foreach($updates as $u)
      <div class="card">
        <h2>{{ $u->title }}</h2>
        <div><small>{{ $u->published_at ? $u->published_at->format('d M Y') : $u->created_at->format('d M Y') }}</small></div>
        <div>{!! simple_format($u->content) !!}</div>
      </div>
    @endforeach
  </div>

  <div style="text-align:center;margin-top:20px">
    <button onclick="window.print()" class="toggle-btn">Print / Save as PDF</button>
  </div>
</div>
@endsection
