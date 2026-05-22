// dashboardAdmin.js

const API_BASE = 'http://127.0.0.1:8000/api';
const token = localStorage.getItem('token');


async function fetchApi(endpoint) {
    const response = await fetch(`${API_BASE}${endpoint}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });

    if (response.status === 401 || response.status === 403) {
        window.location.href = '/';
        throw new Error('Sesión expirada');
    }

    return await response.json();
}


function showNotifications() {
    alert("Notificaciones próximamente");
}

function openSettings() {
    alert("Configuración próximamente");
}


const dateElement = document.getElementById("currentDate");

if (dateElement) {

    const days = [
        "Domingo",
        "Lunes",
        "Martes",
        "Miércoles",
        "Jueves",
        "Viernes",
        "Sábado",
    ];

    const months = [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre",
    ];

    const today = new Date();

    dateElement.textContent =
        `${days[today.getDay()]}, ${today.getDate()} de ${months[today.getMonth()]}`;
}


function toggleFilterPanel() {

    const panel = document.getElementById("filterPanel");

    if (!panel) return;

    panel.style.display =
        panel.style.display === "none" || panel.style.display === ""
            ? "flex"
            : "none";
}

function applyLoanFilters() {

    const query = document
        .getElementById("loanSearch")
        .value.trim()
        .toLowerCase();

    const status = document
        .getElementById("loanStatus")
        .value.toUpperCase();

    const rows = document.querySelectorAll("#loansTable tbody tr");

    rows.forEach((row) => {

        const userText = row.cells[0].innerText.toLowerCase();
        const equipmentText = row.cells[1].innerText.toLowerCase();
        const statusText = row.cells[4].innerText.toUpperCase();

        const matchesQuery =
            !query ||
            userText.includes(query) ||
            equipmentText.includes(query);

        const matchesStatus =
            !status ||
            statusText.includes(status);

        row.style.display =
            matchesQuery && matchesStatus
                ? ""
                : "none";
    });
}

function clearLoanFilters() {

    const search = document.getElementById("loanSearch");
    const status = document.getElementById("loanStatus");

    if (search) search.value = "";
    if (status) status.value = "";

    applyLoanFilters();
}


function toggleHeaderMenu() {

    const menu = document.getElementById("headerMenu");
    const button = document.getElementById("menuToggleBtn");

    if (!menu || !button) return;

    menu.style.display =
        menu.style.display === "block"
            ? "none"
            : "block";
}

document.addEventListener("click", (event) => {

    const menu = document.getElementById("headerMenu");
    const button = document.getElementById("menuToggleBtn");

    if (!menu || !button) return;

    if (
        event.target.closest("#headerMenu") ||
        event.target.closest("#menuToggleBtn")
    ) {
        return;
    }

    menu.style.display = "none";
});


function exportLoanTableToPDF() {

    if (!window.jspdf || !window.jspdf.jsPDF) {

        alert("No se pudo generar el PDF. Intenta recargar la página.");
        return;
    }

    const { jsPDF } = window.jspdf;

    const doc = new jsPDF({
        orientation: "landscape",
        unit: "pt",
        format: "letter",
    });

    const title = "Préstamos Próximos a Vencer";

    const date =
        new Date().toLocaleDateString("es-CR");

    doc.setFontSize(16);
    doc.text(title, 40, 40);

    doc.setFontSize(10);
    doc.text(`Fecha de exportación: ${date}`, 40, 58);

    doc.autoTable({
        html: "#loansTable",
        startY: 70,

        headStyles: {
            fillColor: [13, 110, 253],
            textColor: 255
        },

        styles: {
            fontSize: 9,
            cellPadding: 6
        },

        columnStyles: {
            0: { cellWidth: 120 },
            1: { cellWidth: 170 },
            2: { cellWidth: 100 },
            3: { cellWidth: 80 },
            4: { cellWidth: 80 },
            5: { cellWidth: 100 },
        },

        didParseCell: function (data) {

            if (
                data.section === "body" &&
                data.column.index === 5
            ) {
                data.cell.styles.cellPadding = 4;
            }
        },
    });

    doc.save("prestamos-vencer.pdf");
}


async function loadDashboardStats() {

    try {

        const response = await fetchApi('/dashboard/stats');


        const data = response.data;

        document.getElementById("equiposDisponibles").innerText =
            data.equiposDisponibles;

        document.getElementById("prestamosActivos").innerText =
            data.prestamosActivos;

        document.getElementById("solicitudesPendientes").innerText =
            data.solicitudesPendientes;

        document.getElementById("prestamosDevueltos").innerText =
            data.prestamosDevueltos;

        const total = data.totalEquipos || 1;

        document.getElementById("inv_disponibles").innerText =
            data.equiposDisponibles;

        document.getElementById("inv_activos").innerText =
            data.prestamosActivos;

        document.getElementById("inv_pendientes").innerText =
            data.solicitudesPendientes;

        document.getElementById("inv_devueltos").innerText =
            data.prestamosDevueltos;

        document.getElementById("bar_disponibles").style.width =
            ((data.equiposDisponibles / total) * 100) + "%";

        document.getElementById("bar_activos").style.width =
            ((data.prestamosActivos / total) * 100) + "%";

        document.getElementById("bar_pendientes").style.width =
            ((data.solicitudesPendientes / total) * 100) + "%";

        document.getElementById("bar_devueltos").style.width =
            ((data.prestamosDevueltos / total) * 100) + "%";

    } catch (err) {

        console.error('Error cargando dashboard:', err);
    }
}


async function loadTopEquipos() {

    try {

        const response =
            await fetchApi('/dashboard/top-equipos');

        // ← IMPORTANTE
        const data = response.data;

        let html = "";

        data.forEach((item, index) => {

            html += `
                <div class="d-flex justify-content-between mb-3">
                    <span>${index + 1}. ${item.nombre}</span>
                    <span class="fw-bold">${item.prestamos}</span>
                </div>
            `;
        });

        document.getElementById("topEquipos").innerHTML = html;

    } catch (err) {

        console.error('Error cargando top equipos:', err);
    }
}


async function loadCurrentUser() {

    try {

        const response = await fetchApi('/me');

        // dependiendo de tu endpoint:
        const user = response.data ?? response;

        document.getElementById("userName").innerText =
            user.username;

        document.getElementById("userRole").innerText =
            user.role;

        document.getElementById("userEmail").innerText =
            user.email;

    } catch (err) {

        console.error('Error cargando usuario:', err);
    }
}


document.addEventListener('DOMContentLoaded', () => {

    loadDashboardStats();
    loadTopEquipos();
    loadCurrentUser();
});
