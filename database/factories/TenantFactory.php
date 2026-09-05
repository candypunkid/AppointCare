<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => Str::slug(fake()->unique()->company()),
            'domain' => null,
            'phone' => null,
            'email' => fake()->safeEmail(),
            'description' => null,
            'settings' => null,
            'is_active' => true,
        ];
    }
}
