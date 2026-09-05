<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Call Simulator — {{ config('app.name', 'AppointCare') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white antialiased">

    <div class="min-h-screen px-4 py-8">
        <div class="max-w-5xl mx-auto">

            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">📞 AI Call Simulator</h1>
                    <p class="text-sm text-slate-400 mt-1">
                        Tests the real OpenAI → database pipeline against the same Twilio webhooks — no phone needed.
                    </p>
                </div>
                <a href="{{ route('booking') }}"
                   class="px-4 py-2 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-sm font-semibold transition">
                    + New booking
                </a>
            </div>

            <div class="grid md:grid-cols-5 gap-6">

                {{-- Appointment list --}}
                <div class="md:col-span-2 space-y-3">
                    <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-wider">Appointments</h2>
                    @forelse ($appointments as $appointment)
                        <button type="button"
                                data-id="{{ $appointment['id'] }}"
                                data-name="{{ $appointment['customer_name'] }}"
                                data-service="{{ $appointment['service'] }}"
                                onclick="selectAppointment(this)"
                                class="appt-card w-full text-left bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/10 transition">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold">{{ $appointment['customer_name'] }}</span>
                                <span class="text-xs px-2 py-1 rounded-full status-badge" data-status="{{ $appointment['status'] }}">{{ $appointment['status'] }}</span>
                            </div>
                            <p class="text-sm text-slate-400 mt-1">{{ $appointment['service'] }} — {{ $appointment['scheduled_at'] }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $appointment['phone'] }}</p>
                        </button>
                    @empty
                        <div class="bg-white/5 border border-white/10 rounded-xl p-6 text-sm text-slate-400">
                            No callable appointments yet.
                            <a href="{{ route('booking') }}" class="text-indigo-300 underline">Create one here</a>.
                        </div>
                    @endforelse
                </div>

                {{-- Conversation panel --}}
                <div class="md:col-span-3 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 flex flex-col min-h-[480px]">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 id="convTitle" class="font-semibold">Select an appointment</h2>
                            <p id="convSub" class="text-xs text-slate-400 mt-0.5">Choose an appointment on the left to start an AI call.</p>
                        </div>
                        <button id="startCallBtn" onclick="startCall()"
                                class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-sm font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed"
                                disabled>
                            ▶ Start AI Call
                        </button>
                    </div>

                    <div id="transcript" class="flex-1 space-y-3 overflow-y-auto pr-2 mb-4 max-h-[360px]">
                        <div class="text-center text-xs text-slate-500 py-8">No conversation yet.</div>
                    </div>

                    <div class="flex gap-2">
                        <input id="customerInput" type="text" disabled
                               placeholder="Type what the customer says, e.g. 'Yes, I'll be there'"
                               class="flex-1 px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 outline-none disabled:opacity-40"
                               onkeydown="if (event.key === 'Enter') sendMessage()">
                        <button id="sendBtn" onclick="sendMessage()" disabled
                                class="px-5 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-sm font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed">
                            Send
                        </button>
                    </div>

                    <p id="hint" class="text-xs text-slate-500 mt-3">
                        Try: <span class="text-slate-300">"Yes I'll attend"</span> · <span class="text-slate-300">"I want to cancel"</span> · <span class="text-slate-300">"Please reschedule to Monday 10am"</span> · <span class="text-slate-300">"Talk to a human"</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const state = {
            id: null,
            name: null,
            service: null,
            callSid: null,
            active: false,
        };

        const transcript = document.getElementById('transcript');
        const input = document.getElementById('customerInput');
        const sendBtn = document.getElementById('sendBtn');
        const startBtn = document.getElementById('startCallBtn');

        function selectAppointment(btn) {
            state.id = btn.dataset.id;
            state.name = btn.dataset.name;
            state.service = btn.dataset.service;
            state.callSid = null;
            state.active = false;

            document.getElementById('convTitle').textContent = `${state.name} — ${state.service}`;
            document.getElementById('convSub').textContent = 'Press "Start AI Call" to begin.';
            transcript.innerHTML = '<div class="text-center text-xs text-slate-500 py-8">Call not started.</div>';
            startBtn.disabled = false;
            input.disabled = true;
            sendBtn.disabled = true;
            document.querySelectorAll('.appt-card').forEach(c => c.classList.remove('border-indigo-400'));
            btn.classList.add('border-indigo-400');
            refreshStatus();
        }

        async function startCall() {
            if (!state.id) return;
            state.callSid = 'CA' + 'SIMULATOR' + Math.random().toString(36).slice(2, 12);
            state.active = true;
            startBtn.disabled = true;
            startBtn.textContent = 'Call in progress…';
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();
            transcript.innerHTML = '';

            addBubble('ai', 'Calling customer…');
            await callWebhook();
        }

        async function sendMessage() {
            const text = input.value.trim();
            if (!text || !state.active) return;
            input.value = '';
            addBubble('customer', text);
            await callWebhook(text);
        }

        async function callWebhook(speech = '') {
            addTyping();

            const body = {
                CallSid: state.callSid,
                appointment_id: state.id,
            };
            if (speech) body.SpeechResult = speech;

            try {
                const res = await fetch('/api/twilio/voice', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams(body).toString(),
                });
                const xml = await res.text();
                removeTyping();
                const parsed = new DOMParser().parseFromString(xml, 'text/xml');
                const says = Array.from(parsed.getElementsByTagName('Say')).map(el => el.textContent.trim());
                says.forEach(t => t && addBubble('ai', t));

                const hasHangup = parsed.getElementsByTagName('Hangup').length > 0;
                const hasGather = parsed.getElementsByTagName('Gather').length > 0;

                if (hasHangup && !hasGather) {
                    endCall();
                }
                refreshStatus();
            } catch (err) {
                removeTyping();
                addBubble('ai', '⚠️ Simulator error: ' + err.message);
                endCall();
            }
        }

        function addBubble(speaker, text) {
            const typing = document.querySelector('.typing-bubble');
            if (typing) typing.remove();

            const row = document.createElement('div');
            const isAi = speaker === 'ai';
            row.className = 'flex ' + (isAi ? 'justify-start' : 'justify-end');
            row.innerHTML = `
                <div class="max-w-[80%] px-4 py-2.5 rounded-2xl text-sm ${isAi ? 'bg-white/10 text-slate-100 rounded-bl-sm' : 'bg-indigo-500 text-white rounded-br-sm'}">
                    <span class="block text-[10px] uppercase tracking-wider opacity-60 mb-0.5">${isAi ? 'AI assistant' : 'Customer'}</span>
                    ${escapeHtml(text)}
                </div>`;
            transcript.appendChild(row);
            transcript.scrollTop = transcript.scrollHeight;
        }

        function addTyping() {
            const row = document.createElement('div');
            row.className = 'flex justify-start typing-bubble';
            row.innerHTML = '<div class="px-4 py-2.5 rounded-2xl text-sm bg-white/10 text-slate-400">…</div>';
            transcript.appendChild(row);
            transcript.scrollTop = transcript.scrollHeight;
        }

        function removeTyping() {
            document.querySelectorAll('.typing-bubble').forEach(el => el.remove());
        }

        function endCall() {
            state.active = false;
            input.disabled = true;
            sendBtn.disabled = true;
            startBtn.textContent = '▶ Start AI Call';
            startBtn.disabled = false;
            addBubble('system', 'Call ended.');
        }

        async function refreshStatus() {
            if (!state.id) return;
            try {
                const res = await fetch('/api/simulator/appointment/' + state.id + '/status');
                const data = await res.json();
                if (data.success) {
                    const badge = document.querySelector(`.appt-card[data-id="${state.id}"] .status-badge`);
                    if (badge) {
                        badge.textContent = data.data.status;
                        badge.dataset.status = data.data.status;
                    }
                    document.getElementById('convSub').textContent = `Appointment status: ${data.data.status}`;
                }
            } catch (e) { /* ignore */ }
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    </script>
</body>
</html>
