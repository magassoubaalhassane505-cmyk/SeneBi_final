document.addEventListener("DOMContentLoaded", function() {
  const reapproForm = document.querySelector("#reapproForm");
  if (!reapproForm) return;

  const unitCostInput = document.querySelector("#reapproUnitCost");
  const totalCostInput = document.querySelector("#reapproTotalCost");
  const qtyInput = document.querySelector("#reapproQty");

  function autoCalcTotalCost() {
    const qty = parseFloat(qtyInput?.value) || 0;
    const unit = parseFloat(unitCostInput?.value) || 0;
    if (qty > 0 && unit > 0 && totalCostInput && !totalCostInput.dataset.manuallySet) {
      totalCostInput.value = (qty * unit).toFixed(0);
    }
  }

  function autoCalcUnitCost() {
    const qty = parseFloat(qtyInput?.value) || 0;
    const total = parseFloat(totalCostInput?.value) || 0;
    if (qty > 0 && total > 0 && unitCostInput && !unitCostInput.dataset.manuallySet) {
      unitCostInput.value = (total / qty).toFixed(0);
    }
  }

  if (unitCostInput) {
    unitCostInput.addEventListener("input", function() {
      this.dataset.manuallySet = "true";
      autoCalcTotalCost();
      setTimeout(() => { this.dataset.manuallySet = ""; }, 500);
    });
  }

  if (totalCostInput) {
    totalCostInput.addEventListener("input", function() {
      this.dataset.manuallySet = "true";
      autoCalcUnitCost();
      setTimeout(() => { this.dataset.manuallySet = ""; }, 500);
    });
  }

  if (qtyInput) {
    qtyInput.addEventListener("input", function() {
      autoCalcTotalCost();
      autoCalcUnitCost();
    });
  }

  reapproForm.addEventListener("submit", async function(e) {
    e.preventDefault();

    const stockId = document.querySelector("#reapproItem")?.value || "";
    const quantite = parseFloat(document.querySelector("#reapproQty")?.value) || 0;
    const coutUnitaire = parseFloat(document.querySelector("#reapproUnitCost")?.value) || 0;
    const coutTotal = parseFloat(document.querySelector("#reapproTotalCost")?.value) || 0;
    const obs = document.querySelector("#reapproObs")?.value || "";
    const dateInput = document.querySelector("#reapproDate")?.value || "";
    const date = dateInput ? new Date(dateInput).toISOString().split('T')[0] : new Date().toISOString().split('T')[0];

    if (!stockId || quantite <= 0) {
      showToast("Veuillez sélectionner un intrant et saisir une quantité valide.", "error");
      return;
    }

    const payload = {
      stock_id: stockId,
      quantite: quantite,
      date: date,
      description: obs || "Entrée de stock",
    };

    if (coutUnitaire > 0) payload.cout_unitaire = coutUnitaire;
    else if (coutTotal > 0) payload.cout_total = coutTotal;

    try {
      const csrfToken = window.SeneBI_SERVER?.csrf || '';

      const response = await fetch('/client/api/stocks/entree', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      });

      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        const text = await response.text();
        throw new Error('Erreur serveur: réponse non JSON');
      }

      const result = await response.json();
      if (!response.ok) {
        throw new Error(result.error || "Erreur lors de l'enregistrement");
      }

      const stockRow = document.querySelector("#reapproItem option:checked");
      const intrantNom = stockRow ? stockRow.textContent.split(" (stock:")[0].trim() : "Intrant";

      const stockData = result.stock;
      const seuilCritique = stockData.seuil_critique || 0;
      const quantiteActuelle = stockData.quantite_actuelle || 0;
      const estCritique = quantiteActuelle <= seuilCritique;

      const stockRows = document.querySelectorAll("#stockTableBody tr");
      stockRows.forEach(row => {
        const nameCell = row.cells[0]?.textContent;
        if (nameCell === stockData.nom) {
          const stockCell = row.cells[2];
          const progressFill = stockCell.querySelector('.stock-progress-fill');
          if (progressFill) {
            progressFill.className = `stock-progress-fill ${estCritique ? 'critical' : 'ok'}`;
            progressFill.style.width = `${Math.min(100, (quantiteActuelle / (seuilCritique * 4)) * 100)}%`;
          }
          stockCell.innerHTML = `<div>${quantiteActuelle.toLocaleString("fr-FR")} kg</div>
            <div class="stock-progress-bar">
              <div class="stock-progress-fill ${estCritique ? 'critical' : 'ok'}" style="width: ${Math.min(100, (quantiteActuelle / (seuilCritique * 4)) * 100)}%;"></div>
            </div>`;

          const statusCell = row.cells[5];
          statusCell.innerHTML = estCritique
            ? `<span class="badge critical">Critique (${Math.round((quantiteActuelle / seuilCritique) * 100)}%)</span>`
            : `<span class="badge ok">OK (${Math.round((quantiteActuelle / (seuilCritique * 4)) * 100)}%)</span>`;
          statusCell.className = estCritique ? 'status-bad' : 'status-ok';

          const coutCell = row.cells[4];
          if (coutCell && stockData.cout_unitaire) {
            coutCell.innerHTML = `${Number(stockData.cout_unitaire).toLocaleString("fr-FR")} FCFA`;
          }
        }
      });

      const kpiValues = document.querySelectorAll('.kpi-value');
      const criticalCount = document.querySelectorAll('.status-bad').length;
      if (kpiValues.length >= 3) {
        kpiValues[2].textContent = criticalCount;
        kpiValues[2].style.color = criticalCount > 0 ? '#ef4444' : '';
      }

      const stockRowsForValue = document.querySelectorAll("#stockTableBody tr");
      let totalValue = 0;
      stockRowsForValue.forEach(row => {
        const cells = row.querySelectorAll("td");
        if (cells.length >= 5) {
          const stockText = cells[2]?.textContent || "0";
          const stockVal = parseInt(stockText.replace(/[^\d]/g, '')) || 0;
          const coutText = cells[4]?.textContent || "0";
          const coutVal = parseInt(coutText.replace(/[^\d]/g, '')) || 0;
          totalValue += stockVal * coutVal;
        }
      });
      const valeurStockEl = document.querySelector('.valeur-stock');
      if (valeurStockEl) {
        valeurStockEl.textContent = `${totalValue.toLocaleString("fr-FR")} FCFA`;
      }

      const alertBanner = document.getElementById('stocksLocalAlert');
      const alertText = document.getElementById('stocksLocalAlertText');
      const criticalChip = document.getElementById('criticalChip');
      if (criticalCount > 0) {
        alertBanner?.classList.add('show');
        alertText.textContent = `${criticalCount} intrant(s) en dessous du seuil critique. Réapprovisionnement urgent nécessaire.`;
        criticalChip.textContent = criticalCount.toString();
      } else {
        alertBanner?.classList.remove('show');
      }

      const timeline = document.getElementById('stocksTimeline');
      if (timeline) {
        const newItem = document.createElement('div');
        newItem.className = 'timeline-item';
        newItem.style.cssText = 'display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; background: #dcfce7; margin-bottom: 8px;';
        newItem.innerHTML = `
          <div style="width: 32px; height: 32px; border-radius: 50%; background: #059669; color: white; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-arrow-up" style="font-size: 14px;"></i>
          </div>
          <div style="flex: 1;">
            <div style="font-weight: 600; color: #111827;">${intrantNom}</div>
            <div style="font-size: 12px; color: #64748b;">${new Date(date).toLocaleDateString('fr-FR')}${obs ? ' — ' + obs : ''}</div>
          </div>
          <div style="font-weight: 700; color: #059669;">+${quantite.toLocaleString('fr-FR')} kg</div>
        `;
        timeline.insertBefore(newItem, timeline.firstChild);
      }

      showToast("Réapprovisionnement enregistré avec succès", "success");
      reapproForm.reset();
      const dateEl = document.querySelector("#reapproDate");
      if (dateEl) dateEl.valueAsDate = new Date();

    } catch (error) {
      console.error("Erreur:", error);
      showToast("Erreur: " + (error.message || "Erreur lors de l'enregistrement"), "error");
    }
  });

  function showToast(message, type) {
    const toast = document.createElement("div");
    toast.textContent = message;
    const bgColor = type === "success" ? "#059669" : "#ef4444";
    toast.style.cssText = `
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: ${bgColor};
      color: white;
      padding: 12px 20px;
      border-radius: 12px;
      font-weight: 600;
      z-index: 1000;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = "1";
      toast.style.transform = "translateY(0)";
    }, 100);
    setTimeout(() => {
      toast.style.opacity = "0";
      toast.style.transform = "translateY(20px)";
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }
});
