@extends('layouts.app')

@section('content')
<div class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:10px">
    <h1 style="margin:0">Buy & Sell Listing Reviews</h1>
    <a href="{{ route('admin.dashboard') }}" class="button">← Admin Dashboard</a>
  </div>

  <form method="GET" action="{{ route('admin.listings.index') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:10px">
    <label for="status" style="font-size:13px;font-weight:600;color:#46648a">Status</label>
    <select id="status" name="status">
      <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
      <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
      <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
      <option value="removed" {{ $status === 'removed' ? 'selected' : '' }}>Removed</option>
      <option value="" {{ $status === '' ? 'selected' : '' }}>All</option>
    </select>
    <button type="submit" class="button">Apply</button>
  </form>

  <table style="width:100%;border-collapse:collapse">
    <tr>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Listing</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Seller</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">City</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Payment Proof</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Status</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Action</th>
    </tr>

    @forelse($listings as $item)
      <tr>
        <td style="padding:8px;border-bottom:1px solid #eee">
          <div style="font-weight:700">{{ $item->title }}</div>
          <div style="font-size:12px;color:#5d7698">₹{{ number_format((float) $item->price, 0) }} · {{ ucfirst($item->subcategory ?: 'general') }}</div>
          <div style="margin-top:4px"><a href="{{ route('buy.show', $item) }}" target="_blank">Open listing</a></div>
        </td>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ $item->user?->email ?: '—' }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ $item->city?->name ?: '—' }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">
          <div>ID: {{ $item->payment_transaction_id ?: '—' }}</div>
          @if($item->payment_screenshot_path)
            <div><a href="{{ Storage::url($item->payment_screenshot_path) }}" target="_blank">View Screenshot</a></div>
          @endif
        </td>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ ucfirst($item->status) }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">
          <form method="POST" action="{{ route('admin.listings.status', $item) }}" style="display:grid;gap:6px;max-width:280px">
            @csrf
            @method('PATCH')
            <select name="status">
              <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="active" {{ $item->status === 'active' ? 'selected' : '' }}>Approve (Active)</option>
              <option value="rejected" {{ $item->status === 'rejected' ? 'selected' : '' }}>Reject</option>
              <option value="removed" {{ $item->status === 'removed' ? 'selected' : '' }}>Remove</option>
            </select>
            <textarea name="admin_notes" rows="2" placeholder="Optional admin note">{{ $item->admin_notes }}</textarea>
            <button type="submit" class="button">Update</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="6" style="padding:10px;border-bottom:1px solid #eee">No listing requests found.</td></tr>
    @endforelse
  </table>

  <div style="margin-top:10px">{{ $listings->links() }}</div>
</div>
@endsection
