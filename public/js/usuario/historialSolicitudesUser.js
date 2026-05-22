// historialSolicitudesUser.js

const API_BASE = "http://127.0.0.1:8000/api";
const PUBLIC_BASE = "http://127.0.0.1:8000";

const API_URL = `${API_BASE}/loans/my`;

const ITEMS_PER_PAGE = 4;

let loans = [];
let currentPage = 1;

document.addEventListener("DOMContentLoaded", () => {
  loadLoans();
  loadStats();
});

async function loadLoans() {
  try {
    const token = localStorage.getItem("token");

    const res = await fetch(API_URL, {
      headers: {
        "Authorization": "Bearer " + token,
        "Accept": "application/json"
      }
    });

    if (res.status === 401 || res.status === 403) {
      window.location.href = "/";
      return;
    }

    if (!res.ok) {
      throw new Error("Error al cargar préstamos");
    }

    loans = await res.json();

    // más recientes primero
    loans.reverse();

    renderPage(1);
    renderPagination();
    updateInfo();

  } catch (err) {
    console.error(err);
    alert("Error cargando historial");
  }
}

function renderPage(page) {
  currentPage = page;

  const tbody = document.getElementById("loansTableBody");

  tbody.innerHTML = "";

  if (loans.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-4">
          No tienes solicitudes aún
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

function createRow(loan) {

  const tr = document.createElement("tr");

  const equipment = loan.equipment || {};

  // ← IMPORTANTE
  // Laravel devuelve image_filename e image_url
  const imageUrl = equipment.image_url
    ? equipment.image_url
    : "https://via.placeholder.com/60?text=IMG";

  tr.innerHTML = `
    <td>
      <div class="d-flex align-items-center gap-3">

        <div class="equipment-thumb">
          <img
            src="${imageUrl}"
            alt="${equipment.name || 'Equipo'}"
            style="
              width:60px;
              height:60px;
              object-fit:cover;
              border-radius:10px;
              background:#f2f2f2;
            "
          />
        </div>

        <div>
          <p class="equipment-name mb-0 fw-bold">
            ${equipment.name || "Sin nombre"}
          </p>

          <p class="equipment-id mb-0 text-muted">
            ID: ${equipment.id || "-"}
          </p>
        </div>

      </div>
    </td>

    <td>
      ${formatDate(loan.request_date)}
    </td>

    <td>
      ${formatDate(loan.estimated_end_date)}
    </td>

    <td>
      ${renderStatus(loan.status)}
    </td>

    <td class="text-end">
      <a
        href="/user/prestamos/detalle/${loan.id}"
        class="btn-action-primary"
      >
        Ver Detalles
      </a>
    </td>
  `;

  return tr;
}

function renderStatus(status) {

  let className = "";
  let text = status;

  switch (status) {

    case "APROBADO":
      className = "badge-approved";
      text = "Aprobado";
      break;

    case "PENDIENTE":
      className = "badge-pending";
      text = "Pendiente";
      break;

    case "RECHAZADO":
      className = "badge-rejected";
      text = "Rechazado";
      break;

    case "PRESTADO":
      className = "badge-approved";
      text = "Prestado";
      break;

    case "DEVUELTO":
      className = "badge-returned";
      text = "Devuelto";
      break;
  }

  return `
    <span class="badge-status ${className}">
      <span class="badge-dot"></span>
      ${text}
    </span>
  `;
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

function renderPagination() {

  const totalPages = Math.ceil(loans.length / ITEMS_PER_PAGE);

  const container = document.getElementById("pagination");

  container.innerHTML = "";

  // anterior
  const prev = document.createElement("button");

  prev.className = "pag-btn icon-btn";

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

  container.appendChild(prev);

  // páginas
  for (let i = 1; i <= totalPages; i++) {

    const btn = document.createElement("button");

    btn.className = `
      pag-btn
      ${i === currentPage ? "active" : ""}
    `;

    btn.innerText = i;

    btn.onclick = () => {

      renderPage(i);

      renderPagination();
    };

    container.appendChild(btn);
  }

  // siguiente
  const next = document.createElement("button");

  next.className = "pag-btn icon-btn";

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

  container.appendChild(next);
}

function updateInfo() {

  const info = document.querySelector(".page-info");

  if (!info) return;

  const total = loans.length;

  if (total === 0) {
    info.textContent = "No hay préstamos";
    return;
  }

  const start = (currentPage - 1) * ITEMS_PER_PAGE + 1;

  const end = Math.min(
    currentPage * ITEMS_PER_PAGE,
    total
  );

  info.textContent = `
    Mostrando ${start} - ${end} de ${total} préstamos
  `;
}

async function loadStats() {

  try {

    const token = localStorage.getItem("token");

    const res = await fetch(API_URL, {
      headers: {
        "Authorization": "Bearer " + token,
        "Accept": "application/json"
      }
    });

    if (!res.ok) return;

    const data = await res.json();

    const total = data.length;

    const pendientes = data.filter(l =>
      l.status?.toUpperCase() === "PENDIENTE"
    ).length;

    const aprobados = data.filter(l =>
      ["APROBADO", "PRESTADO"].includes(
        l.status?.toUpperCase()
      )
    ).length;

    const devueltos = data.filter(l =>
      l.status?.toUpperCase() === "DEVUELTO"
    ).length;

    const pendientesEl =
      document.getElementById("pendientes");

    const aprobadosEl =
      document.getElementById("aprobados");

    const totalEl =
      document.getElementById("totalPrestamos");

    const devueltosEl =
      document.getElementById("devueltos");

    if (pendientesEl) {
      pendientesEl.innerText = pendientes;
    }

    if (aprobadosEl) {
      aprobadosEl.innerText = aprobados;
    }

    if (totalEl) {
      totalEl.innerText = total;
    }

    if (devueltosEl) {
      devueltosEl.innerText = devueltos;
    }

  } catch (err) {

    console.error(err);
  }
}
