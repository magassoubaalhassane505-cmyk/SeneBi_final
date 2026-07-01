<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>SeneBI - Inscription Client</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}" />
  </head>
  <body data-page="auth-register">
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

          <h1>Inscription Client</h1>
          <p class="login-subtitle">Demande d'accès pour votre espace agricole</p>

          @if (session('status'))
            <div class="form-feedback success">{{ session('status') }}</div>
          @endif

          <form method="POST" action="{{ route('register.post') }}" class="auth-form">
            @csrf

            @if ($errors->any())
              <div class="form-feedback error">
                @foreach ($errors->all() as $error)
                  <p>{{ $error }}</p>
                @endforeach
              </div>
            @endif

            <label for="name">Nom Complet</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Ex: Mamadou Diallo" required />

            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Ex: votre-email@entreprise.com" required />

            <label for="phone">Téléphone</label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="Ex: +223 XX XX XX XX" required />

            <label for="company">Entreprise / Exploitation</label>
            <input id="company" name="company" type="text" value="{{ old('company') }}" placeholder="Ex: Sidi Agri Mali" required />

            <label for="location">Localisation</label>
            <input id="location" name="location" type="text" value="{{ old('location') }}" placeholder="Ex: Kayes, Mali" required />

            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" placeholder="Minimum 8 caractères" required autocomplete="new-password" />

            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Retapez le mot de passe" required autocomplete="new-password" />

            <button class="btn login-submit" type="submit">Créer mon compte</button>
          </form>

          <div class="auth-switch">
            <p>Vous avez déjà un compte ?</p>
            <a href="{{ route('login') }}" class="btn secondary">Se connecter</a>
          </div>
        </div>
      </section>
    </main>
  </body>
</html>
