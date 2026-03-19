@extends('layouts.app')

@section('content')
<h2>New Rent Record</h2>
@include('rents._form', ['rent' => $rent, 'formAction' => route('rents.store'), 'method' => 'POST'])
@endsection
