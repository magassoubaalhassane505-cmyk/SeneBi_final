<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>SeneBI - Connexion Client</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}" />
  </head>
  <body data-page="auth-login" data-server-side="1">
    <main class="auth-shell">
      <section class="login-frame">
        <aside class="login-photo" aria-hidden="true"></aside>
        <div class="login-panel">
          <div class="login-brand">
            <img
               class="login-brand-image"
              src="{{ asset('assets/img/logo.png') }}"
              alt="Logo SeneBI"
            />
          </div>

          <h1>SeneBI: Espace Client</h1>
          <p class="login-subtitle">Votre plateforme de suivi agricole</p>

          @if (session('status'))
            <div class="form-feedback success">{{ session('status') }}</div>
          @endif

          @if ($errors->any())
            <div class="form-feedback error">
              @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
              @endforeach
            </div>
          @endif

          <form id="loginForm" method="POST" action="{{ url('/login-client') }}" class="auth-form">
            @csrf
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', 'sidi@sidi-agri.sn') }}" placeholder="Ex: votre-email@entreprise.com" required />

            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" value="client123" placeholder="Votre mot de passe" required />

            <button class="btn login-submit" type="submit">Se connecter</button>
            <div id="loginFeedback" class="form-feedback" aria-live="polite"></div>
          </form>

          <div class="auth-switch">
            <p>Vous êtes un manager ?</p>
            <a href="{{ route('login.manager') }}" class="btn secondary">Accès Manager</a>
          </div>

          <div class="auth-switch" style="margin-top: 16px;">
            <a href="{{ route('register') }}" class="text-muted" style="color: #4b5563; font-weight: 500;">Vous n'avez pas de compte ? S'inscrire</a>
          </div>
        </div>
      </section>
    </main>

  </body>
</html>
