// gestionPrestamosAdmin.js
const API_URL = "/loans";
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

    if (!res.ok) {
      const text = await res.text();
      console.error(text);
      throw new Error(text);
    }

    loans = await res.json();

    renderPage(1);
    renderPagination();
    updateInfo();
    updateMetrics();

  } catch (err) {
    console.error(err);
    alert("Error cargando préstamos");
  }
}

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

function createRow(loan) {
  const tr = document.createElement("tr");

  const user = loan.user || {};
  const equipment = loan.equipment || {};

  // usar fullName si existe, si no username
  const displayName = user.fullName || user.username || "Sin usuario";

  // email correcto
  const email = user.email || "";

  // iniciales basadas en nombre real
  const initials = getInitials(displayName);

  tr.innerHTML = `
    <td>
      <div class="d-flex align-items-center gap-3">
        <div class="user-avatar">${initials}</div>
        <div>
          <p class="user-name mb-0">${displayName}</p>
          <p class="user-dept mb-0">${email}</p>
        </div>
      </div>
    </td>

    <td>
      <p class="equip-name mb-0">${equipment.name || 'Sin equipo'}</p>
      <p class="equip-id mb-0">ID: ${equipment.id || '-'}</p>
    </td>

    <td class="date-cell">${formatDate(loan.requestDate)}</td>

    <td>${renderStatus(loan.status)}</td>

    <td>
      <div class="d-flex justify-content-end gap-2 align-items-center">
        <a href="/admin/prestamos/detalle/${loan.id}" class="btn-details">
          Ver Detalles
        </a>

        ${renderAdminActions(loan)}
      </div>
    </td>
  `;

  return tr;
}

function renderAdminActions(loan) {

  if (loan.status === "PENDIENTE") {
    return `
      <button class="btn-approve" onclick="updateStatus(${loan.id}, 'APROBADO')">
        Aprobar
      </button>
      <button class="btn-reject" onclick="updateStatus(${loan.id}, 'RECHAZADO')">
        Rechazar
      </button>
    `;
  }

  if (loan.status === "APROBADO") {
    return `
      <button class="btn-approve" onclick="updateStatus(${loan.id}, 'PRESTADO')">
        Entregar articulo al cliente
      </button>
      <button class="btn-reject" onclick="updateStatus(${loan.id}, 'RECHAZADO')">
        Rechazar
      </button>
    `;
  }

  if (loan.status === "PRESTADO") {
    return `
      <button class="btn-return" onclick="markAsReturned(${loan.id})">
        Marcar como devuelto
      </button>
      <button class="btn-reject" onclick="updateStatus(${loan.id}, 'RECHAZADO')">
        Rechazar
      </button>
    `;
  }

  if (loan.status === "RECHAZADO") {
    return `
      <button class="btn-reevaluate" onclick="updateStatus(${loan.id}, 'PENDIENTE')">
        Re-evaluar
      </button>
      <button class="btn-delete" onclick="deleteLoan(${loan.id})">
      Eliminar
    </button>
    `;
  }

  if (loan.status === "DEVUELTO") {
    return `
      <button class="btn-approve" disabled>Devuelto</button>
      <button class="btn-delete" onclick="deleteLoan(${loan.id})">
      Eliminar
    </button>
    `;
  }

  return "";
}

async function updateStatus(id, status) {
  try {
    const token = localStorage.getItem("jwt");

    const loan = loans.find(l => l.id === id);

    const updatedLoan = {
      ...loan,
      status: status
    };

    const res = await fetch(`/loans/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "Authorization": "Bearer " + token
      },
      body: JSON.stringify(updatedLoan)
    });

    if (!res.ok) {
      let errorMessage = "Error actualizando estado";

      try {
        const errorData = await res.json();
        errorMessage = errorData.message || errorMessage;
      } catch (e) {
        // si no viene JSON, dejamos el mensaje genérico
      }

      throw new Error(errorMessage);
    }

    // recargar datos
    await loadLoans();

  } catch (err) {
    console.error(err);
    alert(err.message);
  }
}

async function markAsReturned(id) {
  try {
    const token = localStorage.getItem("jwt");

    const res = await fetch(`/loans/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "Authorization": "Bearer " + token
      },
      body: JSON.stringify({
        status: "DEVUELTO",
        actualReturnDate: new Date().toISOString().split("T")[0]
      })
    });

    if (!res.ok) throw new Error("Error marcando como devuelto");

    await loadLoans();

  } catch (err) {
    console.error(err);
    alert("Error al marcar como devuelto");
  }
}

function deleteLoan(id) {

  if (!confirm("¿Eliminar esta solicitud?")) {
    return;
  }

  const token = localStorage.getItem("jwt");

  fetch("/loans/" + id, {
    method: "DELETE",
    headers: {
      "Authorization": "Bearer " + token
    }
  })
    .then(res => {
      if (!res.ok) {
        alert("Error al eliminar");
        return;
      }

      // volver a cargar la tabla
      loadLoans();
    })
    .catch(err => {
      console.error(err);
      alert("Error");
    });
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

function renderPagination() {
  const totalPages = Math.ceil(loans.length / ITEMS_PER_PAGE);
  const container = document.querySelector(".page-btn").parentElement;

  container.innerHTML = "";

  // Prev
  const prev = document.createElement("button");
  prev.className = "page-btn page-btn-arrow";
  prev.innerHTML = `<span class="material-symbols-outlined">chevron_left</span>`;
  prev.onclick = () => {
    if (currentPage > 1) {
      renderPage(currentPage - 1);
      renderPagination();
    }
  };
  container.appendChild(prev);

  // Pages
  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.className = "page-btn " + (i === currentPage ? "active" : "");
    btn.innerText = i;

    btn.onclick = () => {
      renderPage(i);
      renderPagination();
    };

    container.appendChild(btn);
  }

  // Next
  const next = document.createElement("button");
  next.className = "page-btn page-btn-arrow";
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
  const info = document.querySelector(".pagination-info");

  const total = loans.length;
  const showing = Math.min(currentPage * ITEMS_PER_PAGE, total);

  info.textContent = `Mostrando ${showing} de ${total} solicitudes`;
}

function updateMetrics() {
  const pendientes = loans.filter(l => l.status === "PENDIENTE").length;
  const aprobados = loans.filter(l => l.status === "APROBADO").length;
  const rechazados = loans.filter(l => l.status === "RECHAZADO").length;

  document.querySelectorAll(".metric-value")[0].textContent = pendientes;
  document.querySelectorAll(".metric-value")[1].textContent = aprobados;
  document.querySelectorAll(".metric-value")[2].textContent = rechazados;
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

function getInitials(name) {
  return name
    .split(" ")
    .map(n => n[0])
    .join("")
    .toUpperCase()
    .substring(0, 2);
}

