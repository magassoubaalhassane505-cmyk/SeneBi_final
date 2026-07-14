(function () {
  const cfg = window.SeneBI_SERVER;
  if (!cfg?.useDb) return;

  function csrfHeaders() {
    return {
      "Content-Type": "application/json",
      Accept: "application/json",
      "X-CSRF-TOKEN": cfg.csrf,
    };
  }

  window.getUiParcelsFromServer = function () {
    if (!Array.isArray(cfg.parcelles)) return null;
    const statsMap = new Map((Array.isArray(cfg.parcelleStats) ? cfg.parcelleStats : []).map(s => [String(s.id), s]));
    return cfg.parcelles.map((p) => {
      // Fusionne les stats initiales avec la parcelle (enrichie par l'API apres refresh)
      const s = { ...(statsMap.get(String(p.id)) || {}), ...p };
      return {
        id: String(p.id),
        name: p.nom,
        culture: p.culture,
        areaHa: Number(p.surface),
        status: s.status || "En culture",
        growth: s.growth ?? 0,
        cost: s.cost || 0,
        performance: s.badge || null,
        performanceClass: s.badgeClass || null,
        plantingDate: s.plantingDate || null,
        region: p.region,
        rendement: s.rendement || 0,
        recoltesCount: s.recoltesCount || 0,
        intrantsCount: s.intrantsCount || 0,
        visitesCount: s.visitesCount || 0,
        lastHarvestDate: s.lastHarvestDate || null,
        lastHarvestQty: s.lastHarvestQty || 0,
        cultureDuration: s.cultureDuration || 0,
        productionEstimee: s.productionEstimee || 0,
        latitude: s.latitude || null,
        longitude: s.longitude || null,
        last_irrigation: s.last_irrigation || null,
        last_traitement: s.last_traitement || null,
        next_intervention: s.next_intervention || null,
        photos: s.photos || [],
        journal: s.journal || [],
      };
    });
  };

  async function refreshParcelles() {
    const res = await fetch(`${cfg.apiBase}/parcelles`, { headers: { Accept: "application/json" } });
    if (!res.ok) return;
    const json = await res.json();
    cfg.parcelles = json.data || [];
    if (Array.isArray(cfg.parcelles)) cfg.parcelleStats = cfg.parcelles;
    if (typeof window.renderParcels === "function") window.renderParcels();
  }

  document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("addParcelForm");
    if (form && !form.dataset.bound) {
      form.dataset.bound = "1";
      form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const payload = {
          nom: document.getElementById("parcelNom")?.value?.trim(),
          region: document.getElementById("parcelRegion")?.value?.trim(),
          surface: Number(document.getElementById("parcelSurface")?.value),
          culture: document.getElementById("parcelCulture")?.value?.trim(),
        };
        const res = await fetch(`${cfg.apiBase}/parcelles`, {
          method: "POST",
          headers: csrfHeaders(),
          body: JSON.stringify(payload),
        });
        if (!res.ok) {
          alert("Impossible d'enregistrer la parcelle.");
          return;
        }
        form.reset();
        await refreshParcelles();
      });
    }
  });

  window.SeneBI_refreshParcelles = refreshParcelles;
})();
