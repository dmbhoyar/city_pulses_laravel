@extends('layouts.app')

@section('content')
<section class="space-y-6">
  <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50 to-white p-6">
    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">My Shop</p>
    <h1 class="mt-1 text-2xl font-bold text-slate-900">Add Offer & Benefit</h1>
    <p class="mt-2 text-sm text-slate-600">Create an attractive public offer for the Offers & Benefits page.</p>
  </div>

  <form action="{{ route('myshop_offer_create') }}" method="POST" enctype="multipart/form-data" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
    @csrf

    <div>
      <label for="title" class="text-sm font-medium text-slate-700">Offer title</label>
      <input type="text" id="title" name="title" value="{{ old('title') }}" class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Example: Summer Offer - 20% Off">
      @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
      <label for="content" class="text-sm font-medium text-slate-700">Offer details</label>
      <textarea id="content" name="content" rows="5" class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Write offer description, terms, and validity.">{{ old('content') }}</textarea>
      @error('content')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
      <label for="photo" class="text-sm font-medium text-slate-700">Offer photo (optional)</label>
      <input type="file" id="photo" name="photo" accept="image/png,image/jpeg,image/webp" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700">
      <p class="mt-1 text-xs text-slate-500">Supported: JPG, PNG, WEBP up to 4 MB.</p>
      @error('photo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex flex-wrap gap-2">
      <button type="submit" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Publish Offer</button>
      <a href="{{ route('myshop') }}" class="inline-flex items-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back to Dashboard</a>
    </div>
  </form>
</section>
@endsection
