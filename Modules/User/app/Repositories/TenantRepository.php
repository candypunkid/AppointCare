<?php

namespace Modules\User\Repositories;

use App\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TenantRepository
{
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Tenant::query();

        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('domain', 'like', "%{$q}%");
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (! empty($filters['sort']) && in_array($filters['sort'], ['name', 'created_at'])) {
            $direction = ($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
            $query->orderBy($filters['sort'], $direction);
        } else {
            $query->latest();
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
