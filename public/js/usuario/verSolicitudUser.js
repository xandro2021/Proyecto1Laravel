// verSolicitudUser.js

const API_BASE = "http://127.0.0.1:8000/api";
const PUBLIC_BASE = "http://127.0.0.1:8000";

document.addEventListener("DOMContentLoaded", async () => {

  const token = localStorage.getItem("token");

  if (!token) {
    window.location.href = "/";
    return;
  }

  // Obtener ID desde URL
  const pathParts = window.location.pathname.split("/");

  const loanId = pathParts[pathParts.length - 1];

  try {

    // En Laravel NO existe /loans/my/{id}
    // Se usa directamente /api/loans/{id}
    const response = await fetch(
      `${API_BASE}/loans/${loanId}`,
      {
        headers: {
          "Authorization": "Bearer " + token,
          "Accept": "application/json"
        }
      }
    );

    if (response.status === 401 || response.status === 403) {
      window.location.href = "/";
      return;
    }

    if (!response.ok) {
      throw new Error("No se pudo cargar la solicitud");
    }

    const loan = await response.json();

    renderLoan(loan);

  } catch (error) {

    console.error(error);

    alert("Error cargando la solicitud");
  }
});

function renderLoan(loan) {

  const user = loan.user || {};

  const equipment = loan.equipment || {};

  // =========================
  // USUARIO
  // =========================

  setText("nombreUsuario", user.full_name);

  setText("idUsuario", user.id);

  setText(
    "roleUsuario",
    user.role || "N/A"
  );

  setText(
    "correoUsuario",
    user.email
  );

  // =========================
  // EQUIPO
  // =========================

  const img = document.querySelector(".equipment-img");

  if (img) {

    img.src = equipment.image_url
      ? equipment.image_url
      : "/img/default.png";
  }

  setText(
    "nombreEquipo",
    equipment.name
  );

  setText(
    "idEquipo",
    "ID: " + (equipment.id || "-")
  );

  const typeEl = document.getElementById("typeEquipo");

  if (typeEl) {

    typeEl.innerHTML = `
      <span
        class="material-symbols-outlined"
        style="font-size:1rem;"
      >
        laptop_mac
      </span>

      ${equipment.type || "Sin categoría"}
    `;
  }

  // =========================
  // FECHAS
  // =========================

  setText(
    "fechaSolicitud",
    formatDate(loan.request_date)
  );

  setText(
    "fechaDevolucionEstimada",
    formatDate(loan.estimated_end_date)
  );

  // =========================
  // DURACIÓN
  // =========================

  const dias = calcularDias(
    loan.request_date,
    loan.estimated_end_date
  );

  const duracionEl =
    document.getElementById("duracion");

  if (duracionEl) {

    duracionEl.textContent =
      dias <= 0
        ? "(Mismo día)"
        : `(Duración: ${dias} día${dias !== 1 ? "s" : ""})`;
  }

  // =========================
  // JUSTIFICACIÓN
  // =========================

  const quote =
    document.querySelector(".purpose-quote");

  if (quote) {

    quote.textContent =
      `"${loan.justification || "Sin justificación"}"`;
  }

  // =========================
  // ESTADO
  // =========================

  renderStatus(loan.status);
}

function renderStatus(status) {

  const badge =
    document.querySelector(".status-badge");

  const button =
    document.querySelector(".btn-approve");

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

  const html = `
    <span class="material-symbols-outlined">
      ${icon}
    </span>

    ${text}
  `;

  if (badge) {
    badge.innerHTML = html;
  }

  if (button) {
    button.innerHTML = html;
  }
}

function formatDate(dateStr) {

  if (!dateStr) return "-";

  const date = new Date(dateStr);

  return date.toLocaleDateString("es-CR", {
    day: "2-digit",
    month: "short",
    year: "numeric"
  });
}

function parseLocalDate(dateStr) {

  if (!dateStr) return null;

  // Laravel manda:
  // 2026-05-22T00:00:00.000000Z

  const onlyDate = dateStr.split("T")[0];

  const [year, month, day] =
    onlyDate.split("-");

  return new Date(year, month - 1, day);
}

function calcularDias(inicio, fin) {

  const d1 = parseLocalDate(inicio);

  const d2 = parseLocalDate(fin);

  if (!d1 || !d2) return 0;

  const diff = d2 - d1;

  return Math.ceil(
    diff / (1000 * 60 * 60 * 24)
  );
}

function setText(id, value) {

  const el = document.getElementById(id);

  if (!el) return;

  el.textContent = value || "-";
}
