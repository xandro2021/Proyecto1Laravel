// verSolicitudUser.js

const PUBLIC_BASE = "http://127.0.0.1:8000";
const API_BASE = `${PUBLIC_BASE}/api`;

document.addEventListener("DOMContentLoaded", async () => {
    await loadLoan();
});

// ─────────────────────────────────────────────
// Cargar solicitud
// ─────────────────────────────────────────────
async function loadLoan() {
    try {
        const token = localStorage.getItem("token");

        if (!token) {
            window.location.href = "/";
            return;
        }

        const loanId = getLoanIdFromURL();

        if (!loanId) {
            alert("ID de solicitud inválido.");
            return;
        }

        const response = await fetch(
            `${API_BASE}/loans/${loanId}`,
            {
                headers: {
                    "Authorization": `Bearer ${token}`,
                    "Accept": "application/json"
                }
            }
        );

        if (response.status === 401 || response.status === 403) {
            window.location.href = "/";
            return;
        }

        if (!response.ok) {
            let errorMessage =
                "No se pudo cargar la solicitud.";

            try {
                const errorData = await response.json();
                errorMessage =
                    errorData.message || errorMessage;
            } catch (_) {}

            throw new Error(errorMessage);
        }

        const loan = await response.json();

        renderLoan(loan);

    } catch (error) {
        console.error(error);
        alert(error.message || "Error cargando la solicitud.");
    }
}

// ─────────────────────────────────────────────
// Obtener ID desde URL
// ─────────────────────────────────────────────
function getLoanIdFromURL() {
    const pathParts = window.location.pathname.split("/");

    const loanId =
        pathParts[pathParts.length - 1];

    return loanId || null;
}

// ─────────────────────────────────────────────
// Render principal
// ─────────────────────────────────────────────
function renderLoan(loan) {
    const user = loan.user || {};
    const equipment = loan.equipment || {};

    renderUser(user);
    renderEquipment(equipment);
    renderDates(loan);
    renderDuration(
        loan.request_date,
        loan.estimated_end_date
    );
    renderJustification(loan.justification);
    renderStatus(loan.status);
}

// ─────────────────────────────────────────────
// Usuario
// ─────────────────────────────────────────────
function renderUser(user) {
    setText(
        "nombreUsuario",
        user.full_name ||
        user.username ||
        "Sin nombre"
    );

    setText(
        "idUsuario",
        user.id || "-"
    );

    setText(
        "roleUsuario",
        user.role || "N/A"
    );

    setText(
        "correoUsuario",
        user.email || "-"
    );
}

// ─────────────────────────────────────────────
// Equipo
// ─────────────────────────────────────────────
function renderEquipment(equipment) {
    const img =
        document.querySelector(".equipment-img");

    if (img) {
        img.src =
            equipment.image_url ||
            "/img/no-image.png";

        img.alt =
            equipment.name || "Equipo";
    }

    setText(
        "nombreEquipo",
        equipment.name || "Sin nombre"
    );

    setText(
        "idEquipo",
        `ID: ${equipment.id || "-"}`
    );

    const typeEl =
        document.getElementById("typeEquipo");

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
}

// ─────────────────────────────────────────────
// Fechas
// ─────────────────────────────────────────────
function renderDates(loan) {
    setText(
        "fechaSolicitud",
        formatDate(loan.request_date)
    );

    setText(
        "fechaDevolucionEstimada",
        formatDate(loan.estimated_end_date)
    );
}

// ─────────────────────────────────────────────
// Duración
// ─────────────────────────────────────────────
function renderDuration(startDate, endDate) {
    const duracionEl =
        document.getElementById("duracion");

    if (!duracionEl) return;

    const dias =
        calcularDias(startDate, endDate);

    duracionEl.textContent =
        dias <= 0
            ? "(Mismo día)"
            : `(Duración: ${dias} día${dias !== 1 ? "s" : ""})`;
}

// ─────────────────────────────────────────────
// Justificación
// ─────────────────────────────────────────────
function renderJustification(justification) {
    const quote =
        document.querySelector(".purpose-quote");

    if (!quote) return;

    quote.textContent = justification
        ? `"${justification}"`
        : '"Sin justificación"';
}

// ─────────────────────────────────────────────
// Estado
// ─────────────────────────────────────────────
function renderStatus(status) {
    const badge =
        document.querySelector(".status-badge");

    const button =
        document.querySelector(".btn-approve");

    let icon = "info";
    let text = status || "Desconocido";

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

// ─────────────────────────────────────────────
// Utilidades
// ─────────────────────────────────────────────
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
