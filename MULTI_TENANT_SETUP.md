# Multi-Tenant AI Appointment Scheduling System

## Overview

This is a comprehensive multi-tenant appointment booking and scheduling system with AI-powered voice conversations powered by Twilio. Customers can book, cancel, or postpone appointments through AI phone calls.

## Architecture

### Multi-Tenancy Structure

- **Subdomain-based**: Each tenant is accessible via `{tenant-slug}.yourdomain.com`
- **Database isolation**: Tenant data is isolated at the database level using `tenant_id`
- **Automatic tenant resolution**: Middleware detects tenant from subdomain and sets it in the request context

### Core Components

#### 1. **Models**

- `Tenant`: Represents a business/organization
- `User`: Users with roles (admin, staff, customer) assigned to tenants
- `Appointment`: Scheduled appointments within a tenant
- `AppointmentRequest`: Initial appointment booking requests
- `AIConversation`: Records of AI conversations with customers

#### 2. **Services**

- `TwilioService`: Handles all Twilio API interactions (voice calls, SMS)
- `AIAppointmentHandler`: Manages AI conversation logic (booking, cancellation, postponement)

#### 3. **Controllers**

- `TwilioWebhookController`: Handles Twilio webhooks (incoming calls, user input, call status)
- `AppointmentController`: Manages appointment booking and management
- `AuthController`: User authentication

#### 4. **Middleware**

- `ResolveTenant`: Detects and sets the current tenant from subdomain

## Setup Guide

### 1. Environment Configuration

Add these to your `.env` file:

```env
# Twilio Configuration
TWILIO_ACCOUNT_SID=your_twilio_account_sid
TWILIO_AUTH_TOKEN=your_twilio_auth_token
TWILIO_PHONE_NUMBER=+1234567890  # Your Twilio phone number

# Multi-Tenant Domain
TENANT_DOMAIN=yourdomain.com
APP_URL=https://yourdomain.com
```

### 2. Database Migrations

Run the migrations to create the necessary tables:

```bash
php artisan migrate
```

This creates:

- `tenants` - Stores tenant information
- `users` - Updated to include `tenant_id`, `phone`, and `role`
- `appointments` - Appointment records
- `appointment_requests` - Initial booking requests
- `ai_conversations` - AI call records

### 3. Twilio Setup

#### Get Twilio Credentials

1. Sign up at [Twilio](https://www.twilio.com)
2. Get your Account SID, Auth Token, and Phone Number from the dashboard
3. Add these to your `.env` file

#### Configure Webhooks

In Twilio Console:

1. Go to Phone Numbers > Active Numbers
2. Select your phone number
3. Set "Incoming Calls" to webhook:
    - URL: `https://yourdomain.com/twilio/incoming-call`
    - Method: POST
4. Set "Status Callbacks":
    - URL: `https://yourdomain.com/twilio/call-status`
    - Method: POST

### 4. DNS Configuration

For subdomain-based multi-tenancy to work, configure your DNS:

```
*.yourdomain.com  A  your.server.ip
```

This allows all subdomains to point to your application.

### 5. Create a Tenant

You can create a tenant using:

```bash
php artisan tinker
```

```php
App\Models\Tenant::create([
    'name' => 'Acme Clinic',
    'slug' => 'acmeclinic',
    'domain' => null,
    'phone' => '+1-555-0000',
    'email' => 'admin@acmeclinic.com',
    'description' => 'Professional medical clinic',
    'is_active' => true,
    'settings' => [],
]);
```

### 6. Create Admin User for Tenant

```php
App\Models\User::create([
    'tenant_id' => 1,  // ID of the tenant created above
    'name' => 'Admin User',
    'email' => 'admin@acmeclinic.com',
    'phone' => '+1-555-1234',
    'password' => bcrypt('secure_password'),
    'role' => 'admin',
    'email_verified_at' => now(),
]);
```

## Usage

### For Customers

1. **Visit the booking page**: `https://acmeclinic.yourdomain.com/book-appointment`
2. **Fill the form** with:
    - Name, email, phone number
    - Service type
    - Preferred date/time (optional)
    - Additional message (optional)
3. **Submit the form**: AI will call within minutes
4. **During the call**: Press:
    - `1` to book an appointment
    - `2` to cancel existing appointment
    - `3` to postpone existing appointment

### For Admins/Staff

1. **Login**: `https://acmeclinic.yourdomain.com/login`
2. **View appointments**: Dashboard shows all appointments
3. **Manually initiate calls**:
    - For appointment requests, click "Call Customer"
    - AI will handle the conversation

## API Response Examples

### Successful Booking

```json
{
    "success": true,
    "action": "booked",
    "appointment_id": 123,
    "prompt": "Great! Your appointment has been confirmed for May 25, 2024 at 2:00 PM for consultation..."
}
```

### Call Initiation

```json
{
    "success": true,
    "message": "Call initiated successfully",
    "conversation_id": 1,
    "call_sid": "CA1234567890..."
}
```

## Database Schema

### Tenants Table

```sql
- id: Primary Key
- name: Tenant business name
- slug: URL-friendly identifier (subdomain)
- domain: Custom domain (optional)
- phone: Contact phone
- email: Contact email
- description: Business description
- logo_path: Logo file path
- settings: JSON settings object
- is_active: Boolean flag
- timestamps
```

### Appointments Table

```sql
- id: Primary Key
- tenant_id: Foreign Key to tenants
- customer_id: Foreign Key to users (customer)
- staff_id: Foreign Key to users (staff)
- service: Service name
- scheduled_at: Appointment datetime
- scheduled_end_at: End datetime
- status: pending|confirmed|completed|cancelled|postponed
- notes: Additional notes
- metadata: JSON metadata
- timestamps
- soft_deletes
```

### AI Conversations Table

```sql
- id: Primary Key
- tenant_id: Foreign Key to tenants
- appointment_request_id: Foreign Key to appointment_requests
- appointment_id: Foreign Key to appointments
- customer_phone: Phone number called
- conversation_type: voice|sms
- twilio_call_sid: Twilio call identifier
- status: initiated|in_progress|completed|failed
- conversation_transcript: Full transcript
- action_taken: booked|cancelled|postponed|none
- started_at: Conversation start time
- ended_at: Conversation end time
- metadata: JSON data
- timestamps
```

## AI Conversation Flow

### Voice Call Flow

1. **Customer submits form**
    - Creates `AppointmentRequest` record
    - Initiates Twilio call

2. **AI answers**
    - Plays greeting with service options

3. **Customer presses digit**
    - 1: Proceeds to booking
    - 2: Proceeds to cancellation
    - 3: Proceeds to postponement

4. **AI processes action**
    - Books/cancels/postpones appointment
    - Confirms with customer

5. **Call ends**
    - Records conversation
    - Sends confirmation SMS (optional)

## Error Handling

### Common Issues

#### "Tenant not found"

- Ensure DNS is properly configured
- Check tenant `slug` matches subdomain
- Verify tenant is marked `is_active = true`

#### "Twilio call failed"

- Verify Twilio credentials in `.env`
- Check phone number format includes country code
- Ensure webhook URLs are publicly accessible

#### "Appointment not found"

- Verify tenant context is properly set
- Check customer phone number matches

## Helper Functions

```php
// Get current tenant
tenant()

// Get current tenant ID
tenant_id()

// Check if request is for a tenant
is_tenant_request()

// Get all active tenants
get_active_tenants()
```

## Testing

### Test Booking Flow

```bash
# 1. Create test tenant
php artisan tinker
Tenant::create(['name' => 'Test Clinic', 'slug' => 'test', 'is_active' => true])

# 2. Create test user
User::create(['tenant_id' => 1, 'name' => 'Test User', 'email' => 'test@test.com', 'password' => bcrypt('test'), 'role' => 'staff'])

# 3. Visit booking form
https://test.yourdomain.local/book-appointment
```

## Troubleshooting

### Calls not connecting

- Verify Twilio account has credits
- Check phone number has voice capability enabled
- Ensure webhook URLs are HTTPS

### Tenant detection failing

- Verify DNS wildcards are configured
- Check `.env` APP_URL is correct
- Test subdomain resolution: `nslookup tenant.yourdomain.com`

### Database errors

- Run migrations: `php artisan migrate`
- Check migrations were applied: `php artisan migrate:status`
- Verify database connection in `.env`

## Production Checklist

- [ ] Configure HTTPS/SSL certificates
- [ ] Set up DNS wildcards
- [ ] Add Twilio webhook URLs with HTTPS
- [ ] Set strong database passwords
- [ ] Enable database backups
- [ ] Configure email notifications
- [ ] Set up call recording storage
- [ ] Test with real Twilio account
- [ ] Monitor Twilio usage and costs
- [ ] Set up error logging and monitoring

## Security Considerations

1. **Tenant Isolation**: All queries automatically filtered by `tenant_id`
2. **User Authorization**: Staff/admin checks ensure users can only access their tenant
3. **Webhook Verification**: Implement Twilio signature verification
4. **Phone Number Security**: Store phone numbers securely
5. **Call Recording**: Store encrypted call recordings

## Future Enhancements

- [ ] SMS-based appointment booking
- [ ] Advanced AI NLP for natural conversations
- [ ] Calendar integration (Google, Outlook)
- [ ] Email reminders and confirmations
- [ ] Customer self-service portal
- [ ] Analytics and reporting dashboard
- [ ] Appointment reminders via SMS
- [ ] Multi-language support
