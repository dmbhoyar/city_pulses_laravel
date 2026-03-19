@extends('layouts.app')

@section('content')
<h2>New Shop</h2>
@include('shops._form', ['shop' => $shop, 'formAction' => route('shops.store'), 'method' => 'POST'])
@endsection
