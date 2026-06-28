<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SeneBI - Espace client</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/visual-harmony.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
      /* Force les styles pour les KPIs du client */
      #kpiTotalHarvest, #kpiCA, #kpiHa, #kpiRend {
        font-weight: bold !important;
        color: inherit !important;
      }
      .kpi-sub span:first-child {
        color: #10b981 !important;
        font-weight: 600 !important;
      }
      
      /* Styles pour l'impression */
      @media print {
        .head-actions {
          display: none !important;
        }
        
        .modal-overlay {
          display: none !important;
        }
        
        .footer-note {
          display: none !important;
        }
        
        body {
          background: white !important;
        }
        
        .card {
          box-shadow: none !important;
          border: 1px solid #000 !important;
          break-inside: avoid;
        }
        
        .kpi-value {
          color: #000 !important;
        }
        
        .kpi-sub span {
          color: #000 !important;
        }
      }
    </style>
  </head>
  <body data-page="client-dashboard">
    <div class="app">
      @include('header-client')

      <main class="container">
        <div class="page-title senebi-page-transition">
          <div>
            <h1>Tableau de Bord Client</h1>
            <p>Suivi de vos performances agricoles et indicateurs clés.</p>
          </div>
          <div class="head-actions" style="display: flex; gap: 10px; align-items: center;">
            <button class="btn" id="clientExportBtn" type="button" style="background: #111827; color: white; border: none; border-radius: 15px; padding: 12px 20px; font-weight: 600; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#1a1a1a'" onmouseout="this.style.backgroundColor='#111827'">Exporter le Rapport PDF</button>
          </div>
        </div>

        <div id="stockAlert" class="alert-banner">
          <div id="stockAlertText">Alerte Stock</div>
          <a class="btn danger" href="{{ route('manager.stocks') }}">Voir le stock</a>
        </div>

        <section class="grid kpis">
          <article class="card" style="cursor: pointer;" onclick="window.location.href='{{ route('client.parcelles') }}'">
            <div class="card-header">
              <p class="card-title">Total Récolté</p>
              <div class="card-icon" aria-hidden="true">
                <i class="fas fa-seedling"></i>
              </div>
            </div>
            <div class="kpi-value">
              <span id="kpiTotalHarvest" style="font-weight: bold !important;">{{ number_format($totalRecolteQte, 0, ',', ' ') }}</span>
              <span class="muted" style="font-size:14px;font-weight:700;">kg</span>
              @php $indicatorClass = $rendementEvolution >= 0 ? 'up' : 'down'; @endphp
              <span class="kpi-indicator {{ $indicatorClass }}">
                <i class="fas fa-arrow-{{ $rendementEvolution >= 0 ? 'up' : 'down' }}"></i>
                {{ number_format(abs($rendementEvolution), 1) }}%
              </span>
            </div>
             <div class="kpi-sub">
               <span style="color: #10b981 !important; font-weight: 600 !important;">Quantité totale récoltée</span>
               <span class="muted">Toutes parcelles confondues</span>
             </div>
          </article>

          <article class="card">
            <div class="card-header">
              <p class="card-title">Chiffre d'Affaires estimé</p>
              <div class="card-icon" aria-hidden="true">
                <i class="fas fa-chart-line"></i>
              </div>
            </div>
            <div class="kpi-value">
              <span id="kpiCA" style="font-weight: bold !important;">{{ number_format($totalCA, 0, ',', ' ') }}</span>
              <span class="muted" style="font-size:14px;font-weight:700;">FCFA</span>
              @php $caIndicatorClass = $caEvolution >= 0 ? 'up' : 'down'; @endphp
              <span class="kpi-indicator {{ $caIndicatorClass }}">
                <i class="fas fa-arrow-{{ $caEvolution >= 0 ? 'up' : 'down' }}"></i>
                {{ number_format(abs($caEvolution), 1) }}%
              </span>
            </div>
            <div class="kpi-sub">
              <span style="color: #10b981 !important; font-weight: 600 !important;">vs mois précédent</span>
              <span class="muted">Prix.unit × quantité récoltée</span>
            </div>
          </article>

          <article class="card">
            <div class="card-header">
              <p class="card-title">Hectares Actifs</p>
              <div class="card-icon" aria-hidden="true">
                <i class="fas fa-map"></i>
              </div>
            </div>
            <div class="kpi-value">
              <span id="kpiHa" style="font-weight: bold !important;">{{ number_format($hectaresActifs, 2, ',', ' ') }}</span>
              <span class="muted" style="font-size:14px;font-weight:700;">ha</span>
              @php $haIndicatorClass = $haEvolution >= 0 ? 'up' : 'down'; @endphp
              <span class="kpi-indicator {{ $haIndicatorClass }}">
                <i class="fas fa-arrow-{{ $haEvolution >= 0 ? 'up' : 'down' }}"></i>
                {{ number_format(abs($haEvolution), 1) }}%
              </span>
            </div>
            <div class="kpi-sub">
              <span style="color: #10b981 !important; font-weight: 600 !important;">Surface totale cultivée</span>
              <span class="muted">Parcelles enregistrées</span>
            </div>
          </article>

          
          <article class="card">
            <div class="card-header">
              <p class="card-title">Rendement Moyen</p>
              <div class="card-icon" aria-hidden="true">
                <i class="fas fa-chart-line"></i>
              </div>
            </div>
            <div class="kpi-value">
              <span id="kpiRend" style="font-weight: bold !important;">{{ number_format($rendementMoyen, 2, ',', ' ') }}</span>
              <span class="muted" style="font-size:14px;font-weight:700;">t/ha</span>
              @php $rendIndicatorClass = $rendementEvolution >= 0 ? 'up' : 'down'; @endphp
              <span class="kpi-indicator {{ $rendIndicatorClass }}">
                <i class="fas fa-arrow-{{ $rendementEvolution >= 0 ? 'up' : 'down' }}"></i>
                {{ number_format(abs($rendementEvolution), 1) }}%
              </span>
            </div>
            <div class="kpi-sub">
              <span style="color: #10b981 !important; font-weight: 600 !important;">Moyenne toutes cultures</span>
              <span class="muted">Quantité / surface récoltée</span>
            </div>
          </article>
        </section>

<section class="grid cards-1" style="margin-top: 28px; margin-bottom: 24px;">
  <article class="card">
    <div class="card-header">
      <div>
        <h3 class="card-title">Météo Agricole</h3>
        <div class="small muted">{{ auth()->user()->location ?? 'Sénégal' }}</div>
      </div>
      <span class="tag muted">Mise à jour: {{ now()->format('H:i') }}</span>
    </div>

    <div class="grid kpis" style="grid-template-columns: repeat(4, minmax(0, 1fr)); margin-top: 0;">
      <article class="card" style="background: #f8fafc; border: 1px solid rgba(15,23,42,0.06);">
        <div class="card-header">
          <p class="card-title">Température</p>
          <div class="icon-box icon-box-sm red" aria-hidden="true">
            <i class="fas fa-temperature-high"></i>
          </div>
        </div>
        <div class="kpi-value" style="font-size: 28px; color: #111827;">
          {{ $meteoData['temperature'] ?? 32 }}°C
        </div>
        <div class="kpi-sub"><span class="muted">Max aujourd'hui</span></div>
      </article>

      <article class="card" style="background: #f8fafc; border: 1px solid rgba(15,23,42,0.06);">
        <div class="card-header">
          <p class="card-title">Humidité</p>
          <div class="icon-box icon-box-sm blue" aria-hidden="true">
            <i class="fas fa-droplet"></i>
          </div>
        </div>
        <div class="kpi-value" style="font-size: 28px; color: #111827;">
          {{ $meteoData['humidity'] ?? 65 }}%
        </div>
        <div class="kpi-sub"><span class="muted">Taux d'humidité</span></div>
      </article>

      <article class="card" style="background: #f8fafc; border: 1px solid rgba(15,23,42,0.06);">
        <div class="card-header">
          <p class="card-title">Pluie</p>
          <div class="icon-box icon-box-sm indigo" aria-hidden="true">
            <i class="fas fa-cloud-rain"></i>
          </div>
        </div>
        <div class="kpi-value" style="font-size: 28px; color: #111827;">
          {{ $meteoData['rainProb'] ?? 20 }}%
        </div>
        <div class="kpi-sub"><span class="muted">Probabilité</span></div>
      </article>

      <article class="card" style="background: #f8fafc; border: 1px solid rgba(15,23,42,0.06);">
        <div class="card-header">
          <p class="card-title">Vent</p>
          <div class="icon-box icon-box-sm green" aria-hidden="true">
            <i class="fas fa-wind"></i>
          </div>
        </div>
        <div class="kpi-value" style="font-size: 28px; color: #111827;">
          {{ $meteoData['windSpeed'] ?? 12 }} km/h
        </div>
        <div class="kpi-sub"><span class="muted">Vitesse moyenne</span></div>
      </article>
    </div>

    <div class="grid cards-3" style="grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 10px; margin-top: 14px;">
      @foreach($meteoData['forecast'] ?? [] as $day)
      <article class="card" style="background: #f9fafb; border: 1px solid rgba(15,23,42,0.06); text-align: center; padding: 12px;">
        <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 6px;">{{ $day['day'] }}</div>
        <div class="icon-box icon-box-sm" style="margin: 0 auto 6px; color: var(--accent);">
          <i class="fas fa-{{ $day['icon'] === 'sun' ? 'sun' : ($day['icon'] === 'cloud-sun' ? 'cloud-sun' : ($day['icon'] === 'cloud-rain' ? 'cloud-rain' : 'cloud')) }}"></i>
        </div>
        <div style="font-size: 16px; font-weight: 700; color: #111827;">{{ $day['temp'] }}°</div>
      </article>
      @endforeach
    </div>

    @if(!empty($meteoData['alerts']))
    <div class="dashboard-insight" style="margin-top: 14px;">
      <div class="icon-box-sm icon-box amber" style="display: inline-flex; margin-right: 8px; vertical-align: middle;">
        <i class="fas fa-triangle-exclamation"></i>
      </div>
      <span>{{ $meteoData['alerts'][0]['message'] ?? 'Conditions météo favorables' }}</span>
    </div>
    @endif
  </article>
</section>

        <div class="secondary-kpis" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; margin-bottom: 24px;">
          <div class="mini-kpi" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; text-align: center;">
            <div style="font-size: 20px; font-weight: 700; color: #111827;">{{ $parcellesActives ?? 0 }}</div>
            <div style="font-size: 11px; color: #6b7280;">Parcelles actives</div>
          </div>
          <div class="mini-kpi" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; text-align: center;">
            <div style="font-size: 20px; font-weight: 700; color: #ef4444;">{{ $intrantsCritiques ?? 0 }}</div>
            <div style="font-size: 11px; color: #6b7280;">Intrants critiques</div>
          </div>
          <div class="mini-kpi" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; text-align: center;">
            <div style="font-size: 20px; font-weight: 700; color: #10b981;">{{ $visitesRealisees ?? 0 }}</div>
            <div style="font-size: 11px; color: #6b7280;">Visites réalisées</div>
          </div>
          <div class="mini-kpi" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; text-align: center;">
            <div style="font-size: 20px; font-weight: 700; color: #8b5cf6;">{{ $culturesExploitees ?? 0 }}</div>
            <div style="font-size: 11px; color: #6b7280;">Cultures exploitées</div>
          </div>
        </div>

        <section class="grid cards-2" style="margin-bottom: 24px;">
          <article class="card" style="min-height: 280px;">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">Prévision de Production</h3>
                <div class="small muted">Estimation saisonnière</div>
              </div>
              <span class="tag {{ $productionTendance === 'hausse' ? 'success' : ($productionTendance === 'baisse' ? 'danger' : 'muted') }}">
                {{ $productionTendance === 'hausse' ? 'En hausse' : ($productionTendance === 'baisse' ? 'En baisse' : 'Stable') }}
              </span>
            </div>
            <div style="padding: 16px;">
              <div style="text-align: center; margin-bottom: 20px;">
                <div style="font-size: 36px; font-weight: 700; color: #111827;">
                  {{ number_format($productionEstimee, 0, ',', ' ') }}
                  <span style="font-size: 18px; color: #6b7280;">kg</span>
                </div>
                <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Production estimée</div>
              </div>
              <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                  <div style="height: 100%; width: {{ $productionConfiance ?? 70 }}%; background: linear-gradient(90deg, #10b981, #3b82f6);"></div>
                </div>
                <span style="font-size: 13px; font-weight: 600; color: #111827;">{{ $productionConfiance ?? 70 }}%</span>
              </div>
              <div style="font-size: 12px; color: #6b7280;">
                <i class="fas fa-info-circle" style="color: #3b82f6; margin-right: 4px;"></i>
                Estimation basée sur les données historiques et les tendances actuelles
              </div>
            </div>
          </article>

          <article class="card" style="min-height: 280px;">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">Objectifs de la Saison</h3>
                <div class="small muted">Suivi de vos cibles</div>
              </div>
              <span class="tag muted">2026</span>
            </div>
            <div style="padding: 16px;">
              <div style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                  <span style="font-size: 13px; color: #374151;">Production</span>
                  <span style="font-size: 13px; color: #6b7280;">{{ number_format($totalRecolteQte, 0, ',', ' ') }}/{{ number_format($objectif->objectif_production ?? 0, 0, ',', ' ') }} kg</span>
                </div>
                <div style="height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden;">
                  <div style="height: 100%; width: {{ $objectif && $objectif->objectif_production > 0 ? min(100, ($totalRecolteQte / $objectif->objectif_production) * 100) : 0 }}%; background: #10b981;"></div>
                </div>
              </div>
              <div style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                  <span style="font-size: 13px; color: #374151;">Chiffre d'Affaires</span>
                  <span style="font-size: 13px; color: #6b7280;">{{ number_format($totalCA, 0, ',', ' ') }}/{{ number_format($objectif->objectif_ca ?? 0, 0, ',', ' ') }} FCFA</span>
                </div>
                <div style="height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden;">
                  <div style="height: 100%; width: {{ $objectif && $objectif->objectif_ca > 0 ? min(100, ($totalCA / $objectif->objectif_ca) * 100) : 0 }}%; background: #3b82f6;"></div>
                </div>
              </div>
              <div style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                  <span style="font-size: 13px; color: #374151;">Surface</span>
                  <span style="font-size: 13px; color: #6b7280;">{{ number_format($hectaresActifs, 2, ',', ' ') }}/{{ number_format($objectif->objectif_surface ?? 0, 2, ',', ' ') }} ha</span>
                </div>
                <div style="height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden;">
                  <div style="height: 100%; width: {{ $objectif && $objectif->objectif_surface > 0 ? min(100, ($hectaresActifs / $objectif->objectif_surface) * 100) : 0 }}%; background: #8b5cf6;"></div>
                </div>
              </div>
              <button id="openObjectifsModal" type="button" style="width: 100%; padding: 8px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13px; color: #374151; cursor: pointer;">
                <i class="fas fa-edit" style="margin-right: 6px;"></i>Définir les objectifs
              </button>
            </div>
          </article>
        </section>

        <p id="dashboardInsight" class="dashboard-insight" role="status"></p>

        <section class="grid cards-2">
          <article class="card" style="min-height: 320px;">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">Évolution du Prix des Céréales</h3>
                <div class="small muted">Courbe dynamique (Chart.js)</div>
              </div>
              <span class="tag muted">FCFA/kg</span>
            </div>
            <div class="cereal-price-toolbar">
              <label class="cereal-price-label" for="cerealPriceSelect">Culture affichée</label>
              <select id="cerealPriceSelect" class="cereal-price-select" aria-label="Choisir la culture pour la courbe de prix">
                <option value="Riz">Riz</option>
                <option value="Maïs">Maïs</option>
                <option value="Coton">Coton</option>
              </select>
            </div>
            <div style="height: 260px;">
              <canvas id="priceChart"></canvas>
            </div>
          </article>

          <article class="card" style="min-height: 320px;">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">Distribution des Cultures</h3>
                <div class="small muted">Riz / Maïs / Coton</div>
              </div>
            </div>
<div style="height: 260px;">
               <canvas id="cultureChart"></canvas>
             </div>
          </article>

        </section>

        <section class="grid cards-2">
          <article class="card" style="min-height: 320px;">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">Conseils SeneBI</h3>
                <div class="small muted">Recommandations personnalisees</div>
              </div>
              <span class="tag muted">IA</span>
            </div>
            <div style="padding: 16px;">
              @if(!empty($recommendations) && count($recommendations) > 0)
                @foreach($recommendations as $rec)
                  <div style="display: flex; gap: 10px; align-items: flex-start; margin-bottom: 12px; padding: 10px; background: #f9fafb; border-radius: 8px; border-left: 3px solid #10b981;">
                    <i class="fas fa-lightbulb" style="color: #10b981; margin-top: 2px;"></i>
                    <span style="font-size: 14px; color: #374151; line-height: 1.5;">{{ $rec }}</span>
                  </div>
                @endforeach
              @else
                <div style="text-align: center; color: #9ca3af; padding: 20px; font-size: 14px;">
                  <i class="fas fa-check-circle" style="font-size: 32px; color: #10b981; margin-bottom: 8px; display: block;"></i>
                  Aucun conseil particulier. Votre exploitation est en bon etat.
                </div>
              @endif
            </div>
          </article>

          <article class="card" style="min-height: 320px;">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">Activites Recentes</h3>
                <div class="small muted">Dernieres actions enregistrees</div>
              </div>
              <span class="tag muted">Historique</span>
            </div>
            <div style="padding: 16px;">
              @php
                $recentActivities = \App\Models\Notification::where('user_id', auth()->id())
                  ->orderByDesc('created_at')
                  ->limit(8)
                  ->get();
              @endphp
              @if($recentActivities->count() > 0)
                @foreach($recentActivities as $activity)
                  <div style="display: flex; gap: 10px; align-items: flex-start; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #f3f4f6;">
                    <div style="width: 8px; height: 8px; border-radius: 50%; background: {{ $activity->level === 'danger' ? '#ef4444' : ($activity->level === 'warning' ? '#f59e0b' : '#10b981') }}; margin-top: 8px; flex-shrink: 0;"></div>
                    <div style="flex: 1; min-width: 0;">
                      <div style="font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 2px;">{{ $activity->title }}</div>
                      <div style="font-size: 12px; color: #6b7280; line-height: 1.4;">{{ $activity->message }}</div>
                      <div style="font-size: 11px; color: #9ca3af; margin-top: 2px;">{{ $activity->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                  </div>
                @endforeach
              @else
                <div style="text-align: center; color: #9ca3af; padding: 20px; font-size: 14px;">
                  Aucune activite recente enregistree.
                </div>
              @endif
            </div>
          </article>
        </section>

        @if(!empty($additionalAlerts))
          <div style="margin-top: 24px; display: flex; flex-wrap: wrap; gap: 12px;">
            @foreach($additionalAlerts as $alert)
              <div class="alert-card" style="flex: 1; min-width: 280px; background: {{ $alert['type'] === 'danger' ? '#fef2f2' : ($alert['type'] === 'warning' ? '#fffbeb' : '#eff6ff') }}; border-left: 3px solid {{ $alert['type'] === 'danger' ? '#ef4444' : ($alert['type'] === 'warning' ? '#f59e0b' : '#3b82f6') }}; border-radius: 6px; padding: 12px;">
                <div style="display: flex; gap: 10px; align-items: flex-start;">
                  <i class="fas fa-{{ $alert['icon'] ?? 'info-circle' }}" style="color: {{ $alert['type'] === 'danger' ? '#ef4444' : ($alert['type'] === 'warning' ? '#f59e0b' : '#3b82f6') }}; margin-top: 2px;"></i>
                  <div>
                    <div style="font-weight: 600; color: #111827; font-size: 13px;">{{ $alert['title'] ?? 'Information' }}</div>
                    <div style="font-size: 12px; color: #4b5563;">{{ $alert['message'] }}</div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif

        <div style="margin-top: 24px; background: white; border-radius: 8px; padding: 20px; border: 1px solid #e5e7eb;">
          <h3 style="margin: 0 0 12px 0; font-size: 16px; color: #111827;">
            <i class="fas fa-chart-bar" style="color: #8b5cf6; margin-right: 8px;"></i>
            Comparaison Régionale
          </h3>
          <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
            @if($ecartRegional != 0)
              <div style="flex: 1; min-width: 200px;">
                <div style="font-size: 14px; color: #6b7280; margin-bottom: 4px;">Rendement vs région ({{ $region }})</div>
                <div style="font-size: 24px; font-weight: 700; color: {{ $ecartRegional > 0 ? '#10b981' : '#ef4444' }};">
                  {{ $ecartRegional > 0 ? '+' : '' }}{{ number_format($ecartRegional, 1) }}%
                </div>
                <div style="font-size: 12px; color: #6b7280;">
                  {{ $ecartRegional > 0 ? 'Vous êtes au-dessus' : 'En dessous' }} de la moyenne régionale
                </div>
              </div>
            @else
              <div style="flex: 1; min-width: 200px;">
                <div style="font-size: 14px; color: #6b7280; margin-bottom: 4px;">Rendement vs région ({{ $region }})</div>
                <div style="font-size: 18px; color: #6b7280;">Aucune donnée régionale disponible</div>
              </div>
            @endif
            <div style="flex: 1; min-width: 200px; font-size: 13px; color: #374151; line-height: 1.5;">
              @if($ecartRegional > 10)
                <i class="fas fa-trophy" style="color: #f59e0b;"></i>
                Vous faites partie des meilleures exploitations de votre région !
              @elseif($ecartRegional > 0)
                <i class="fas fa-thumbs-up" style="color: #10b981;"></i>
                Bonne performance, continuez sur cette voie.
              @elseif($ecartRegional < -10)
                <i class="fas fa-exclamation-circle" style="color: #f59e0b;"></i>
                Travaillez vos pratiques pour améliorer votre rendement.
              @else
                <i class="fas fa-seedling" style="color: #3b82f6;"></i>
                Continuez à enrichir vos données pour une comparaison précise.
              @endif
            </div>
          </div>
        </div>
      </main>
    @include('partials.footer-client')
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <script src="{{ asset('assets/js/core.js') }}"></script>
<script>
       window.SeneBI_DASHBOARD = {
         totalHarvestKg: {{ $totalRecolteQte }},
         totalCA: {{ $totalCA }},
         hectaresActifs: {{ $hectaresActifs }},
         rendementMoyen: {{ number_format($rendementMoyen, 4, '.', '') }},
         caEvolution: {{ number_format($caEvolution, 2, '.', '') }},
         haEvolution: {{ number_format($haEvolution, 2, '.', '') }},
         rendementEvolution: {{ number_format($rendementEvolution, 2, '.', '') }},
         prixCultures: @json($prixCultures),
         culturesLabels: @json($culturesLabels),
         culturesData: @json($culturesData),
         stockCritical: @json($stockCritical),
         objectifProduction: {{ $objectif->objectif_production ?? 0 }},
         objectifCA: {{ $objectif->objectif_ca ?? 0 }},
         objectifSurface: {{ $objectif->objectif_surface ?? 0 }},
         productionEstimee: {{ $productionEstimee }},
         productionTendance: @json($productionTendance),
         productionConfiance: {{ $productionConfiance }},
         ecartRegional: {{ number_format($ecartRegional, 2, '.', '') }},
         region: @json($region),
         additionalAlerts: @json($additionalAlerts ?? []),
       };
    </script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    
    
    <!-- Script pour forcer les styles avec MutationObserver -->
    <script>
      console.log("🚀 Script KPI Styles chargé !");
      
      function applyKPIStyles() {
        console.log("⚡ Application des styles KPI...");
        
        // Forcer les styles des chiffres en gras
        const kpiElements = ['#kpiTotalHarvest', '#kpiCA', '#kpiHa', '#kpiRend'];
        kpiElements.forEach(id => {
          const el = document.querySelector(id);
          if (el) {
            el.style.cssText = 'font-weight: bold !important; color: inherit !important;';
            console.log(`✅ Style gras FORCÉ sur ${id}: ${el.textContent}`);
          } else {
            console.log(`❌ Élément ${id} non trouvé`);
          }
        });
        
        // Forcer les styles des badges verts
        const kpiSubElements = document.querySelectorAll('.kpi-sub span:first-child');
        kpiSubElements.forEach((el, index) => {
          el.style.cssText = 'color: #10b981 !important; font-weight: 600 !important;';
          console.log(`✅ Style vert FORCÉ sur badge ${index + 1}: ${el.textContent}`);
        });
        
        console.log("🎯 Styles KPI appliqués avec succès !");
      }
      
      // Attendre que la page soit complètement chargée
      window.addEventListener('load', function() {
        console.log("📄 Page complètement chargée !");
        setTimeout(applyKPIStyles, 100);
      });
      
      // Surveiller les changements sur les KPIs
      const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
          if (mutation.type === 'childList' || mutation.type === 'characterData') {
            const target = mutation.target;
            if (target.id && ['kpiTotalHarvest', 'kpiCA', 'kpiHa', 'kpiRend'].includes(target.id)) {
              console.log(`🔄 Changement détecté sur ${target.id}, réapplication des styles...`);
              setTimeout(applyKPIStyles, 50);
            }
          }
        });
      });
      
      // Démarrer l'observation après le chargement
      setTimeout(() => {
        const kpiContainer = document.querySelector('.kpis');
        if (kpiContainer) {
          observer.observe(kpiContainer, {
            childList: true,
            subtree: true,
            characterData: true
          });
          console.log("👁️ MutationObserver démarré sur les KPIs !");
        }
        
        // Application initiale
        applyKPIStyles();
      }, 200);
      
// Fonction d'export PDF avec window.print()
       const clientExportBtn = document.getElementById('clientExportBtn');
       clientExportBtn.addEventListener('click', function() {
         window.print();
       });

       // Animation Timeline pour les activités récentes
       document.addEventListener('DOMContentLoaded', function() {
         const timelineItems = document.querySelectorAll('.activity-timeline-item');
         timelineItems.forEach((item, index) => {
           item.style.opacity = '0';
           item.style.transform = 'translateY(10px)';
           setTimeout(() => {
             item.style.transition = 'all 0.3s ease';
             item.style.opacity = '1';
             item.style.transform = 'translateY(0)';
           }, index * 50);
         });
       });

       // Modal Objectifs
       const openObjectifsModal = document.getElementById('openObjectifsModal');
       if (openObjectifsModal) {
         openObjectifsModal.addEventListener('click', function() {
           const modal = document.getElementById('objectifsModal');
           if (modal) {
             modal.style.display = 'flex';
             modal.style.opacity = '0';
             setTimeout(() => modal.style.opacity = '1', 10);
           }
         });
       }
     </script>

     <!-- Modal Objectifs de Saison -->
     <div id="objectifsModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
       <div class="modal-content" style="background: white; border-radius: 12px; padding: 24px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;">
         <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
           <h3 style="margin: 0; font-size: 18px;">Objectifs de la Saison 2026</h3>
           <button id="closeObjectifsModal" type="button" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #6b7280;">
             <i class="fas fa-times"></i>
           </button>
         </div>
         <form id="objectifsForm" style="display: flex; flex-direction: column; gap: 16px;">
           <div>
             <label style="display: block; font-size: 13px; color: #374151; margin-bottom: 6px;">Production cible (kg)</label>
             <input type="number" name="objectif_production" id="objectifProduction" min="0" step="100" 
               value="{{ $objectif->objectif_production ?? '' }}" 
               style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px;" 
               placeholder="Ex: 50000" />
           </div>
           <div>
             <label style="display: block; font-size: 13px; color: #374151; margin-bottom: 6px;">Chiffre d'Affaires cible (FCFA)</label>
             <input type="number" name="objectif_ca" id="objectifCA" min="0" step="1000" 
               value="{{ $objectif->objectif_ca ?? '' }}" 
               style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px;" 
               placeholder="Ex: 2500000" />
           </div>
           <div>
             <label style="display: block; font-size: 13px; color: #374151; margin-bottom: 6px;">Surface cible (ha)</label>
             <input type="number" name="objectif_surface" id="objectifSurface" min="0" step="0.1" 
               value="{{ $objectif->objectif_surface ?? '' }}" 
               style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px;" 
               placeholder="Ex: 15" />
           </div>
           <div style="display: flex; gap: 12px; margin-top: 8px;">
             <button type="submit" class="btn-primary" style="flex: 1; padding: 12px; background: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600;">Enregistrer</button>
             <button type="button" id="cancelObjectifs" class="btn-ghost" style="flex: 1; padding: 12px; background: #f3f4f6; color: #374151; border: none; border-radius: 6px;">Annuler</button>
           </div>
         </form>
       </div>
     </div>

     <script>
       const closeObjectifsModal = document.getElementById('closeObjectifsModal');
       const cancelObjectifs = document.getElementById('cancelObjectifs');
       const objectifsModal = document.getElementById('objectifsModal');
       
       if (closeObjectifsModal) closeObjectifsModal.addEventListener('click', () => objectifsModal.style.display = 'none');
       if (cancelObjectifs) cancelObjectifs.addEventListener('click', () => objectifsModal.style.display = 'none');
       
       document.getElementById('objectifsForm').addEventListener('submit', function(e) {
         e.preventDefault();
         const data = {
           objectif_production: document.getElementById('objectifProduction').value,
           objectif_ca: document.getElementById('objectifCA').value,
           objectif_surface: document.getElementById('objectifSurface').value,
         };
         fetch('{{ route('client.api.objectifs') }}', {
           method: 'POST',
           headers: {
             'Content-Type': 'application/json',
             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
           },
           body: JSON.stringify(data)
         }).then(r => r.json()).then(() => {
           alert('Objectifs enregistrés avec succès !');
           location.reload();
         });
       });
     </script>
   </body>
</html>
