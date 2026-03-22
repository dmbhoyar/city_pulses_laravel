@extends('layouts.app')

@section('content')
<div class="panel" style="max-width:980px;margin:0 auto;">
	<div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:10px">
		<h2 style="margin:0">Post Rental Listing</h2>
		<a href="{{ route('rents.index') }}" class="button">← Back to Rentals</a>
	</div>
	@include('rents._form', ['listing' => $listing, 'formAction' => route('rents.store'), 'method' => 'POST'])
</div>
@endsection
