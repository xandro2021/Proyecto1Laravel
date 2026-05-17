// editarItemAdmin.js
const API_BASE = 'http://localhost:8080';
let selectedImage = null;
let equipmentId = null;
let currentImageFilename = null;

// ── Obtener ID de la URL ──────────────────────────────────
function getEquipmentIdFromUrl() {
  const pathSegments = window.location.pathname.split('/');
  const idIndex = pathSegments.indexOf('editar');
  return idIndex !== -1 ? pathSegments[idIndex + 1] : null;
}

// ── Cambiar título ───────────────────────────────────────
function changePageTitle() {
  const pageTitle = document.querySelector('.page-title');
  const pageSubtitle = document.querySelector('.page-subtitle');
  const btnGuardar = document.getElementById('btn-guardar');
  const btnGuardarMobile = document.getElementById('btn-guardar-mobile');

  if (pageTitle) pageTitle.textContent = 'Editar Equipo';
  if (pageSubtitle) pageSubtitle.textContent = 'Actualiza los datos y especificaciones del activo institucional.';
  if (btnGuardar) btnGuardar.textContent = 'Guardar Cambios';
  if (btnGuardarMobile) btnGuardarMobile.textContent = 'Guardar Cambios';
}

// ── Cargar datos del equipo ───────────────────────────────
async function loadEquipment(id) {
  const token = localStorage.getItem('jwt');

  try {
    const response = await fetch(`${API_BASE}/equipment/${id}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });

    if (response.status === 401 || response.status === 403) {
      window.location.href = '/';
      return;
    }

    const equipment = await response.json();
    populateForm(equipment);

  } catch (error) {
    console.error('Error al cargar equipo:', error);
    const alert = document.getElementById('form-alert');
    const alertMsg = document.getElementById('form-alert-msg');
    alertMsg.textContent = 'No se pudo cargar el equipo. Intenta nuevamente.';
    alert.classList.remove('d-none');
  }
}

// ── Llenar el formulario con datos ────────────────────────
function populateForm(equipment) {
  equipmentId = equipment.id;
  currentImageFilename = equipment.imageFilename;
  console.log(equipment);

  document.getElementById('input-name').value = equipment.name;
  document.getElementById('input-type').value = equipment.type;
  document.getElementById('input-serial').value = equipment.id || '';
  document.getElementById('input-description').value = equipment.description;
  document.getElementById('input-stock').value = equipment.stock;
  document.getElementById('input-status').value = equipment.status;

  // Actualizar resumen
  document.getElementById('summary-name').textContent = equipment.name;
  document.getElementById('summary-type').textContent = equipment.type;
  document.getElementById('summary-stock').textContent = equipment.stock;
  document.getElementById('summary-status').textContent = equipment.status;

  // Cargar imagen si existe
  if (equipment.imageFilename) {
    const imagePreview = document.querySelector('.aspect-square-custom');
    imagePreview.style.backgroundImage = `url(/uploads/equipment/${equipment.imageFilename})`;
    imagePreview.style.backgroundSize = 'cover';
    imagePreview.style.backgroundPosition = 'center';

    // Ocultar iconos y texto del placeholder
    const icon = imagePreview.querySelector('.material-symbols-outlined');
    const text = imagePreview.querySelector('.fw-semibold');
    const subtext = imagePreview.querySelector('.text-muted');

    if (icon) icon.style.display = 'none';
    if (text) text.textContent = 'Imagen actual';
    if (subtext) subtext.style.display = 'none';
  }

  updateFormState();
}

// ── Fecha de hoy ──────────────────────────────────────────
document.getElementById('fecha-ingreso').textContent =
  new Date().toLocaleDateString('es-CR', { year: 'numeric', month: 'long', day: 'numeric' });

// ── Previsualización de imagen ────────────────────────────
const imagePreview = document.querySelector('.aspect-square-custom');
const imageInput = document.createElement('input');
imageInput.type = 'file';
imageInput.accept = 'image/jpeg,image/png,image/webp';
imageInput.style.display = 'none';
document.body.appendChild(imageInput);

imagePreview.addEventListener('click', () => imageInput.click());

imageInput.addEventListener('change', (e) => {
  const file = e.target.files[0];
  if (!file) return;

  // Validar tamaño (máx 5MB)
  const maxSize = 5 * 1024 * 1024;
  if (file.size > maxSize) {
    alert('La imagen es demasiado grande (máx 5MB)');
    return;
  }

  selectedImage = file;

  // Preview
  const reader = new FileReader();
  reader.onload = (event) => {
    imagePreview.style.backgroundImage = `url(${event.target.result})`;
    imagePreview.style.backgroundSize = 'cover';
    imagePreview.style.backgroundPosition = 'center';

    // Ocultar iconos y texto del placeholder
    const icon = imagePreview.querySelector('.material-symbols-outlined');
    const text = imagePreview.querySelector('.fw-semibold');
    const subtext = imagePreview.querySelector('.text-muted');

    if (icon) icon.style.display = 'none';
    if (text) text.textContent = file.name;
    if (subtext) subtext.style.display = 'none';
  };
  reader.readAsDataURL(file);
});

// ── Preview de estado ─────────────────────────────────────
const statusSelect = document.getElementById('input-status');

statusSelect.addEventListener('change', () => {
  const cfg = statusSelect.value;
  document.getElementById('summary-status').textContent = cfg;
});

// ── Controles de stock ────────────────────────────────────
const stockInput = document.getElementById('input-stock');

document.getElementById('btn-increase').addEventListener('click', () => {
  stockInput.value = parseInt(stockInput.value || 0) + 1;
  document.getElementById('summary-stock').textContent = stockInput.value;
});

document.getElementById('btn-decrease').addEventListener('click', () => {
  const val = parseInt(stockInput.value || 0);
  if (val > 0) {
    stockInput.value = val - 1;
    document.getElementById('summary-stock').textContent = stockInput.value;
  }
});

stockInput.addEventListener('input', () => {
  document.getElementById('summary-stock').textContent = stockInput.value || '0';
});

// ── Resumen en tiempo real ────────────────────────────────
document.getElementById('input-name').addEventListener('input', function () {
  document.getElementById('summary-name').textContent = this.value || '—';
  updateFormState();
});

document.getElementById('input-type').addEventListener('input', function () {
  document.getElementById('summary-type').textContent = this.value || '—';
  updateFormState();
});

function updateFormState() {
  const name = document.getElementById('input-name').value.trim();
  const type = document.getElementById('input-type').value.trim();
  const desc = document.getElementById('input-description').value.trim();
  const completo = name && type && desc;
  document.getElementById('form-estado-label').textContent = completo ? 'Listo para guardar' : 'Sin completar';
}

document.getElementById('input-description').addEventListener('input', updateFormState);

// ── Guardar cambios (EDICIÓN) ─────────────────────────────
async function guardarEquipo() {
  const name = document.getElementById('input-name').value.trim();
  const type = document.getElementById('input-type').value.trim();
  const description = document.getElementById('input-description').value.trim();
  const stock = parseInt(stockInput.value) || 0;
  const status = statusSelect.value;

  const alert = document.getElementById('form-alert');
  const alertMsg = document.getElementById('form-alert-msg');
  const success = document.getElementById('form-success');

  // Validación
  if (!name || !type || !description) {
    alertMsg.textContent = 'Por favor completa los campos obligatorios: Nombre, Tipo y Descripción.';
    alert.classList.remove('d-none');
    success.classList.add('d-none');
    return;
  }

  alert.classList.add('d-none');

  const token = localStorage.getItem('jwt');

  // ← FormData para enviar archivo + otros campos
  const formData = new FormData();
  formData.append('name', name);
  formData.append('type', type);
  formData.append('description', description);
  formData.append('stock', stock);
  formData.append('status', status);

  if (selectedImage) {
    formData.append('image', selectedImage);
  }

  try {
    document.querySelector('#btn-guardar').disabled = true;

    const response = await fetch(`${API_BASE}/equipment/${equipmentId}`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`
      },
      body: formData
    });

    if (response.ok) {
      success.classList.remove('d-none');
      setTimeout(() => window.location.href = '/admin/catalogo', 1500);
    } else if (response.status === 401 || response.status === 403) {
      alertMsg.textContent = 'Sesión expirada. Por favor inicia sesión nuevamente.';
      alert.classList.remove('d-none');
      setTimeout(() => window.location.href = '/', 1500);
    } else {
      alertMsg.textContent = 'Error al guardar los cambios. Intenta nuevamente.';
      alert.classList.remove('d-none');
      document.querySelector('#btn-guardar').disabled = false;
    }
  } catch (error) {
    console.error('Error:', error);
    alertMsg.textContent = 'No se pudo conectar con el servidor.';
    alert.classList.remove('d-none');
    document.querySelector('#btn-guardar').disabled = false;
  }
}

document.getElementById('btn-guardar').addEventListener('click', guardarEquipo);
document.getElementById('btn-guardar-mobile').addEventListener('click', guardarEquipo);

// Efecto escala en botón guardar
document.querySelectorAll('.btn-scale-active').forEach(btn => {
  btn.addEventListener('click', function () {
    this.style.transform = 'scale(0.97)';
    setTimeout(() => { this.style.transform = ''; }, 120);
  });
});


// ── Inicializar ───────────────────────────────────────────
document.querySelector('#btn-guardar').disabled = false;
const id = getEquipmentIdFromUrl();
if (id) {
  changePageTitle();
  loadEquipment(id);
}
