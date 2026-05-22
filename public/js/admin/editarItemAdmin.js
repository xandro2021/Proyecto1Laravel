// editarItemAdmin.js

const API_BASE = 'http://127.0.0.1:8000/api';

let selectedImage = null;
let equipmentId = null;
let currentImageFilename = null;


async function fetchApi(endpoint, options = {}) {

    const token = localStorage.getItem('token');

    const response = await fetch(`${API_BASE}${endpoint}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            ...(options.headers || {})
        },
        ...options
    });

    if (response.status === 401 || response.status === 403) {
        window.location.href = '/';
        throw new Error('Sesión expirada');
    }

    return response;
}


function getEquipmentIdFromUrl() {

    const pathSegments =
        window.location.pathname.split('/');

    const idIndex =
        pathSegments.indexOf('editar');

    return idIndex !== -1
        ? pathSegments[idIndex + 1]
        : null;
}


function changePageTitle() {

    const pageTitle =
        document.querySelector('.page-title');

    const pageSubtitle =
        document.querySelector('.page-subtitle');

    const btnGuardar =
        document.getElementById('btn-guardar');

    const btnGuardarMobile =
        document.getElementById('btn-guardar-mobile');

    if (pageTitle) {
        pageTitle.textContent = 'Editar Equipo';
    }

    if (pageSubtitle) {
        pageSubtitle.textContent =
            'Actualiza los datos y especificaciones del activo institucional.';
    }

    if (btnGuardar) {
        btnGuardar.textContent = 'Guardar Cambios';
    }

    if (btnGuardarMobile) {
        btnGuardarMobile.textContent =
            'Guardar Cambios';
    }
}


async function loadEquipment(id) {

    try {

        const response =
            await fetchApi(`/equipment/${id}`);

        const result = await response.json();


        const equipment = result.data;

        populateForm(equipment);

    } catch (error) {

        console.error(
            'Error al cargar equipo:',
            error
        );

        const alert =
            document.getElementById('form-alert');

        const alertMsg =
            document.getElementById('form-alert-msg');

        alertMsg.textContent =
            'No se pudo cargar el equipo. Intenta nuevamente.';

        alert.classList.remove('d-none');
    }
}


function populateForm(equipment) {

    equipmentId = equipment.id;

    currentImageFilename =
        equipment.image_filename;

    console.log(equipment);

    document.getElementById('input-name').value =
        equipment.name;

    document.getElementById('input-type').value =
        equipment.type;

    document.getElementById('input-serial').value =
        equipment.id || '';

    document.getElementById('input-description').value =
        equipment.description;

    document.getElementById('input-stock').value =
        equipment.stock;

    document.getElementById('input-status').value =
        equipment.status;

    // resumen
    document.getElementById('summary-name').textContent =
        equipment.name;

    document.getElementById('summary-type').textContent =
        equipment.type;

    document.getElementById('summary-stock').textContent =
        equipment.stock;

    document.getElementById('summary-status').textContent =
        equipment.status;

    // imagen
    if (equipment.image_url) {

        const imagePreview =
            document.querySelector('.aspect-square-custom');

        imagePreview.style.backgroundImage =
            `url(${equipment.image_url})`;

        imagePreview.style.backgroundSize = 'cover';

        imagePreview.style.backgroundPosition = 'center';

        const icon =
            imagePreview.querySelector(
                '.material-symbols-outlined'
            );

        const text =
            imagePreview.querySelector('.fw-semibold');

        const subtext =
            imagePreview.querySelector('.text-muted');

        if (icon) icon.style.display = 'none';

        if (text) {
            text.textContent = 'Imagen actual';
        }

        if (subtext) {
            subtext.style.display = 'none';
        }
    }

    updateFormState();
}


document.getElementById('fecha-ingreso').textContent =
    new Date().toLocaleDateString(
        'es-CR',
        {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }
    );


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

    if (!file) return;

    // max 5MB
    const maxSize = 5 * 1024 * 1024;

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

        imagePreview.style.backgroundSize = 'cover';

        imagePreview.style.backgroundPosition = 'center';

        const icon =
            imagePreview.querySelector(
                '.material-symbols-outlined'
            );

        const text =
            imagePreview.querySelector('.fw-semibold');

        const subtext =
            imagePreview.querySelector('.text-muted');

        if (icon) icon.style.display = 'none';

        if (text) {
            text.textContent = file.name;
        }

        if (subtext) {
            subtext.style.display = 'none';
        }
    };

    reader.readAsDataURL(file);
});


const statusSelect =
    document.getElementById('input-status');

statusSelect.addEventListener('change', () => {

    document.getElementById(
        'summary-status'
    ).textContent = statusSelect.value;
});


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
    .addEventListener('input', updateFormState);

function updateFormState() {

    const name =
        document.getElementById(
            'input-name'
        ).value.trim();

    const type =
        document.getElementById(
            'input-type'
        ).value.trim();

    const desc =
        document.getElementById(
            'input-description'
        ).value.trim();

    const completo =
        name && type && desc;

    document.getElementById(
        'form-estado-label'
    ).textContent =
        completo
            ? 'Listo para guardar'
            : 'Sin completar';
}


async function guardarEquipo() {

    const name =
        document.getElementById(
            'input-name'
        ).value.trim();

    const type =
        document.getElementById(
            'input-type'
        ).value.trim();

    const description =
        document.getElementById(
            'input-description'
        ).value.trim();

    const stock =
        parseInt(stockInput.value) || 0;

    const status =
        statusSelect.value;

    const alert =
        document.getElementById('form-alert');

    const alertMsg =
        document.getElementById('form-alert-msg');

    const success =
        document.getElementById('form-success');

    // validación
    if (!name || !type || !description) {

        alertMsg.textContent =
            'Por favor completa los campos obligatorios: Nombre, Tipo y Descripción.';

        alert.classList.remove('d-none');

        success.classList.add('d-none');

        return;
    }

    alert.classList.add('d-none');

    // form data
    const formData = new FormData();

    formData.append('name', name);
    formData.append('type', type);
    formData.append('description', description);
    formData.append('stock', stock);
    formData.append('status', status);

    // Laravel PUT + multipart
    formData.append('_method', 'PUT');

    if (selectedImage) {
        formData.append('image', selectedImage);
    }

    try {

        document.querySelector(
            '#btn-guardar'
        ).disabled = true;

        const response =
            await fetchApi(
                `/equipment/${equipmentId}`,
                {
                    method: 'POST',
                    body: formData
                }
            );

        const result =
            await response.json();

        if (response.ok) {

            success.classList.remove('d-none');

            setTimeout(() => {

                window.location.href =
                    '/admin/catalogo';

            }, 1500);

        } else {

            alertMsg.textContent =
                result.message ||
                'Error al guardar los cambios.';

            alert.classList.remove('d-none');

            document.querySelector(
                '#btn-guardar'
            ).disabled = false;
        }

    } catch (error) {

        console.error('Error:', error);

        alertMsg.textContent =
            'No se pudo conectar con el servidor.';

        alert.classList.remove('d-none');

        document.querySelector(
            '#btn-guardar'
        ).disabled = false;
    }
}

// EVENTOS

document.getElementById('btn-guardar')
    .addEventListener('click', guardarEquipo);

document.getElementById('btn-guardar-mobile')
    .addEventListener('click', guardarEquipo);

// efecto escala
document.querySelectorAll('.btn-scale-active')
    .forEach(btn => {

        btn.addEventListener('click', function () {

            this.style.transform = 'scale(0.97)';

            setTimeout(() => {

                this.style.transform = '';

            }, 120);
        });
    });

// INIT

document.querySelector('#btn-guardar').disabled = false;

const id = getEquipmentIdFromUrl();

if (id) {

    changePageTitle();
    loadEquipment(id);
}
