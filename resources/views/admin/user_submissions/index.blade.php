@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>
  #cpAdminSub{--ink:#1a1208;--ink2:#2d2416;--ink3:#5a4d3a;--paper:#f8f3e8;--paper2:#f2ead8;--cream:#faf7f0;--red:#b91c1c;--red2:#dc2626;--gold:#b8860b;--gold2:#d4a017;--gold3:#f0c040;--green:#166534;--rule:rgba(26,18,8,.13);--rule2:rgba(26,18,8,.36);--fh:'Playfair Display',serif;--fm:'JetBrains Mono',monospace;color:var(--ink);font-family:var(--fm)}
  #cpAdminSub *{box-sizing:border-box}
  #cpAdminSub .cp-mast{background:var(--ink);color:var(--paper);padding:.55rem 1.2rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;gap:.5rem;flex-wrap:wrap;border-bottom:3px solid var(--gold2)}
  #cpAdminSub .cp-mast h1{font-family:var(--fh);font-size:1.3rem;font-weight:900;margin:0;letter-spacing:-.4px}
  #cpAdminSub .cp-mast .badge{font-family:var(--fm);font-size:10px;font-weight:700;letter-spacing:2px;background:var(--red);color:#fff;padding:3px 9px;text-transform:uppercase}
  #cpAdminSub .alert-ok{border-left:4px solid var(--green);background:rgba(22,101,52,.08);padding:.6rem .9rem;margin-bottom:1rem;font-size:11px;font-weight:700;color:var(--green);letter-spacing:.5px}
  /* filter bar */
  #cpAdminSub .fbar{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.2rem;align-items:center}
  #cpAdminSub .fbar a{font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:4px 12px;border:2px solid var(--rule2);color:var(--ink3);text-decoration:none;transition:all .15s}
  #cpAdminSub .fbar a:hover,#cpAdminSub .fbar a.on{background:var(--ink);color:var(--paper);border-color:var(--ink)}
  #cpAdminSub .fbar .count{font-size:9px;color:var(--ink3);margin-left:auto}
  /* table */
  #cpAdminSub table{width:100%;border-collapse:collapse;font-size:12px}
  #cpAdminSub thead tr{background:var(--ink);color:var(--paper)}
  #cpAdminSub thead th{padding:.55rem .8rem;font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;text-align:left;white-space:nowrap}
  #cpAdminSub tbody tr{border-bottom:1px solid var(--rule);background:var(--cream);transition:background .15s}
  #cpAdminSub tbody tr:hover{background:var(--paper2)}
  #cpAdminSub td{padding:.55rem .8rem;vertical-align:top;font-size:11px;line-height:1.5}
  #cpAdminSub .td-title{font-family:var(--fh);font-size:.88rem;font-weight:700;color:var(--ink)}
  #cpAdminSub .td-content{font-size:10px;color:var(--ink3);margin-top:2px;line-height:1.4}
  #cpAdminSub .td-user{font-weight:700;color:var(--ink2)}
  #cpAdminSub .td-email{font-size:10px;color:var(--ink3)}
  /* status pills */
  #cpAdminSub .pill{display:inline-block;font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:2px 8px;border-radius:1px}
  #cpAdminSub .pill-pending{background:rgba(180,140,11,.15);color:var(--gold);border:1px solid var(--gold)}
  #cpAdminSub .pill-approved{background:rgba(22,101,52,.12);color:var(--green);border:1px solid var(--green)}
  #cpAdminSub .pill-inactive{background:rgba(26,18,8,.08);color:var(--ink3);border:1px solid var(--rule2)}
  #cpAdminSub .pill-rejected{background:rgba(185,28,28,.1);color:var(--red);border:1px solid var(--red)}
  /* type tag */
  #cpAdminSub .tag{display:inline-block;font-size:9px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;padding:1px 6px;border:1px solid var(--rule2);color:var(--ink3)}
  /* photo thumb */
  #cpAdminSub .thumb{width:54px;height:40px;object-fit:cover;border:1px solid var(--rule2);display:block}
  /* action buttons */
  #cpAdminSub .acts{display:flex;flex-wrap:wrap;gap:5px;align-items:center}
  #cpAdminSub .acts form{margin:0}
  #cpAdminSub .btn-approve{background:var(--green);color:#fff;border:none;padding:4px 10px;font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:opacity .15s}
  #cpAdminSub .btn-approve:hover{opacity:.82}
  #cpAdminSub .btn-reject{background:var(--gold2);color:var(--ink);border:none;padding:4px 10px;font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:opacity .15s}
  #cpAdminSub .btn-reject:hover{opacity:.82}
  #cpAdminSub .btn-inactivate{background:var(--ink3);color:#fff;border:none;padding:4px 10px;font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:opacity .15s}
  #cpAdminSub .btn-inactivate:hover{opacity:.82}
  #cpAdminSub .btn-delete{background:var(--red);color:#fff;border:none;padding:4px 10px;font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:opacity .15s}
  #cpAdminSub .btn-delete:hover{background:var(--red2)}
  /* empty */
  #cpAdminSub .empty{text-align:center;padding:2.5rem 1rem;font-size:11px;color:var(--ink3);letter-spacing:1px;text-transform:uppercase;border:1px dashed var(--rule2)}
  @media(max-width:900px){#cpAdminSub table,#cpAdminSub thead,#cpAdminSub tbody,#cpAdminSub th,#cpAdminSub td,#cpAdminSub tr{display:block}#cpAdminSub thead{display:none}#cpAdminSub tbody tr{margin-bottom:1rem;border:1px solid var(--rule2)}#cpAdminSub td::before{content:attr(data-label)' · ';font-weight:700;font-size:9px;letter-spacing:1px;text-transform:uppercase;color:var(--ink3)}}
</style>

<div id="cpAdminSub">

  <div class="cp-mast">
    <h1>{{ __('ui.user_submissions') }}</h1>
    <span class="badge">Admin · CityPulse Desk</span>
  </div>

  @if(session('success'))
    <div class="alert-ok">✓ &nbsp;{{ session('success') }}</div>
  @endif

  @php
    $filter = request('status', 'all');
    $counts = [
      'all'      => $submissions->count(),
      'pending'  => $submissions->where('status', 'pending')->count(),
      'approved' => $submissions->where('status', 'approved')->count(),
      'rejected' => $submissions->where('status', 'rejected')->count(),
      'inactive' => $submissions->where('status', 'inactive')->count(),
    ];
    $visible = $filter === 'all' ? $submissions : $submissions->where('status', $filter);
  @endphp

  <div class="fbar">
    <a href="?status=all"      class="{{ $filter === 'all'      ? 'on' : '' }}">{{ __('ui.actions') }} All ({{ $counts['all'] }})</a>
    <a href="?status=pending"  class="{{ $filter === 'pending'  ? 'on' : '' }}">Pending ({{ $counts['pending'] }})</a>
    <a href="?status=approved" class="{{ $filter === 'approved' ? 'on' : '' }}">{{ __('ui.status_approved') }} ({{ $counts['approved'] }})</a>
    <a href="?status=rejected" class="{{ $filter === 'rejected' ? 'on' : '' }}">{{ __('ui.status_rejected') }} ({{ $counts['rejected'] }})</a>
    <a href="?status=inactive" class="{{ $filter === 'inactive' ? 'on' : '' }}">{{ __('ui.status_inactive') }} ({{ $counts['inactive'] }})</a>
    <span class="count">{{ $visible->count() }} {{ __('ui.upd_citypulse_desk') }}</span>
  </div>

  @if($visible->isEmpty())
    <div class="empty">No submissions found.</div>
  @else
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>{{ __('ui.user') }}</th>
          <th>{{ __('ui.title') }} / {{ __('ui.content') }}</th>
          <th>{{ __('ui.type') }}</th>
          <th>{{ __('ui.status') }}</th>
          <th>{{ __('ui.photo') }}</th>
          <th>Date</th>
          <th>{{ __('ui.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($visible as $submission)
          <tr>
            <td data-label="ID">{{ $submission->id }}</td>
            <td data-label="{{ __('ui.user') }}">
              <div class="td-user">{{ $submission->user->name ?? '—' }}</div>
              <div class="td-email">{{ $submission->user->email ?? '' }}</div>
            </td>
            <td data-label="{{ __('ui.title') }}">
              <div class="td-title">{{ $submission->title }}</div>
              <div class="td-content">{{ \Illuminate\Support\Str::limit($submission->content, 80) }}</div>
            </td>
            <td data-label="{{ __('ui.type') }}">
              <span class="tag">{{ __('ui.type_' . $submission->type) }}</span>
            </td>
            <td data-label="{{ __('ui.status') }}">
              @php
                $pillClass = match($submission->status) {
                  'approved' => 'pill-approved',
                  'rejected' => 'pill-rejected',
                  'inactive' => 'pill-inactive',
                  default    => 'pill-pending',
                };
              @endphp
              <span class="pill {{ $pillClass }}">{{ __('ui.status_' . $submission->status) }}</span>
            </td>
            <td data-label="{{ __('ui.photo') }}">
              @if($submission->photo)
                <img class="thumb" src="{{ asset('storage/' . $submission->photo) }}" alt="">
              @else
                <span style="font-size:10px;color:var(--rule2)">—</span>
              @endif
            </td>
            <td data-label="Date" style="white-space:nowrap;font-size:10px;color:var(--ink3)">
              {{ $submission->created_at->format('d M Y') }}<br>{{ $submission->created_at->format('h:i A') }}
            </td>
            <td data-label="{{ __('ui.actions') }}">
              <div class="acts">
                @if($submission->status !== 'approved')
                  <form method="POST" action="{{ route('admin.user_submissions.approve', $submission->id) }}">
                    @csrf
                    <button type="submit" class="btn-approve">✓ {{ __('ui.approve') }}</button>
                  </form>
                @endif

                @if(!in_array($submission->status, ['rejected', 'inactive']))
                  <form method="POST" action="{{ route('admin.user_submissions.reject', $submission->id) }}">
                    @csrf
                    <button type="submit" class="btn-reject">✕ {{ __('ui.reject') }}</button>
                  </form>
                @endif

                @if($submission->status === 'approved')
                  <form method="POST" action="{{ route('admin.user_submissions.inactivate', $submission->id) }}">
                    @csrf
                    <button type="submit" class="btn-inactivate">◌ {{ __('ui.inactivate') }}</button>
                  </form>
                @endif

                <form method="POST" action="{{ route('admin.user_submissions.destroy', $submission->id) }}"
                      onsubmit="return confirm('{{ __('ui.confirm_delete_submission') }}')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-delete">🗑 {{ __('ui.delete') }}</button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

</div>
@endsection
