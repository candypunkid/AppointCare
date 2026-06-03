<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>AppointCare — Smart Multi‑Tenant Appointments</title>
  <meta name="description" content="AppointCare — multi-tenant appointment scheduling with AI reminders and Twilio integration." />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Accent gradient */
    .accent-gradient { background-image: linear-gradient(90deg,#7c3aed,#06b6d4); }
    .glass { background: rgba(255,255,255,0.06); backdrop-filter: blur(6px); }
  </style>
</head>
<body class="bg-gray-900 text-gray-100 antialiased leading-relaxed">

  <header class="py-6">
    <div class="max-w-6xl mx-auto px-6 flex items-center justify-between">
      <a href="/" class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg accent-gradient flex items-center justify-center shadow-lg"> 
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10v6a2 2 0 0 1-2 2h-4"/><path d="M3 14V8a2 2 0 0 1 2-2h4"/><path d="M7 10h10"/></svg>
        </div>
        <span class="font-semibold text-xl">AppointCare</span>
      </a>

      <nav class="hidden md:flex items-center gap-6 text-sm text-gray-300">
        <a href="#features" class="hover:text-white">Features</a>
        <a href="#how" class="hover:text-white">How it works</a>
        <a href="#pricing" class="hover:text-white">Pricing</a>
        <a href="#contact" class="hover:text-white">Contact</a>
        <a href="/login" class="ml-4 px-4 py-2 bg-white text-gray-900 rounded-md font-medium">Sign in</a>
      </nav>
    </div>
  </header>

  <main>
    <section class="pt-12 pb-20">
      <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
        <div>
          <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">Smart appointment scheduling for service businesses</h1>
          <p class="text-gray-300 mb-6">Multi-tenant SaaS with AI reminders, Twilio voice & SMS, staff schedules, and beautiful analytics — built for scale.</p>

          <div class="flex gap-4">
            <a href="/register" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 rounded-md font-semibold shadow">Get started — Free trial</a>
            <a href="#features" class="px-6 py-3 border border-gray-700 rounded-md text-gray-200 hover:bg-gray-800">View features</a>
          </div>

          <div class="mt-8 grid grid-cols-2 gap-4 text-sm text-gray-400">
            <div class="glass p-4 rounded-lg">
              <strong class="text-white">AI reminders</strong>
              <div>GPT-driven voice & SMS reminders</div>
            </div>
            <div class="glass p-4 rounded-lg">
              <strong class="text-white">Multi-tenant</strong>
              <div>Tenant scoped data & admin controls</div>
            </div>
            <div class="glass p-4 rounded-lg">
              <strong class="text-white">Staff schedules</strong>
              <div>Conflict detection & availability</div>
            </div>
            <div class="glass p-4 rounded-lg">
              <strong class="text-white">Integrations</strong>
              <div>Twilio, OpenAI, Email, Webhooks</div>
            </div>
          </div>
        </div>

        <div class="relative">
          <div class="bg-gradient-to-br from-gray-800 to-gray-700 rounded-xl p-6 shadow-2xl">
            <div class="bg-gray-900 rounded-lg p-6">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <div class="text-sm text-gray-400">Hair Salon — Kathmandu</div>
                  <div class="text-white font-semibold">Alexandra</div>
                </div>
                <div class="text-sm text-gray-400">Tomorrow • 10:00 AM</div>
              </div>

              <div class="mt-4 bg-gray-800 p-4 rounded-md">
                <div class="text-sm text-gray-300">Appointment with</div>
                <div class="text-white font-medium">Haircut — 30 mins</div>
              </div>

              <div class="mt-6 flex gap-3">
                <button class="flex-1 px-4 py-3 bg-green-500 rounded-md text-black font-semibold">Confirm</button>
                <button class="flex-1 px-4 py-3 bg-red-600 rounded-md">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="features" class="py-16 bg-gradient-to-b from-transparent to-gray-900/30">
      <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-3xl font-bold mb-6">Features</h2>
        <div class="grid md:grid-cols-3 gap-6">
          <div class="p-6 bg-gray-800 rounded-lg">
            <h3 class="font-semibold mb-2">Smart Reminders</h3>
            <p class="text-gray-400 text-sm">Automated voice & SMS reminders with AI intent detection to confirm, cancel or reschedule.</p>
          </div>
          <div class="p-6 bg-gray-800 rounded-lg">
            <h3 class="font-semibold mb-2">Multi‑Tenant</h3>
            <p class="text-gray-400 text-sm">Isolated tenant data, owner dashboards and configurable business settings.</p>
          </div>
          <div class="p-6 bg-gray-800 rounded-lg">
            <h3 class="font-semibold mb-2">Staff & Services</h3>
            <p class="text-gray-400 text-sm">Manage staff schedules, services, pricing and capacity with conflict detection.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="how" class="py-16">
      <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold mb-4">How it works</h2>
        <p class="text-gray-400 mb-8">Customers request appointments → AI voice/agent confirms → Appops notify staff and update calendar.</p>

        <div class="grid md:grid-cols-3 gap-6">
          <div class="p-6 bg-gray-800 rounded-lg">
            <div class="text-2xl font-bold mb-2">1</div>
            <div class="font-semibold">Book</div>
            <div class="text-gray-400 text-sm">Booking via web, phone, or staff.</div>
          </div>
          <div class="p-6 bg-gray-800 rounded-lg">
            <div class="text-2xl font-bold mb-2">2</div>
            <div class="font-semibold">Confirm</div>
            <div class="text-gray-400 text-sm">AI and Twilio confirm or collect intents.</div>
          </div>
          <div class="p-6 bg-gray-800 rounded-lg">
            <div class="text-2xl font-bold mb-2">3</div>
            <div class="font-semibold">Attend</div>
            <div class="text-gray-400 text-sm">Staff manage schedule; analytics measure performance.</div>
          </div>
        </div>
      </div>
    </section>

    <section id="pricing" class="py-16 bg-gray-800">
      <div class="max-w-6xl mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold mb-6">Pricing</h2>
        <div class="grid md:grid-cols-3 gap-6">
          <div class="p-6 bg-gray-900 rounded-lg">
            <div class="text-xl font-semibold mb-2">Starter</div>
            <div class="text-gray-400 mb-4">$19 / month</div>
            <ul class="text-gray-400 text-sm mb-4">
              <li>Up to 3 staff</li>
              <li>Basic reminders</li>
              <li>Email support</li>
            </ul>
            <a href="/register" class="inline-block px-4 py-2 bg-indigo-600 rounded-md">Start Free</a>
          </div>
          <div class="p-6 bg-gradient-to-br from-indigo-600 to-teal-400 rounded-lg text-black">
            <div class="text-xl font-semibold mb-2">Pro</div>
            <div class="mb-4">$49 / month</div>
            <ul class="text-sm mb-4">
              <li>Unlimited staff</li>
              <li>AI voice & SMS</li>
              <li>Priority support</li>
            </ul>
            <a href="/register" class="inline-block px-4 py-2 bg-white rounded-md">Start Free</a>
          </div>
          <div class="p-6 bg-gray-900 rounded-lg">
            <div class="text-xl font-semibold mb-2">Enterprise</div>
            <div class="text-gray-400 mb-4">Custom pricing</div>
            <ul class="text-gray-400 text-sm mb-4">
              <li>SLA & onboarding</li>
              <li>White‑label</li>
              <li>Dedicated support</li>
            </ul>
            <a href="#contact" class="inline-block px-4 py-2 bg-indigo-600 rounded-md">Contact Sales</a>
          </div>
        </div>
      </div>
    </section>

    <section id="contact" class="py-16">
      <div class="max-w-3xl mx-auto px-6">
        <h2 class="text-2xl font-bold mb-4 text-center">Get in touch</h2>
        <p class="text-gray-400 text-center mb-6">Questions? Need a demo? Send us a message and we'll reach out within one business day.</p>

        <form action="mailto:sales@appointcare.com" method="POST" enctype="text/plain" class="grid gap-4">
          <input name="name" placeholder="Your name" class="p-3 rounded-md bg-gray-800 border border-gray-700" />
          <input name="email" placeholder="Email" class="p-3 rounded-md bg-gray-800 border border-gray-700" />
          <textarea name="message" rows="4" placeholder="How can we help?" class="p-3 rounded-md bg-gray-800 border border-gray-700"></textarea>
          <div class="text-center">
            <button type="submit" class="px-6 py-3 bg-indigo-600 rounded-md">Send message</button>
          </div>
        </form>
      </div>
    </section>
  </main>

  <!-- AI assistant widget -->
  <div id="ai-widget" class="fixed bottom-6 right-6 w-96">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="px-4 py-3 bg-gray-900 text-white font-semibold">Ask our assistant</div>
      <div class="p-3">
        <textarea id="aiPrompt" rows="3" class="w-full p-2 border rounded-md" placeholder="Ask about bookings, features, or how AppointCare works..."></textarea>
        <div class="mt-2 flex items-center gap-2">
          <button id="aiAsk" class="flex-1 px-3 py-2 bg-indigo-600 text-white rounded-md">Ask</button>
          <button id="aiClear" class="px-3 py-2 border rounded-md">Clear</button>
        </div>
        <pre id="aiAnswer" class="mt-3 text-sm text-gray-700 whitespace-pre-wrap"></pre>
      </div>
    </div>
  </div>

  <script>
    async function postJSON(url, body) {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify(body)
      });
      return res.json();
    }

    document.getElementById('aiAsk').addEventListener('click', async () => {
      const prompt = document.getElementById('aiPrompt').value.trim();
      if (!prompt) return;
      document.getElementById('aiAnswer').textContent = 'Thinking...';
      const result = await postJSON('{{ route('ai.respond') }}', { prompt });
      if (result.answer) {
        document.getElementById('aiAnswer').textContent = result.answer;
      } else if (result.error) {
        document.getElementById('aiAnswer').textContent = 'Error: ' + result.error;
      } else {
        document.getElementById('aiAnswer').textContent = 'No response';
      }
    });

    document.getElementById('aiClear').addEventListener('click', () => {
      document.getElementById('aiPrompt').value = '';
      document.getElementById('aiAnswer').textContent = '';
    });
  </script>

  <footer class="py-8 border-t border-gray-800 mt-12">
    <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="text-sm text-gray-400">© AppointCare — Built for service businesses</div>
      <div class="text-sm text-gray-400">Privacy · Terms · Documentation</div>
    </div>
  </footer>

</body>
</html>
