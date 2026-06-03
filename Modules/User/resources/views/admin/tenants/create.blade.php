@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Create Tenant</h1>

  <form action="{{ route('admin.tenants.store') }}" method="POST" class="space-y-4">
    @csrf
    <div>
      <label class="block text-sm text-gray-300">Name</label>
      <input name="name" class="w-full p-2 rounded bg-gray-800 border border-gray-700" />
    </div>
    <div>
      <p class="text-sm text-slate-400">A URL-friendly slug will be generated automatically from the tenant name.</p>
    </div>
    <div>
      <label class="block text-sm text-gray-300">Domain (optional)</label>
      <input name="domain" class="w-full p-2 rounded bg-gray-800 border border-gray-700" />
    </div>
    <div>
      <button class="px-4 py-2 bg-indigo-600 rounded">Create</button>
    </div>
  </form>
</div>

@endsection
