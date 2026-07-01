<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>SeneBI - Nouveau mot de passe</title>
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
            <img class="login-brand-image" src="{{ asset('assets/img/logo.png') }}" alt="Logo SeneBI" />
          </div>

          <h1>Nouveau mot de passe</h1>
          <p class="login-subtitle">Choisissez un mot de passe sécurisé (8 caractères minimum)</p>

          @if ($errors->any())
            <div class="form-feedback error">
              @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
              @endforeach
            </div>
          @endif

          <form method="POST" action="{{ route('password.update') }}" class="auth-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}" />

            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required />

            <label for="password">Nouveau mot de passe</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" />

            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" />

            <button class="btn login-submit" type="submit">Enregistrer</button>
          </form>

          <div class="auth-switch" style="margin-top: 16px;">
            <a href="{{ route('login') }}">Retour à la connexion</a>
          </div>
        </div>
      </section>
    </main>
  </body>
</html>
