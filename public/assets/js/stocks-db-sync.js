(function () {
  const cfg = window.SeneBI_SERVER;
  if (!cfg?.useDb || !window.SeneBI) return;

  function applyStocksToState() {
    const stocks = cfg.stocks;
    if (!Array.isArray(stocks) || !stocks.length) return;

    const state = SeneBI.loadState();
    const season = SeneBI.getSeasonData(state);

    stocks.forEach((row) => {
      const name = String(row.nom || "").toLowerCase();
      const qty = Number(row.quantite_actuelle || 0);
      if (name.includes("urée") || name.includes("uree")) season.inventory.ureeKg = qty;
      else if (name.includes("npk")) season.inventory.npkKg = qty;
      else if (name.includes("semence")) season.inventory.semencesKg = qty;
    });

    SeneBI.saveState(state);
  }

  function hookSaveState() {
    const original = SeneBI.saveState.bind(SeneBI);
    SeneBI.saveState = function (state) {
      original(state);
      const season = SeneBI.getSeasonData(state);
      const inv = season.inventory || {};
      persistStockByName("urée", Number(inv.ureeKg || 0));
      persistStockByName("npk", Number(inv.npkKg || 0));
      persistStockByName("semence", Number(inv.semencesKg || 0));
    };
  }

  async function persistStockByName(match, quantity) {
    const stock = (cfg.stocks || []).find((s) => String(s.nom || "").toLowerCase().includes(match));
    if (!stock) return;
    const res = await fetch(`${cfg.apiBase}/stocks/${stock.id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN": cfg.csrf,
      },
      body: JSON.stringify({ quantite_actuelle: quantity }),
    });
    if (res.ok) {
      const json = await res.json();
      const idx = cfg.stocks.findIndex((s) => s.id === stock.id);
      if (idx >= 0) cfg.stocks[idx] = json.data;
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
      applyStocksToState();
      hookSaveState();
    });
  } else {
    applyStocksToState();
    hookSaveState();
  }
})();
