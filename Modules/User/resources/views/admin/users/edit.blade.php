@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Edit User</h1>

  <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')
    <div>
      <label class="block text-sm text-gray-300">Name</label>
      <input name="name" value="{{ $user->name }}" class="w-full p-2 rounded bg-gray-800 border border-gray-700" />
    </div>
    <div>
      <label class="block text-sm text-gray-300">Email</label>
      <input name="email" value="{{ $user->email }}" class="w-full p-2 rounded bg-gray-800 border border-gray-700" />
    </div>
    <div>
      <label class="block text-sm text-gray-300">Role</label>
      <select name="role" class="w-full p-2 rounded bg-gray-800 border border-gray-700">
        <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
        <option value="tenant_admin" {{ $user->role === 'tenant_admin' ? 'selected' : '' }}>Tenant Admin</option>
        <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
        <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
      </select>
    </div>
    <div>
      <button class="px-4 py-2 bg-indigo-600 rounded">Update</button>
    </div>
  </form>
</div>

@endsection
