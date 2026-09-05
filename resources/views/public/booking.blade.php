<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Book an Appointment — {{ config('app.name', 'AppointCare') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white antialiased">

    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-lg">

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-white to-indigo-300 bg-clip-text text-transparent">
                    {{ $tenant?->name ?? config('app.name', 'AppointCare') }}
                </h1>
                <p class="text-sm text-slate-400 mt-2">
                    Fill in your details and our AI assistant will call you to confirm your appointment.
                </p>
            </div>

            <form id="bookingForm"
                  class="space-y-4 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl">

                <div>
                    <label for="customer_name" class="block text-sm font-medium text-slate-300 mb-1">Full Name</label>
                    <input type="text" id="customer_name" name="customer_name" required
                           class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label for="customer_email" class="block text-sm font-medium text-slate-300 mb-1">Email Address</label>
                    <input type="email" id="customer_email" name="customer_email" required
                           class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label for="customer_phone" class="block text-sm font-medium text-slate-300 mb-1">Phone (Nepal)</label>
                    <input type="tel" id="customer_phone" name="customer_phone" required
                           placeholder="e.g. 9841234567 or +977 9841234567"
                           class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 outline-none">
                    <p class="text-xs text-slate-400 mt-1">We will call this number to confirm your booking.</p>
                </div>

                <div>
                    <label for="service" class="block text-sm font-medium text-slate-300 mb-1">Service</label>
                    <select id="service" name="service"
                            class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white focus:ring-2 focus:ring-indigo-500 outline-none [&>option]:text-slate-900">
                        @foreach ($services as $service)
                            <option value="{{ $service }}">{{ $service }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="appointment_date" class="block text-sm font-medium text-slate-300 mb-1">Preferred Date & Time</label>
                    <input type="datetime-local" id="appointment_date" name="appointment_date" required min="{{ $minDate }}T00:00"
                           class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-300 mb-1">Notes (optional)</label>
                    <textarea id="description" name="description" rows="2" maxlength="1000"
                              class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                </div>

                <div id="formMessage" class="hidden text-sm rounded-xl px-4 py-3"></div>

                <button type="submit" id="submitBtn"
                        class="w-full px-4 py-3 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white font-semibold transition">
                    Book & Request AI Call
                </button>
            </form>

            <p class="text-center text-xs text-slate-500 mt-6">
                Powered by {{ config('app.name', 'AppointCare') }} AI — Twilio voice + OpenAI.
            </p>
        </div>
    </div>

    <script>
        const form = document.getElementById('bookingForm');
        const msg = document.getElementById('formMessage');
        const btn = document.getElementById('submitBtn');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            msg.classList.add('hidden');
            btn.disabled = true;
            btn.textContent = 'Submitting…';

            const payload = {
                customer_name: form.customer_name.value.trim(),
                customer_email: form.customer_email.value.trim(),
                customer_phone: form.customer_phone.value.trim(),
                service: form.service.value,
                appointment_date: form.appointment_date.value,
                description: form.description.value.trim() || null,
            };

            try {
                const res = await fetch('/api/book-and-call', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (!res.ok) {
                    const firstError = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Something went wrong.');
                    showMsg(firstError, 'bg-red-500/10 text-red-300 border-red-500/20');
                    return;
                }

                form.reset();
                showMsg(data.message || 'Booking created! Our AI assistant will call you shortly.', 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20');
            } catch (err) {
                showMsg('Network error: ' + err.message, 'bg-red-500/10 text-red-300 border-red-500/20');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Book & Request AI Call';
            }
        });

        function showMsg(text, cls) {
            msg.textContent = text;
            msg.className = 'text-sm rounded-xl px-4 py-3 border ' + cls;
            msg.classList.remove('hidden');
        }
    </script>
</body>
</html>
