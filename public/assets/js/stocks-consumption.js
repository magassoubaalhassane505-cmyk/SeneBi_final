// Script simple pour gérer le formulaire de consommation
document.addEventListener("DOMContentLoaded", function() {
  const consumeForm = document.querySelector("#consumeForm");
  if (!consumeForm) return;

  consumeForm.addEventListener("submit", async function(e) {
    e.preventDefault();
    
    const region = document.querySelector("#consumeRegion")?.value || "";
    const parcelle = document.querySelector("#consumeParcel")?.value || "";
    const intrant = document.querySelector("#consumeItem")?.value || "";
    const quantite = parseFloat(document.querySelector("#consumeQty")?.value) || 0;
    const dateInput = document.querySelector("#consumeDate")?.value || "";
    const date = dateInput ? new Date(dateInput).toISOString().split('T')[0] : new Date().toISOString().split('T')[0];
    
    if (!parcelle || !intrant || quantite <= 0) {
      showToast("Veuillez remplir tous les champs", "error");
      return;
    }
    
    try {
      const csrfToken = window.SeneBI_SERVER?.csrf || '';
      
      const response = await fetch('/client/api/consommation', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          region: region,
          parcelle: parcelle,
          date: date,
          intrant: intrant,
          quantite: quantite
        })
      });
      
      const contentType = response.headers.get('content-type');
      
      if (!contentType || !contentType.includes('application/json')) {
        const text = await response.text();
        throw new Error('Erreur serveur: réponse non JSON');
      }
      
      const result = await response.json();
      
      if (!response.ok) {
        throw new Error(result.error || 'Erreur lors de l\'enregistrement');
      }
      
      // Mettre à jour le tableau
      const stockRows = document.querySelectorAll("#stockTableBody tr");
      stockRows.forEach(row => {
        const nameCell = row.cells[0]?.textContent;
        if (nameCell === result.stock.nom) {
          const stockCell = row.cells[2];
          const statusCell = row.cells[5];
          
          stockCell.innerHTML = `<div>${result.stock.quantite_actuelle.toLocaleString("fr-FR")} kg</div>
            <div class="stock-progress-bar">
              <div class="stock-progress-fill ${result.stock.est_critique ? 'critical' : 'ok'}" 
                   style="width: ${Math.min(100, (result.stock.quantite_actuelle / (result.stock.seuil_critique * 4)) * 100)}%"></div>
            </div>`;
          
          const progressFill = stockCell.querySelector('.stock-progress-fill');
          if (progressFill) {
            progressFill.className = `stock-progress-fill ${result.stock.est_critique ? 'critical' : 'ok'}`;
            progressFill.style.width = `${Math.min(100, (result.stock.quantite_actuelle / (result.stock.seuil_critique * 4)) * 100)}%`;
          }
          
          statusCell.innerHTML = result.stock.est_critique 
            ? `<span class="badge critical">Critique (${Math.round((result.stock.quantite_actuelle / result.stock.seuil_critique) * 100)}%)</span>`
            : `<span class="badge ok">OK (${Math.round((result.stock.quantite_actuelle / (result.stock.seuil_critique * 4)) * 100)}%)</span>`;
          statusCell.className = result.stock.est_critique ? 'status-bad' : 'status-ok';
        }
      });
      
      // Mettre à jour les KPI
      const kpiValues = document.querySelectorAll('.kpi-value');
      const criticalCount = document.querySelectorAll('.status-bad').length;
      if (kpiValues.length >= 3) {
        kpiValues[2].textContent = criticalCount;
        kpiValues[2].style.color = criticalCount > 0 ? '#ef4444' : '';
      }
      
      // Mettre à jour la timeline
      const timeline = document.getElementById('stocksTimeline');
      if (timeline) {
        const newItem = document.createElement('div');
        newItem.className = 'timeline-item';
        newItem.style.cssText = 'display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; background: #fee2e2; margin-bottom: 8px;';
        newItem.innerHTML = `
          <div style="width: 32px; height: 32px; border-radius: 50%; background: #ef4444; color: white; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-arrow-down" style="font-size: 14px;"></i>
          </div>
          <div style="flex: 1;">
            <div style="font-weight: 600; color: #111827;">${intrant}</div>
            <div style="font-size: 12px; color: #64748b;">${new Date(date).toLocaleDateString('fr-FR')}</div>
          </div>
          <div style="font-weight: 700; color: #ef4444;">-${quantite.toLocaleString('fr-FR')} kg</div>
        `;
        timeline.insertBefore(newItem, timeline.firstChild);
      }
      
      // Mettre à jour l'alerte
      const alertBanner = document.getElementById('stocksLocalAlert');
      const alertText = document.getElementById('stocksLocalAlertText');
      const criticalChip = document.getElementById('criticalChip');
      
      if (criticalCount > 0) {
        alertBanner?.classList.add('show');
        alertText.textContent = `${criticalCount} intrant(s) en dessous du seuil critique. Réapprovisionnement urgent nécessaire.`;
        criticalChip.textContent = criticalCount.toString();
      }
      
      showToast("Consommation enregistrée avec succès", "success");
      consumeForm.reset();
      
    } catch (error) {
      console.error("Erreur:", error);
      showToast("Erreur: " + (error.message || "Erreur lors de l'enregistrement"), "error");
    }
  });
  
  function showToast(message, type) {
    const toast = document.createElement("div");
    toast.textContent = message;
    const bgColor = type === "success" ? "#10b981" : "#ef4444";
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
