@extends('layouts.app')

@section('content')
<h2 style="margin:6px 0 14px">Edit Farming Blog</h2>
@include('farming._form', ['farming' => $farming, 'formAction' => route('farming.update', $farming->id), 'method' => 'PATCH'])
@endsection
