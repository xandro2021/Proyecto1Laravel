<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OfficeLend - Catálogo de Equipos</title>

    <script type="text/javascript" src="/js/usuario/userGuard.js"></script>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Fuentes -->
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;family=Inter:wght@300;400;500;600&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/user/catalogoUserStyles.css') }}" type="text/css" media="screen" >
</head>

<body>
    <!-- catalogoUser.html -->
    <header class="sticky-top bg-white shadow-sm" style="backdrop-filter: blur(8px)">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Logo -->
                <div class="d-flex align-items-center gap-2">
                    <img alt="OfficeLend Logo" class="h-10 w-10 rounded-3" src="../img/logo.png" />
                    <span class="fs-3 fw-bold text-custom-primary font-headline tracking-tight">OfficeLend</span>
                </div>

                <!-- Navegación desktop -->
                <nav class="d-none d-md-flex gap-4">
                    <a class="nav-link active text-custom-primary" href="/user/catalogo">Equipos</a>
                    <a class="nav-link text-custom-primary" href="/user/prestamos">Préstamos</a>
                    <!-- salir ir a login -->
                    <a class="nav-link text-custom-primary" href="#" onclick="logout()">Salir</a>
                </nav>

                <!-- Acciones usuario -->
                <div class="d-flex align-items-center gap-3">

                    <span id="viewModeLabel" class="small fw-semibold text-custom-primary">Usuario</span>

                    <a href="/user/catalogo" class="btn btn-link p-0 text-custom-primary">
                        <span id="viewModeIcon" class="material-symbols-outlined" style="font-size: 32px;">Usuario</span>
                    </a>

                    <button class="btn btn-link text-custom-primary p-2">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                    <button class="btn btn-link text-custom-primary p-2">
                        <span class="material-symbols-outlined">settings</span>
                    </button>
                    <div class="rounded-circle overflow-hidden border border-light shadow-sm"
                        style="width: 40px; height: 40px">
                        <img alt="User profile" class="w-100 h-100 object-fit-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDw7nxzJSdrR71CDJcr7kC74LjSK0FQ4lqpD3NebHzuBF2OKLcobdpMmXLTfXGjKhnwCsGDV2mN9HPKDx-tqzf23jNvhXxFM75S6XKC3LG98PiUY7HxPnK2lIS1M_4GLStSAhndvd9Qn50PRYC5jcBbYgw4BSiKmrhfaCEilKQgy1fH7KuLwKDGqS4BSOc6v4w6xKL722wJvMxtVxo4_cE_vR5qmR6qfuI_PH9r7xPCSIJzDIrCuivh_EQ1OP5_X1nTqItkinNBbVc" />
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container py-5">
        <!-- Hero Section -->
        <section class="row align-items-center mb-5 g-4">
            <div class="col-lg-7">
                <h1 class="display-4 fw-bold font-headline text-custom-primary lh-1 mb-3">
                    Catálogo de Equipos
                </h1>
                <p class="lead text-muted">
                    Herramientas de vanguardia para su investigación. Encuentre, reserve
                    y gestione sus equipos tecnológicos de forma centralizada.
                </p>
            </div>
            <div class="col-lg-5">
                <div class="hero-gradient p-4 p-md-5 rounded-4 text-white shadow position-relative overflow-hidden">
                    <div class="position-relative z-1">
                        <span class="badge bg-white text-custom-primary text-uppercase fw-bold mb-2">Estado
                            Global</span>
                        <div class="h2 fw-bold font-headline">94% Disponibilidad</div>
                        <div class="mt-3 d-flex align-items-center gap-2 small">
                            <span class="material-symbols-outlined" >check_circle</span>
                            <span>Sistemas en línea y operativos</span>
                        </div>
                    </div>
                    <span class="material-symbols-outlined position-absolute bottom-0 end-0 opacity-10"
                        style="font-size: 12rem; transform: translate(20%, 20%)">inventory_2</span>
                </div>
            </div>
        </section>

        <!-- Filtros y búsqueda -->
        <section class="mb-5">
            <div class="d-flex flex-column flex-lg-row gap-3 align-items-stretch mb-4">
                <!-- Buscador -->
                <div class="position-relative flex-grow-1">
                    <span
                        class="material-symbols-outlined position-absolute top-50 start-0 translate-middle-y ms-4 text-secondary">
                        search
                    </span>
                    <input type="text" class="form-control input-custom shadow-sm"
                        placeholder="Buscar por nombre, ID o especificación técnica..." />
                </div>

                <!-- Botones -->
                <div class="d-flex gap-3">
                    <button class="btn btn-filter d-flex align-items-center gap-2 border-soft">
                        <span class="material-symbols-outlined">tune</span>
                        Añadir Filtros
                        <span class="material-symbols-outlined small">expand_more</span>
                    </button>

                    <button class="btn btn-primary-custom-custom d-flex align-items-center gap-2 shadow-md">
                        <span class="material-symbols-outlined">person_search</span>
                        Ver Mis Equipos
                    </button>
                </div>
            </div>

            <!-- Filtros -->
            <div class="p-4 bg-white rounded-2xl border-soft shadow-sm">
                <div class="row g-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="label-custom">Categoría</label>
                        <select class="form-select mt-2 rounded-xl">
                            <option>Todos los equipos</option>
                            <option>Laptops</option>
                            <option>Cámaras</option>
                            <option>Accesorios</option>
                            <option>Audio</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="label-custom">Fabricante</label>
                        <select class="form-select mt-2 rounded-xl">
                            <option>Cualquier marca</option>
                            <option>Apple</option>
                            <option>Canon</option>
                            <option>Dell</option>
                            <option>Sony</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="label-custom">Duración Préstamo</label>
                        <select class="form-select mt-2 rounded-xl">
                            <option>Cualquier periodo</option>
                            <option>Corto (1-3 días)</option>
                            <option>Medio (4-10 días)</option>
                            <option>Largo (10+ días)</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="label-custom">Estado</label>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <span class="small fw-medium">Disponible ahora</span>
                            <input type="checkbox" class="form-check-input" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categorías / Chips -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4 overflow-auto pb-2">
            <button class="btn btn-primary-custom rounded-pill px-4">Todos</button>
            <button class="btn btn-outline-secondary rounded-pill px-4">
                Laptops
            </button>
            <button class="btn btn-outline-secondary rounded-pill px-4">
                Cámaras
            </button>
            <button class="btn btn-outline-secondary rounded-pill px-4">
                Accesorios
            </button>
            <button class="btn btn-outline-secondary rounded-pill px-4">
                Audio
            </button>

            <div class="ms-auto d-flex align-items-center gap-2 text-nowrap">
                <span class="text-muted small fw-bold text-uppercase">Ordenar:</span>
                <select class="form-select form-select-sm w-auto border-0 bg-transparent">
                    <option>Recientes</option>
                    <option>Nombre A-Z</option>
                    <option>Disponibilidad</option>
                </select>
            </div>
        </div>

        <!-- Grid de equipos -->
        <div id="equipmentContainer" class="row g-4"></div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <p class="page-info mb-0">Mostrando 6 de 28 artículos</p>
            <div class="d-flex gap-2" id="pagination"></div>
        </div>
    </main>

    <footer class="bg-surface-container-low py-5 mt-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img alt="OfficeLend Mini" class="h-8 w-8 rounded" src="../img/logo.png" />
                        <span class="fs-4 fw-bold text-custom-primary font-headline">OfficeLend</span>
                    </div>
                    <p class="text-muted">
                        Gestión organizacional de activos tecnológicos para la excelencia
                        operativa.
                    </p>
                </div>
                <div class="col-md-3">
                    <h5 class="font-headline fw-bold text-custom-primary mb-3">
                        Recursos
                    </h5>
                    <ul class="list-unstyled text-muted">
                        <li>
                            <a href="#" class="text-decoration-none">Guía de usuario</a>
                        </li>
                        <li>
                            <a href="#" class="text-decoration-none">Políticas de préstamo</a>
                        </li>
                        <li>
                            <a href="#" class="text-decoration-none">Soporte técnico</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="font-headline fw-bold text-custom-primary mb-3">
                        Legal
                    </h5>
                    <ul class="list-unstyled text-muted">
                        <li>
                            <a href="#" class="text-decoration-none">Términos de uso</a>
                        </li>
                        <li><a href="#" class="text-decoration-none">Privacidad</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" />
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-muted">
                <p>© 2024 OfficeLend. Plataforma de Control de Equipos.</p>
                <span class="badge bg-white border text-muted">v2.4.0 Edition</span>
            </div>
        </div>
    </footer>

    <script src="/js/usuario/catalogoUser.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
