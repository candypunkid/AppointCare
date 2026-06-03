@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto p-6">
  <h1 class="text-3xl font-bold mb-4">{{ $tenant->name }} — Tenant Dashboard</h1>
  <p class="text-gray-500 mb-6">Welcome, {{ $user->name }} — manage data for your tenant only.</p>

  <div class="grid gap-6 md:grid-cols-4">
    <div class="rounded-3xl bg-white p-6 shadow-sm">
      <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Tenant users</p>
      <p class="mt-4 text-4xl font-semibold text-slate-900">{{ $tenantUserCount }}</p>
      <p class="mt-2 text-sm text-slate-500">All users assigned to this tenant.</p>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow-sm">
      <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Appointments</p>
      <p class="mt-4 text-4xl font-semibold text-slate-900">{{ $appointmentCount }}</p>
      <p class="mt-2 text-sm text-slate-500">Scheduled or completed appointments.</p>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow-sm">
      <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Requests</p>
      <p class="mt-4 text-4xl font-semibold text-slate-900">{{ $requestCount }}</p>
      <p class="mt-2 text-sm text-slate-500">Appointment requests for this tenant.</p>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow-sm">
      <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Customers</p>
      <p class="mt-4 text-4xl font-semibold text-slate-900">{{ $activeCustomers }}</p>
      <p class="mt-2 text-sm text-slate-500">Customers created under this tenant.</p>
    </div>
  </div>

  <div class="mt-8 grid gap-6 md:grid-cols-3">
    <div class="rounded-3xl bg-slate-900 p-6 text-white shadow-xl">
      <h2 class="text-xl font-semibold">Tenant Users</h2>
      <p class="mt-2 text-sm text-slate-300">View or manage your tenant's users.</p>
      <a href="{{ route('tenant.users.index') }}" class="mt-4 inline-flex rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900">Manage users</a>
    </div>
    <div class="rounded-3xl bg-slate-900 p-6 text-white shadow-xl">
      <h2 class="text-xl font-semibold">Appointments</h2>
      <p class="mt-2 text-sm text-slate-300">Review your tenant appointment schedule.</p>
      <a href="#" class="mt-4 inline-flex rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900">View appointments</a>
    </div>
    <div class="rounded-3xl bg-slate-900 p-6 text-white shadow-xl">
      <h2 class="text-xl font-semibold">Tenant Settings</h2>
      <p class="mt-2 text-sm text-slate-300">Update tenant profile and preferences.</p>
      <a href="#" class="mt-4 inline-flex rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900">Settings</a>
    </div>
  </div>
</div>

@endsection
