// verSolicitudUser.js
document.addEventListener("DOMContentLoaded", async () => {
  const token = localStorage.getItem("jwt");

  if (!token) {
    window.location.href = "/login";
    return;
  }

  // 1. Obtener ID desde la URL
  const pathParts = window.location.pathname.split("/");
  const loanId = pathParts[pathParts.length - 1];

  try {
    const isAdmin = localStorage.getItem("role") === "ROLE_ADMIN";

    // Si es admin, entonces cargo los datos desde el endpoint de admin
    const url = isAdmin
      ? `/loans/${loanId}`
      : `/loans/my/${loanId}`;

    const response = await fetch(url, {
      headers: {
        "Authorization": "Bearer " + token
      }
    });

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
  const user = loan.user;
  const equipment = loan.equipment;

  //  Usuario
  document.getElementById("nombreUsuario").textContent = user.fullName;
  document.getElementById("idUsuario").textContent = user.id;
  document.getElementById("roleUsuario").textContent = user.role?.name || user.role || "N/A";
  document.getElementById("correoUsuario").textContent = user.email;

  //  Equipo
  document.querySelector(".equipment-img").src = getImageUrl(equipment.imageFilename);

  document.getElementById("nombreEquipo").textContent = equipment.name;
  document.getElementById("idEquipo").textContent = "ID: " + equipment.id;
  document.getElementById("typeEquipo").innerHTML = `
        <span class="material-symbols-outlined" style="font-size:1rem;">laptop_mac</span>
        ${equipment.category || "Sin categoría"}
    `;

  //  Fechas
  document.getElementById("fechaSolicitud").textContent = formatDate(loan.requestDate);
  document.getElementById("fechaDevolucionEstimada").textContent = formatDate(loan.estimatedEndDate);

  //  Duración
  const dias = calcularDias(loan.requestDate, loan.estimatedEndDate);
  document.getElementById("duracion").textContent = `(Duración: ${dias} días)`;

  // Justificación
  document.querySelector(".purpose-quote").textContent = `"${loan.justification}"`;

  // Estado
  const estado = formatStatus(loan.status);

  document.querySelector(".btn-approve").innerHTML = `
        <span class="material-symbols-outlined">info</span>
        ${estado}
    `;

  document.querySelector(".status-badge").innerHTML = `
        <span class="material-symbols-outlined">info</span>
        ${estado}
    `;
}

function formatDate(dateStr) {
  const date = new Date(dateStr);
  return date.toLocaleDateString("es-CR", {
    day: "numeric",
    month: "short",
    year: "numeric"
  });
}

function calcularDias(inicio, fin) {
  const d1 = new Date(inicio);
  const d2 = new Date(fin);

  const diff = d2 - d1;
  return Math.ceil(diff / (1000 * 60 * 60 * 24));
}

function getImageUrl(filename) {
  if (!filename) {
    return "/img/default.png";
  }
  return `/uploads/equipment/${filename}`;
}

function formatStatus(status) {
  switch (status) {
    case "PENDIENTE": return "Pendiente";
    case "APROBADO": return "Aprobado";
    case "RECHAZADO": return "Rechazado";
    case "DEVUELTO": return "Devuelto";
    default: return status;
  }
}
