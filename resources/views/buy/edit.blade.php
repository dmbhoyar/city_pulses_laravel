@extends('layouts.app')

@section('content')
<div class="panel" style="max-width:980px;margin:0 auto;">
	<div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:10px">
		<h2 style="margin:0">Edit Buy & Sell Listing</h2>
		<a href="{{ route('buy.show', $listing) }}" class="button">← Back to Listing</a>
	</div>
	@include('buy._form', ['buy' => $listing, 'formAction' => route('buy.update', $listing->id), 'method' => 'PATCH'])
</div>
@endsection
