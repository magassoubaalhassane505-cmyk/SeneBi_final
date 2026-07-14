(function () {
  function fmtHectaresWord(n) {
    const v = Number(n || 0);
    const num = v.toLocaleString("fr-FR", { maximumFractionDigits: 1 });
    return `${num} hectares`;
  }
  function fmtKg(n) {
    return `${Number(n || 0).toLocaleString("fr-FR")} kg`;
  }
  function fmtKgPerHa(n) {
    const v = Math.round(Number(n || 0));
    return `${v.toLocaleString("fr-FR")} kg/ha`;
  }
  function fmtCost(n) {
    return `${Number(n || 0).toLocaleString("fr-FR")} FCFA`;
  }

  function fmtDate(d) {
    if (!d) return null;
    return new Date(d).toLocaleDateString("fr-FR");
  }

  function renderParcelsList(uiParcels, list) {
    const badgeClass = (st) => (st === "En culture" ? "green" : st === "Récoltée" ? "blue" : "yellow");

    if (!uiParcels.length) {
      list.innerHTML = '<p class="small muted" style="padding:24px;text-align:center;">Aucune parcelle enregistrée. Utilisez le formulaire ci-dessus.</p>';
      const parcelSelect = SeneBI.qs("#parcelle-recoltee");
      if (parcelSelect) parcelSelect.innerHTML = '<option value="">Sélectionner une parcelle</option>';
      return;
    }

    list.innerHTML = uiParcels
      .map((p) => {
        const hasHarvest = p.lastHarvestQty && Number(p.lastHarvestQty || 0) > 0;
        const yieldKgHa = hasHarvest ? Number(p.lastHarvestQty) / Number(p.areaHa || 1) : null;
        const dateFr = p.lastHarvestDate ? new Date(p.lastHarvestDate).toLocaleDateString("fr-FR") : null;
        const journal = Array.isArray(p.journal) ? p.journal : [];
        const journalHtml = journal.length
          ? journal.map((j) => `<li class="parcel-journal-item"><span class="parcel-journal-k">${j.label}</span><span class="parcel-journal-v">${j.value}</span></li>`).join("")
          : '<li class="parcel-journal-item"><span class="parcel-journal-k">Aucune activité</span><span class="parcel-journal-v">—</span></li>';

        const photosHtml = p.photos && p.photos.length > 0
          ? `<div class="parcel-photos-gallery" style="display:flex;gap:6px;margin-top:10px;flex-wrap:wrap;">
              ${p.photos.slice(0, 3).map(photo => `<img src="/storage/${photo.photo_path}" alt="${photo.legende || ''}" style="width:50px;height:50px;border-radius:6px;object-fit:cover;border:1px solid #e5e7eb;">`).join('')}
              ${p.photos.length > 3 ? `<div style="width:50px;height:50px;border-radius:6px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:11px;color:#6b7280;">+${p.photos.length - 3}</div>` : ''}
            </div>`
          : '';

        const mapBtn = p.latitude && p.longitude
          ? `<button class="btn-map" onclick="openMapModal(${p.latitude}, ${p.longitude}, '${p.name}')" style="background:none;border:1px solid #e5e7eb;border-radius:6px;padding:4px 8px;font-size:11px;color:#3b82f6;cursor:pointer;margin-right:8px;">
              <i class="fas fa-map-marker-alt"></i> Voir sur la carte
            </button>`
          : '';

        return `
          <article class="parcel-card ${p.status === "En jachère" ? 'fallow-card' : ''}">
            <div class="parcel-head">
              <div class="parcel-name">${p.name}</div>
              ${p.performance && p.performanceClass ? `<span class="${p.performanceClass}">${p.performance}</span>` : ''}
              <span class="badge ${badgeClass(p.status)}">${p.status}</span>
            </div>

            <div class="parcel-growth">
              <div class="growth-bar">
                <div class="growth-fill" style="width: ${p.growth}%"></div>
              </div>
              <span class="growth-text">${p.growth}% de croissance</span>
            </div>

            <div class="parcel-body">
              <div class="parcel-grid">
                <div class="kv">
                  <div class="k">Culture</div>
                  <div class="v">
                    ${p.culture}
                    ${p.status === "En culture" && p.plantingDate ? `<div class="planting-date">Semé le : ${p.plantingDate}</div>` : ""}
                    ${p.status === "En jachère" && p.lastActivity ? `<div class="planting-date">Dernière activité : ${p.lastActivity}</div>` : ""}
                  </div>
                </div>
                <div class="kv"><div class="k">Surface</div><div class="v">${fmtHectaresWord(p.areaHa)}</div></div>
                <div class="kv"><div class="k">Dernière récolte</div><div class="v">${dateFr || "—"}</div></div>

                <div class="kv"><div class="k">Quantité</div><div class="v">${hasHarvest ? fmtKg(p.lastHarvestQty) : "—"}</div></div>
                <div class="kv"><div class="k">Récoltes</div><div class="v">${p.recoltesCount || 0}</div></div>
                <div class="kv"><div class="k">Intrants</div><div class="v">${p.intrantsCount || 0}</div></div>
                <div class="kv"><div class="k">Visites</div><div class="v">${p.visitesCount || 0}</div></div>
                <div class="kv"><div class="k">Durée culture</div><div class="v">${p.cultureDuration ? p.cultureDuration + ' j' : '—'}</div></div>

                <div class="kv"><div class="k">Production estimée</div><div class="v">${fmtKg(p.productionEstimee || 0)}</div></div>

                <div class="kv">
                  <div class="k">Calendrier travaux</div>
                  <div class="v" style="font-size:11px;">
                    <div><i class="fas fa-tint"></i> Arrosage: ${fmtDate(p.last_irrigation) || '—'}</div>
                    <div><i class="fas fa-spray-can"></i> Traitement: ${fmtDate(p.last_traitement) || '—'}</div>
                    <div><i class="fas fa-calendar-check"></i> Prochaine: ${fmtDate(p.next_intervention) || '—'}</div>
                  </div>
                </div>

                ${mapBtn || photosHtml ? `<div class="kv" style="grid-column:span 2;">${mapBtn}${photosHtml}</div>` : ''}

                <div class="kv"><div class="k">Coût investi</div><div class="v">${fmtCost(p.cost)}</div></div>
                <div class="kv">
                  <div class="k">Rendement</div>
                  <div class="v yield">
                    <span>${hasHarvest ? fmtKgPerHa(yieldKgHa) : "—"}</span>
                    ${hasHarvest ? `<span class="yield-check" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>` : ""}
                    ${hasHarvest && p.performance ? `<span class="performance-indicator" title="+${p.performance}% par rapport à la saison dernière" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 14l5-5 5 5"/></svg></span>` : ""}
                  </div>
                </div>
                <div></div>
              </div>
            </div>

            <aside class="parcel-journal" aria-label="Journal d'activites">
              <div class="parcel-journal-title">Journal d'activites</div>
              <ul class="parcel-journal-list">
                ${journalHtml}
              </ul>
            </aside>

            ${p.status !== "En jachère" ? `
            <div class="parcel-actions">
              <button class="apply-intrant-btn" onclick="window.location.href='/client/stocks'">
                Appliquer Intrant
              </button>
            </div>
            ` : ''}
          </article>
        `;
      })
      .join("");

    const parcelSelect = SeneBI.qs("#parcelle-recoltee");
    if (parcelSelect) {
      parcelSelect.innerHTML = `<option value="">Sélectionner une parcelle</option>` + uiParcels.map((p) => `<option value="${p.id}">${p.name}</option>`).join("");
    }
  }

  window.openMapModal = function (lat, lng, name) {
    alert(`Localisation de ${name}\nLatitude: ${lat}, Longitude: ${lng}\n(Fonctionnalité carte à implémenter)`);
  };

  window.renderParcels = function () {
    const list = SeneBI.qs("#parcelsList");
    if (!list) return;

    if (typeof window.getUiParcelsFromServer === "function") {
      const fromServer = window.getUiParcelsFromServer();
      if (fromServer) {
        renderParcelsList(fromServer, list);
        return;
      }
    }

    const uiParcels = [
      { id: "BKO-N1", name: "Parcelle Bamako Nord-1", culture: "Riz", areaHa: 4.2, status: "En culture", growth: 80, cost: 98000, performance: 12, plantingDate: "10/01/2026" },
      { id: "BKO-S1", name: "Parcelle Bamako Sud-1", culture: "Maïs", areaHa: 3.5, status: "Récoltée", growth: 100, cost: 78000, performance: 18, plantingDate: "20/11/2025" },
      { id: "BKO-C1", name: "Parcelle Bamako Centre-1", culture: "Coton", areaHa: 2.8, status: "En culture", growth: 65, cost: 67000, performance: null, plantingDate: "15/02/2026" },
      { id: "PN", name: "Parcelle Nord", culture: "Riz", areaHa: 5.5, status: "En culture", growth: 75, cost: 125500, performance: null, plantingDate: "12/01/2026" },
      { id: "PS", name: "Parcelle Sud", culture: "Maïs", areaHa: 3.2, status: "Récoltée", growth: 100, cost: 82000, performance: 15, plantingDate: "15/11/2025" },
      { id: "PE", name: "Parcelle Est", culture: "Coton", areaHa: 4.0, status: "En culture", growth: 40, cost: 95000, performance: null, plantingDate: "20/02/2026" },
      { id: "PO", name: "Parcelle Ouest", culture: "Riz", areaHa: 2.8, status: "En jachère", growth: 0, cost: 45000, performance: null, lastActivity: "01/02/2026" },
      { id: "PC", name: "Parcelle Centre", culture: "Maïs", areaHa: 6.0, status: "En culture", growth: 85, cost: 158000, performance: null, plantingDate: "08/01/2026" },
    ];

    renderParcelsList(uiParcels, list);
  };

  function bindForm() {
    const openBtn = SeneBI.qs("#openHarvestBtn");
    const panel = SeneBI.qs("#harvestPanel");
    const cancelBtn = SeneBI.qs("#cancelHarvestBtn");
    if (panel) {
      panel.classList.remove("show");
      panel.setAttribute("aria-hidden", "true");
    }
    if (openBtn && panel && !openBtn.dataset.bound) {
      openBtn.dataset.bound = "1";
      openBtn.addEventListener("click", () => {
        const willShow = !panel.classList.contains("show");
        panel.classList.toggle("show", willShow);
        panel.setAttribute("aria-hidden", willShow ? "false" : "true");
        if (willShow) panel.scrollIntoView({ behavior: "smooth", block: "start" });
      });
    }
    if (cancelBtn && panel && !cancelBtn.dataset.bound) {
      cancelBtn.dataset.bound = "1";
      cancelBtn.addEventListener("click", () => {
        panel.classList.remove("show");
        panel.setAttribute("aria-hidden", "true");
      });
    }
  }

  document.addEventListener("DOMContentLoaded", function () {
    const auth = SeneBI.requireRole(["manager", "client"], "Acces refuse.");
    if (!auth) return;
    const readOnly = false;
    const openBtn = SeneBI.qs("#openHarvestBtn");
    if (openBtn) openBtn.hidden = readOnly;
    const panel = SeneBI.qs("#harvestPanel");
    if (panel && readOnly) {
      panel.classList.remove("show");
      panel.hidden = true;
      panel.setAttribute("aria-hidden", "true");
    }

    const state = SeneBI.loadState();
    SeneBI.renderTopbar(state);
    if (!readOnly) bindForm();
    try {
      renderParcels();
    } catch (err) {
      console.error("Erreur lors du rendu des parcelles:", err);
    }
    window.addEventListener("senebi:seasonChanged", () => SeneBI.renderTopbar(state));
  });
})();