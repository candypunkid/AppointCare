@extends('user::components.layouts.master')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">User: {{ $user->name }}</h1>

    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Role:</strong> {{ $user->role }}</p>

    <a href="{{ route('user.index') }}" class="text-blue-600">Back to list</a>
</div>
@endsection
