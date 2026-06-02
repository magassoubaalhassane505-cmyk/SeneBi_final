<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    /** @deprecated Redirige vers la connexion unique */
    public function loginClient()
    {
        return redirect()->route('login');
    }

    /** @deprecated Redirige vers la connexion unique */
    public function loginManager()
    {
        return redirect()->route('login');
    }

    public function portal()
    {
        $approvedClients = \App\Models\User::where('role', 'client')
            ->where('is_active', true)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('secure-portal', compact('approvedClients'));
    }

    public function error403()
    {
        return view('403');
    }

    /** @deprecated Délègue à la connexion unique */
    public function authenticate(Request $request)
    {
        return $this->login($request);
    }

    /** @deprecated Délègue à la connexion unique */
    public function authenticateClient(Request $request)
    {
        return $this->login($request);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $email = Str::lower(trim($request->input('email')));
        $password = (string) $request->input('password');

        $user = User::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Identifiants incorrects. Vérifiez l\'email et le mot de passe saisis à l\'inscription.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'last_login_at')) {
            $user->forceFill(['last_login_at' => now()])->saveQuietly();
        }

        if ($user->role === 'client') {
            if ($user->status === 'pending' || ! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => 'Votre compte est en attente de validation par un manager.',
                ]);
            }

            if ($user->status === 'rejected') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => 'Votre demande d\'inscription a été refusée. Contactez SeneBI.',
                ]);
            }

            if ($user->status !== 'approved') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => 'Votre compte n\'est pas encore autorisé à se connecter.',
                ]);
            }
        }

        if (in_array($user->role, ['manager', 'admin'], true)) {
            return redirect()->intended(route('manager.dashboard'));
        }

        if ($user->role === 'client') {
            return redirect()->intended(route('client.dashboard'));
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        throw ValidationException::withMessages([
            'email' => 'Rôle utilisateur non reconnu.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
