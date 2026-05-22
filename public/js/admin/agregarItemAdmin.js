// agregarItemAdmin.js

const API_BASE = 'http://127.0.0.1:8000/api';

let selectedImage = null;

document.querySelector('#btn-guardar').disabled = false;

/* ─────────────────────────────────────────────
   Fecha actual
───────────────────────────────────────────── */

document.getElementById('fecha-ingreso').textContent =
    new Date().toLocaleDateString(
        'es-CR',
        {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }
    );

/* ─────────────────────────────────────────────
   Preview imagen
───────────────────────────────────────────── */

const imagePreview =
    document.querySelector('.aspect-square-custom');

const imageInput =
    document.createElement('input');

imageInput.type = 'file';

imageInput.accept =
    'image/jpeg,image/png,image/webp';

imageInput.style.display = 'none';

document.body.appendChild(imageInput);

imagePreview.addEventListener(
    'click',
    () => imageInput.click()
);

imageInput.addEventListener('change', (e) => {

    const file = e.target.files[0];

    if (!file) {
        return;
    }

    // max 5MB
    const maxSize =
        5 * 1024 * 1024;

    if (file.size > maxSize) {

        alert(
            'La imagen es demasiado grande (máx 5MB)'
        );

        return;
    }

    selectedImage = file;

    const reader = new FileReader();

    reader.onload = (event) => {

        imagePreview.style.backgroundImage =
            `url(${event.target.result})`;

        imagePreview.style.backgroundSize =
            'cover';

        imagePreview.style.backgroundPosition =
            'center';

        // ocultar placeholder
        const icon =
            imagePreview.querySelector(
                '.material-symbols-outlined'
            );

        const text =
            imagePreview.querySelector(
                '.fw-semibold'
            );

        const subtext =
            imagePreview.querySelector(
                '.text-muted'
            );

        if (icon) {
            icon.style.display = 'none';
        }

        if (text) {
            text.textContent = file.name;
        }

        if (subtext) {
            subtext.style.display = 'none';
        }
    };

    reader.readAsDataURL(file);
});

/* ─────────────────────────────────────────────
   Estado preview
───────────────────────────────────────────── */

const statusSelect =
    document.getElementById('input-status');

statusSelect.addEventListener('change', () => {

    document.getElementById(
        'summary-status'
    ).textContent = statusSelect.value;
});

/* ─────────────────────────────────────────────
   Stock controls
───────────────────────────────────────────── */

const stockInput =
    document.getElementById('input-stock');

document.getElementById('btn-increase')
    .addEventListener('click', () => {

        stockInput.value =
            parseInt(stockInput.value || 0) + 1;

        document.getElementById(
            'summary-stock'
        ).textContent = stockInput.value;
    });

document.getElementById('btn-decrease')
    .addEventListener('click', () => {

        const val =
            parseInt(stockInput.value || 0);

        if (val > 0) {

            stockInput.value = val - 1;

            document.getElementById(
                'summary-stock'
            ).textContent = stockInput.value;
        }
    });

stockInput.addEventListener('input', () => {

    document.getElementById(
        'summary-stock'
    ).textContent = stockInput.value || '0';
});

/* ─────────────────────────────────────────────
   Resumen realtime
───────────────────────────────────────────── */

document.getElementById('input-name')
    .addEventListener('input', function () {

        document.getElementById(
            'summary-name'
        ).textContent = this.value || '—';

        updateFormState();
    });

document.getElementById('input-type')
    .addEventListener('input', function () {

        document.getElementById(
            'summary-type'
        ).textContent = this.value || '—';

        updateFormState();
    });

document.getElementById('input-description')
    .addEventListener(
        'input',
        updateFormState
    );

function updateFormState() {

    const name =
        document.getElementById('input-name')
            .value
            .trim();

    const type =
        document.getElementById('input-type')
            .value
            .trim();

    const desc =
        document.getElementById('input-description')
            .value
            .trim();

    const completo =
        name &&
        type &&
        desc;

    document.getElementById(
        'form-estado-label'
    ).textContent =
        completo
            ? 'Listo para guardar'
            : 'Sin completar';
}

/* ─────────────────────────────────────────────
   Guardar equipo
───────────────────────────────────────────── */

async function guardarEquipo() {

    const name =
        document.getElementById('input-name')
            .value
            .trim();

    const type =
        document.getElementById('input-type')
            .value
            .trim();

    const description =
        document.getElementById('input-description')
            .value
            .trim();

    const stock =
        parseInt(stockInput.value) || 0;

    const status =
        statusSelect.value;

    const alertBox =
        document.getElementById('form-alert');

    const alertMsg =
        document.getElementById('form-alert-msg');

    const success =
        document.getElementById('form-success');

    // validacion
    if (!name || !type || !description) {

        alertMsg.textContent =
            'Por favor completa los campos obligatorios: Nombre, Tipo y Descripción.';

        alertBox.classList.remove('d-none');

        success.classList.add('d-none');

        return;
    }

    alertBox.classList.add('d-none');

    const token =
        localStorage.getItem('token');

    const formData =
        new FormData();

    formData.append('name', name);

    formData.append('type', type);

    formData.append('description', description);

    formData.append('stock', stock);

    formData.append('status', status);

    if (selectedImage) {

        formData.append(
            'image',
            selectedImage
        );
    }

    try {

        document.querySelector(
            '#btn-guardar'
        ).disabled = true;

        const response = await fetch(
            `${API_BASE}/equipment`,
            {
                method: 'POST',

                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Accept-Language': 'es'
                },

                // NO Content-Type
                body: formData
            }
        );

        const result =
            await response.json();

        if (response.ok) {

            console.log(result);

            success.classList.remove('d-none');

            setTimeout(() => {

                window.location.href =
                    '/admin/catalogo';

            }, 1500);

        } else if (
            response.status === 401 ||
            response.status === 403
        ) {

            alertMsg.textContent =
                result.message ||
                'Sesión expirada.';

            alertBox.classList.remove('d-none');

            setTimeout(() => {

                window.location.href = '/';

            }, 1500);

        } else {

            alertMsg.textContent =
                result.message ||
                'Error al guardar el equipo.';

            alertBox.classList.remove('d-none');

            document.querySelector(
                '#btn-guardar'
            ).disabled = false;
        }

    } catch (error) {

        console.error('Error:', error);

        alertMsg.textContent =
            'No se pudo conectar con el servidor.';

        alertBox.classList.remove('d-none');

        document.querySelector(
            '#btn-guardar'
        ).disabled = false;
    }
}

document.getElementById('btn-guardar')
    .addEventListener(
        'click',
        guardarEquipo
    );

document.getElementById('btn-guardar-mobile')
    .addEventListener(
        'click',
        guardarEquipo
    );

/* ─────────────────────────────────────────────
   Animacion botones
───────────────────────────────────────────── */

document.querySelectorAll('.btn-scale-active')
    .forEach(btn => {

        btn.addEventListener(
            'click',
            function () {

                this.style.transform =
                    'scale(0.97)';

                setTimeout(() => {

                    this.style.transform = '';

                }, 120);
            }
        );
    });
