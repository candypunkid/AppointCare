# Multi-Tenant AI Appointment System - Quick Start Guide

## What's Been Built

A complete multi-tenant appointment booking system with AI-powered voice calls using Twilio. Each business gets its own subdomain (e.g., `clinic1.appointcare.com`, `salon.appointcare.com`).

## Key Features

✅ **Multi-Tenant Architecture** - Each tenant is isolated with its own data  
✅ **AI Voice Calls** - Twilio integration for automated appointment handling  
✅ **Appointment Management** - Book, cancel, postpone via AI conversation  
✅ **Modern UI** - Beautiful appointment booking form with glassmorphism design  
✅ **Subdomain Routing** - Automatic tenant detection from subdomain

## Quick Setup (10 minutes)

### Step 1: Get Twilio Credentials

1. Sign up at [Twilio](https://www.twilio.com) (free trial)
2. Get: Account SID, Auth Token, Phone Number

### Step 2: Update `.env`

```env
TWILIO_ACCOUNT_SID=your_sid_here
TWILIO_AUTH_TOKEN=your_token_here
TWILIO_PHONE_NUMBER=+1234567890

APP_URL=https://yourdomain.local
TENANT_DOMAIN=yourdomain.local
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

### Step 4: Create Test Tenant & User

```bash
php artisan tinker

# Create tenant
Tenant::create([
    'name' => 'Test Clinic',
    'slug' => 'testclinic',
    'is_active' => true,
    'email' => 'admin@testclinic.com',
    'phone' => '+1-555-0000',
]);

# Create admin user
User::create([
    'tenant_id' => 1,
    'name' => 'Admin',
    'email' => 'admin@testclinic.com',
    'password' => bcrypt('password'),
    'phone' => '+1-555-1111',
    'role' => 'admin',
    'email_verified_at' => now(),
]);
```

### Step 5: Configure DNS (Local Testing)

Add to your `hosts` file:

```
127.0.0.1  testclinic.yourdomain.local
127.0.0.1  yourdomain.local
```

Or use Laravel's built-in server:

```bash
php artisan serve --host=testclinic.yourdomain.local
```

### Step 6: Test Booking Form

Visit: `https://testclinic.yourdomain.local/book-appointment`

## File Structure

```
app/
├── Models/
│   ├── Tenant.php          # Tenant model
│   ├── Appointment.php      # Appointment model
│   ├── AppointmentRequest.php
│   └── AIConversation.php
├── Services/
│   ├── TwilioService.php   # Twilio integration
│   └── AIAppointmentHandler.php  # AI logic
├── Http/
│   └── Middleware/
│       └── ResolveTenant.php  # Tenant detection
└── Helpers/
    └── TenantHelper.php    # Helper functions

database/migrations/
├── 2024_01_01_create_tenants_table.php
├── 2024_01_02_add_tenants_to_users_table.php
├── 2024_01_03_create_appointments_table.php
├── 2024_01_04_create_appointment_requests_table.php
└── 2024_01_05_create_ai_conversations_table.php

Modules/User/
├── app/Http/Controllers/
│   ├── AuthController.php
│   ├── AppointmentController.php
│   └── TwilioWebhookController.php
├── resources/views/
│   ├── auth/
│   │   ├── login.blade.php
│   │   └── register.blade.php
│   └── appointments/
│       └── book.blade.php
└── routes/web.php
```

## How It Works

### Customer Journey

1. Customer visits `https://clinic.domain.com/book-appointment`
2. Fills appointment request form
3. Submits form → AI call initiated
4. Twilio calls customer
5. Customer presses:
    - `1` to **book** appointment
    - `2` to **cancel** existing appointment
    - `3` to **postpone** existing appointment
6. AI confirms action and appointment is saved

### Admin/Staff Journey

1. Login at `https://clinic.domain.com/login`
2. View dashboard with all appointments
3. Can manually initiate calls for pending requests
4. View appointment records and history

## Database Models Explained

### `Tenant`

Business/organization using the system

- Unique `slug` for subdomain routing
- Settings for customization
- Staff and customer users

### `User`

Any user in the system (admin, staff, customer)

- Assigned to specific `tenant_id`
- Role-based: admin, staff, or customer
- Relationships to appointments

### `Appointment`

Scheduled appointment

- Links customer, staff, and tenant
- Status: pending, confirmed, completed, cancelled, postponed
- Metadata for additional data

### `AppointmentRequest`

Initial booking request before confirmation

- Customer details (name, phone, email)
- Preferred date/time
- Initiates AI conversation

### `AIConversation`

Record of each AI call

- Twilio call SID
- Conversation transcript
- Action taken (book/cancel/postpone)
- Metadata for analysis

## Available Routes

### Public Routes

- `GET /book-appointment` - Booking form
- `POST /book-appointment` - Submit booking
- `GET /register` - Registration
- `POST /register` - Register user
- `GET /login` - Login form
- `POST /login` - Authenticate

### Twilio Webhooks (Auto-handled)

- `POST /twilio/incoming-call` - Incoming call handler
- `POST /twilio/handle-input` - User input (digit pressed)
- `POST /twilio/call-status` - Call status updates

### Protected Routes (Login required)

- `GET /users` - View users
- `GET /appointments` - View appointments
- `POST /appointments/{id}/initiate-call` - Manually call customer
- `PUT /appointments/{id}` - Update appointment
- `DELETE /appointments/{id}` - Cancel appointment

## Helper Functions

```php
// Get current tenant
tenant()

// Get tenant ID
tenant_id()

// Check if in tenant request context
is_tenant_request()

// Get all active tenants
get_active_tenants()
```

## Testing the AI Conversation

### Test Flow

1. **Go to booking form**

    ```
    https://testclinic.yourdomain.local/book-appointment
    ```

2. **Fill form with valid phone number**
    - Name: John Doe
    - Email: john@example.com
    - Phone: +1-555-123-4567 (use real number or Twilio test number)
    - Service: Consultation

3. **Submit and answer call**
    - You'll receive a call from Twilio
    - AI will ask you to press 1, 2, or 3
    - Press 1 to book appointment

4. **Check database**
    ```bash
    php artisan tinker
    AIConversation::latest()->first()
    Appointment::latest()->first()
    ```

## Troubleshooting

### "Tenant not found"

✓ Check subdomain matches tenant `slug`  
✓ Ensure tenant has `is_active = true`  
✓ Test DNS/hosts file resolution

### No Twilio calls coming through

✓ Verify Account SID and Auth Token  
✓ Check Twilio phone number has credit  
✓ Ensure webhook URLs are HTTPS in production  
✓ Check Twilio logs for errors

### "Conversation not found" error

✓ Ensure middleware is detecting tenant  
✓ Check Twilio call SID is being saved  
✓ Verify database connection

## Environment Variables Checklist

```env
# Core
APP_URL=https://yourdomain.local
APP_DEBUG=true  # Set to false in production

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=appointcare
DB_USERNAME=root
DB_PASSWORD=

# Twilio
TWILIO_ACCOUNT_SID=ACxxxxxxxxxx
TWILIO_AUTH_TOKEN=xxxxxxxxxxxxx
TWILIO_PHONE_NUMBER=+1234567890

# Mail (optional, for notifications)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
```

## Next Steps

1. **Configure email notifications** for appointment confirmations
2. **Set up call recording** in Twilio for quality assurance
3. **Add SMS notifications** for reminders
4. **Create admin dashboard** for analytics
5. **Set up calendar integration** with Google Calendar
6. **Add appointment reminders** (24h, 1h before)
7. **Implement payment processing** if needed

## Security Notes

- All requests are scoped to the current tenant via middleware
- User authorization checks prevent cross-tenant access
- Phone numbers are stored securely in database
- Implement Twilio signature verification in production
- Use HTTPS in production (webhook URLs must be HTTPS)
- Regularly backup conversation data

## Production Deployment

1. Configure SSL certificates (Let's Encrypt)
2. Set up DNS wildcards for subdomains
3. Update Twilio webhook URLs to production domain
4. Enable query logging for debugging
5. Set up monitoring and alerting
6. Configure automated backups
7. Review and set resource limits

## Support

For issues or questions:

1. Check MULTI_TENANT_SETUP.md for detailed documentation
2. Review database schema in migrations
3. Check Laravel logs: `storage/logs/`
4. Enable debug mode in `.env` temporarily

---

**Ready to go!** Start with `php artisan serve` and visit your booking form. 🚀
