@extends('layouts.app')

@section('content')
@include('shared.coming_soon', [
  'chip' => '🛠️ MyService Upgrade',
  'title' => 'Service Profiles are Coming Soon',
  'subtitle' => 'We are building a cleaner and faster MyService experience for plumbers, mechanics, and local professionals.',
  'slogan' => 'Set your service page in minutes. Get customers in your city, not just clicks. 🚀',
  'primaryUrl' => route('home'),
  'primaryLabel' => 'Back to Home',
  'secondaryUrl' => route('about'),
  'secondaryLabel' => 'About AajchaOffer'
])
@endsection
