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

  <!-- ATTENDANCE REQUESTS -->
  <div class="mt-8 rounded-3xl bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-bold text-slate-900">Attendance Requests</h2>
      <span class="inline-flex px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold uppercase tracking-widest">
        {{ $newRequestCount }} new
      </span>
    </div>

    <p class="mb-4 text-sm text-slate-500">
      Latest appointment requests submitted via the public booking form for {{ $tenant->name }}.
    </p>

    @if($recentRequests->isEmpty())
      <div class="p-8 rounded-2xl bg-slate-50 text-center text-slate-400">
        No attendance / appointment requests yet.
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-xs uppercase tracking-widest text-slate-400 border-b border-slate-200">
              <th class="py-3 px-4 font-bold">Customer</th>
              <th class="py-3 px-4 font-bold">Service</th>
              <th class="py-3 px-4 font-bold">Preferred</th>
              <th class="py-3 px-4 font-bold">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentRequests as $request)
              <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                <td class="py-3 px-4">
                  <div class="font-semibold text-slate-900">{{ $request->customer_name }}</div>
                  <div class="text-xs text-slate-400">{{ $request->customer_phone }}</div>
                </td>
                <td class="py-3 px-4 capitalize text-slate-600">{{ $request->service }}</td>
                <td class="py-3 px-4 text-slate-600">
                  {{ $request->preferred_at?->format('M j, Y g:i A') ?? '—' }}
                </td>
                <td class="py-3 px-4">
                  @php
                    $statusColors = [
                        'new' => 'bg-sky-100 text-sky-700',
                        'contacted' => 'bg-amber-100 text-amber-700',
                        'scheduled' => 'bg-emerald-100 text-emerald-700',
                        'cancelled' => 'bg-rose-100 text-rose-700',
                    ];
                  @endphp
                  <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest {{ $statusColors[$request->status] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ $request->status }}
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>

@endsection
