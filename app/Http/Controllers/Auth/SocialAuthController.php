<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Supported OAuth providers
     */
    protected array $providers = ['google', 'github'];

    /**
     * Redirect to OAuth provider
     */
    public function redirect(string $provider): RedirectResponse
    {
        if (!in_array($provider, $this->providers)) {
            return redirect()->route('login')->withErrors([
                'social' => 'Invalid social provider.',
            ]);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle OAuth callback
     */
    public function callback(string $provider): RedirectResponse
    {
        if (!in_array($provider, $this->providers)) {
            return redirect()->route('login')->withErrors([
                'social' => 'Invalid social provider.',
            ]);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'social' => 'Unable to authenticate with ' . ucfirst($provider) . '. Please try again.',
            ]);
        }

        // Check if user exists with this email
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // User exists, log them in
            if (!$user->is_active) {
                return redirect()->route('login')->withErrors([
                    'social' => 'Your account has been deactivated.',
                ]);
            }

            // Update social provider info
            $user->update([
                'social_provider' => $provider,
                'social_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'last_login_at' => now(),
            ]);
        } else {
            // Create new user
            $user = User::create([
                'username' => $this->generateUniqueUsername($socialUser->getName() ?? $socialUser->getNickname() ?? 'user'),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(), // Social users are auto-verified
                'social_provider' => $provider,
                'social_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'is_active' => true,
            ]);
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Generate a unique username from name
     */
    protected function generateUniqueUsername(string $name): string
    {
        // Convert to lowercase, remove special chars, replace spaces with underscores
        $username = Str::slug($name, '_');
        $username = preg_replace('/[^a-z0-9_]/', '', $username);

        // Ensure minimum length
        if (strlen($username) < 3) {
            $username = 'user_' . $username;
        }

        // Truncate if too long
        $username = substr($username, 0, 25);

        // Check uniqueness and add number if needed
        $originalUsername = $username;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . '_' . $counter;
            $counter++;
        }

        return $username;
    }
}
