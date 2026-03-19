@extends('layouts.app')

@section('content')
@include('shared.coming_soon', [
  'chip' => '💳 Subscription Update',
  'title' => 'Smart Plans are Coming Soon',
  'subtitle' => 'We are preparing clear yearly plans with better onboarding and transparent pricing for every local business.',
  'slogan' => 'Grow your dukaan online — without heavy monthly cost. 📈',
  'primaryUrl' => route('shop_dashboard'),
  'primaryLabel' => 'Back to Dashboard',
  'secondaryUrl' => route('about'),
  'secondaryLabel' => 'Learn More'
])
@endsection
