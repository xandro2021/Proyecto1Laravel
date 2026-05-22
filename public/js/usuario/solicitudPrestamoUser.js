// solicitudPrestamoUser.js

const PUBLIC_BASE = "http://127.0.0.1:8000";
const API_BASE = `${PUBLIC_BASE}/api`;
const API_URL = `${API_BASE}/loans`;

document.addEventListener("DOMContentLoaded", () => {
    cargarEquipo();
    inicializarFormulario();
});

// ─────────────────────────────────────────────
// Inicializar formulario
// ─────────────────────────────────────────────
function inicializarFormulario() {
    const form = document.querySelector("form");

    if (!form) return;

    const today = new Date().toISOString().split("T")[0];

    const fechaDevolucionInput =
        document.getElementById("fecha-devolucion");

    if (fechaDevolucionInput) {
        fechaDevolucionInput.min = today;
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        await enviarSolicitud(today);
    });
}

// ─────────────────────────────────────────────
// Obtener ID desde URL
// ─────────────────────────────────────────────
function getEquipmentIdFromURL() {
    const pathParts = window.location.pathname.split("/");
    const equipmentId = parseInt(
        pathParts[pathParts.length - 1],
        10
    );

    return isNaN(equipmentId) ? null : equipmentId;
}

// ─────────────────────────────────────────────
// Enviar solicitud
// ─────────────────────────────────────────────
async function enviarSolicitud(today) {
    try {
        const token = localStorage.getItem("token");

        if (!token) {
            window.location.href = "/";
            return;
        }

        const equipmentId = getEquipmentIdFromURL();

        if (!equipmentId) {
            alert("ID de equipo inválido.");
            return;
        }

        const justification = document
            .getElementById("justificacion")
            ?.value
            .trim();

        const estimatedEndDate = document
            .getElementById("fecha-devolucion")
            ?.value;

        const compliance = document
            .getElementById("compliance")
            ?.checked;

        // Validaciones
        if (!compliance) {
            alert("Debe aceptar los términos y condiciones.");
            return;
        }

        if (!justification || !estimatedEndDate) {
            alert("Complete todos los campos requeridos.");
            return;
        }

        // Validar fecha
        if (estimatedEndDate < today) {
            alert("La fecha de devolución no puede ser anterior a hoy.");
            return;
        }

        const loanData = {
            request_date: today,
            estimated_end_date: estimatedEndDate,
            justification,
            equipment_id: equipmentId
        };

        const submitButton = document.querySelector(
            'button[type="submit"]'
        );

        if (submitButton) {
            submitButton.disabled = true;
        }

        const res = await fetch(API_URL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            },
            body: JSON.stringify(loanData)
        });

        if (res.status === 401 || res.status === 403) {
            window.location.href = "/";
            return;
        }

        if (!res.ok) {
            let errorMessage =
                "Error al crear la solicitud.";

            try {
                const errorData = await res.json();
                errorMessage =
                    errorData.message || errorMessage;
            } catch (_) {}

            throw new Error(errorMessage);
        }

        alert("Solicitud enviada correctamente.");

        window.location.href = "/user/prestamos";

    } catch (err) {
        console.error(err);
        alert(err.message || "Error al enviar la solicitud.");
    } finally {
        const submitButton = document.querySelector(
            'button[type="submit"]'
        );

        if (submitButton) {
            submitButton.disabled = false;
        }
    }
}

// ─────────────────────────────────────────────
// Cargar datos del equipo
// ─────────────────────────────────────────────
async function cargarEquipo() {
    try {
        const token = localStorage.getItem("token");

        if (!token) {
            window.location.href = "/";
            return;
        }

        const equipmentId = getEquipmentIdFromURL();

        if (!equipmentId) {
            alert("ID de equipo inválido.");
            return;
        }

        const res = await fetch(
            `${API_BASE}/equipment/${equipmentId}`,
            {
                headers: {
                    "Authorization": `Bearer ${token}`,
                    "Accept": "application/json"
                }
            }
        );

        if (res.status === 401 || res.status === 403) {
            window.location.href = "/";
            return;
        }

        if (!res.ok) {
            throw new Error(
                "No se pudo cargar la información del equipo."
            );
        }

        const response = await res.json();
        actualizarVistaEquipo(response.data);

    } catch (err) {
        console.error(err);
        alert("Error cargando el equipo.");
    }
}

// ─────────────────────────────────────────────
// Actualizar UI del equipo
// ─────────────────────────────────────────────
function actualizarVistaEquipo(equipment) {
    const equipmentName =
        document.getElementById("equipment-name");

    const equipmentId =
        document.getElementById("equipment-id");

    const equipmentType =
        document.getElementById("equipment-type");

    const equipmentImage =
        document.getElementById("equipment-image");

    if (equipmentName) {
        equipmentName.textContent =
            equipment.name || "Sin nombre";
    }

    if (equipmentId) {
        equipmentId.textContent =
            `ID: ${equipment.id || "-"}`;
    }

    if (equipmentType) {
        equipmentType.textContent =
            equipment.type || "Sin categoría";
    }

    if (equipmentImage) {
        equipmentImage.src =
            equipment.image_url ||
            "/img/no-image.png";

        equipmentImage.alt =
            equipment.name || "Equipo";
    }
}
