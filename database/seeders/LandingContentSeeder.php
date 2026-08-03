<?php

namespace Database\Seeders;

use App\Models\LandingFaq;
use App\Models\LandingFeature;
use App\Models\LandingHowStep;
use App\Models\LandingIndustry;
use App\Models\LandingPlan;
use App\Models\LandingStat;
use App\Models\LandingTestimonial;
use Illuminate\Database\Seeder;

class LandingContentSeeder extends Seeder
{
    public function run(): void
    {
        LandingFeature::insert([
            ['icon' => '🌐', 'title' => 'Accept Online Bookings', 'description' => 'Enable hassle-free online bookings through our intuitive, mobile-optimized platform.', 'sort_order' => 1],
            ['icon' => '💬', 'title' => 'SMS & Email Notifications', 'description' => 'Stay informed with instant notifications via SMS and email.', 'sort_order' => 2],
            ['icon' => '📱', 'title' => 'Client & Admin App', 'description' => 'Access your booking system anytime, anywhere.', 'sort_order' => 3],
            ['icon' => '💳', 'title' => 'Accept Payments', 'description' => 'Securely accept payments for bookings through an integrated payment gateway.', 'sort_order' => 4],
            ['icon' => '🔗', 'title' => 'Integrations & API', 'description' => 'Twilio, OpenAI, webhooks and more for custom workflows.', 'sort_order' => 5],
            ['icon' => '⚙️', 'title' => 'Custom Features', 'description' => 'Tailor your system with custom features to match your unique business needs.', 'sort_order' => 6],
            ['icon' => '🎨', 'title' => 'Full Customization', 'description' => 'Personalize your booking site with custom branding, colors, and design.', 'sort_order' => 7],
            ['icon' => '🎁', 'title' => 'Products & Promotions', 'description' => 'Boost sales by offering products and promotions alongside bookings.', 'sort_order' => 8],
        ]);

        LandingHowStep::insert([
            ['step_number' => 1, 'title' => 'Book', 'description' => 'Clients book via web, phone, or staff interface.', 'sort_order' => 1],
            ['step_number' => 2, 'title' => 'Confirm', 'description' => 'AI-powered reminders and Twilio confirm appointments, detect intents, and notify staff.', 'sort_order' => 2],
            ['step_number' => 3, 'title' => 'Attend', 'description' => 'Staff manage schedules with conflict detection and analytics dashboards.', 'sort_order' => 3],
        ]);

        LandingIndustry::insert([
            ['icon' => '🏥', 'name' => 'Doctors & Healthcare', 'sort_order' => 1],
            ['icon' => '💼', 'name' => 'Business Consultants', 'sort_order' => 2],
            ['icon' => '💻', 'name' => 'Freelancers', 'sort_order' => 3],
            ['icon' => '⚖️', 'name' => 'Lawyers & Attorneys', 'sort_order' => 4],
            ['icon' => '🎯', 'name' => 'Consultants', 'sort_order' => 5],
            ['icon' => '🏋️', 'name' => 'Professional Trainers', 'sort_order' => 6],
            ['icon' => '📊', 'name' => 'Financial Advisors', 'sort_order' => 7],
            ['icon' => '📚', 'name' => 'Tutors & Teachers', 'sort_order' => 8],
        ]);

        LandingPlan::insert([
            ['name' => 'Basic', 'monthly_price' => 9.99, 'yearly_price' => 99.99, 'is_popular' => false, 'features' => json_encode(['5 Staff members', '10 daily appointments', '150 monthly appointments', 'Email support']), 'button_text' => 'Subscribe Now', 'badge_text' => null, 'sort_order' => 1],
            ['name' => 'Standard', 'monthly_price' => 14.99, 'yearly_price' => 139.99, 'is_popular' => true, 'features' => json_encode(['10 Staff members', '12 daily appointments', '300 monthly appointments', 'Priority support']), 'button_text' => 'Subscribe Now', 'badge_text' => 'Most Popular', 'sort_order' => 2],
            ['name' => 'Premium', 'monthly_price' => 19.99, 'yearly_price' => 199.99, 'is_popular' => false, 'features' => json_encode(['15 Staff members', '15 daily appointments', '450 monthly appointments', 'AI voice & SMS']), 'button_text' => 'Subscribe Now', 'badge_text' => null, 'sort_order' => 3],
            ['name' => 'Elite', 'monthly_price' => 39.99, 'yearly_price' => 399.99, 'is_popular' => false, 'features' => json_encode(['50 Staff members', '50 daily appointments', '1,500 monthly appointments', 'White-label & SLA']), 'button_text' => 'Subscribe Now', 'badge_text' => null, 'sort_order' => 4],
        ]);

        LandingTestimonial::insert([
            ['rating' => 5, 'text' => 'has truly transformed the way I manage appointments for my business. With its intuitive interface and robust features, scheduling has become a breeze.', 'author_name' => 'John Doe', 'author_role' => 'Healthcare Provider', 'sort_order' => 1],
            ['rating' => 5, 'text' => 'As a busy professional, I rely on this platform to keep my schedule organized. The platform is incredibly user-friendly and the AI features are game-changing.', 'author_name' => 'David Smith', 'author_role' => 'Business Consultant', 'sort_order' => 2],
            ['rating' => 5, 'text' => 'has been a lifesaver for my small business. It\'s packed with all the features I need to manage appointments efficiently.', 'author_name' => 'Alex Hae', 'author_role' => 'Freelancer', 'sort_order' => 3],
            ['rating' => 5, 'text' => 'has simplified my appointment management process. The automation features like reminders and follow-ups have helped me stay perfectly organized.', 'author_name' => 'Steve Warn', 'author_role' => 'Attorney', 'sort_order' => 4],
        ]);

        LandingFaq::insert([
            ['question' => 'What is this platform?', 'answer' => 'An intuitive online platform designed to simplify appointment scheduling and management for businesses.', 'sort_order' => 1],
            ['question' => 'How does it work?', 'answer' => 'Create, manage, and track appointments through a user-friendly interface with AI-powered automation.', 'sort_order' => 2],
            ['question' => 'Is it suitable for my business?', 'answer' => 'Yes — it caters to healthcare, beauty, education, legal, and professional services.', 'sort_order' => 3],
            ['question' => 'How secure is my data?', 'answer' => 'We employ industry-standard encryption and security protocols to safeguard your data.', 'sort_order' => 4],
            ['question' => 'How can I get started?', 'answer' => 'Simply sign up for an account, customize your settings, and start scheduling.', 'sort_order' => 5],
            ['question' => 'Does it integrate with Twilio & OpenAI?', 'answer' => 'Yes — Twilio for voice & SMS, OpenAI for AI-powered reminders and intent detection.', 'sort_order' => 6],
        ]);

        LandingStat::insert([
            ['label' => 'Customer Satisfaction', 'value' => '98%', 'sort_order' => 1],
            ['label' => 'Subscription Members', 'value' => '15M', 'sort_order' => 2],
            ['label' => 'Cost Savings', 'value' => '40%', 'sort_order' => 3],
            ['label' => 'Positive Reviews', 'value' => '69K', 'sort_order' => 4],
        ]);
    }
}
