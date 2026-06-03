@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Edit User</h1>

    <form action="{{ route('tenant.users.update', $user) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input name="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full rounded-md border px-3 py-2" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input name="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full rounded-md border px-3 py-2" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Role</label>
            <select name="role" class="mt-1 block w-full rounded-md border px-3 py-2">
                <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
            </select>
        </div>

        <div>
            <button class="rounded-md bg-indigo-600 px-4 py-2 text-white">Save</button>
        </div>
    </form>
</div>

@endsection
