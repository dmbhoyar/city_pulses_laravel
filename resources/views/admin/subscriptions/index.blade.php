@extends('layouts.app')

@section('content')
<div class="panel">
  <h1>Subscriptions & Template Unlocks</h1>

  <h2 style="margin-top:14px">Yearly Subscriptions</h2>
  <table style="width:100%;border-collapse:collapse">
    <tr>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">User</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Shop</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Plan</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Amount</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Payment Proof</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Status</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Action</th>
    </tr>
    @forelse($subscriptions as $sub)
      <tr>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ $sub->user?->email }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ $sub->shop?->name }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ $sub->plan_key ?: 'yearly_base' }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">₹{{ number_format((float) $sub->amount, 2) }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">
          <div>ID: {{ $sub->payment_transaction_id ?: '—' }}</div>
          @if($sub->payment_screenshot_path)
            <div><a href="{{ Storage::url($sub->payment_screenshot_path) }}" target="_blank">View Screenshot</a></div>
          @endif
          @if($sub->comment)
            <div style="margin-top:4px;font-size:12px;color:#556">{{ $sub->comment }}</div>
          @endif
        </td>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ ucfirst($sub->status) }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">
          <form action="{{ route('admin.subscriptions.status', $sub) }}" method="POST" style="display:grid;gap:6px;max-width:280px">
            @csrf
            @method('PATCH')
            <select name="status">
              <option value="pending" {{ $sub->status === 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="active" {{ $sub->status === 'active' ? 'selected' : '' }}>Active</option>
              <option value="cancelled" {{ $sub->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
              <option value="failed" {{ $sub->status === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
            <textarea name="admin_notes" rows="2" placeholder="Optional admin note...">{{ $sub->admin_notes }}</textarea>
            <button type="submit" class="button">Update</button>
            @if($sub->reviewed_at)
              <div style="font-size:12px;color:#667085">
                Reviewed {{ $sub->reviewed_at->format('d M Y, h:i A') }}
                @if($sub->reviewer)
                  by {{ $sub->reviewer->email }}
                @endif
              </div>
            @endif
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="7" style="padding:10px;border-bottom:1px solid #eee">No subscription records.</td></tr>
    @endforelse
  </table>

  <div style="margin-top:8px">{{ $subscriptions->links() }}</div>

  <h2 style="margin-top:22px">Template Unlock Requests (Astro Dynamic ₹{{ number_format((float) $astroUnlockPrice, 0) }})</h2>
  <p style="margin:6px 0 10px;color:#5f7595;font-size:13px">Configured SLA: {{ $unlockSlaHours }} hours</p>
  <table style="width:100%;border-collapse:collapse">
    <tr>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">User</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Shop</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Payment Proof</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Status</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Action</th>
    </tr>
    @forelse($unlockRequests as $req)
      <tr>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ $req->user?->email }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ $req->shop?->name }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">
          <div>ID: {{ $req->payment_transaction_id ?: '—' }}</div>
          @if($req->payment_screenshot_path)
            <div><a href="{{ Storage::url($req->payment_screenshot_path) }}" target="_blank">View Screenshot</a></div>
          @endif
          @if($req->comment)
            <div style="margin-top:4px;font-size:12px;color:#556">{{ $req->comment }}</div>
          @endif
        </td>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ ucfirst($req->status) }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">
          <form action="{{ route('admin.template_unlock_requests.status', $req) }}" method="POST" style="display:grid;gap:6px;max-width:280px">
            @csrf
            @method('PATCH')
            <select name="status">
              <option value="pending" {{ $req->status === 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="approved" {{ $req->status === 'approved' ? 'selected' : '' }}>Approve</option>
              <option value="rejected" {{ $req->status === 'rejected' ? 'selected' : '' }}>Reject</option>
            </select>
            <textarea name="admin_notes" rows="2" placeholder="Optional admin note...">{{ $req->admin_notes }}</textarea>
            <button type="submit" class="button">Update</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="5" style="padding:10px;border-bottom:1px solid #eee">No template unlock requests.</td></tr>
    @endforelse
  </table>

  <div style="margin-top:8px">{{ $unlockRequests->links() }}</div>

  <h2 style="margin-top:22px">Buy/Sell & Rent Listing Requests</h2>
  <p style="margin:6px 0 10px;color:#5f7595;font-size:13px">Submitted marketplace listings appear here for admin review. Approve to publish under the selected city.</p>
  <table style="width:100%;border-collapse:collapse">
    <tr>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Seller</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Listing</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">City</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Payment Proof</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Status</th>
      <th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Action</th>
    </tr>
    @forelse($listingRequests as $listing)
      <tr>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ $listing->user?->email ?: '—' }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">
          <div style="font-weight:700">{{ $listing->title }}</div>
          <div style="font-size:12px;color:#556">{{ $listing->category === 'rent' ? 'Rent' : 'Buy/Sell' }} · ₹{{ number_format((float) $listing->price, 0) }}{{ $listing->category === 'rent' ? '/mo' : '' }} · {{ ucfirst($listing->subcategory ?: 'general') }}</div>
          <div style="margin-top:4px"><a href="{{ $listing->category === 'rent' ? route('rents.show', $listing) : route('buy.show', $listing) }}" target="_blank">Open Listing</a></div>
        </td>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ $listing->city?->name ?: '—' }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">
          <div>ID: {{ $listing->payment_transaction_id ?: '—' }}</div>
          @if($listing->payment_screenshot_path)
            <div><a href="{{ Storage::url($listing->payment_screenshot_path) }}" target="_blank">View Screenshot</a></div>
          @endif
        </td>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ ucfirst($listing->status) }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">
          <form action="{{ route('admin.subscriptions.listings.status', $listing) }}" method="POST" style="display:grid;gap:6px;max-width:280px">
            @csrf
            @method('PATCH')
            <select name="status">
              <option value="pending" {{ $listing->status === 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="active" {{ $listing->status === 'active' ? 'selected' : '' }}>Approve (Active)</option>
              <option value="rejected" {{ $listing->status === 'rejected' ? 'selected' : '' }}>Reject</option>
              <option value="removed" {{ $listing->status === 'removed' ? 'selected' : '' }}>Remove</option>
            </select>
            <textarea name="admin_notes" rows="2" placeholder="Optional admin note...">{{ $listing->admin_notes }}</textarea>
            <button type="submit" class="button">Update</button>
            @if($listing->reviewed_at)
              <div style="font-size:12px;color:#667085">
                Reviewed {{ $listing->reviewed_at->format('d M Y, h:i A') }}
                @if($listing->reviewer)
                  by {{ $listing->reviewer->email }}
                @endif
              </div>
            @endif
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="6" style="padding:10px;border-bottom:1px solid #eee">No listing requests.</td></tr>
    @endforelse
  </table>

  <div style="margin-top:8px">{{ $listingRequests->links() }}</div>
</div>
@endsection
