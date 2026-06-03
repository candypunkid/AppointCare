@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Edit Tenant</h1>

  <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')
    <div>
      <label class="block text-sm text-gray-300">Name</label>
      <input name="name" value="{{ $tenant->name }}" class="w-full p-2 rounded bg-gray-800 border border-gray-700" />
    </div>
    <div>
      <p class="text-sm text-slate-400">Slug is generated from the name. Edit the name to update the slug (it will remain unique).</p>
    </div>
    <div>
      <label class="block text-sm text-gray-300">Domain</label>
      <input name="domain" value="{{ $tenant->domain }}" class="w-full p-2 rounded bg-gray-800 border border-gray-700" />
    </div>
    <div>
      <label class="inline-flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" {{ $tenant->is_active ? 'checked' : '' }} /> Active
      </label>
    </div>
    <div>
      <button class="px-4 py-2 bg-indigo-600 rounded">Update</button>
    </div>
  </form>
</div>

@endsection
