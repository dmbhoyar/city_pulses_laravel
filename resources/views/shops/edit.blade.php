@extends('layouts.app')

@section('content')
<h2>Edit Shop</h2>
@include('shops._form', ['shop' => $shop, 'formAction' => route('shops.update', $shop->id), 'method' => 'PATCH'])
@endsection
