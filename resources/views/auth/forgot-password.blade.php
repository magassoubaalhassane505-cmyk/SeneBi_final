<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>SeneBI - Mot de passe oublié</title>
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

          <h1>Mot de passe oublié</h1>
          <p class="login-subtitle">Indiquez votre email pour recevoir un lien de réinitialisation</p>

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

          <form method="POST" action="{{ route('password.email') }}" class="auth-form">
            @csrf
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" />

            <button class="btn login-submit" type="submit">Envoyer le lien</button>
          </form>

          <div class="auth-switch" style="margin-top: 16px;">
            <a href="{{ route('login') }}">Retour à la connexion</a>
          </div>
        </div>
      </section>
    </main>
  </body>
</html>
