# AppointCare - Complete Implementation Guide

## Project Overview

AppointCare is a full-stack Laravel application with nWidart modular architecture that enables:

1. **Customers** to book appointments through a web form
2. **Automated AI calls** via Twilio to confirm appointments
3. **Real-time voice conversation** with OpenAI's Realtime API
4. **Automatic status updates** and notifications via email/SMS
5. **Admin dashboard** to manage all appointments

## Architecture

The project is built with **nWidart Laravel Modules**, providing a clean, scalable modular structure:

```
AppointCare/
├── Modules/
│   ├── Appointment/    # Core appointment logic & models
│   ├── Twilio/         # Voice call management
│   ├── AICall/         # OpenAI integration
│   └── Notification/   # Status notifications
├── app/                # Core application code
├── config/             # Application configuration
└── routes/             # Main application routes
```

## Project Structure

### 1. **Appointment Module** (`Modules/Appointment`)

**Models:**

- `Appointment` - Main appointment model with status tracking

**Controllers:**

- `AppointmentController` - RESTful API endpoints for appointments

**Jobs:**

- `InitiateAICall` - Queued job that triggers Twilio calls

**Events:**

- `AppointmentStatusChanged` - Broadcasted when status updates

**Observers:**

- `AppointmentObserver` - Logs all appointment changes

**Database:**

- `create_appointments_table` migration

**Routes:**

- `POST /appointments` - Create new appointment (public)
- `GET /appointments/{id}` - View appointment details
- `GET /admin/appointments` - List all (admin, requires auth)
- `PUT /admin/appointments/{id}` - Update (admin)
- `DELETE /admin/appointments/{id}` - Delete (admin)
- `POST /admin/appointments/{id}/retry-call` - Retry call (admin)

### 2. **Twilio Module** (`Modules/Twilio`)

**Services:**

- `TwilioService` - Handles all Twilio API interactions
    - `initiateCall()` - Place outbound calls
    - `handleStatusCallback()` - Receive call status updates
    - `disconnectCall()` - Terminate calls

**Controllers:**

- `TwilioWebhookController` - Receives webhooks from Twilio
    - `/twilio/webhook/stream` - Audio streaming setup
    - `/twilio/webhook/status` - Call status updates
    - `/twilio/health` - Health check

**Middleware:**

- `ValidateTwilioWebhook` - Validates Twilio signature on all webhooks

**Configuration:**

- Uses credentials from `.env`:
    - `TWILIO_SID` - Account SID
    - `TWILIO_AUTH_TOKEN` - Auth token
    - `TWILIO_PHONE_NUMBER` - Outbound phone number
    - `TWILIO_WEBHOOK_BASE_URL` - Webhook URL base

### 3. **AICall Module** (`Modules/AICall`)

**Services:**

- `OpenAIRealtimeService` - WebSocket connection to OpenAI Realtime API
    - `connect()` - Establish WebSocket connection
    - `sendMessage()` - Send text to AI
    - `sendAudio()` - Stream audio data
    - `listen()` - Receive AI responses
    - `disconnect()` - Close connection

**Features:**

- Configurable system instructions for the AI agent
- G.711 μ-law audio format (8kHz, same as Twilio)
- Realtime voice conversation
- Transcription and response generation

**Configuration:**

- Uses credentials from `.env`:
    - `OPENAI_API_KEY` - OpenAI API key
    - `OPENAI_REALTIME_MODEL` - Model name (default: gpt-4o-realtime-preview)

### 4. **Notification Module** (`Modules/Notification`)

**Notifications:**

- `AppointmentStatusNotification` - Multi-channel notification
    - Email notifications
    - SMS via Twilio

**Listeners:**

- `SendAppointmentNotification` - Listens to AppointmentStatusChanged event

**Event Flow:**

1. Appointment status changes
2. `AppointmentStatusChanged` event is fired
3. `SendAppointmentNotification` listener catches it
4. Customer receives email and SMS

## Installation & Setup

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Environment Setup

Copy `.env.example` to `.env` and configure:

```bash
cp .env.example .env
```

**Critical Variables:**

```env
APP_NAME=AppointCare
APP_URL=https://appointcare.local

# Database
DB_CONNECTION=mysql
DB_DATABASE=appointcare
DB_USERNAME=root
DB_PASSWORD=

# Queue (Redis recommended)
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis

# Broadcasting
BROADCAST_DRIVER=reverb
REVERB_APP_ID=appointcare
REVERB_APP_KEY=your-key
REVERB_APP_SECRET=your-secret

# Twilio
TWILIO_SID=ACxxxxxxxxxx
TWILIO_AUTH_TOKEN=your_token
TWILIO_PHONE_NUMBER=+1234567890
TWILIO_WEBHOOK_BASE_URL=https://appointcare.local

# OpenAI
OPENAI_API_KEY=sk-xxx
OPENAI_REALTIME_MODEL=gpt-4o-realtime-preview
```

### 3. Database Setup

```bash
php artisan migrate
php artisan db:seed
```

### 4. Configure Queue (Redis)

Ensure Redis is running:

```bash
redis-cli ping
# Should return: PONG
```

### 5. Enable Modules

```bash
php artisan module:enable Appointment
php artisan module:enable Twilio
php artisan module:enable AICall
php artisan module:enable Notification
```

## Data Flow

### Appointment Creation → AI Call

```
Customer submits form
    ↓
POST /appointments
    ↓
AppointmentController@store()
    ↓
Appointment created (status: pending)
    ↓
InitiateAICall job dispatched → Redis queue
    ↓
Queue worker processes job
    ↓
TwilioService->initiateCall()
    ↓
Twilio places outbound call to customer phone
    ↓
Customer answers
    ↓
Twilio connects to /twilio/webhook/stream
    ↓
OpenAI Realtime connection established
    ↓
AI agent confirms appointment details
    ↓
Conversation recorded and transcribed
    ↓
Call ends
    ↓
Twilio sends status callback
    ↓
/twilio/webhook/status processes update
    ↓
Appointment status updated (confirmed/completed/failed)
    ↓
AppointmentStatusChanged event triggered
    ↓
SendAppointmentNotification listener catches event
    ↓
Email + SMS sent to customer
```

## Running the Application

### Development Server

```bash
composer dev
```

This runs:

- Laravel dev server (`php artisan serve`)
- Queue worker (`php artisan queue:listen`)
- Log viewer (`php artisan pail`)
- Vite dev server (`npm run dev`)

### Production

1. **Build assets:**

    ```bash
    npm run build
    ```

2. **Cache config:**

    ```bash
    php artisan config:cache
    php artisan route:cache
    ```

3. **Start queue worker:**

    ```bash
    php artisan queue:work redis --queue=calls --tries=3
    ```

4. **Enable broadcasting (Reverb):**
    ```bash
    php artisan reverb:start
    ```

## API Endpoints

### Public Endpoints

**Create Appointment:**

```
POST /appointments
Content-Type: application/json

{
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_phone": "+1234567890",
  "appointment_date": "2024-01-15T10:00:00",
  "description": "Consultation",
  "appointment_type": "Initial"
}
```

**View Appointment:**

```
GET /appointments/{id}
```

### Admin Endpoints (Requires Auth)

**List All Appointments:**

```
GET /admin/appointments
Authorization: Bearer {token}
```

**Update Appointment:**

```
PUT /admin/appointments/{id}
Authorization: Bearer {token}

{
  "status": "confirmed",
  "notes": "Updated notes"
}
```

**Delete Appointment:**

```
DELETE /admin/appointments/{id}
Authorization: Bearer {token}
```

**Retry Call:**

```
POST /admin/appointments/{id}/retry-call
Authorization: Bearer {token}
```

### Twilio Webhooks (No Auth Required)

**Stream Setup:**

```
POST /twilio/webhook/stream
```

**Status Callback:**

```
POST /twilio/webhook/status
```

**Health Check:**

```
GET /twilio/health
```

## Configuration Files

### `config/services.php`

- Twilio credentials and webhook URL
- OpenAI API configuration

### `config/queue.php`

- Redis queue configuration
- "calls" queue specifically for appointment calls

### `Modules/AICall/config/aicall.php`

- OpenAI model and audio format settings
- System instructions for AI agent

## Key Features

✅ **Modular Architecture** - Clean separation of concerns
✅ **Queued Processing** - Non-blocking appointment processing
✅ **Real-time Voice** - OpenAI Realtime API integration
✅ **Webhooks** - Secure Twilio webhook handling
✅ **Event Broadcasting** - Real-time status updates
✅ **Multi-channel Notifications** - Email + SMS
✅ **Error Handling** - Retry logic and failure handling
✅ **Logging** - Comprehensive logging of all operations
✅ **RESTful API** - Standard JSON API
✅ **Database Tracking** - Full audit trail of appointments

## Deployment Checklist

- [ ] Configure production database
- [ ] Set `.env` variables from provider dashboards
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Cache configuration: `php artisan config:cache`
- [ ] Build assets: `npm run build`
- [ ] Start queue worker: `php artisan queue:work redis --queue=calls`
- [ ] Enable Reverb: `php artisan reverb:start`
- [ ] Configure Twilio webhook URLs to production domain
- [ ] Set up SSL certificate for HTTPS
- [ ] Configure Redis for persistence
- [ ] Set up log rotation

## Troubleshooting

### "Queue worker not processing jobs"

- Check Redis is running: `redis-cli ping`
- Check queue connection in `.env`: `QUEUE_CONNECTION=redis`
- Restart worker: `php artisan queue:work redis --queue=calls`

### "Twilio webhook signature invalid"

- Verify `TWILIO_AUTH_TOKEN` matches dashboard
- Ensure webhook URL is HTTPS
- Check URL matches exactly in Twilio settings

### "OpenAI connection timeout"

- Verify `OPENAI_API_KEY` is valid
- Check internet connection
- Ensure no firewall blocking WebSocket connections

### "Appointments not receiving calls"

- Verify `TWILIO_PHONE_NUMBER` is valid
- Check customer phone number format (include country code)
- Review Twilio logs in dashboard
- Check application logs: `storage/logs/laravel.log`

## Support & Documentation

- **Laravel Documentation**: https://laravel.com/docs
- **nWidart Modules**: https://nwidart.com/laravel-modules
- **Twilio Docs**: https://www.twilio.com/docs
- **OpenAI Realtime**: https://platform.openai.com/docs/realtime
- **Redis**: https://redis.io/documentation

## License

MIT License - See LICENSE file for details

---

**Version**: 1.0.0
**Last Updated**: June 2024
**Author**: AppointCare Team
