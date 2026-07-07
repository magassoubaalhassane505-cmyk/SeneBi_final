// SeneBI - Filtre par région du Mali - Module additionnel
(function() {
    'use strict';
    
    class RegionFilter {
        constructor() {
            this.currentRegion = 'all';
            this.regionData = this.initRegionData();
            this.init();
        }

        init() {
            this.bindEvents();
            this.updateDashboard();
        }

        initRegionData() {
            if (window.SeneBI_REGIONS_DATA) {
                return window.SeneBI_REGIONS_DATA;
            }
            // Simulation des données par région du Mali
            return {
                all: {
                    name: 'Toutes les régions',
                    totalHarvest: 245000,
                    ca: 14200000,
                    hectares: 18.8,
                    rendement: 13.0,
                    cultures: [
                        { name: 'Riz', percent: 44 },
                        { name: 'Maïs', percent: 33 },
                        { name: 'Coton', percent: 23 }
                    ],
                    prices: [
                        { month: 'Jan', price: 345 },
                        { month: 'Fév', price: 352 },
                        { month: 'Mar', price: 360 },
                        { month: 'Avr', price: 358 },
                        { month: 'Mai', price: 366 },
                        { month: 'Jun', price: 370 },
                        { month: 'Jul', price: 375 },
                        { month: 'Aoû', price: 378 },
                        { month: 'Sep', price: 382 },
                        { month: 'Oct', price: 385 },
                        { month: 'Nov', price: 388 },
                        { month: 'Déc', price: 392 }
                    ]
                },
                bko: {
                    name: 'Bamako',
                    totalHarvest: 28000,
                    ca: 1680000,
                    hectares: 2.1,
                    rendement: 13.3,
                    cultures: [
                        { name: 'Riz', percent: 48 },
                        { name: 'Maïs', percent: 35 },
                        { name: 'Coton', percent: 17 }
                    ],
                    prices: [
                        { month: 'Jan', price: 350 },
                        { month: 'Fév', price: 355 },
                        { month: 'Mar', price: 362 },
                        { month: 'Avr', price: 360 },
                        { month: 'Mai', price: 368 },
                        { month: 'Jun', price: 372 },
                        { month: 'Jul', price: 378 },
                        { month: 'Aoû', price: 381 },
                        { month: 'Sep', price: 385 },
                        { month: 'Oct', price: 388 },
                        { month: 'Nov', price: 392 },
                        { month: 'Déc', price: 395 }
                    ]
                },
                kay: {
                    name: 'Kayes',
                    totalHarvest: 42000,
                    ca: 2520000,
                    hectares: 3.5,
                    rendement: 12.0,
                    cultures: [
                        { name: 'Riz', percent: 35 },
                        { name: 'Maïs', percent: 40 },
                        { name: 'Coton', percent: 25 }
                    ],
                    prices: [
                        { month: 'Jan', price: 340 },
                        { month: 'Fév', price: 348 },
                        { month: 'Mar', price: 355 },
                        { month: 'Avr', price: 353 },
                        { month: 'Mai', price: 361 },
                        { month: 'Jun', price: 365 },
                        { month: 'Jul', price: 370 },
                        { month: 'Aoû', price: 373 },
                        { month: 'Sep', price: 377 },
                        { month: 'Oct', price: 380 },
                        { month: 'Nov', price: 384 },
                        { month: 'Déc', price: 388 }
                    ]
                },
                kou: {
                    name: 'Koulikoro',
                    totalHarvest: 38000,
                    ca: 2280000,
                    hectares: 2.8,
                    rendement: 13.6,
                    cultures: [
                        { name: 'Riz', percent: 50 },
                        { name: 'Maïs', percent: 30 },
                        { name: 'Coton', percent: 20 }
                    ],
                    prices: [
                        { month: 'Jan', price: 342 },
                        { month: 'Fév', price: 349 },
                        { month: 'Mar', price: 356 },
                        { month: 'Avr', price: 354 },
                        { month: 'Mai', price: 362 },
                        { month: 'Jun', price: 366 },
                        { month: 'Jul', price: 371 },
                        { month: 'Aoû', price: 374 },
                        { month: 'Sep', price: 378 },
                        { month: 'Oct', price: 381 },
                        { month: 'Nov', price: 385 },
                        { month: 'Déc', price: 389 }
                    ]
                },
                seg: {
                    name: 'Ségou',
                    totalHarvest: 45000,
                    ca: 2700000,
                    hectares: 3.2,
                    rendement: 14.1,
                    cultures: [
                        { name: 'Riz', percent: 52 },
                        { name: 'Maïs', percent: 28 },
                        { name: 'Coton', percent: 20 }
                    ],
                    prices: [
                        { month: 'Jan', price: 338 },
                        { month: 'Fév', price: 345 },
                        { month: 'Mar', price: 352 },
                        { month: 'Avr', price: 350 },
                        { month: 'Mai', price: 358 },
                        { month: 'Jun', price: 362 },
                        { month: 'Jul', price: 367 },
                        { month: 'Aoû', price: 370 },
                        { month: 'Sep', price: 374 },
                        { month: 'Oct', price: 377 },
                        { month: 'Nov', price: 381 },
                        { month: 'Déc', price: 385 }
                    ]
                },
                sik: {
                    name: 'Sikasso',
                    totalHarvest: 52000,
                    ca: 3120000,
                    hectares: 3.8,
                    rendement: 13.7,
                    cultures: [
                        { name: 'Riz', percent: 40 },
                        { name: 'Maïs', percent: 38 },
                        { name: 'Coton', percent: 22 }
                    ],
                    prices: [
                        { month: 'Jan', price: 344 },
                        { month: 'Fév', price: 351 },
                        { month: 'Mar', price: 358 },
                        { month: 'Avr', price: 356 },
                        { month: 'Mai', price: 364 },
                        { month: 'Jun', price: 368 },
                        { month: 'Jul', price: 373 },
                        { month: 'Aoû', price: 376 },
                        { month: 'Sep', price: 380 },
                        { month: 'Oct', price: 383 },
                        { month: 'Nov', price: 387 },
                        { month: 'Déc', price: 391 }
                    ]
                },
                mop: {
                    name: 'Mopti',
                    totalHarvest: 18000,
                    ca: 1080000,
                    hectares: 1.4,
                    rendement: 12.9,
                    cultures: [
                        { name: 'Riz', percent: 45 },
                        { name: 'Maïs', percent: 32 },
                        { name: 'Coton', percent: 23 }
                    ],
                    prices: [
                        { month: 'Jan', price: 346 },
                        { month: 'Fév', price: 353 },
                        { month: 'Mar', price: 360 },
                        { month: 'Avr', price: 358 },
                        { month: 'Mai', price: 366 },
                        { month: 'Jun', price: 370 },
                        { month: 'Jul', price: 375 },
                        { month: 'Aoû', price: 378 },
                        { month: 'Sep', price: 382 },
                        { month: 'Oct', price: 385 },
                        { month: 'Nov', price: 389 },
                        { month: 'Déc', price: 393 }
                    ]
                },
                tom: {
                    name: 'Tombouctou',
                    totalHarvest: 8000,
                    ca: 480000,
                    hectares: 0.8,
                    rendement: 10.0,
                    cultures: [
                        { name: 'Riz', percent: 30 },
                        { name: 'Maïs', percent: 45 },
                        { name: 'Coton', percent: 25 }
                    ],
                    prices: [
                        { month: 'Jan', price: 348 },
                        { month: 'Fév', price: 355 },
                        { month: 'Mar', price: 362 },
                        { month: 'Avr', price: 360 },
                        { month: 'Mai', price: 368 },
                        { month: 'Jun', price: 372 },
                        { month: 'Jul', price: 377 },
                        { month: 'Aoû', price: 380 },
                        { month: 'Sep', price: 384 },
                        { month: 'Oct', price: 387 },
                        { month: 'Nov', price: 391 },
                        { month: 'Déc', price: 395 }
                    ]
                },
                gao: {
                    name: 'Gao',
                    totalHarvest: 6000,
                    ca: 360000,
                    hectares: 0.6,
                    rendement: 10.0,
                    cultures: [
                        { name: 'Riz', percent: 28 },
                        { name: 'Maïs', percent: 47 },
                        { name: 'Coton', percent: 25 }
                    ],
                    prices: [
                        { month: 'Jan', price: 350 },
                        { month: 'Fév', price: 357 },
                        { month: 'Mar', price: 364 },
                        { month: 'Avr', price: 362 },
                        { month: 'Mai', price: 370 },
                        { month: 'Jun', price: 374 },
                        { month: 'Jul', price: 379 },
                        { month: 'Aoû', price: 382 },
                        { month: 'Sep', price: 386 },
                        { month: 'Oct', price: 389 },
                        { month: 'Nov', price: 393 },
                        { month: 'Déc', price: 397 }
                    ]
                },
                kid: {
                    name: 'Kidal',
                    totalHarvest: 3000,
                    ca: 180000,
                    hectares: 0.3,
                    rendement: 10.0,
                    cultures: [
                        { name: 'Riz', percent: 25 },
                        { name: 'Maïs', percent: 50 },
                        { name: 'Coton', percent: 25 }
                    ],
                    prices: [
                        { month: 'Jan', price: 352 },
                        { month: 'Fév', price: 359 },
                        { month: 'Mar', price: 366 },
                        { month: 'Avr', price: 364 },
                        { month: 'Mai', price: 372 },
                        { month: 'Jun', price: 376 },
                        { month: 'Jul', price: 381 },
                        { month: 'Aoû', price: 384 },
                        { month: 'Sep', price: 388 },
                        { month: 'Oct', price: 391 },
                        { month: 'Nov', price: 395 },
                        { month: 'Déc', price: 399 }
                    ]
                }
            };
        }

        bindEvents() {
            const regionSelect = document.getElementById('regionSelect');
            if (regionSelect) {
                regionSelect.addEventListener('change', (e) => {
                    this.currentRegion = e.target.value;
                    this.updateDashboard();
                    this.showRegionNotification();
                });
            }
        }

        updateDashboard() {
            const data = this.regionData[this.currentRegion];
            if (!data) return;

            // Mettre à jour les KPIs du manager s'ils existent
            this.updateKPI('total-production', data.totalHarvest.toString());
            this.updateKPI('ca-estime', (data.ca / 1000000).toString());
            this.updateKPI('kpiHa', data.hectares.toString());
            this.updateKPI('nombre-agriculteurs', (data.activeFarmers || 0).toString());

            // Mettre à jour les KPIs du client s'ils existent
            this.updateKPI('kpiTotalHarvest', data.totalHarvest.toString());
            this.updateKPI('kpiCA', data.ca.toString());
            this.updateKPI('kpiRend', data.rendement.toString());

            // Mettre à jour le titre de la page
            this.updatePageTitle(data.name);

            // Mettre à jour les graphiques si ils existent
            this.updateCharts(data);
        }

        updateKPI(elementId, value) {
            const element = document.getElementById(elementId);
            if (element) {
                // Animation de comptage
                const currentValue = parseInt(element.textContent.replace(/[^0-9]/g, '')) || 0;
                const targetValue = parseFloat(value.replace(/[^0-9.]/g, ''));
                
                this.animateValue(element, currentValue, targetValue, 500);
            }
        }

        animateValue(element, start, end, duration) {
            const startTime = performance.now();
            const elementId = element.id;
            
            const updateValue = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                const currentValue = start + (end - start) * this.easeOutQuad(progress);
                
                if (elementId === 'kpiCA' || elementId === 'ca-estime') {
                    element.textContent = currentValue.toFixed(1);
                } else if (elementId === 'kpiHa' || elementId === 'kpiRend') {
                    element.textContent = currentValue.toFixed(1);
                } else {
                    element.textContent = Math.round(currentValue).toLocaleString();
                }
                
                if (progress < 1) {
                    requestAnimationFrame(updateValue);
                }
            };
            
            requestAnimationFrame(updateValue);
        }

        easeOutQuad(t) {
            return t * (2 - t);
        }

        updatePageTitle(regionName) {
            const pageSubtitle = document.querySelector('.page-title p');
            if (pageSubtitle) {
                const originalText = 'Vue d\'ensemble des performances agricoles, avec analyse des tendances et alertes opérationnelles.';
                if (this.currentRegion === 'all') {
                    pageSubtitle.textContent = originalText;
                } else {
                    pageSubtitle.textContent = `Vue d'ensemble des performances agricoles pour la région de ${regionName}, avec analyse des tendances et alertes opérationnelles.`;
                }
            }
        }

        updateCharts(data) {
            // Mettre à jour le graphique de production totale par culture (Manager Dashboard)
            const prodChart = window.productionChartInstance;
            if (prodChart && data.cultures) {
                prodChart.data.labels = data.cultures.map(c => c.name);
                prodChart.data.datasets[0].data = data.cultures.map(c => c.amount);
                prodChart.update();
            }

            // Mettre à jour le graphique des alertes (Manager Dashboard)
            const alertsChart = window.alertsChartInstance;
            if (alertsChart && data.alertesParType) {
                const labels = [];
                const values = [];
                const colors = [];
                const map = data.alertesParType;
                if (map.stock_critique) { labels.push('Stock critique'); values.push(map.stock_critique); colors.push('#ef4444'); }
                if (map.faible_rentabilite) { labels.push('Faible rentabilité'); values.push(map.faible_rentabilite); colors.push('#f59e0b'); }
                if (map.faible_activite) { labels.push('Faible activité'); values.push(map.faible_activite); colors.push('#3b82f6'); }
                
                alertsChart.data.labels = labels;
                alertsChart.data.datasets[0].data = values;
                alertsChart.data.datasets[0].backgroundColor = colors;
                alertsChart.update();
            }

            // Mettre à jour le graphique des prix des céréales (Client Dashboard)
            const priceChart = window.dashboardCharts?.priceChart;
            if (priceChart && data.prices) {
                priceChart.data.datasets[0].data = data.prices.map(p => p.price);
                priceChart.update();
            }

            // Mettre à jour le graphique de distribution des cultures (Client Dashboard)
            const cultureChart = window.dashboardCharts?.cultureChart;
            if (cultureChart && data.cultures) {
                cultureChart.data.datasets[0].data = data.cultures.map(c => c.percent || c.amount);
                cultureChart.update();
                
                // Mettre à jour la culture dominante
                const dominantCulture = document.getElementById('dominantCulture');
                if (dominantCulture) {
                    const dominant = data.cultures.reduce((max, c) => (c.percent || c.amount) > (max.percent || max.amount) ? c : max);
                    dominantCulture.textContent = dominant.name;
                }
            }
        }

        showRegionNotification() {
            const data = this.regionData[this.currentRegion];
            if (!data || this.currentRegion === 'all') return;

            // Créer une notification temporaire
            const notification = document.createElement('div');
            notification.className = 'region-notification';
            notification.innerHTML = `
                <div class="region-notification-content">
                    <span class="region-icon">📍</span>
                    <span class="region-text">Affichage des données pour ${data.name}</span>
                    <button class="region-close" onclick="this.parentElement.parentElement.remove()">×</button>
                </div>
            `;
            
            // Ajouter au body
            document.body.appendChild(notification);
            
            // Animation d'entrée
            setTimeout(() => notification.classList.add('show'), 10);
            
            // Retirer après 3 secondes
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Méthode pour obtenir les données actuelles
        getCurrentRegionData() {
            return this.regionData[this.currentRegion];
        }

        // Méthode pour réinitialiser à "Toutes les régions"
        resetToAll() {
            this.currentRegion = 'all';
            const regionSelect = document.getElementById('regionSelect');
            if (regionSelect) {
                regionSelect.value = 'all';
            }
            this.updateDashboard();
        }
    }

    // Créer l'instance globale
    window.regionFilter = new RegionFilter();

    // Sauvegarder les graphiques existants pour pouvoir les mettre à jour
    window.dashboardCharts = window.dashboardCharts || {};

})();
