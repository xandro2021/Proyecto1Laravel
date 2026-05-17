// catalogoUser.js
const API_URL = "/equipment";
const ITEMS_PER_PAGE = 8;

let equipments = [];
let currentPage = 1;

// Obtener datos
async function loadEquipments() {
    try {
        const token = localStorage.getItem("jwt");

        const res = await fetch(API_URL, {
            headers: {
                "Authorization": "Bearer " + token
            }
        });


        if (res.status === 401 || res.status === 403) {
            window.location.href = '/';
        }

        if (!res.ok) throw new Error("Error al cargar equipos");

        equipments = await res.json();

        renderPage(1);
        renderPagination();

    } catch (err) {
        console.error(err);
    }
}

// Renderizar tarjetas
function renderPage(page) {
    currentPage = page;

    const container = document.getElementById("equipmentContainer");
    container.innerHTML = "";

    const start = (page - 1) * ITEMS_PER_PAGE;
    const end = start + ITEMS_PER_PAGE;

    const pageItems = equipments.slice(start, end);

    pageItems.forEach(eq => {
        const card = createCard(eq);
        container.appendChild(card);
    });
}

// Crear tarjeta
function createCard(eq) {
    const col = document.createElement("div");
    col.className = "col-12 col-sm-6 col-lg-4 col-xl-3";

    const statusClass = getStatusClass(eq.status);
    const statusText = eq.status;

    const imageUrl = eq.imageFilename
        ? `/uploads/equipment/${eq.imageFilename}`
        : "https://via.placeholder.com/300x200";

    col.innerHTML = `
        <div class="card-custom">
            <div class="card-img-wrapper">
                <img src="${imageUrl}">
                <span class="badge-custom ${statusClass}">
                    ${statusText}
                </span>
            </div>

            <div class="card-body-custom">
                <div class="d-flex justify-content-between mb-2">
                    <h5 class="fw-bold text-custom-primary">${eq.name}</h5>
                    <span class="card-code">ID-${eq.id}</span>
                </div>

                <p class="text-muted small flex-grow-1">
                    ${eq.description || "Sin descripción"}
                </p>

                ${renderButton(eq)}
            </div>
        </div>
    `;

    return col;
}

// Botón según estado
function renderButton(eq) {
    switch (eq.status) {
        case "DISPONIBLE":
            return `
                <a href="/user/catalogo/solicitud/${eq.id}" class="btn-card btn-card--disponible">
                    <span class="material-symbols-outlined">calendar_today</span>
                    Solicitar Préstamo
                </a>
            `;
        case "OCUPADO":
            return `
                <a href="#" class="btn-card btn-disabled">
                    <span class="material-symbols-outlined">event_busy</span>
                    No Disponible
                </a>
            `;
        case "MANTENIMIENTO":
            return `
                <a href="#" class="btn-card btn-disabled">
                    <span class="material-symbols-outlined">build</span>
                    En Mantenimiento
                </a>
            `;
        default:
            return "";
    }
}

// Clases visuales
function getStatusClass(status) {
    switch (status) {
        case "DISPONIBLE": return "badge-disponible";
        case "OCUPADO": return "badge-solicitud";
        case "MANTENIMIENTO": return "badge-mantenimiento";
        default: return "";
    }
}

// Paginación
function renderPagination() {
    const totalPages = Math.ceil(equipments.length / ITEMS_PER_PAGE);
    const container = document.getElementById("pagination");

    container.innerHTML = "";

    // botón anterior
    const prev = document.createElement("button");
    prev.className = "pag-btn icon-btn";
    prev.innerHTML = `<span class="material-symbols-outlined">chevron_left</span>`;
    prev.onclick = () => {
        if (currentPage > 1) {
            renderPage(currentPage - 1);
        }
    };
    container.appendChild(prev);

    // páginas
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

    // botón siguiente
    const next = document.createElement("button");
    next.className = "pag-btn icon-btn";
    next.innerHTML = `<span class="material-symbols-outlined">chevron_right</span>`;
    next.onclick = () => {
        if (currentPage < totalPages) {
            renderPage(currentPage + 1);
        }
    };
    container.appendChild(next);
}

// Inicializar
document.addEventListener("DOMContentLoaded", loadEquipments);
