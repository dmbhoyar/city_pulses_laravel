@extends('layouts.app')

@section('content')
<h2>New Buy Record</h2>
@include('buy._form', ['buy' => $buy, 'formAction' => route('buy.store'), 'method' => 'POST'])
@endsection
