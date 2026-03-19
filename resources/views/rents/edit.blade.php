@extends('layouts.app')

@section('content')
<h2>Edit Rent Record</h2>
@include('rents._form', ['rent' => $rent, 'formAction' => route('rents.update', $rent->id), 'method' => 'PATCH'])
@endsection
