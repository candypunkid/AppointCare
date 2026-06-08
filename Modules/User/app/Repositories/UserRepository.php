<?php

namespace Modules\User\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = User::with('tenant');

        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (!empty($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }

        if (!empty($filters['sort']) && in_array($filters['sort'], ['name', 'email', 'created_at'])) {
            $direction = ($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
            $query->orderBy($filters['sort'], $direction);
        } else {
            $query->latest();
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
