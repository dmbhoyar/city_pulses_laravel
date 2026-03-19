@extends('layouts.app')

@section('content')
<h2>New Listing</h2>
@include('listings._form', ['listing' => $listing, 'formAction' => route('listings.store'), 'method' => 'POST'])
@endsection
