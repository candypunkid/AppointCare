<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        Log::info('AuthController.showRegister called', ['ip' => request()->ip()]);

        // Redirect authenticated users to their dashboard
        if (Auth::check()) {
            $user = Auth::user();
            $isSuper = method_exists($user, 'hasRole') ? $user->hasRole('super_admin') : ($user->role === 'super_admin');
            return redirect()->route($isSuper ? 'admin.dashboard' : ($user->role === 'tenant_admin' ? 'tenant.dashboard' : 'dashboard.index'));
        }

        return view('user::auth.register');
    }

    /**
     * Handle registration.
     */
    public function register(Request $request)
    {
        Log::info('AuthController.register called', ['email' => $request->input('email'), 'ip' => $request->ip()]);

        // Prevent authenticated users from registering
        if (Auth::check()) {
            $user = Auth::user();
            $isSuper = method_exists($user, 'hasRole') ? $user->hasRole('super_admin') : ($user->role === 'super_admin');
            return redirect()->route($isSuper ? 'admin.dashboard' : ($user->role === 'tenant_admin' ? 'tenant.dashboard' : 'dashboard.index'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:super_admin,tenant_admin,staff,customer'],
            'tenant_name' => ['required_if:role,tenant_admin', 'string', 'max:255'],
            'tenant_domain' => ['nullable', 'string', 'max:255'],
        ]);

        $tenantId = null;

        if ($validated['role'] === 'tenant_admin') {
            // create tenant for this user
            $slug = Str::slug($validated['tenant_name']);
            // ensure unique slug
            $baseSlug = $slug ?: Str::slug($validated['tenant_name'] ?? $validated['name']);
            $counter = 0;
            $candidate = $baseSlug;
            while (Tenant::where('slug', $candidate)->exists()) {
                $counter++;
                $candidate = $baseSlug . '-' . $counter;
            }
            $tenant = Tenant::create([
                'name' => $validated['tenant_name'],
                'slug' => $candidate,
                'domain' => $validated['tenant_domain'] ?? null,
                'is_active' => true,
            ]);

            $tenantId = $tenant->id;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'tenant_id' => $tenantId,
            'role' => $validated['role'],
        ]);

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        $isSuper = method_exists($user, 'hasRole') ? $user->hasRole('super_admin') : ($user->role === 'super_admin');

        $intended = session()->pull('url.intended');
        if ($intended) {
            $path = parse_url($intended, PHP_URL_PATH) ?: '';
            if (! $isSuper && str_starts_with($path, '/admin')) {
                return redirect()->route('tenant.dashboard');
            }
            return redirect()->to($intended);
        }

        return redirect()->route($isSuper ? 'admin.dashboard' : ($user->role === 'tenant_admin' ? 'tenant.dashboard' : 'dashboard.index'));
    }

    /**
     * Determine the correct dashboard route by role.
     */
    protected function redirectToDashboard(User $user)
    {
        if ($user->role === 'super_admin') {
            return route('admin.dashboard', absolute: false);
        }

        if ($user->role === 'tenant_admin') {
            return route('tenant.dashboard', absolute: false);
        }

        return route('dashboard.index', absolute: false);
    }

    /**
     * Show the login form.
     */
    public function showLogin()
    {
        Log::info('AuthController.showLogin called', ['ip' => request()->ip()]);

        // Redirect authenticated users to their dashboard
        if (Auth::check()) {
            $user = Auth::user();
            $isSuper = method_exists($user, 'hasRole') ? $user->hasRole('super_admin') : ($user->role === 'super_admin');
            return redirect()->route($isSuper ? 'admin.dashboard' : ($user->role === 'tenant_admin' ? 'tenant.dashboard' : 'dashboard.index'));
        }

        return view('user::auth.login');
    }

    /**
     * Handle login.
     */
    public function login(Request $request)
    {
        Log::info('AuthController.login attempt', ['email' => $request->input('email'), 'ip' => $request->ip()]);

        // Prevent authenticated users from logging in again
        if (Auth::check()) {
            $user = Auth::user();
            $isSuper = method_exists($user, 'hasRole') ? $user->hasRole('super_admin') : ($user->role === 'super_admin');
            return redirect()->route($isSuper ? 'admin.dashboard' : ($user->role === 'tenant_admin' ? 'tenant.dashboard' : 'dashboard.index'));
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $isSuper = method_exists($user, 'hasRole') ? $user->hasRole('super_admin') : ($user->role === 'super_admin');

            $intended = session()->pull('url.intended');
            if ($intended) {
                $path = parse_url($intended, PHP_URL_PATH) ?: '';
                if (! $isSuper && str_starts_with($path, '/admin')) {
                    return redirect()->route('tenant.dashboard');
                }
                return redirect()->to($intended);
            }

            return redirect()->route($isSuper ? 'admin.dashboard' : ($user->role === 'tenant_admin' ? 'tenant.dashboard' : 'dashboard.index'));
        }
        Log::warning('AuthController.login failed', ['email' => $request->input('email'), 'ip' => $request->ip()]);
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Log::info('AuthController.logout called', ['ip' => $request->ip(), 'user_id' => auth()->id()]);
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
