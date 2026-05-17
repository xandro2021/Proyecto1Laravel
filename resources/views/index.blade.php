<!-- index.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officelend - Gestión de Préstamos</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- Iconos -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

    <!-- CSS propio -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

    <div class="container-fluid vh-100 overflow-hidden">
        <div class="row h-100">

            <!-- Aside (imagen) -->
            <aside class="col-lg-6 d-none d-lg-block position-relative p-0">
                <img src="/img/login.png"
                    class="img-cover" alt="Comunidad">

                <div class="overlay d-flex flex-column justify-content-center justify-content-lg-end text-white p-5">
                    <header>
                        <span class="badge bg-warning text-dark mb-3">Officelend para Todos</span>
                        <h2 class="display-4 fw-bold">Comparte con Facilidad</h2>
                        <p>
                            Transforma la forma en que gestionas préstamos en tu oficina, hogar u organización.
                        </p>
                    </header>

                    <section class="d-flex gap-5 mt-4 border-top pt-4">
                        <div>
                            <strong class="fs-3">100%</strong>
                            <p class="small">Organización Personal</p>
                        </div>
                        <div>
                            <strong class="fs-3">Simple</strong>
                            <p class="small">Gestión de Préstamos</p>
                        </div>
                    </section>
                </div>
            </aside>

            <!-- Main -->
            <main class="col-lg-6 d-flex flex-column justify-content-center align-items-center p-4">

                <section class="card shadow-lg p-4 w-100" style="max-width: 500px;">

                    <header class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-primary">inventory_2</span>
                            <span class="fw-bold fs-5">officelend</span>
                        </div>

                        <h1 class="h3 fw-bold">Mi Officelend Personal</h1>
                        <p class="text-muted">Accede a tu Inventario</p>
                    </header>

                    <!-- Form -->
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="username" placeholder="admin">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>

                            <div class="input-group">
                                <input type="password" class="form-control" id="password">

                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                            </div>
                        </div>

                        <div id="errorMsg" class="text-danger small mb-2 d-none">Usuario o contraseña incorrectos.</div>
                        <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                    </form>

                    <footer class="text-center mt-3">
                        <p class="small">
                            ¿Nuevo? <a href="/register">Crea tu cuenta</a>
                        </p>
                    </footer>
                </section>

                <!-- Tips -->
                <section class="mt-5 w-100" style="max-width: 700px;">
                    <header class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h5">Tips y Novedades</h3>
                        <a href="#" class="small">Explorar</a>
                    </header>

                    <div class="row g-3">
                        <article class="col-md-6">
                            <div class="card h-100 p-3">
                                <small class="text-warning">Organización</small>
                                <h4 class="h6">Inventario del hogar en 5 minutos</h4>
                                <p class="small text-muted">Organiza herramientas fácilmente.</p>
                            </div>
                        </article>

                        <article class="col-md-6">
                            <div class="card h-100 p-3">
                                <small class="text-warning">Comunidad</small>
                                <h4 class="h6">Nuevas funciones para grupos</h4>
                                <p class="small text-muted">Comparte entre vecinos fácilmente.</p>
                            </div>
                        </article>
                    </div>
                </section>

                <!-- Footer -->
                <footer class="mt-5 text-center small text-muted">
                    <nav class="d-flex justify-content-center gap-3 mb-2">
                        <a href="#">Términos</a>
                        <a href="#">Privacidad</a>
                        <a href="#">Soporte</a>
                    </nav>
                    <p>© 2024 OFFICELEND</p>
                </footer>

            </main>

        </div>
    </div>

    <script type="text/javascript" src="{{ asset('js/login.js') }}"></script>
</body>

</html>
