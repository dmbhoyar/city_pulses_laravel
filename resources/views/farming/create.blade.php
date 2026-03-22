@extends('layouts.app')

@section('content')
<h2 style="margin:6px 0 14px">Share Your Farming Blog</h2>
@include('farming._form', ['farming' => $farming, 'formAction' => route('farming.store'), 'method' => 'POST'])
@endsection
