@extends('layouts.app')

@section('content')
<h2>Edit Buy Record</h2>
@include('buy._form', ['buy' => $buy, 'formAction' => route('buy.update', $buy->id), 'method' => 'PATCH'])
@endsection
