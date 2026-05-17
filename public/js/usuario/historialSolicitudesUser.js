// historialSolicitudesUser.js
const API_URL = "/loans/my";
const ITEMS_PER_PAGE = 4;

let loans = [];
let currentPage = 1;

document.addEventListener("DOMContentLoaded", loadLoans);

async function loadLoans() {
  try {
    const token = localStorage.getItem("jwt");

    const res = await fetch(API_URL, {
      headers: {
        "Authorization": "Bearer " + token
      }
    });

    if (res.status === 401 || res.status === 403) {
      window.location.href = "/";
      return;
    }

    if (!res.ok) throw new Error("Error al cargar préstamos");

    loans = await res.json();

    renderPage(1);
    renderPagination();
    updateInfo();

  } catch (err) {
    console.error(err);
    alert("Error cargando historial");
  }
}

function renderLoans(loans) {
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

  loans.forEach(loan => {
    const row = createRow(loan);
    tbody.appendChild(row);
  });
}

function createRow(loan) {
  const tr = document.createElement("tr");

  const equipment = loan.equipment || {};

  const imageUrl = equipment.imageFilename
    ? `/uploads/equipment/${equipment.imageFilename}`
    : "https://via.placeholder.com/60";

  tr.innerHTML = `
    <td>
      <div class="d-flex align-items-center gap-3">
        <div class="equipment-thumb">
          <img src="${imageUrl}" alt="${equipment.name || 'Equipo'}" />
        </div>
        <div>
          <p class="equipment-name mb-0">${equipment.name || 'Sin nombre'}</p>
          <p class="equipment-id mb-0">ID: ${equipment.id || '-'}</p>
        </div>
      </div>
    </td>

    <td>${formatDate(loan.requestDate)}</td>
    <td>${formatDate(loan.estimatedEndDate)}</td>

    <td>${renderStatus(loan.status)}</td>

    <td class="text-end">
      <a href="/user/prestamos/detalle/${loan.id}" class="btn-action-primary">
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
      break;
    case "PENDIENTE":
      className = "badge-pending";
      break;
    case "RECHAZADO":
      className = "badge-rejected";
      break;
    case "DEVUELTO":
      className = "badge-returned";
      break;
  }

  return `
    <span class="badge-status ${className}">
      <span class="badge-dot"></span>
      ${text}
    </span>
  `;
}

function renderActions(loan) {
  return `
        <a href="/user/prestamos/detalle/${loan.id}" class="btn-action-primary">
            Ver Detalles
        </a>
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

function renderPagination() {
  const totalPages = Math.ceil(loans.length / ITEMS_PER_PAGE);
  const container = document.getElementById("pagination");

  container.innerHTML = "";

  // Botón anterior
  const prev = document.createElement("button");
  prev.className = "pag-btn icon-btn";
  prev.innerHTML = `<span class="material-symbols-outlined">chevron_left</span>`;
  prev.onclick = () => {
    if (currentPage > 1) {
      renderPage(currentPage - 1);
      renderPagination();
    }
  };
  container.appendChild(prev);

  // Botones de páginas
  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.className = "pag-btn " + (i === currentPage ? "active" : "");
    btn.innerText = i;

    btn.onclick = () => {
      renderPage(i);
      renderPagination();
    };

    container.appendChild(btn);
  }

  // Botón siguiente
  const next = document.createElement("button");
  next.className = "pag-btn icon-btn";
  next.innerHTML = `<span class="material-symbols-outlined">chevron_right</span>`;
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

  const total = loans.length;
  const showing = Math.min(currentPage * ITEMS_PER_PAGE, total);

  info.textContent = `Mostrando ${showing} de ${total} préstamos`;
}

fetch("http://localhost:8080/loans/my", {
  headers: {
    "Authorization": "Bearer " + localStorage.getItem("jwt")
  }
})
  .then(res => res.json())
  .then(data => {

    const total = data.length;

    const pendientes = data.filter(l =>
      l.status?.toUpperCase() === "PENDIENTE"
    ).length;

    const enUso = data.filter(l =>
      l.status?.toUpperCase() === "APROBADO"
    ).length;

    document.getElementById("pendientes").innerText = pendientes;
    document.getElementById("aprobados").innerText = enUso;
    document.getElementById("totalPrestamos").innerText = total;

  })
  .catch(err => console.log(err));