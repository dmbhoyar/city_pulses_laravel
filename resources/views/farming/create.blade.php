@extends('layouts.app')

@section('content')
<h2>New Farming Record</h2>
@include('farming._form', ['farming' => $farming, 'formAction' => route('farming.store'), 'method' => 'POST'])
@endsection
