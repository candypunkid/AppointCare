@extends('user::components.layouts.master')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Edit User</h1>

    <form action="{{ route('user.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block">Name</label>
            <input name="name" class="border p-2 w-full" value="{{ old('name', $user->name) }}">
        </div>
        <div class="mb-4">
            <label class="block">Email</label>
            <input name="email" class="border p-2 w-full" value="{{ old('email', $user->email) }}">
        </div>
        <div class="mb-4">
            <label class="block">Role</label>
            <select name="role" class="border p-2 w-full">
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
            </select>
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
