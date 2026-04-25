@extends('layouts.app')

@section('title', 'My Invoices')

@section('content')
<style>
:root { --inv-blue:#1e40af;--inv-blue-lt:#dbeafe;--inv-green:#15803d;--inv-green-lt:#dcfce7;--inv-amber:#b45309;--inv-amber-lt:#fef3c7;--inv-red:#dc2626;--inv-red-lt:#fee2e2;--inv-gray:#374151;--inv-gray-lt:#f3f4f6; }
.inv-wrap { max-width:900px;margin:0 auto;padding:1.2rem 1rem 3rem }
.inv-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:1.4rem;flex-wrap:wrap;gap:.6rem }
.inv-title { font-family:'Playfair Display',serif;font-size:1.7rem;color:#1a1208;margin:0 }
.inv-btn-new { background:#1a1208;color:#fff;text-decoration:none;padding:.55rem 1.1rem;border-radius:6px;font-size:.85rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
.inv-btn-new:hover { background:#b91c1c;color:#fff }

/* Stats */
.inv-stats { display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:.75rem;margin-bottom:1.4rem }
.inv-stat { background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:.9rem 1rem;text-align:center }
.inv-stat-num { font-size:1.5rem;font-weight:800;line-height:1.1 }
.inv-stat-label { font-size:.72rem;color:#6b7280;margin-top:.2rem;text-transform:uppercase;letter-spacing:.5px }
.inv-stat.total .inv-stat-num { color:#1a1208 }
.inv-stat.paid .inv-stat-num { color:var(--inv-green) }
.inv-stat.sent .inv-stat-num { color:var(--inv-blue) }
.inv-stat.draft .inv-stat-num { color:var(--inv-amber) }
.inv-stat.earned .inv-stat-num { color:var(--inv-green);font-size:1.2rem }

/* Filters */
.inv-filters { display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem;align-items:center }
.inv-filter { padding:.38rem .85rem;border-radius:20px;font-size:.8rem;font-weight:600;text-decoration:none;border:1px solid #d1d5db;color:#374151;background:#fff }
.inv-filter:hover,.inv-filter.active { background:#1a1208;color:#fff;border-color:#1a1208 }
.inv-filter.f-paid.active { background:var(--inv-green);border-color:var(--inv-green) }
.inv-filter.f-sent.active { background:var(--inv-blue);border-color:var(--inv-blue) }
.inv-filter.f-draft.active { background:var(--inv-amber);border-color:var(--inv-amber) }
.inv-filter.f-cancelled.active { background:var(--inv-gray);border-color:var(--inv-gray) }
.inv-search { display:flex;flex:1;min-width:180px;max-width:300px;gap:.3rem }
.inv-search input { flex:1;border:1px solid #d1d5db;border-radius:20px;padding:.38rem .9rem;font-size:.82rem;outline:none }
.inv-search input:focus { border-color:#1a1208 }
.inv-search button { background:#1a1208;color:#fff;border:none;border-radius:20px;padding:.38rem .75rem;cursor:pointer;font-size:.82rem }

/* Table */
.inv-table-wrap { background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden }
.inv-table { width:100%;border-collapse:collapse }
.inv-table th { background:#f9fafb;padding:.65rem 1rem;font-size:.75rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;text-align:left;border-bottom:1px solid #e5e7eb }
.inv-table td { padding:.75rem 1rem;border-bottom:1px solid #f3f4f6;font-size:.875rem;vertical-align:middle }
.inv-table tr:last-child td { border-bottom:none }
.inv-table tr:hover td { background:#f9fafb }
.inv-num { font-weight:700;color:#1a1208;font-family:monospace;font-size:.8rem }
.inv-client { font-weight:600;color:#1a1208 }
.inv-sub { font-size:.75rem;color:#9ca3af;margin-top:.1rem }
.inv-amount { font-weight:700;color:#1a1208 }
.inv-badge { display:inline-block;padding:.18rem .55rem;border-radius:12px;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.3px }
.inv-overdue { background:#fef2f2;color:#dc2626;border:1px solid #fecaca }
.inv-actions { display:flex;gap:.3rem }
.inv-act { width:30px;height:30px;border-radius:6px;border:1px solid #e5e7eb;background:#f9fafb;color:#374151;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:.82rem;cursor:pointer }
.inv-act:hover { background:#1a1208;color:#fff;border-color:#1a1208 }
.inv-act.danger:hover { background:#dc2626;border-color:#dc2626 }
.inv-empty { text-align:center;padding:3rem 1rem;color:#9ca3af }
.inv-empty svg { opacity:.3;margin-bottom:.75rem }

@media(max-width:600px){
  .inv-table th:nth-child(3),.inv-table td:nth-child(3) { display:none }
  .inv-table th:nth-child(4),.inv-table td:nth-child(4) { display:none }
}
</style>

<div class="inv-wrap">
    {{-- Header --}}
    <div class="inv-header">
        <h1 class="inv-title">📄 My Invoices</h1>
        <a href="{{ route('invoices.create') }}" class="inv-btn-new">
            <span style="font-size:1.1rem">+</span> New Invoice
        </a>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem;font-weight:600">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="inv-stats">
        <div class="inv-stat total">
            <div class="inv-stat-num">{{ $stats['total'] }}</div>
            <div class="inv-stat-label">Total</div>
        </div>
        <div class="inv-stat draft">
            <div class="inv-stat-num">{{ $stats['draft'] }}</div>
            <div class="inv-stat-label">Draft</div>
        </div>
        <div class="inv-stat sent">
            <div class="inv-stat-num">{{ $stats['sent'] }}</div>
            <div class="inv-stat-label">Sent</div>
        </div>
        <div class="inv-stat paid">
            <div class="inv-stat-num">{{ $stats['paid'] }}</div>
            <div class="inv-stat-label">Paid</div>
        </div>
        <div class="inv-stat earned">
            <div class="inv-stat-num">₹{{ number_format($stats['total_earned'], 0) }}</div>
            <div class="inv-stat-label">Earned</div>
        </div>
    </div>

    {{-- Filters + Search --}}
    <div class="inv-filters">
        @php $cur = request('status',''); $q = request('q',''); @endphp
        <a href="{{ route('invoices.index', ['q'=>$q]) }}" class="inv-filter {{ $cur==='' ? 'active' : '' }}">All</a>
        <a href="{{ route('invoices.index', ['status'=>'draft','q'=>$q]) }}" class="inv-filter f-draft {{ $cur==='draft' ? 'active' : '' }}">Draft</a>
        <a href="{{ route('invoices.index', ['status'=>'sent','q'=>$q]) }}" class="inv-filter f-sent {{ $cur==='sent' ? 'active' : '' }}">Sent</a>
        <a href="{{ route('invoices.index', ['status'=>'paid','q'=>$q]) }}" class="inv-filter f-paid {{ $cur==='paid' ? 'active' : '' }}">Paid</a>
        <a href="{{ route('invoices.index', ['status'=>'cancelled','q'=>$q]) }}" class="inv-filter f-cancelled {{ $cur==='cancelled' ? 'active' : '' }}">Cancelled</a>

        <form method="GET" action="{{ route('invoices.index') }}" class="inv-search" style="margin-left:auto">
            @if($cur) <input type="hidden" name="status" value="{{ $cur }}"> @endif
            <input type="search" name="q" placeholder="Search client, #number…" value="{{ $q }}">
            <button type="submit">🔍</button>
        </form>
    </div>

    {{-- Table --}}
    <div class="inv-table-wrap">
        @if($invoices->isEmpty())
            <div class="inv-empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p style="font-weight:600;color:#374151;font-size:1rem">No invoices yet</p>
                <p style="font-size:.85rem">Create your first invoice to get started</p>
                <a href="{{ route('invoices.create') }}" style="display:inline-block;margin-top:.75rem;background:#1a1208;color:#fff;padding:.5rem 1.2rem;border-radius:6px;text-decoration:none;font-weight:700;font-size:.85rem">+ Create Invoice</a>
            </div>
        @else
            <table class="inv-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Due</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $inv)
                    @php $badge = $inv->status_badge; $overdue = $inv->isOverdue(); @endphp
                    <tr>
                        <td><div class="inv-num">{{ $inv->invoice_number }}</div></td>
                        <td>
                            <div class="inv-client">{{ $inv->client_name }}</div>
                            @if($inv->client_phone)
                                <div class="inv-sub">{{ $inv->client_phone }}</div>
                            @endif
                        </td>
                        <td style="color:#374151">{{ optional($inv->invoice_date)->format('d M Y') }}</td>
                        <td>
                            @if($inv->due_date)
                                <span style="color:{{ $overdue ? '#dc2626' : '#374151' }};font-weight:{{ $overdue ? 700 : 400 }}">
                                    {{ $inv->due_date->format('d M Y') }}
                                    @if($overdue) ⚠ @endif
                                </span>
                            @else
                                <span style="color:#d1d5db">—</span>
                            @endif
                        </td>
                        <td><span class="inv-amount">₹{{ number_format($inv->total, 2) }}</span></td>
                        <td>
                            @if($overdue && $inv->status !== 'paid')
                                <span class="inv-badge inv-overdue">Overdue</span>
                            @else
                                <span class="inv-badge" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};border:1px solid {{ $badge['border'] }}">{{ $badge['label'] }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="inv-actions">
                                <a href="{{ route('invoices.show', $inv) }}" class="inv-act" title="View">👁</a>
                                <a href="{{ route('invoices.edit', $inv) }}" class="inv-act" title="Edit">✏️</a>
                                <form method="POST" action="{{ route('invoices.destroy', $inv) }}" onsubmit="return confirm('Delete this invoice?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inv-act danger" title="Delete">🗑</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($invoices->hasPages())
                <div style="padding:.75rem 1rem;border-top:1px solid #e5e7eb">
                    {{ $invoices->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
