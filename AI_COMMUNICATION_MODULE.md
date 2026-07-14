# AI Communication Module

## Overview

The AI Communication Module integrates OpenAI and Twilio to automatically call customers via voice, understand their responses using natural language processing, and manage appointments without human intervention.

## Folder Structure

```
app/
├── Console/
├── Events/
│   ├── AppointmentConfirmed.php       # Fired when AI confirms an appointment
│   ├── AppointmentCancelled.php        # Fired when AI cancels an appointment
│   └── AppointmentRescheduled.php      # Fired when AI reschedules an appointment
├── Http/
│   ├── Controllers/
│   │   ├── TwilioController.php        # Manages outbound calls, SMS, call logs, analytics
│   │   ├── VoiceWebhookController.php  # Handles Twilio voice/webhook callbacks
│   │   └── AppointmentAIController.php # AI analysis, availability, conversation history
│   └── Middleware/
├── Jobs/
│   ├── MakeReminderCallJob.php         # Dispatches reminder calls (24h/2h before)
│   └── ProcessConversationJob.php      # Processes AI conversation and takes action
├── Listeners/
│   ├── SendSMSNotification.php         # Sends SMS on appointment status changes
│   └── UpdateAnalytics.php             # Updates cached analytics counters
├── Models/
│   ├── Appointment.php                 # (existing) Added callLogs(), aiActions() relations
│   ├── CallLog.php                     # Logs each Twilio call
│   ├── ConversationLog.php             # Individual speaker turns in a call
│   └── AiAction.php                    # Tracks AI decisions & confidence
├── Providers/
│   └── EventServiceProvider.php        # Maps events to listeners
├── Repositories/
│   ├── AppointmentRepository.php       # Appointment queries, analytics, reminders
│   └── CallLogRepository.php           # Call log queries and stats
└── Services/
    ├── OpenAIService.php               # OpenAI intent detection & conversation analysis
    ├── TwilioService.php               # Twilio voice calls, SMS, TwiML generation
    ├── ConversationService.php         # Orchestrates call flow & conversation handling
    └── AppointmentService.php          # Business logic for appointment CRUD via AI

config/
├── appointcare.php                     # Module configuration (business hours, admin phone, etc.)
└── services.php                        # (existing) Twilio & OpenAI credentials

database/
├── factories/
│   ├── AppointmentFactory.php          # Factory for testing
│   └── CallLogFactory.php              # Factory for testing
└── migrations/
    ├── 2026_06_21_000001_create_call_logs_table.php
    ├── 2026_06_21_000002_create_conversation_logs_table.php
    └── 2026_06_21_000003_create_ai_actions_table.php

routes/
├── api.php                             # API routes (twilio webhooks, openai, dashboard)
└── console.php                         # Scheduler for reminder calls

tests/
├── Feature/
│   ├── TwilioWebhookTest.php           # Tests webhook responses
│   └── AppointmentAIControllerTest.php # Tests AI controller endpoints
└── Unit/
    ├── OpenAIServiceTest.php           # Tests intent detection
    ├── ConversationServiceTest.php     # Tests conversation flow
    ├── AppointmentServiceTest.php      # Tests appointment business logic
    └── AppointmentRepositoryTest.php   # Tests repository queries
```

## Database ER Diagram

```
┌─────────────────┐       ┌──────────────────┐
│   appointments   │       │    call_logs     │
├─────────────────┤       ├──────────────────┤
│ id (PK)         │──┐    │ id (PK)          │
│ tenant_id       │  │    │ appointment_id   │── FK
│ customer_id     │  │    │ twilio_call_sid  │ (unique)
│ staff_id        │  │    │ transcript       │
│ service         │  │    │ detected_intent  │
│ scheduled_at    │  │    │ ai_response      │
│ scheduled_end_at│  │    │ recording_url    │
│ status          │  │    │ duration         │
│ notes           │  │    │ status           │
│ metadata        │  │    │ created_at       │
│ created_at      │  │    │ updated_at       │
│ updated_at      │  │    └────────┬─────────┘
│ deleted_at      │  │             │ 1
└────────┬────────┘  │             │
         │           │    ┌────────┴──────────┐
         │           │    │ conversation_logs │
         │           │    ├───────────────────┤
         │           │    │ id (PK)           │
         │           │    │ call_log_id       │── FK
         │           │    │ speaker           │ (ai|customer)
         │           │    │ message           │
         │           │    │ created_at        │
         │           └────│                   │
         │                └───────────────────┘
         │
         │    ┌──────────────────┐
         └────│   ai_actions     │
              ├──────────────────┤
              │ id (PK)          │
              │ appointment_id   │── FK
              │ action           │ (confirm|cancel|reschedule|transfer_to_human)
              │ old_value        │
              │ new_value        │
              │ confidence       │ (decimal 0.00 - 1.00)
              │ created_at       │
              │ updated_at       │
              └──────────────────┘
```

## Sequence Diagram - Reminder Call Flow

```
┌─────────┐   ┌────────────┐   ┌───────────┐   ┌─────────┐   ┌──────────┐
│ Laravel  │   │ MakeRemind- │   │ Twilio    │   │ OpenAI  │   │Customer  │
│ Scheduler│   │ erCallJob   │   │ Service   │   │Service  │   │ Phone    │
└────┬─────┘   └─────┬──────┘   └─────┬─────┘   └────┬────┘   └────┬─────┘
     │                │                │              │             │
     │  hourly/       │                │              │             │
     │  30min         │                │              │             │
     │───────────────>│                │              │             │
     │                │  POST /calls   │              │             │
     │                │───────────────>│              │             │
     │                │                │  Make Voice  │             │
     │                │                │  Call        │             │
     │                │                │══════════════════════════>│
     │                │                │              │             │
     │                │                │  POST /voice │             │
     │                │                │  (Webhook)   │             │
     │                │ VoiceWebhook   │<─────────────│             │
     │                │ Controller     │              │             │
     │                │<───────────────│              │             │
     │                │                │              │             │
     │                │ Process        │              │             │
     │                │ Conversation   │              │             │
     │                │ Job            │              │             │
     │                │───┐            │              │             │
     │                │   │ analyzeIntent()           │             │
     │                │<──┘           │              │             │
     │                │                │  POST /status│             │
     │                │                │<─────────────│             │
     │                │                │              │             │
     │                │ Action taken:  │              │             │
     │                │ confirm/cancel │              │             │
     │                │ reschedule     │              │             │
     │                │ /transfer      │              │             │
     │                │                │              │             │
     │                │ Event fired +  │              │             │
     │                │ SMS sent       │              │             │
```

## Conversation Flow Diagram

```
AI Call Initiated
       │
       ▼
AI: Greeting + Appointment Details
       │
       ▼
Customer Responds (Speech)
       │
       ▼
OpenAI Analyzes Intent
       │
       ├── confirm_appointment ───► Update status → "confirmed"
       │                                │
       │                                ▼
       │                          Send SMS + Fire Event
       │
       ├── cancel_appointment ────► Update status → "cancelled"
       │                                │
       │                                ▼
       │                          Store reason + Send SMS
       │
       ├── reschedule_appointment ─► Extract date/time
       │                                │
       │                                ▼
       │                          Check slot availability
       │                                │
       │                          ┌─────┴─────┐
       │                          │           │
       │                      Available   Unavailable
       │                          │           │
       │                          ▼           ▼
       │                    Update + SMS  Offer alternatives
       │
       ├── ask_question ──────────► Generate natural response
       │                                │
       │                                ▼
       │                          Continue conversation
       │
       ├── transfer_to_human ────► Dial admin phone
       │
       └── unknown ──────────────► Ask clarifying question
                                      │
                                      ▼
                                  Continue loop
```

## Setup Instructions

### 1. Environment Configuration

Add to your `.env` file:

```env
# Twilio
TWILIO_SID=your_twilio_account_sid
TWILIO_AUTH_TOKEN=your_twilio_auth_token
TWILIO_PHONE_NUMBER=+1234567890
TWILIO_WEBHOOK_BASE_URL=https://your-ngrok-or-domain.com

# OpenAI
OPENAI_API_KEY=sk-your-openai-api-key
OPENAI_MODEL=gpt-4o-mini

# AppointCare
APPOINTCARE_ADMIN_PHONE=+1234567890
BUSINESS_HOURS_START=9
BUSINESS_HOURS_END=17
SLOT_DURATION_MINUTES=60
REMINDER_24H_ENABLED=true
REMINDER_2H_ENABLED=true

# Queue (required for async call processing)
QUEUE_CONNECTION=database
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Run Migrations

```bash
php artisan migrate
```

This creates the following tables:
- `call_logs` - Tracks each Twilio call
- `conversation_logs` - Records individual conversation turns
- `ai_actions` - Logs AI decisions and confidence scores

### 4. Twilio Webhook Configuration

In your Twilio Console, configure:

- **Voice URL**: `https://your-domain.com/api/twilio/voice`
- **Status Callback URL**: `https://your-domain.com/api/twilio/status`
- **Incoming Calls**: `https://your-domain.com/api/twilio/incoming-call`

For local development, use [ngrok](https://ngrok.com/):
```bash
ngrok http 8000
```
Then set `TWILIO_WEBHOOK_BASE_URL` to your ngrok URL.

### 5. Queue Worker

Start the queue worker to process calls:

```bash
php artisan queue:work --queue=calls,conversations,default
```

### 6. Scheduler

Add to your server's cron:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

The scheduler automatically dispatches reminder calls:
- **Hourly**: Calls 24 hours before appointment
- **Every 30 minutes**: Calls 2 hours before appointment

### 7. Testing

```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

## API Endpoints

### Public Endpoints (No Auth)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/book-and-call` | **Submit booking form → creates appointment → triggers AI call** |
| POST | `/api/twilio/voice` | Twilio voice webhook for call flow |
| POST | `/api/twilio/status` | Twilio call status callback |
| POST | `/api/twilio/outbound-call` | Initial outbound call TwiML |
| POST | `/api/twilio/incoming-call` | Handles incoming calls |
| POST | `/api/twilio/gather` | Handles speech/digit gathering |

### Protected Endpoints (Auth Required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/openai/analyze` | Analyze conversation intent |
| POST | `/api/ai/appointments/initiate-call` | Trigger reminder call |
| GET | `/api/ai/appointments/{id}/conversations` | Get conversation history |
| GET | `/api/ai/appointments/availability` | Check slot availability |
| GET | `/api/ai/appointments/slots` | Get available time slots |
| POST | `/api/twilio/initiate-call` | Initiate outbound call |
| POST | `/api/twilio/send-sms` | Send SMS notification |
| GET | `/api/twilio/call-logs` | List call logs |
| GET | `/api/dashboard/analytics` | Dashboard analytics |

## Environment Variables Reference

| Variable | Default | Description |
|----------|---------|-------------|
| `TWILIO_SID` | - | Twilio Account SID |
| `TWILIO_AUTH_TOKEN` | - | Twilio Auth Token |
| `TWILIO_PHONE_NUMBER` | - | Twilio phone number for calls/SMS |
| `TWILIO_WEBHOOK_BASE_URL` | `https://appointcare.local` | Public URL for webhooks |
| `OPENAI_API_KEY` | - | OpenAI API key |
| `OPENAI_MODEL` | `gpt-4o-mini` | OpenAI model for intent detection |
| `AI_MOCK` | `true` | Enable mock mode (no API key needed for dev) |
| `APPOINTCARE_ADMIN_PHONE` | - | Phone number for human transfer |
| `BUSINESS_HOURS_START` | `9` | Business hours start (24h) |
| `BUSINESS_HOURS_END` | `17` | Business hours end (24h) |
| `SLOT_DURATION_MINUTES` | `60` | Appointment slot duration |
| `REMINDER_24H_ENABLED` | `true` | Enable 24-hour reminder calls |
| `REMINDER_2H_ENABLED` | `true` | Enable 2-hour reminder calls |
| `QUEUE_CONNECTION` | `sync` | Queue driver (use `database` or `redis` in production) |

## Key Design Decisions

1. **SOLID Principles**: Each service has a single responsibility. Services depend on abstractions.
2. **Repository Pattern**: Data access is abstracted behind repositories for testability.
3. **Event-Driven Architecture**: Status changes fire events that trigger SMS notifications and analytics updates.
4. **Queue-Based Processing**: Voice calls and AI analysis run asynchronously via Laravel queues.
5. **Mock Mode**: The AI service works without an OpenAI key in development, returning canned responses.
6. **Nepali Language Support**: The OpenAI prompt instructs to respond in Nepali (Devanagari) when the customer speaks Nepali.
7. **Confidence Tracking**: Every AI action logs a confidence score for analytics and auditing.
8. **Error Resilience**: Failed calls are retried with backoff; all errors are logged.
