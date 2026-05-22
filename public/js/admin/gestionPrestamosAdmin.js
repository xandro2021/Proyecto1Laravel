// gestionPrestamosAdmin.js

const PUBLIC_BASE = "http://127.0.0.1:8000";
const API_BASE = "http://127.0.0.1:8000/api";
const API_URL = `${API_BASE}/loans`;
const ITEMS_PER_PAGE = 4;

let loans = [];
let currentPage = 1;

document.addEventListener("DOMContentLoaded", loadLoans);

// ─────────────────────────────────────────────
// CARGAR PRÉSTAMOS
// ─────────────────────────────────────────────
async function loadLoans() {

  try {

    const token = localStorage.getItem("token");

    const res = await fetch(API_URL, {
      headers: {
        "Authorization": `Bearer ${token}`,
        "Accept": "application/json",
        "Accept-Language": localStorage.getItem("lang") || "es"
      }
    });

    if (res.status === 401 || res.status === 403) {
      window.location.href = "/";
      return;
    }

    if (!res.ok) {
      throw new Error("Error cargando préstamos");
    }

    const response = await res.json();

    // ← IMPORTANTE
    loans = response.data || [];

    // Más recientes primero
    loans.reverse();

    renderPage(1);
    renderPagination();
    updateInfo();
    updateMetrics();

  } catch (err) {

    console.error("Error:", err);

    const tbody = document.querySelector(".data-table tbody");

    if (tbody) {
      tbody.innerHTML = `
        <tr>
          <td colspan="5" class="text-center py-4 text-danger">
            Error al cargar préstamos
          </td>
        </tr>
      `;
    }
  }
}

// ─────────────────────────────────────────────
// RENDER PAGINA
// ─────────────────────────────────────────────
function renderPage(page) {

  currentPage = page;

  const tbody = document.querySelector(".data-table tbody");

  tbody.innerHTML = "";

  if (loans.length === 0) {

    tbody.innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-4">
          No hay solicitudes
        </td>
      </tr>
    `;

    return;
  }

  const start = (page - 1) * ITEMS_PER_PAGE;
  const end = start + ITEMS_PER_PAGE;

  const pageItems = loans.slice(start, end);

  pageItems.forEach(loan => {
    tbody.appendChild(createRow(loan));
  });

  updateInfo();
}

// ─────────────────────────────────────────────
// CREAR FILA
// ─────────────────────────────────────────────
function createRow(loan) {

  const tr = document.createElement("tr");

  const user = loan.user || {};
  const equipment = loan.equipment || {};

  // Laravel devuelve full_name
  const displayName =
    user.full_name ||
    user.username ||
    "Sin usuario";

  const email = user.email || "";

  const initials = getInitials(displayName);

  tr.innerHTML = `
    <td>
      <div class="d-flex align-items-center gap-3">

        <div class="user-avatar">
          ${initials}
        </div>

        <div>
          <p class="user-name mb-0">
            ${displayName}
          </p>

          <p class="user-dept mb-0">
            ${email}
          </p>
        </div>

      </div>
    </td>

    <td>

      <div class="d-flex align-items-center gap-3">

        <img
          src="${equipment.image_url || '/img/no-image.png'}"
          alt="${equipment.name || 'Equipo'}"
          style="
            width:60px;
            height:60px;
            object-fit:cover;
            border-radius:10px;
            background:#f3f4f6;
          "
        >

        <div>
          <p class="equip-name mb-0">
            ${equipment.name || 'Sin equipo'}
          </p>

          <p class="equip-id mb-0">
            ID: ${equipment.id || '-'}
          </p>
        </div>

      </div>

    </td>

    <td class="date-cell">
      ${formatDate(loan.request_date)}
    </td>

    <td>
      ${renderStatus(loan.status)}
    </td>

    <td>

      <div class="d-flex justify-content-end gap-2 align-items-center">

        <a
          href="/admin/prestamos/detalle/${loan.id}"
          class="btn-details"
        >
          Ver detalles
        </a>

        ${renderAdminActions(loan)}

      </div>

    </td>
  `;

  return tr;
}

// ─────────────────────────────────────────────
// BOTONES ADMIN
// ─────────────────────────────────────────────
function renderAdminActions(loan) {

  if (loan.status === "PENDIENTE") {

    return `
      <button
        class="btn-approve"
        onclick="updateStatus(${loan.id}, 'APROBADO')"
      >
        Aprobar
      </button>

      <button
        class="btn-reject"
        onclick="updateStatus(${loan.id}, 'RECHAZADO')"
      >
        Rechazar
      </button>
    `;
  }

  if (loan.status === "APROBADO") {

    return `
      <button
        class="btn-approve"
        onclick="updateStatus(${loan.id}, 'PRESTADO')"
      >
        Entregar
      </button>

      <button
        class="btn-reject"
        onclick="updateStatus(${loan.id}, 'RECHAZADO')"
      >
        Rechazar
      </button>
    `;
  }

  if (loan.status === "PRESTADO") {

    return `
      <button
        class="btn-return"
        onclick="markAsReturned(${loan.id})"
      >
        Marcar devuelto
      </button>
    `;
  }

  if (loan.status === "RECHAZADO") {

    return `
      <button
        class="btn-reevaluate"
        onclick="updateStatus(${loan.id}, 'PENDIENTE')"
      >
        Re-evaluar
      </button>

      <button
        class="btn-delete"
        onclick="deleteLoan(${loan.id})"
      >
        Eliminar
      </button>
    `;
  }

  if (loan.status === "DEVUELTO") {

    return `
      <button class="btn-approve" disabled>
        Devuelto
      </button>

      <button
        class="btn-delete"
        onclick="deleteLoan(${loan.id})"
      >
        Eliminar
      </button>
    `;
  }

  return "";
}

// ─────────────────────────────────────────────
// ACTUALIZAR ESTADO
// ─────────────────────────────────────────────
async function updateStatus(id, status) {

  try {

    const token = localStorage.getItem("token");

    const res = await fetch(`${API_URL}/${id}`, {

      method: "PUT",

      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${token}`,
        "Accept": "application/json",
        "Accept-Language": localStorage.getItem("lang") || "es"
      },

      body: JSON.stringify({
        status: status
      })
    });

    const data = await res.json();

    if (!res.ok) {
      throw new Error(data.message || "Error actualizando estado");
    }

    await loadLoans();

  } catch (err) {

    console.error(err);

    alert(err.message);
  }
}

// ─────────────────────────────────────────────
// MARCAR DEVUELTO
// ─────────────────────────────────────────────
async function markAsReturned(id) {

  try {

    const token = localStorage.getItem("token");

    const res = await fetch(`${API_URL}/${id}`, {

      method: "PUT",

      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${token}`,
        "Accept": "application/json",
        "Accept-Language": localStorage.getItem("lang") || "es"
      },

      body: JSON.stringify({
        status: "DEVUELTO"
      })
    });

    const data = await res.json();

    if (!res.ok) {
      throw new Error(data.message || "Error marcando como devuelto");
    }

    await loadLoans();

  } catch (err) {

    console.error(err);

    alert(err.message);
  }
}

// ─────────────────────────────────────────────
// ELIMINAR
// ─────────────────────────────────────────────
async function deleteLoan(id) {

  if (!confirm("¿Eliminar esta solicitud?")) {
    return;
  }

  try {

    const token = localStorage.getItem("token");

    const res = await fetch(`${API_URL}/${id}`, {

      method: "DELETE",

      headers: {
        "Authorization": `Bearer ${token}`,
        "Accept": "application/json",
        "Accept-Language": localStorage.getItem("lang") || "es"
      }
    });

    const data = await res.json();

    if (!res.ok) {
      throw new Error(data.message || "Error eliminando préstamo");
    }

    await loadLoans();

  } catch (err) {

    console.error(err);

    alert(err.message);
  }
}

// ─────────────────────────────────────────────
// BADGES
// ─────────────────────────────────────────────
function renderStatus(status) {

  let className = "";
  let text = status;

  switch (status) {

    case "APROBADO":
      className = "badge-approved";
      break;

    case "PENDIENTE":
      className = "badge-pending";
      break;

    case "PRESTADO":
      className = "badge-approved";
      break;

    case "RECHAZADO":
      className = "badge-rejected";
      break;

    case "DEVUELTO":
      className = "badge-returned";
      break;
  }

  return `
    <span class="${className}">
      <span class="badge-dot"></span>
      ${text}
    </span>
  `;
}

// ─────────────────────────────────────────────
// PAGINACIÓN
// ─────────────────────────────────────────────
function renderPagination() {

  const totalPages = Math.ceil(loans.length / ITEMS_PER_PAGE);

  const paginationWrapper = document.querySelector(".pagination-wrapper");

  if (!paginationWrapper) return;

  paginationWrapper.innerHTML = "";

  // PREV
  const prev = document.createElement("button");

  prev.className = "page-btn page-btn-arrow";

  prev.innerHTML = `
    <span class="material-symbols-outlined">
      chevron_left
    </span>
  `;

  prev.disabled = currentPage === 1;

  prev.onclick = () => {

    if (currentPage > 1) {

      renderPage(currentPage - 1);
      renderPagination();
    }
  };

  paginationWrapper.appendChild(prev);

  // PAGES
  for (let i = 1; i <= totalPages; i++) {

    const btn = document.createElement("button");

    btn.className =
      "page-btn " +
      (i === currentPage ? "active" : "");

    btn.innerText = i;

    btn.onclick = () => {

      renderPage(i);
      renderPagination();
    };

    paginationWrapper.appendChild(btn);
  }

  // NEXT
  const next = document.createElement("button");

  next.className = "page-btn page-btn-arrow";

  next.innerHTML = `
    <span class="material-symbols-outlined">
      chevron_right
    </span>
  `;

  next.disabled = currentPage === totalPages;

  next.onclick = () => {

    if (currentPage < totalPages) {

      renderPage(currentPage + 1);
      renderPagination();
    }
  };

  paginationWrapper.appendChild(next);
}

// ─────────────────────────────────────────────
// INFO
// ─────────────────────────────────────────────
function updateInfo() {

  const info = document.querySelector(".pagination-info");

  if (!info) return;

  const total = loans.length;

  if (total === 0) {
    info.textContent = "No hay solicitudes";
    return;
  }

  const start = (currentPage - 1) * ITEMS_PER_PAGE + 1;

  const end = Math.min(
    currentPage * ITEMS_PER_PAGE,
    total
  );

  info.textContent =
    `Mostrando ${start}-${end} de ${total} solicitudes`;
}

// ─────────────────────────────────────────────
// MÉTRICAS
// ─────────────────────────────────────────────
function updateMetrics() {

  const pendientes =
    loans.filter(l => l.status === "PENDIENTE").length;

  const aprobados =
    loans.filter(l => l.status === "APROBADO").length;

  const rechazados =
    loans.filter(l => l.status === "RECHAZADO").length;

  const metrics =
    document.querySelectorAll(".metric-value");

  if (metrics[0]) metrics[0].textContent = pendientes;
  if (metrics[1]) metrics[1].textContent = aprobados;
  if (metrics[2]) metrics[2].textContent = rechazados;
}

// ─────────────────────────────────────────────
// FORMAT DATE
// ─────────────────────────────────────────────
function formatDate(dateString) {

  if (!dateString) {
    return "-";
  }

  const date = new Date(dateString);

  return date.toLocaleDateString("es-CR", {
    day: "2-digit",
    month: "short",
    year: "numeric"
  });
}

// ─────────────────────────────────────────────
// INICIALES
// ─────────────────────────────────────────────
function getInitials(name) {

  return name
    .split(" ")
    .map(n => n[0])
    .join("")
    .toUpperCase()
    .substring(0, 2);
}
