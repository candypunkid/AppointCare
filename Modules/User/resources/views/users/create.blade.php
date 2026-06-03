@extends('user::components.layouts.master')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Create User</h1>

    <form action="{{ route('user.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block">Name</label>
            <input name="name" class="border p-2 w-full" value="{{ old('name') }}">
        </div>
        <div class="mb-4">
            <label class="block">Email</label>
            <input name="email" class="border p-2 w-full" value="{{ old('email') }}">
        </div>
        <div class="mb-4">
            <label class="block">Password</label>
            <input type="password" name="password" class="border p-2 w-full">
        </div>
        <div class="mb-4">
            <label class="block">Role</label>
            <select name="role" class="border p-2 w-full">
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="customer">Customer</option>
            </select>
        </div>
        <button class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
