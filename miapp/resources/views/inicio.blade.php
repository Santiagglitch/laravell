<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TECNICELL RM</title>

    {{-- CSS local --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- Fuente --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('Imagenes/Logo.webp') }}">

    <meta name="description" content="Bienvenidos a Fonrio, un sistema para gestionar inventario, compras, ventas y devoluciones de forma organizada y eficiente.">
</head>
<body>

    {{-- CARRUSEL --}}
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000" data-bs-pause="false">

        <div class="position-absolute top-0 end-0 p-3" style="z-index: 10;">
            {{-- Cuando tengas login en Laravel, cambia href="#" por route('login') --}}
           <a href="{{ route('login.form') }}" class="btn btn-primary" style="background-color: #778ee9;">Iniciar sesión</a>

        </div>

        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" aria-label="Slide 4"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="4" aria-label="Slide 5"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('Imagenes/Portada.jpg') }}" class="d-block w-100" style="height: 300px; object-fit: cover" alt="Portada 1">
            </div>

            <div class="carousel-item">
                <img src="{{ asset('Imagenes/Portada 2.jpg') }}" class="d-block w-100" style="height: 300px; object-fit: cover" alt="Portada 2">
            </div>

            <div class="carousel-item">
                <img src="{{ asset('Imagenes/Portada 3.jpg') }}" class="d-block w-100" style="height: 300px; object-fit: cover" alt="Portada 3">
            </div>

            <div class="carousel-item">
                <img src="{{ asset('Imagenes/portada 4.jpg') }}" class="d-block w-100" style="height: 300px; object-fit: cover" alt="Portada 4">
            </div>

            <div class="carousel-item">
                <img src="{{ asset('Imagenes/portada 5.jpg') }}" class="d-block w-100" style="height: 300px; object-fit: cover" alt="Portada 5">
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>

    <header class="header">
        <div class="menu container">
            <img src="{{ asset('Imagenes/Logo.webp') }}" alt="Logo esquina" class="logo">
        </div>
    </header>

    <div class="container text-center card mb-3">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="{{ asset('Imagenes/Logo.webp') }}" class="img-fluid rounded-start" alt="logo">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title">TECNICELL RM</h5><br>
                    <p class="card-text">
                        Sabemos lo mucho que te esfuerzas día a día para sacar adelante tu negocio.
                        Por eso, este proyecto está pensado especialmente en ti. Busca mejorar y hacer
                        más eficiente la gestión de tu local de ventas de celulares y accesorios.
                        Queremos ayudarte a tener un mayor control, ahorrar tiempo y facilitar tu trabajo
                        con una herramienta moderna, clara y fácil de usar. Porque tu negocio merece crecer
                        con el apoyo de soluciones que realmente te impulsen.
                    </p>
                    <a href="#" class="btn btn-primary" style="background-color: #778ee9;">Más Información</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container seccion">
        <h2 class="titulo text-center">Celulares y Accesorios</h2>
        <br><br>

        <div class="d-flex justify-content-center">
            <div class="row my-2 w-100" style="max-width: 900px;">

                <div class="col-md-6">
                    <h2 class="categoria">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-phone" viewBox="0 0 16 16">
                            <path d="M11 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                            <path d="M8 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                        </svg> Tipos de celulares
                    </h2>
                    <ul>
                        <li><strong>Gama baja:</strong> Básicos y económicos.</li>
                        <li><strong>Gama media:</strong> Buen rendimiento a precio justo.</li>
                        <li><strong>Gama alta:</strong> Potentes, con mejores cámaras y pantallas.</li>
                    </ul>
                </div>

                <div class="col-md-6">
                    <h2 class="categoria">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-headphones" viewBox="0 0 16 16">
                            <path d="M8 3a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V8a6 6 0 1 1 12 0v5a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h1V8a5 5 0 0 0-5-5"/>
                        </svg> Tipos de accesorios
                    </h2>
                    <ul>
                        <li><strong>Protección:</strong> Fundas.</li>
                        <li><strong>Carga:</strong> Cargadores, cables.</li>
                        <li><strong>Audio:</strong> Audífonos, parlantes.</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <div class="container">
        <div class="container text-center">
            <h2>Celulares</h2>
        </div>
        <div class="celulares group">
            <div class="celular">
                <img src="{{ asset('Imagenes/Redmi.jpg') }}" alt="iPhone 15">
                <h3>iPhone 15</h3>
                <p>Pantalla OLED de 6.1", cámara de 48 MP, chip A16 Bionic, carga MagSafe, USB-C, 5G.</p>
            </div>

            <div class="celular">
                <img src="{{ asset('Imagenes/Iphone32.jpg') }}" alt="Redmi 13">
                <h3>Redmi 13</h3>
                <p>Helio G91-Ultra, 8 GB RAM, 256 GB almacenamiento, cámara 108 MP, batería 5030 mAh, carga 33W.</p>
            </div>

            <div class="celular">
                <img src="{{ asset('Imagenes/Samsung.jpg') }}" alt="Samsung Galaxy A55">
                <h3>Samsung Galaxy A55</h3>
                <p>Pantalla 6.6" 120Hz, Exynos 1480, 8 GB RAM, cámara 50 MP, batería 5000 mAh, carga 25W.</p>
            </div>
        </div>
    </div>

    <div class="container text-center">
        <h2>Accesorios</h2>
        <div class="accesorios-container container">
            <div class="accesorio">
                <img src="{{ asset('Imagenes/Forros para celular.jpg') }}" alt="Forros para celular">
                <h3>Forros para celular</h3>
                <p>Silicona, plástico, cuero, billetera, rugerizados, con batería, transparentes.</p>
            </div>

            <div class="accesorio">
                <img src="{{ asset('Imagenes/Audífonos.jpg') }}" alt="Audífonos">
                <h3>Audífonos</h3>
                <p>In-ear, on-ear, over-ear, Bluetooth, cancelación de ruido, micrófono, resistencia al agua.</p>
            </div>

            <div class="accesorio">
                <img src="{{ asset('Imagenes/Cargadores.jpg') }}" alt="Cargadores">
                <h3>Cargadores</h3>
                <p>USB-A, USB-C, Lightning, inalámbricos (Qi), carga rápida, portátiles y solares.</p>
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="#" class="btn-1">Información</a><br><br>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-row">

                <div class="Fonrio">
                    <h4>Fonrio</h4>
                    <ul>
                        <li><a href="#">¿Quiénes somos?</a></li>
                        <li><a href="#">¿Por qué Fonrio?</a></li>
                        <li><a href="#">Política de privacidad</a></li>
                    </ul>
                </div>

                <div class="Ayuda">
                    <h4>Ayuda</h4>
                    <ul>
                        <li><a href="#">Preguntas frecuentes</a></li>
                    </ul>
                </div>

                <div class="Tienda">
                    <h4>Tienda</h4>
                    <ul>
                        <li><a href="#">Celulares</a></li>
                        <li><a href="#">Accesorios</a></li>
                        <li><a href="#">Integrantes</a></li>
                    </ul>
                </div>

                <div class="Clases_link">
                    <h4>Síguenos</h4>
                    <div class="social-links">
                        {{-- Aquí tus SVG de redes como ya los tenías --}}
                        {{-- ... --}}
                    </div>
                </div>

            </div>
        </div>
        <p class="copy">Copyright © 2025 Fonrio</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
