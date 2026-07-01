<header class="topbar">
   <div class="topbar-inner">
     <!-- Partie Gauche : Logo SeneBI -->
     <a class="brand" href="/">
       <img class="logo-img" src="{{ asset('assets/img/logo.png') }}" alt="Logo SeneBI" />
       <div class="brand-title">
         <strong>SeneBI</strong>
         <span>Business Intelligence Agricole</span>
       </div>
     </a>

     <div class="topbar-right">
       <!-- Partie Centrale : Navigation -->
       <nav class="nav public-nav">
         <a href="/" class="{{ request()->path() == '/' or request()->path() == '' ? 'active' : '' }}">
           <span>Accueil</span>
         </a>
         <a href="/solutions" class="{{ request()->is('solutions*') ? 'active' : '' }}">
           <span>Solutions</span>
         </a>
         <a href="/a-propos" class="{{ request()->is('a-propos*') ? 'active' : '' }}">
           <span>À propos</span>
         </a>
         <a href="/faq" class="{{ request()->is('faq*') ? 'active' : '' }}">
           <span>FAQ</span>
         </a>
         <a href="/contact" class="{{ request()->is('contact*') ? 'active' : '' }}">
           <span>Contact</span>
         </a>
       </nav>

       <!-- Partie Droite : Actions -->
       <div class="topbar-actions">
         <a class="btn" href="/connexion" style="background: transparent; color: var(--text); border: 1px solid rgba(15,23,42,0.08);">Se connecter</a>
         <a class="btn" href="/inscription" style="background: var(--accent); color: #fff;">Créer un compte</a>
       </div>
     </div>
   </div>
</header>

<!-- Menu hamburger mobile -->
@include('partials.nav-mobile')

<style>
.public-nav a.active {
  background: #dcfce7;
  color: #14532d;
  font-weight: 600;
  border-left: 3px solid #10b981;
  border-radius: 0 8px 8px 0;
  transition: all 0.2s ease;
}

.public-nav a.active:hover {
  background: #bbf7d0;
  border-left-color: #059669;
}
</style>

<script>
(function() {
  function getNavLinks() {
    return [
      { href: '/', label: 'Accueil', active: {{ (request()->path() == '/' || request()->path() == '') ? 'true' : 'false' }} },
      { href: '/solutions', label: 'Solutions', active: {{ request()->is('solutions*') ? 'true' : 'false' }} },
      { href: '/a-propos', label: 'À propos', active: {{ request()->is('a-propos*') ? 'true' : 'false' }} },
      { href: '/faq', label: 'FAQ', active: {{ request()->is('faq*') ? 'true' : 'false' }} },
      { href: '/contact', label: 'Contact', active: {{ request()->is('contact*') ? 'true' : 'false' }} }
    ];
  }

  function initMobileNav() {
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const mobileNav = document.getElementById('mobileNav');
    const mobileNavClose = document.getElementById('mobileNavClose');
    const mobileNavLinks = document.getElementById('mobileNavLinks');
    const mobileNavFooter = document.getElementById('mobileNavFooter');
    const overlay = document.createElement('div');
    overlay.className = 'mobile-nav-overlay';
    overlay.id = 'mobileNavOverlay';
    document.body.appendChild(overlay);

    const links = getNavLinks();
    mobileNavLinks.innerHTML = links.map(link => 
      `<a href="${link.href}" class="mobile-nav-link ${link.active ? 'active' : ''}">
        <span>${link.label}</span>
      </a>`
    ).join('');

    mobileNavFooter.innerHTML = `
      <a class="btn" href="/connexion" style="width: 100%; text-align: center; margin-bottom: 8px;">Se connecter</a>
      <a class="btn" href="/inscription" style="width: 100%; text-align: center; background: var(--accent); color: #fff;">Créer un compte</a>
    `;

    function openNav() {
      mobileNav.classList.add('active');
      overlay.classList.add('active');
      hamburgerBtn.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
    }

    function closeNav() {
      mobileNav.classList.remove('active');
      overlay.classList.remove('active');
      hamburgerBtn.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }

    hamburgerBtn.addEventListener('click', openNav);
    mobileNavClose.addEventListener('click', closeNav);
    overlay.addEventListener('click', closeNav);

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && mobileNav.classList.contains('active')) {
        closeNav();
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMobileNav);
  } else {
    initMobileNav();
  }
})();
</script>