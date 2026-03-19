@extends('layouts.app')

@section('content')
<h2>Edit Listing</h2>
@include('listings._form', ['listing' => $listing, 'formAction' => route('listings.update', $listing->id), 'method' => 'PATCH'])
@endsection
