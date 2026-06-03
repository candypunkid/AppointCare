<?php

namespace Modules\Appointment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $tenant = tenant();

        $appointments = Appointment::where('tenant_id', $tenant->id)->latest()->paginate(15);

        return response()->json($appointments);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|integer',
            'staff_id' => 'required|integer',
            'service' => 'required|string',
            'scheduled_at' => 'required|date',
            'scheduled_end_at' => 'nullable|date',
        ]);

        $data['tenant_id'] = tenant()->id;
        $data['status'] = 'pending';

        $appointment = Appointment::create($data);

        return response()->json($appointment, 201);
    }

    public function reschedule(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $this->authorize('update', $appointment);

        $data = $request->validate([
            'scheduled_at' => 'required|date',
            'scheduled_end_at' => 'nullable|date',
        ]);

        $appointment->update($data + ['status' => 'rescheduled']);

        return response()->json($appointment);
    }

    public function cancel(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $this->authorize('delete', $appointment);

        $appointment->update(['status' => 'cancelled']);

        return response()->json($appointment);
    }
}
