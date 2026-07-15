<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title>SeneBI — Parcelles</title>
  <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/parcelles.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/visual-harmony.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/region-filter.css') }}" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  </head>
  <body data-page="parcels">
    <div class="app">
     @include('header-client')  

      <main class="container">
        <div class="page-hero senebi-page-transition">
          <div class="hero-header">
            <h1 class="hero-title">Gestion des Parcelles & Récoltes</h1>
            <p class="hero-subtitle">Suivi de la production et des rendements</p>
          </div>

          <div class="hero-dashboard">
            <div class="weather-advice" id="weatherAdvice">
              <div class="icon-box-sm icon-box green" style="display: inline-flex; margin-right: 8px; vertical-align: middle;">
                <i class="fas fa-lightbulb"></i>
              </div>
              <span>Conseil du jour : C'est le moment idéal pour l'arrosage de la Parcelle Nord.</span>
            </div>

            <div class="hero-actions">
              <div class="region-selector">
                <label for="regionSelectParcel" class="region-label">Région</label>
                <select id="regionSelectParcel" class="region-dropdown">
                  <option value="all">Toutes les régions</option>
                  <option value="bko">Bamako</option>
                  <option value="kay">Kayes</option>
                  <option value="kou">Koulikoro</option>
                  <option value="seg">Ségou</option>
                  <option value="sik">Sikasso</option>
                  <option value="mop">Mopti</option>
                  <option value="tom">Tombouctou</option>
                  <option value="gao">Gao</option>
                  <option value="kid">Kidal</option>
                </select>
              </div>

              <!-- Widget Météo Dynamique -->
              <div class="weather-widget" id="weatherWidget">
                <div class="weather-content">
                  <div class="icon-box-sm icon-box amber" style="margin-right: 8px;">
                    <i class="fas fa-sun"></i>
                  </div>
                  <span class="weather-text">Météo Bamako : 34°C - Ensoleillé</span>
                </div>
              </div>

              <button class="action-btn" id="openHarvestBtn" type="button">
                <i class="fas fa-calculator" aria-hidden="true"></i>
                <span>Saisir une récolte</span>
              </button>
            </div>
          </div>
        </div>

        <section class="card" id="addParcelPanel" style="margin-bottom: 24px;">
          <h2 class="panel-title" style="margin-bottom: 16px;">Ajouter une parcelle</h2>
          <form class="panel-form" id="addParcelForm">
            <div class="panel-grid-2">
              <div class="panel-field">
                <label for="parcelNom">Nom</label>
                <input id="parcelNom" type="text" placeholder="Ex: Parcelle Nord" required />
              </div>
              <div class="panel-field">
                <label for="parcelRegion">Région</label>
                <input id="parcelRegion" type="text" placeholder="Ex: Kayes" required />
              </div>
              <div class="panel-field">
                <label for="parcelSurface">Surface (ha)</label>
                <input id="parcelSurface" type="number" min="0.1" step="0.1" placeholder="Ex: 4.5" required />
              </div>
              <div class="panel-field">
                <label for="parcelCulture">Culture</label>
                <select id="parcelCulture" required>
                  <option value="">Sélectionner une culture</option>
                  <option value="Riz">Riz</option>
                  <option value="Maïs">Maïs</option>
                  <option value="Coton">Coton</option>
                </select>
              </div>
              <div class="panel-field">
                <label for="parcelPlantingDate">Date de semis</label>
                <input id="parcelPlantingDate" type="date" />
              </div>
            </div>
            <div class="panel-actions">
              <button class="btn-primary" type="submit">Enregistrer la parcelle</button>
            </div>
          </form>
        </section>

        <section class="card harvest-panel" id="harvestPanel" aria-hidden="true">
          <h2 class="panel-title">Nouvelle Récolte</h2>

          <form class="panel-form" id="harvestForm">
              <div class="panel-field">
                <label for="parcelle-recoltee">Parcelle</label>
                <select id="parcelle-recoltee" required>
                  <option value="">Sélectionner une parcelle</option>
                </select>
              </div>

            <div class="panel-grid-2">
              <div class="panel-field">
                <label for="date-recolte">Date de récolte</label>
                <div class="input-icon">
                  <input id="date-recolte" type="date" required />
                  <span class="icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="4" width="18" height="18" rx="4"></rect>
                      <path d="M16 2v4M8 2v4"></path>
                      <path d="M3 10h18"></path>
                    </svg>
                  </span>
                </div>
              </div>
              <div class="panel-field">
                <label for="quantite-recoltee">Poids récolté (kg)</label>
                <input id="quantite-recoltee" type="number" min="1" step="1" placeholder="Ex: 15000" required />
              </div>
            </div>

            <div class="panel-actions">
              <button class="btn-primary" type="submit">Enregistrer</button>
              <button class="btn-ghost" id="cancelHarvestBtn" type="button">Annuler</button>
            </div>
          </form>
        </section>

        <section class="parcels-list" id="parcelsList"></section>
      </main>
      @include('partials.footer-client')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
      window.SeneBI_SERVER = {
        useDb: true,
        csrf: @json(csrf_token()),
        apiBase: @json(url('/client/api')),
        parcelles: @json($parcelles),
        parcelleStats: @json($parcelleStats ?? collect()),
        user: @json(Auth::user() ? ['id' => Auth::user()->id, 'name' => Auth::user()->name] : null),
      };
    </script>
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <script src="{{ asset('assets/js/core.js') }}"></script>
    <script src="{{ asset('assets/js/parcelles-db.js') }}"></script>
    <script src="{{ asset('assets/js/parcelles.js') }}"></script>
    <script src="{{ asset('assets/js/region-filter.js') }}"></script>
    <script src="{{ asset('assets/js/region-rentabilite.js') }}"></script>
    <script src="{{ asset('assets/js/region-parcelles-fix.js') }}"></script>
    
    <!-- Script pour sauvegarder les récoltes dans le localStorage partagé -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // FONCTION DE SAUVEGARDE INTELLIGENTE MULTI-CLIENTS
        function saveHarvestToSharedStorage(harvestData) {
          try {
            let existingHarvests = JSON.parse(localStorage.getItem('total_recolte_senebi') || '[]');
            const newHarvest = {
              ...harvestData,
              timestamp: new Date().toISOString(),
              saison: '2026',
              id: Date.now()
            };
            existingHarvests.push(newHarvest);
            localStorage.setItem('total_recolte_senebi', JSON.stringify(existingHarvests));
            const totalsByClient = {};
            existingHarvests.forEach(harvest => {
              const client = harvest.client || 'Inconnu';
              totalsByClient[client] = (totalsByClient[client] || 0) + parseFloat(harvest.quantite || 0);
            });
            localStorage.setItem('totaux_par_client', JSON.stringify(totalsByClient));
            const totalQuantity = existingHarvests.reduce((sum, harvest) => sum + parseFloat(harvest.quantite || 0), 0);
            localStorage.setItem('total_quantite_recoltee', totalQuantity.toString());
            console.log('Récolte sauvegardée pour le client:', harvestData.client);
            return true;
          } catch (error) {
            console.error('Erreur lors de la sauvegarde de la récolte:', error);
            return false;
          }
        }

        async function saveHarvestToDatabase(harvestData) {
          try {
            const selectEl = document.getElementById('parcelle-recoltee');
            const parcelleId = selectEl ? selectEl.value : null;
            if (!parcelleId) {
              console.warn('ID de parcelle manquant, impossible de sauvegarder en base.');
              return false;
            }
            const response = await fetch(`${window.SeneBI_SERVER.apiBase}/recoltes`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.SeneBI_SERVER.csrf || '',
              },
              body: JSON.stringify({
                parcelle_id: parseInt(parcelleId),
                date: harvestData.date,
                quantite: harvestData.quantite,
                culture: harvestData.culture,
              }),
            });
            if (!response.ok) {
              const text = await response.text();
              console.error('Erreur API recolte:', response.status, text);
              return false;
            }
            const result = await response.json();
            console.log('Récolte sauvegardée en base:', result);
            return true;
          } catch (error) {
            console.error('Erreur lors de la sauvegarde en base:', error);
            return false;
          }
        }
        
        // INTERCEPTER LA SOUMISSION DU FORMULAIRE DE RÉCOLTE
        const harvestForm = document.getElementById('harvestForm');
        if (harvestForm) {
          harvestForm.addEventListener('submit', async function(e) {
            e.preventDefault(); // Empêcher la soumission normale

            const parcelleSelect = document.getElementById('parcelle-recoltee');
            const parcelleId = parcelleSelect ? parcelleSelect.value : '';
            const date = document.getElementById('date-recolte')?.value || '';
            const quantite = document.getElementById('quantite-recoltee')?.value || '';

            // Validation (sans le champ "Nom du client" : l'utilisateur est déjà authentifié)
            if (!parcelleId || !date || !quantite || quantite <= 0) {
              alert('Veuillez remplir tous les champs correctement.');
              return;
            }

            // Récupérer les informations réelles de la parcelle depuis la base (SeneBI_SERVER.parcelles)
            const parcelleData = (window.SeneBI_SERVER?.parcelles || []).find(p => String(p.id) === String(parcelleId)) || {};
            const surface = parseFloat(parcelleData.surface || 0);
            const rendement = surface > 0 ? (parseFloat(quantite) / surface) : 0;

            // Créer l'objet de données de récolte (lié à l'agriculteur connecté + à la parcelle)
            const harvestData = {
              client: window.SeneBI_SERVER?.user?.name || '',
              parcelle: parcelleId,
              date: date,
              quantite: parseFloat(quantite),
              culture: parcelleData.culture || '',
              surface: surface,
              rendementEstime: rendement,
            };

            // Sauvegarder dans le localStorage partagé (pour compatibilité)
            const successLocal = saveHarvestToSharedStorage(harvestData);

            // Sauvegarder dans la base de données via API (lié à la parcelle + utilisateur connecté)
            const apiSuccess = await saveHarvestToDatabase(harvestData);

            if (apiSuccess || successLocal) {
              showSuccessMessage('Récolte enregistrée avec succès ! Statistiques mises à jour.');

              // Réinitialiser le formulaire
              harvestForm.reset();

              // Fermer le panneau de récolte
              const harvestPanel = document.getElementById('harvestPanel');
              if (harvestPanel) {
                harvestPanel.setAttribute('aria-hidden', 'true');
                harvestPanel.classList.remove('show');
                harvestPanel.style.display = 'none';
              }

              // Rafraîchir les parcelles et leurs statistiques (quantité, rendement, production, rentabilité)
              if (typeof window.SeneBI_refreshParcelles === 'function') {
                window.SeneBI_refreshParcelles();
              }
            } else {
              alert("Erreur lors de l'enregistrement de la récolte. Veuillez réessayer.");
            }
          });
        }

        // FONCTION HELPER : Obtenir les données réelles d'une parcelle par son id
        function getParcelleDataById(parcelleId) {
          const parcelles = window.SeneBI_SERVER?.parcelles || [];
          return parcelles.find(p => String(p.id) === String(parcelleId)) || null;
        }

        // FONCTION D'AFFICHAGE DU MESSAGE DE SUCCÈS
        function showSuccessMessage(message) {
          // Créer l'élément de message
          const successDiv = document.createElement('div');
          successDiv.textContent = message;
          successDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            z-index: 1002;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            max-width: 400px;
            line-height: 1.4;
          `;
          
          document.body.appendChild(successDiv);
          
          // Animation d'apparition
          setTimeout(() => {
            successDiv.style.opacity = '1';
            successDiv.style.transform = 'translateY(0)';
          }, 100);
          
          // Masquer après 4 secondes
          setTimeout(() => {
            successDiv.style.opacity = '0';
            successDiv.style.transform = 'translateY(-20px)';
            setTimeout(() => {
              if (document.body.contains(successDiv)) {
                document.body.removeChild(successDiv);
              }
            }, 300);
          }, 4000);
        }
        
        console.log('🔄 Script de sauvegarde des récoltes client chargé avec succès');
      });
    </script>
  </body>
</html>
