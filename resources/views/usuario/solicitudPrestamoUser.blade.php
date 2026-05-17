<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Solicitud de Préstamo - OfficeLend</title>
    <script type="text/javascript" src="/js/usuario/userGuard.js"></script>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/user/solicitudPrestamoUser.css') }}" type="text/css" media="screen" >
</head>

<body>
    <!-- solicitudPrestamoUser.html -->
    <header class="sticky-top bg-white shadow-sm" style="backdrop-filter: blur(8px);">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Logo -->
                <div class="d-flex align-items-center gap-2">
                    <img alt="OfficeLend Logo" class="h-10 w-10 rounded-3" src="/img/logo.png">
                    <span class="fs-3 fw-bold text-custom-primary font-headline tracking-tight">OfficeLend</span>
                </div>

                <!-- Navegación desktop -->
                <nav class="d-none d-md-flex gap-4">

                    <a class="nav-link text-custom-primary " href="/user/catalogo">Equipos</a>
                    <a class="nav-link active text-custom-primary " href="/user/prestamos">Préstamos</a>
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
                        style="width: 40px; height: 40px;">
                        <img alt="User profile" class="w-100 h-100 object-fit-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDw7nxzJSdrR71CDJcr7kC74LjSK0FQ4lqpD3NebHzuBF2OKLcobdpMmXLTfXGjKhnwCsGDV2mN9HPKDx-tqzf23jNvhXxFM75S6XKC3LG98PiUY7HxPnK2lIS1M_4GLStSAhndvd9Qn50PRYC5jcBbYgw4BSiKmrhfaCEilKQgy1fH7KuLwKDGqS4BSOc6v4w6xKL722wJvMxtVxo4_cE_vR5qmR6qfuI_PH9r7xPCSIJzDIrCuivh_EQ1OP5_X1nTqItkinNBbVc">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ── Main Content ── -->
    <main class="container py-5">

        <!-- Page Header -->
        <header class="mb-5">
            <h1 class="page-title">Solicitud de Préstamo</h1>
            <p class="page-subtitle">Complete los detalles de su solicitud para la reserva de equipo institucional.
                Nuestras revisiones se procesan en un plazo de 24 horas.</p>
        </header>

        <div class="row g-4 align-items-start">

            <!-- ── Equipment Sidebar ── -->
            <div class="col-12 col-lg-4">
                <div class="d-flex flex-column gap-3">

                    <!-- Equipment Card -->
                    <div class="equipment-card">
                        <div class="equipment-image-wrap">
                            <img id="equipment-image" src="" alt="Equipo" />
                        </div>
                        <div>
                            <span class="item-badge">Item Seleccionado</span>
                            <p id="equipment-name" class="item-title mb-3"></p>
                            <div class="row meta-divider g-3">
                                <div class="col-6">
                                    <span class="meta-label">ID Activo</span>
                                    <span class="meta-value" id="equipment-id"></span>
                                </div>
                                <div class="col-6">
                                    <span class="meta-label">Categoría</span>
                                    <span class="meta-value" id="equipment-type"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Banner -->
                    <div class="info-banner">
                        <span class="material-symbols-outlined">info</span>
                        <p>Este equipo requiere autorización de nivel 2. Asegúrese de que su justificación sea
                            detallada.</p>
                    </div>

                </div>
            </div>

            <!-- ── Request Form ── -->
            <div class="col-12 col-lg-8">
                <div class="form-section">
                    <form>

                        <!-- Justificación -->
                        <div class="form-block">
                            <label class="form-section-title" for="justificacion">Justificación del Préstamo</label>
                            <textarea class="ol-textarea" id="justificacion" rows="4"
                                placeholder="Describa el proyecto o investigación para el cual requiere este equipo..."></textarea>
                        </div>

                        <!-- Fechas -->
                        <div class="form-block">
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-section-title" for="fecha-devolucion">Fecha de Devolución
                                        Estimada</label>
                                    <input class="ol-input" id="fecha-devolucion" type="date" />
                                </div>
                            </div>
                        </div>

                        <!-- Compliance -->
                        <div class="form-block">
                            <div class="compliance-row">
                                <input type="checkbox" id="compliance" />
                                <label for="compliance">Acepto los términos y condiciones de uso del equipo y me
                                    comprometo a su cuidado institucional.</label>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="form-block d-flex flex-column flex-md-row-reverse align-items-center gap-3 pt-2">
                            <button type="submit" class="btn-submit w-100 w-md-auto">Enviar Solicitud</button>
                            <a class="btn-cancel w-100 w-md-auto" href="/user/catalogo">Cancelar</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </main>

    <!-- ── Bottom Mobile Nav ── -->
    <nav class="bottom-nav">
        <a href="#" class="bottom-nav-item">
            <span class="material-symbols-outlined">home</span>
            <span class="label">Home</span>
        </a>
        <a href="#" class="bottom-nav-item">
            <span class="material-symbols-outlined">search</span>
            <span class="label">Search</span>
        </a>
        <a href="#" class="bottom-nav-item active">
            <span class="material-symbols-outlined">inventory_2</span>
            <span class="label">Loans</span>
        </a>
        <a href="#" class="bottom-nav-item">
            <span class="material-symbols-outlined">account_circle</span>
            <span class="label">Profile</span>
        </a>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/usuario/solicitudPrestamoUser.js"></script>
</body>

</html>
