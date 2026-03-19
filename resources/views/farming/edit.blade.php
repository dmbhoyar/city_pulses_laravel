@extends('layouts.app')

@section('content')
<h2>Edit Farming Record</h2>
@include('farming._form', ['farming' => $farming, 'formAction' => route('farming.update', $farming->id), 'method' => 'PATCH'])
@endsection
