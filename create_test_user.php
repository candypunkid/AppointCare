<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Check if user already exists
$existing = User::where('email', 'testadmin@test.local')->first();
if ($existing) {
    echo "User already exists: " . $existing->email . " (ID: " . $existing->id . ")\n";
    exit(0);
}

// Create test super_admin user
$user = User::create([
    'name' => 'Test Admin',
    'email' => 'testadmin@test.local',
    'password' => Hash::make('password123'),
    'role' => 'super_admin',
    'tenant_id' => null,
    'email_verified_at' => now(),
]);

echo "User created: " . $user->email . " (ID: " . $user->id . ", Role: " . $user->role . ")\n";
