// verSolicitudAdmin.js

const PUBLIC_BASE = "http://127.0.0.1:8000";
const API_BASE = "http://127.0.0.1:8000/api";
const API_URL = `${API_BASE}/loans`;

document.addEventListener("DOMContentLoaded", loadLoanDetail);

let currentLoan = null;

async function loadLoanDetail() {
  try {
    const token = localStorage.getItem("token");
    const loanId = getLoanIdFromURL();

    const res = await fetch(`${API_URL}/${loanId}`, {
      headers: {
        "Authorization": `Bearer ${token}`
      }
    });

    if (res.status === 401 || res.status === 403) {
      window.location.href = "/";
      return;
    }

    if (!res.ok) {
      throw new Error("Error cargando detalle");
    }

    const response = await res.json();

    // Laravel ahora devuelve:
    // { message: "...", data: {...} }

    const loan = response.data || response;

    renderLoan(loan);

  } catch (err) {
    console.error(err);
    alert("Error cargando detalle del préstamo");
  }
}

function getLoanIdFromURL() {
  const parts = window.location.pathname.split("/");
  return parts[parts.length - 1];
}

function renderLoan(loan) {
  currentLoan = loan;

  renderUser(loan.user);
  renderEquipment(loan.equipment);
  renderDates(loan);
  renderStatus(loan.status);
  renderJustification(loan.justification);

  setupActionButtons(loan);
}

function renderUser(user) {
  if (!user) return;

  setText("Nombre Completo", user.full_name || user.username || "-");
  setText("Correo de Contacto", user.email || "-");
  setText("ID de Empleado", user.id || "-");
  setText("Role", user.role || "-");
}

function renderEquipment(equipment) {
  if (!equipment) return;

  setText("Nombre del Equipo", equipment.name || "-");
  setText("Número de Serie", `ID: ${equipment.id || "-"}`);
  setText("Categoría", equipment.type || "-");

  const img = document.querySelector(".equipment-img");

  if (img && equipment.image_url) {
    img.src = equipment.image_url;
  }
}

function renderDates(loan) {
  const requestDate =
    loan.request_date || loan.requestDate;

  const estimatedEndDate =
    loan.estimated_end_date || loan.estimatedEndDate;

  setTextExact("Fecha de Solicitud", formatDate(requestDate));

  setTextExact(
    "Devolución Estimada",
    formatDate(estimatedEndDate)
  );

  renderDuration(requestDate, estimatedEndDate);
}

function parseLocalDate(dateStr) {
  const [year, month, day] = dateStr.split("-");
  return new Date(year, month - 1, day);
}

function renderDuration(startDateStr, endDateStr) {
  const el = document.getElementById("numDias");

  if (!el || !startDateStr || !endDateStr) return;

  const start = parseLocalDate(startDateStr);
  const end = parseLocalDate(endDateStr);

  const diffTime = end - start;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays <= 0) {
    el.textContent = "(Mismo día)";
    return;
  }

  el.textContent = `(Duración: ${diffDays} día${diffDays !== 1 ? "s" : ""})`;
}

function renderStatus(status) {
  const badge = document.querySelector(".status-badge");

  if (!badge) return;

  let icon = "info";
  let text = status;

  switch (status) {
    case "PENDIENTE":
      icon = "schedule";
      text = "Pendiente";
      break;

    case "APROBADO":
      icon = "check_circle";
      text = "Aprobado";
      break;

    case "PRESTADO":
      icon = "inventory_2";
      text = "Prestado";
      break;

    case "RECHAZADO":
      icon = "cancel";
      text = "Rechazado";
      break;

    case "DEVUELTO":
      icon = "done_all";
      text = "Devuelto";
      break;
  }

  badge.innerHTML = `
    <span class="material-symbols-outlined">${icon}</span>
    ${text}
  `;
}

function renderJustification(text) {
  const el = document.querySelector(".purpose-quote");

  if (el) {
    el.textContent = text ? `"${text}"` : '"Sin justificación"';
  }
}

function setText(label, value) {
  const labels = document.querySelectorAll(".field-label");

  labels.forEach(l => {
    if (l.textContent.includes(label)) {
      const target = l.nextElementSibling;

      if (target) {
        target.textContent = value || "-";
      }
    }
  });
}

function setTextExact(label, value) {
  const labels = document.querySelectorAll(".field-label");

  labels.forEach(l => {
    if (l.textContent.trim() === label) {
      const target = l.nextElementSibling;

      if (target) {
        target.textContent = value || "-";
      }
    }
  });
}

function formatDate(dateString) {
  if (!dateString) return "-";

  const date = new Date(dateString);

  return date.toLocaleDateString("es-CR", {
    day: "2-digit",
    month: "short",
    year: "numeric"
  });
}

function setupActionButtons(loan) {
  const actionBtn = document.getElementById("action-btn");
  const rejectBtn = document.getElementById("reject-btn");

  if (!actionBtn) return;

  actionBtn.onclick = null;
  actionBtn.disabled = false;

  if (rejectBtn) {
    rejectBtn.onclick = null;
    rejectBtn.style.display = "none";
  }

  switch (loan.status) {

    case "PENDIENTE":
      actionBtn.innerHTML = `
        <span class="material-symbols-outlined">check_circle</span>
        Aprobar Préstamo
      `;

      actionBtn.className = "btn-approve";

      actionBtn.onclick = () =>
        updateStatus(loan.id, "APROBADO");

      if (rejectBtn) {
        rejectBtn.style.display = "block";

        rejectBtn.onclick = () =>
          updateStatus(loan.id, "RECHAZADO");
      }

      break;

    case "APROBADO":
      actionBtn.innerHTML = `
        <span class="material-symbols-outlined">inventory_2</span>
        Entregar al cliente
      `;

      actionBtn.className = "btn-approve";

      actionBtn.onclick = () =>
        updateStatus(loan.id, "PRESTADO");

      if (rejectBtn) {
        rejectBtn.style.display = "block";

        rejectBtn.onclick = () =>
          updateStatus(loan.id, "RECHAZADO");
      }

      break;

    case "PRESTADO":
      actionBtn.innerHTML = `
        <span class="material-symbols-outlined">assignment_return</span>
        Marcar como devuelto
      `;

      actionBtn.className = "btn-return";

      actionBtn.onclick = () =>
        markAsReturned(loan.id);

      break;

    case "RECHAZADO":
      actionBtn.innerHTML = `
        <span class="material-symbols-outlined">refresh</span>
        Re-evaluar
      `;

      actionBtn.className = "btn-reevaluate";

      actionBtn.onclick = () =>
        updateStatus(loan.id, "PENDIENTE");

      break;

    case "DEVUELTO":
      actionBtn.innerHTML = `
        <span class="material-symbols-outlined">done</span>
        Devuelto
      `;

      actionBtn.disabled = true;

      break;
  }
}

async function updateStatus(id, status) {
  try {
    const token = localStorage.getItem("token");

    const updatedLoan = {
      ...currentLoan,
      status
    };

    const res = await fetch(`${API_BASE}/loans/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${token}`
      },
      body: JSON.stringify(updatedLoan)
    });

    if (res.status === 401 || res.status === 403) {
      window.location.href = "/";
      return;
    }

    if (!res.ok) {
      let msg = "Error actualizando estado";

      try {
        const errorData = await res.json();

        msg = errorData.message || msg;

      } catch (_) {}

      throw new Error(msg);
    }

    await loadLoanDetail();

  } catch (err) {
    console.error(err);
    alert(err.message);
  }
}

async function markAsReturned(id) {
  try {
    const token = localStorage.getItem("token");

    const res = await fetch(`${API_BASE}/loans/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${token}`
      },
      body: JSON.stringify({
        status: "DEVUELTO",
        actual_return_date: new Date()
          .toISOString()
          .split("T")[0]
      })
    });

    if (res.status === 401 || res.status === 403) {
      window.location.href = "/";
      return;
    }

    if (!res.ok) {
      throw new Error("Error marcando como devuelto");
    }

    await loadLoanDetail();

  } catch (err) {
    console.error(err);
    alert("Error al marcar como devuelto");
  }
}
