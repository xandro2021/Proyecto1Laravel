<!-- agregarItemAdmin.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>OfficeLend - Agregar Nuevo Equipo</title>
    <script type="text/javascript" src="/js/admin/adminGuard.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="/css/admin/agregarItemAdmin.css" type="text/css" media="screen" />

    <link rel="stylesheet" href="{{ asset('css/admin/agregarItemAdmin.css') }}">
</head>

<body class="bg-surface">
    <header class="sticky-top bg-white shadow-sm" style="backdrop-filter: blur(8px);">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <img alt="OfficeLend Logo" class="h-10 w-10 rounded-3" src="/img/logo.png">
                    <span class="fs-3 fw-bold text-custom-primary font-headline tracking-tight">OfficeLend</span>
                </div>
                <nav class="d-none d-md-flex gap-4">
                    <a class="nav-link text-custom-primary" href="/admin/dashboard">Dashboard</a>
                    <a class="nav-link active text-custom-primary" href="/admin/catalogo">Equipos</a>
                    <a class="nav-link text-custom-primary" href="/admin/prestamos">Préstamos</a>
                    <!-- salir ir a login -->
                    <a class="nav-link text-custom-primary" href="#" onclick="logout()">Salir</a>
                </nav>
                <div class="d-flex align-items-center gap-3">

                    <span id="viewModeLabel" class="small fw-semibold text-custom-primary"> Admin </span>

                    <a href="/user/catalogo" class="btn btn-link p-0 text-custom-primary">
                        <span id="viewModeIcon" class="material-symbols-outlined" style="font-size: 32px;"></span>
                    </a>

                    <button class="btn btn-link text-custom-primary p-2">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                     <button type="button" class="btn btn-link text-custom-primary p-2" onclick="openSettings()" title="Abrir configuración">
                        <span class="material-symbols-outlined fs-n3">settings</span>
                    </button>
                  <button id="menuToggleBtn" type="button" class="btn btn-link p-0 rounded-circle overflow-hidden border border-light shadow-sm" style="width: 40px; height: 40px;" onclick="toggleHeaderMenu()" title="Mostrar opciones">
                        <img alt="User profile" class="w-100 h-100 object-fit-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDw7nxzJSdrR71CDJcr7kC74LjSK0FQ4lqpD3NebHzuBF2OKLcobdpMmXLTfXGjKhnwCsGDV2mN9HPKDx-tqzf23jNvhXxFM75S6XKC3LG98PiUY7HxPnK2lIS1M_4GLStSAhndvd9Qn50PRYC5jcBbYgw4BSiKmrhfaCEilKQgy1fH7KuLwKDGqS4BSOc6v4w6xKL722wJvMxtVxo4_cE_vR5qmR6qfuI_PH9r7xPCSIJzDIrCuivh_EQ1OP5_X1nTqItkinNBbVc">
                    </button>
                     <div id="headerMenu" class="bg-white border rounded-3 shadow-sm" style="display:none; position:absolute; right:0; top:56px; min-width: 180px; z-index:1000;">
                        <a class="d-block px-3 py-2 text-decoration-none text-dark" href="/admin/dashboard">Dashboard</a>
                        <a class="d-block px-3 py-2 text-decoration-none text-dark" href="/admin/catalogo">Equipos</a>
                        <a class="d-block px-3 py-2 text-decoration-none text-dark" href="/admin/prestamos">Préstamos</a>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="d-block w-100 text-start px-3 py-2 btn btn-link text-dark" onclick="logout()">Salir</button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container py-5">
        <!-- Encabezado editorial -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-4 mb-5">
            <div>
                <h2 class="page-title">Agregar Nuevo Equipo</h2>
                <p class="page-subtitle">Registra y cataloga nuevos activos institucionales con precisión.</p>
            </div>
            <div class="d-flex gap-3">
                <a th:href="@{/admin/catalogo}"
                    class="btn btn-outline-secondary flex-grow-1 py-2 rounded-lg border-outline-variant text-custom-primary fw-semibold">
                    Cancelar
                </a>
                <button id="btn-guardar"
                    class="btn bg-gradient-br-secondary text-white fw-bold px-5 py-2 rounded-lg shadow-sm btn-scale-active"
                    style="background: linear-gradient(135deg, #855300, #fea619); border: none;">
                    Guardar Equipo
                </button>
            </div>
        </div>

        <!-- Bento grid -->
        <div class="row g-4">

            <!-- Columna izquierda: Estado e identidad -->
            <div class="col-lg-4 col-12">
                <div class="d-flex flex-column gap-4">

                    <!-- Tarjeta de estado -->
                    <div class="bg-surface-container-lowest rounded-xl p-4 shadow-sm"
                        style="box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid rgba(197,197,211,0.2);">

                        <label class="text-uppercase fw-bold text-muted small tracking-wide mb-3 d-block"
                            style="font-size: 0.625rem;">Asset Visualization</label>
                        <div class="aspect-square-custom rounded-lg bg-surface-container-low border-2 border-dashed border-outline-variant d-flex flex-column align-items-center justify-content-center text-center p-4 cursor-pointer transition-all"
                            style="cursor: pointer; transition: all 0.2s;">
                            <span class="material-symbols-outlined fs-1 text-muted mb-3">add_a_photo</span>
                            <p class="small fw-semibold text-on-surface-variant mb-1">Upload Equipment Image</p>
                            <p class="small text-muted" style="font-size: 0.625rem;">JPG, PNG or WEBP (Max. 5MB)</p>
                        </div>

                        <div class="mt-2">
                            <label class="fw-bold text-custom-primary mb-2 small">Estado actual</label>
                            <div class="position-relative">
                                <select id="input-status" class="form-custom-input w-100">
                                    <option value="DISPONIBLE" selected>Disponible</option>
                                    <option value="OCUPADO">Ocupado</option>
                                    <option value="MANTENIMIENTO">Mantenimiento</option>
                                </select>
                                <span
                                    class="material-symbols-outlined position-absolute top-50 end-0 translate-middle-y me-2 text-muted"
                                    style="pointer-events:none;">expand_more</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de stock -->
                    <div class="bg-surface-container-lowest rounded-xl p-4 shadow-sm"
                        style="box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid rgba(197,197,211,0.2);">
                        <label class="text-uppercase fw-bold text-muted small tracking-wide mb-3 d-block"
                            style="font-size: 0.625rem;">Inventario</label>

                        <label class="fw-bold text-custom-primary mb-2 small">Cantidad / Stock</label>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" id="btn-decrease"
                                class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                                style="width:36px; height:36px; padding:0;">
                                <span class="material-symbols-outlined" style="font-size:1.1rem;">remove</span>
                            </button>
                            <input type="number" id="input-stock" class="form-custom-input text-center fw-bold"
                                style="width: 80px; font-size: 1.2rem;" min="0" value="1" />
                            <button type="button" id="btn-increase"
                                class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                                style="width:36px; height:36px; padding:0;">
                                <span class="material-symbols-outlined" style="font-size:1.1rem;">add</span>
                            </button>
                        </div>
                        <p class="text-muted small mt-2 mb-0">Unidades disponibles para préstamo.</p>
                    </div>

                    <!-- Tarjeta meta de activo -->
                    <div class="bg-primary-container text-on-primary-container p-4 rounded-xl position-relative overflow-hidden shadow-sm"
                        style="background-color: #1e3a8a;">
                        <div class="position-relative z-1">
                            <h4 class="text-uppercase fw-bold opacity-75 small mb-4 text-white">Información del Registro
                            </h4>
                            <div class="mb-3">
                                <p class="small opacity-75 mb-0">Fecha de Ingreso</p>
                                <p class="font-headline fw-black fs-5 text-white tracking-tight" id="fecha-ingreso">—
                                </p>
                            </div>
                            <div class="bg-white bg-opacity-10 w-100 my-3" style="height: 1px;"></div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="small opacity-75 mb-0">Estado del Formulario</p>
                                    <p class="fw-semibold text-white mb-0" id="form-estado-label">Sin completar</p>
                                </div>
                                <span class="material-symbols-outlined fs-1 opacity-25">inventory_2</span>
                            </div>
                        </div>
                        <div class="position-absolute bottom-0 end-0 opacity-10">
                            <span class="material-symbols-outlined display-1 span-card-logo">inventory</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Columna derecha: Especificaciones -->
            <div class="col-lg-8 col-12">
                <div class="bg-surface-container-lowest rounded-xl p-4 p-lg-5 shadow-sm border"
                    style="border-color: rgba(197,197,211,0.15);">
                    <div class="border-bottom pb-3 mb-4 border-surface-container-high">
                        <h3 class="font-headline fw-bold text-custom-primary fs-4">Especificaciones del Equipo</h3>
                        <p class="text-on-surface-variant small mt-1">Complete todos los campos para registrar el activo
                            correctamente.</p>
                    </div>

                    <div class="row g-4">

                        <!-- Nombre -->
                        <div class="col-12">
                            <label class="fw-bold text-custom-primary small text-uppercase mb-2">
                                Nombre del Equipo <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="input-name" class="form-custom-input w-100"
                                placeholder='Ej. MacBook Pro 14", Silla Aeron, Monitor Dell...' />
                        </div>

                        <!-- Tipo / Categoría (input de texto con datalist) -->
                        <div class="col-md-6">
                            <label class="fw-bold text-custom-primary small text-uppercase mb-2">
                                Tipo / Categoría <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="text" id="input-type" class="form-custom-input w-100"
                                    placeholder="Ej. Laptop, Tablet, Mobiliario..." list="type-suggestions"
                                    autocomplete="off" />
                                <datalist id="type-suggestions">
                                    <option value="Laptop">
                                    <option value="Tablet">
                                    <option value="Monitor">
                                    <option value="Cámara">
                                    <option value="Periférico">
                                    <option value="Mobiliario">
                                    <option value="Accesorio">
                                    <option value="Proyector">
                                    <option value="Impresora">
                                </datalist>
                                <span
                                    class="material-symbols-outlined position-absolute end-0 top-50 translate-middle-y me-2 text-muted"
                                    style="pointer-events:none;">category</span>
                            </div>
                            <p class="text-muted small mt-1 mb-0">Escribe libremente o selecciona una sugerencia.</p>
                        </div>

                        <!-- Número de serie -->
                        <div th:if="${modo == 'editar'}" class="col-md-6">
                            <label class="fw-bold text-custom-primary small text-uppercase mb-2">
                                Número de Serie / ID de Activo
                            </label>
                            <input type="text" id="input-serial" class="form-custom-input w-100"
                                placeholder="SN-XXXX-XXXX" disabled />
                        </div>

                        <!-- Descripción -->
                        <div class="col-12">
                            <label class="fw-bold text-custom-primary small text-uppercase mb-2">
                                Descripción / Especificaciones Técnicas <span class="text-danger">*</span>
                            </label>
                            <textarea id="input-description" class="form-custom-input w-100" rows="5"
                                placeholder="Detalla procesador, RAM, almacenamiento u otros requisitos técnicos relevantes..."></textarea>
                        </div>

                    </div>

                    <!-- Resumen antes de guardar -->
                    <div class="mt-5 pt-3 border-top border-surface-container-high">
                        <label class="fw-bold text-custom-primary small text-uppercase mb-3 d-block">
                            Resumen del Registro
                        </label>
                        <div class="bg-surface-container-low p-4 rounded-lg">
                            <div class="row g-2 small">
                                <div class="col-6">
                                    <span class="text-muted">Nombre:</span>
                                    <span id="summary-name" class="fw-semibold ms-1 text-on-surface">—</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted">Tipo:</span>
                                    <span id="summary-type" class="fw-semibold ms-1 text-on-surface">—</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted">Stock:</span>
                                    <span id="summary-stock" class="fw-semibold ms-1 text-on-surface">1</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted">Estado:</span>
                                    <span id="summary-status" class="fw-semibold ms-1 text-on-surface">Disponible</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alerta de error -->
                    <div id="form-alert" class="alert alert-danger mt-3 d-none" role="alert">
                        <span class="material-symbols-outlined align-middle me-1" style="font-size:1rem;">error</span>
                        <span id="form-alert-msg">Por favor completa los campos obligatorios.</span>
                    </div>

                    <!-- Alerta de éxito -->
                    <div id="form-success" class="alert alert-success mt-3 d-none" role="alert">
                        <span class="material-symbols-outlined align-middle me-1"
                            style="font-size:1rem;">check_circle</span>
                        Equipo registrado correctamente. Redirigiendo al catálogo...
                    </div>

                </div>
            </div>
        </div>
    </main>

    <!-- Barra de acción móvil -->
    <div class="mobile-action-bar position-fixed bottom-0 start-0 end-0 bg-white shadow-lg p-3 gap-3 z-1030 d-md-none">
        <a th:href="@{/admin/catalogo}"
            class="btn btn-outline-secondary flex-grow-1 py-2 rounded-lg border-outline-variant text-custom-primary fw-semibold">
            Cancelar
        </a>
        <button id="btn-guardar-mobile" class="btn text-white flex-grow-1 py-2 rounded-lg fw-semibold"
            style="background-color: #855300;">Guardar</button>
    </div>

    <div class="grain-overlay"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/admin/editarItemAdmin.js"></script>

</body>

</html>
