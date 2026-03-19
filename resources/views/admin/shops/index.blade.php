@extends('layouts.app')

@section('content')
<div class="panel">
  <h1>Shops</h1>
  <table style="width:100%;border-collapse:collapse">
    <tr>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Name</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Owner</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">City</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Actions</th>
    </tr>
    @foreach($shops as $s)
      <tr>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ $s->name }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ $s->user?->email }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ $s->city?->name }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">
          <form action="{{ route('admin.shops.destroy', $s->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Remove shop?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="button danger">Remove</button>
          </form>
        </td>
      </tr>
    @endforeach
  </table>
</div>
@endsection
