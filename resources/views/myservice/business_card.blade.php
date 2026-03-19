@extends('layouts.app')

@section('content')
@include('shared.coming_soon', [
  'chip' => '🪪 Business Card Refresh',
  'title' => 'Digital Business Card is Coming Soon',
  'subtitle' => 'A polished business card builder with instant sharing and WhatsApp-ready format is under development.',
  'slogan' => 'Your name, your work, your city — one tap to share. ✨',
  'primaryUrl' => route('myservice'),
  'primaryLabel' => 'Back to MyService',
  'secondaryUrl' => route('home'),
  'secondaryLabel' => 'Go to Home'
])
@endsection
