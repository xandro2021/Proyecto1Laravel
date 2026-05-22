// catalogoUser.js
(() => {

    alert('funciona');
    console.log('hola');
    const API_URL = "http://127.0.0.1:8000/api/equipment";
    const ITEMS_PER_PAGE = 8;

    let equipments = [];
    let currentPage = 1;

    // ─────────────────────────────────────────────
    // Obtener datos
    // ─────────────────────────────────────────────
    async function loadEquipments() {
        try {
            const token = localStorage.getItem("token");

            const res = await fetch(API_URL, {
                headers: {
                    "Authorization": `Bearer ${token}`
                }
            });

            if (res.status === 401 || res.status === 403) {
                window.location.href = "/";
                return;
            }

            if (!res.ok) {
                throw new Error("Error al cargar equipos");
            }

            const response = await res.json();

            // Laravel ahora devuelve:
            // { message: "...", data: [...] }

            equipments = response.data || [];

            renderPage(1);
            renderPagination();

        } catch (err) {
            console.error(err);

            const container = document.getElementById("equipmentContainer");

            if (container) {
                container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        Error al cargar equipos.
                    </div>
                </div>
            `;
            }
        }
    }

    // ─────────────────────────────────────────────
    // Renderizar tarjetas
    // ─────────────────────────────────────────────
    function renderPage(page) {
        currentPage = page;

        const container = document.getElementById("equipmentContainer");

        if (!container) return;

        container.innerHTML = "";

        if (equipments.length === 0) {
            container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-secondary text-center">
                    No hay equipos disponibles.
                </div>
            </div>
        `;
            return;
        }

        const start = (page - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;

        const pageItems = equipments.slice(start, end);

        pageItems.forEach(eq => {
            const card = createCard(eq);
            container.appendChild(card);
        });
    }

    // ─────────────────────────────────────────────
    // Crear tarjeta
    // ─────────────────────────────────────────────
    function createCard(eq) {
        const col = document.createElement("div");

        col.className = "col-12 col-sm-6 col-lg-4 col-xl-3";

        const statusClass = getStatusClass(eq.status);
        const statusText = eq.status || "DESCONOCIDO";

        // usar image_url si viene del backend
        const imageUrl =
            eq.image_url ||
            "https://via.placeholder.com/300x200";

        col.innerHTML = `
        <div class="card-custom">
            <div class="card-img-wrapper">
                <img
                    src="${imageUrl}"
                    alt="${eq.name || 'Equipo'}"
                >

                <span class="badge-custom ${statusClass}">
                    ${statusText}
                </span>
            </div>

            <div class="card-body-custom">
                <div class="d-flex justify-content-between mb-2">
                    <h5 class="fw-bold text-custom-primary">
                        ${eq.name || "Sin nombre"}
                    </h5>

                    <span class="card-code">
                        ID-${eq.id || "-"}
                    </span>
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

    // ─────────────────────────────────────────────
    // Botón según estado
    // ─────────────────────────────────────────────
    function renderButton(eq) {

        switch (eq.status) {

            case "DISPONIBLE":
                return `
                <a
                    href="/user/catalogo/solicitud/${eq.id}"
                    class="btn-card btn-card--disponible"
                >
                    <span class="material-symbols-outlined">
                        calendar_today
                    </span>

                    Solicitar Préstamo
                </a>
            `;

            case "OCUPADO":
                return `
                <button class="btn-card btn-disabled" disabled>
                    <span class="material-symbols-outlined">
                        event_busy
                    </span>

                    No Disponible
                </button>
            `;

            case "MANTENIMIENTO":
                return `
                <button class="btn-card btn-disabled" disabled>
                    <span class="material-symbols-outlined">
                        build
                    </span>

                    En Mantenimiento
                </button>
            `;

            default:
                return `
                <button class="btn-card btn-disabled" disabled>
                    Estado desconocido
                </button>
            `;
        }
    }

    // ─────────────────────────────────────────────
    // Clases visuales
    // ─────────────────────────────────────────────
    function getStatusClass(status) {

        switch (status) {

            case "DISPONIBLE":
                return "badge-disponible";

            case "OCUPADO":
                return "badge-solicitud";

            case "MANTENIMIENTO":
                return "badge-mantenimiento";

            default:
                return "";
        }
    }

    // ─────────────────────────────────────────────
    // Paginación
    // ─────────────────────────────────────────────
    function renderPagination() {

        const totalPages = Math.ceil(
            equipments.length / ITEMS_PER_PAGE
        );

        const container = document.getElementById("pagination");

        if (!container) return;

        container.innerHTML = "";

        if (totalPages <= 1) {
            return;
        }

        // ── Botón anterior ──
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

        // ── Botones de página ──
        for (let i = 1; i <= totalPages; i++) {

            const btn = document.createElement("button");

            btn.className =
                "pag-btn " +
                (i === currentPage ? "active" : "");

            btn.innerText = i;

            btn.onclick = () => {
                renderPage(i);
                renderPagination();
            };

            container.appendChild(btn);
        }

        // ── Botón siguiente ──
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

    // ─────────────────────────────────────────────
    // Inicializar
    // ─────────────────────────────────────────────
    document.addEventListener(
        "DOMContentLoaded",
        loadEquipments
    );
})();

