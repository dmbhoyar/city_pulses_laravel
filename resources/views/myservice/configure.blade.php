@extends('layouts.app')

@section('content')
@include('shared.coming_soon', [
  'chip' => '⚙️ Configuration in Progress',
  'title' => 'Service Page Setup is Coming Soon',
  'subtitle' => 'We are simplifying the setup flow so every business owner can launch a page without technical steps.',
  'slogan' => 'Less setup. More business. 💼',
  'primaryUrl' => route('myservice'),
  'primaryLabel' => 'Back to MyService',
  'secondaryUrl' => route('home'),
  'secondaryLabel' => 'Go to Home'
])
@endsection
