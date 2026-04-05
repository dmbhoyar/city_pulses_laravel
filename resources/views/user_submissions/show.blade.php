@extends('layouts.app')

@section('title', $submission->title . ' · ' . __('ui.community_stories'))

@section('content')
<div class="container" style="max-width:860px;margin:0 auto;padding:1.2rem 1rem 2rem">
    <div style="margin-bottom:1rem">
        <a href="{{ route('updates.index') }}" style="font-size:.85rem;color:#b91c1c;text-decoration:none">← {{ __('ui.back') }} {{ __('ui.updates') }}</a>
    </div>

    <article style="background:#f8f3e8;border:1px solid rgba(26,18,8,.2);padding:1.2rem 1.2rem 1.4rem">
        <div style="font-size:.74rem;font-weight:700;letter-spacing:1.6px;color:#b91c1c;text-transform:uppercase;margin-bottom:.45rem">
            {{ strtoupper($submission->type ?: 'story') }}
        </div>

        <h1 style="margin:0 0 .45rem;font-family:'Playfair Display',serif;font-size:2rem;line-height:1.15;color:#1a1208">
            {{ $submission->title }}
        </h1>

        <div style="font-size:.82rem;color:#5a4d3a;margin-bottom:1rem">
            {{ $submission->user->name ?? __('ui.community_stories') }} · {{ optional($submission->created_at)->format('d M Y, h:i A') }}
        </div>

        @if($submission->photo)
            <img
                src="{{ asset('storage/' . $submission->photo) }}"
                alt="{{ $submission->title }}"
                style="display:block;width:100%;max-height:420px;object-fit:cover;border:1px solid rgba(26,18,8,.16);margin-bottom:1rem"
            >
        @endif

        <div style="font-size:1rem;line-height:1.8;color:#2d2416;word-wrap:break-word">
            {!! nl2br(e($submission->content)) !!}
        </div>
    </article>
</div>
@endsection
