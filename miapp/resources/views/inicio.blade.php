<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TECNICELL RM</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" type="image/png" href="{{ asset('Imagenes/Logo.webp') }}">

    <meta name="description" content="Bienvenidos a Fonrio, un sistema para gestionar inventario, compras, ventas y devoluciones de forma organizada y eficiente.">
</head>
<body>

    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000" data-bs-pause="false">

        <div class="position-absolute top-0 end-0 p-3" style="z-index: 10;">
            <a href="{{ route('login.form') }}" class="btn btn-primary" style="background-color: #778ee9;">Iniciar sesión</a>
        </div>

        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="4"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('Imagenes/Portada.jpg') }}" class="d-block w-100" style="height:300px; object-fit:cover">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('Imagenes/Portada 2.jpg') }}" class="d-block w-100" style="height:300px; object-fit:cover">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('Imagenes/Portada 3.jpg') }}" class="d-block w-100" style="height:300px; object-fit:cover">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('Imagenes/portada 4.jpg') }}" class="d-block w-100" style="height:300px; object-fit:cover">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('Imagenes/portada 5.jpg') }}" class="d-block w-100" style="height:300px; object-fit:cover">
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <header class="header">
        <div class="menu container">
            <img src="{{ asset('Imagenes/Logo.webp') }}" alt="Logo" class="logo">
        </div>
    </header>

    <div class="container text-center card mb-3">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="{{ asset('Imagenes/Logo.webp') }}" class="img-fluid rounded-start">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title">TECNICELL RM</h5><br>
                    <p class="card-text">
                        Sabemos lo mucho que te esfuerzas día a día para sacar adelante tu negocio.
                        Este proyecto busca mejorar la gestión de tu local de ventas de celulares y accesorios,
                        ayudándote a ahorrar tiempo y tener mayor control con una herramienta clara y fácil de usar.
                    </p>
                    <a href="#" class="btn btn-primary" style="background-color:#778ee9;">Más Información</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container seccion">
        <h2 class="titulo text-center">Celulares y Accesorios</h2><br><br>

        <div class="row justify-content-center" style="max-width:900px; margin:auto">
            <div class="col-md-6">
                <h2 class="categoria">Tipos de celulares</h2>
                <ul>
                    <li><strong>Gama baja:</strong> Básicos y económicos.</li>
                    <li><strong>Gama media:</strong> Buen rendimiento.</li>
                    <li><strong>Gama alta:</strong> Máxima potencia.</li>
                </ul>
            </div>

            <div class="col-md-6">
                <h2 class="categoria">Tipos de accesorios</h2>
                <ul>
                    <li><strong>Protección:</strong> Fundas.</li>
                    <li><strong>Carga:</strong> Cargadores y cables.</li>
                    <li><strong>Audio:</strong> Audífonos y parlantes.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container text-center">
        <h2>Celulares</h2>
        <div class="celulares group">
            <div class="celular">
                <img src="{{ asset('Imagenes/Redmi.jpg') }}">
                <h3>iPhone 15</h3>
                <p>Pantalla OLED, cámara 48 MP, chip A16, USB-C.</p>
            </div>

            <div class="celular">
                <img src="{{ asset('Imagenes/Iphone32.jpg') }}">
                <h3>Redmi 13</h3>
                <p>Helio G91, 8 GB RAM, 256 GB.</p>
            </div>

            <div class="celular">
                <img src="{{ asset('Imagenes/Samsung.jpg') }}">
                <h3>Samsung Galaxy A55</h3>
                <p>Pantalla 120Hz, 8 GB RAM.</p>
            </div>
        </div>
    </div>

    <div class="container text-center">
        <h2>Accesorios</h2>
        <div class="accesorios-container">
            <div class="accesorio">
                <img src="{{ asset('Imagenes/Forros para celular.jpg') }}">
                <h3>Forros</h3>
                <p>Silicona, cuero y reforzados.</p>
            </div>

            <div class="accesorio">
                <img src="{{ asset('Imagenes/Audífonos.jpg') }}">
                <h3>Audífonos</h3>
                <p>Bluetooth y con micrófono.</p>
            </div>

            <div class="accesorio">
                <img src="{{ asset('Imagenes/Cargadores.jpg') }}">
                <h3>Cargadores</h3>
                <p>Carga rápida e inalámbrica.</p>
            </div>
        </div>

        <a href="#" class="btn-1 mt-3">Información</a>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-row">
                <div>
                    <h4>Fonrio</h4>
                    <ul>
                        <li><a href="#">¿Quiénes somos?</a></li>
                        <li><a href="#">¿Por qué Fonrio?</a></li>
                        <li><a href="#">Política de privacidad</a></li>
                    </ul>
                </div>

                <div>
                    <h4>Ayuda</h4>
                    <ul>
                        <li><a href="#">Preguntas frecuentes</a></li>
                    </ul>
                </div>

                <div>
                    <h4>Tienda</h4>
                    <ul>
                        <li><a href="#">Celulares</a></li>
                        <li><a href="#">Accesorios</a></li>
                        <li><a href="#">Integrantes</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <p class="copy">Copyright © 2025 Fonrio</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
