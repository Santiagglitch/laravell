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
                        <li><a href="{{ route('pie.pag') }}#quienes-somos">¿Quiénes somos?</a></li>
                        <li> <a href="{{ route('pie.pag') }}#porque-fonrio">¿Por qué Fonrio?</a></li>
                        <li><a href="{{ route('pie.pag') }}#politica-privacidad">Política de privacidad</a></li>
                    </ul>
                </div>

                <div class="Ayuda">
                    <h4>Ayuda</h4>
                    <ul>
                        <li><a href="{{ route('pie.pag') }}#preguntas-frecuentes">Preguntas frecuentes</a></li>
                    </ul>
                </div>

                <div class="Tienda">
                    <h4>Tienda</h4>
                    <ul>
                        <li ><a href="{{ route('pie.pag') }}#celulares">Celulares</a></li>
                        <li><a href="{{ route('pie.pag') }}#accesorio">Accesorios</a></li>
                        <li><a href="{{ route('pie.pag') }}#Integrantes">Integrantes</a></li>
                    </ul>
                </div>

                <div class="Clases_link">
                    <h4>Síguenos</h4>
                    <div class="social-links">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                        <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
                        <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                        </svg>
                        <a href="https://wa.me/573142600632?text=Hola%20FONRIO%2C%20quiero%20más%20información"
                        target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                        <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                        </svg>


                    </div>
                </div>
            </div>
        </div>
        <p class="copy">Copyright © 2025 Fonrio</p>
    </footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>  