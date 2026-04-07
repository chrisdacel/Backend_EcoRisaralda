<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="img/svg" href="./img/sitios_ecoturisticos/nature-svgrepo-com.svg">
    @vite(['resources/css/app.css', 'resources/css/style_Coleccion.css', 'resources/js/app.js'])
    <title>Conexión EcoRisaralda</title>
    <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- header -->
    <header class="header">
        <div id="logotipo">
            <a href="/"><img src="{{ asset('img/Pagina_inicio/nature-svgrepo-com.svg') }}" alt="Logo empresa" id="logo"></a>
            <div>
                <a class="header-brand" href="/"><h3>Conexion</h3><h5>EcoRisaralda</h5></a>
            </div>
        </div>
        
        <div id="parte_derecha">
            @auth
                <a href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit">Cerrar Sesión</button>
                </form>
            @else
                <a class="btn_register_navbar" href="{{ route('register') }}"><button id="register_btn">Registrarse</button></a>
                <a class="btn_register_navbar" href="{{ route('login') }}"><button id="loguin_btn">Iniciar Sesión</button></a>
            @endauth

            <div class="dropdown">
                <img src="{{ asset('img/Pagina_inicio/img_drop_down.png') }}" alt="Menú" id="img_dropdown">
                <div class="dropdown-info">
                    <a class="link_btn_responsive" href="/"><button class="dropdown-item"><p>Inicio</p></button></a>
                    <a class="link_btn_responsive" href="/Coleccion"><button class="dropdown-item"><p>Colección</p></button></a>
                    <a class="link_btn_responsive" href="/sobre-nosotros"><button class="dropdown-item"><p>Acerca Nosotros</p></button></a>
                </div>
            </div>
        </div>
    </header>

    <button id="botonArriba" title="Volver arriba">
        <img src="{{ asset('img/Coleccion_sitios_ecoturisticos/arrow-up2.svg') }}" alt="">
    </button>

    <main>
        <section id="seccion_01">
            <div id="seccion_mejores_destinos">
                <div id="contenedor_mejores_destinos">
                    <div id="parte_izquierda_slider">
                        <div class="carousel-container_mejores_destinos">
                            <div class="slider-center-large">
                                <div class="item"><img src="{{ asset('img/Coleccion_sitios_ecoturisticos/ecoturismo.jpg') }}" alt="Imagen 1"></div>
                                <div class="item"><img src="{{ asset('img/Coleccion_sitios_ecoturisticos/Nevado-del-Tolima-WalterV-1024x683.jpeg') }}" alt="Imagen 3"></div>
                                <div class="item"><img src="{{ asset('img/Coleccion_sitios_ecoturisticos/Pasadia-termales-de-santa-rosa-de-cabal-y-filandia.webp') }}" alt="Imagen 4"></div>
                                <div class="item"><img src="{{ asset('img/Coleccion_sitios_ecoturisticos/photo-1532185922611-3410b1898a1c.jpg') }}" alt="Imagen 5"></div>
                                <div class="item"><img src="{{ asset('img/Coleccion_sitios_ecoturisticos/Santuario-Fauna-Flora-Otun-Quimbaya-Ucumari-13.jpg') }}" alt="Imagen 5"></div>
                                <div class="item"><img src="{{ asset('img/Coleccion_sitios_ecoturisticos/guasimo.jpg') }}" alt="Imagen 2"></div>
                            </div>
                        </div>
                    </div>

                    <div id="parte_derecha_titulo">
                        <div class="titulos_mejores_sitios">
                            <h1>Conoce los mejores destinos</h1>
                            <h1>turísticos en un clic</h1>
                        </div>
                        <div class="buscar">
                            <form action="/Coleccion" method="GET">
                                <input type="text" 
                                       name="search" 
                                       placeholder="Buscar en EcoRisaralda..." 
                                       class="buscador"
                                       value="{{ request('search') }}">
                                <button type="submit">
                                    <img src="{{ asset('img/Pagina_inicio/search-svgrepo-com.svg') }}" alt="Buscar" class="lupe">
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Colección de sitios -->
        <section id="seccion_02">
            <div class="container">
                @if(request('search'))
                    <h2 class="search-results-title">
                        Resultados para: "{{ request('search') }}" 
                        <span>({{ $places->count() }} encontrados)</span>
                    </h2>
                @else
                    <h2 class="search-results-title">Todos los sitios turísticos</h2>
                @endif

                <div class="slider">
                    <button class="atras_btn"><span class="flecha">&lsaquo;</span></button>
                    
                    <div class="contenedor_carrusel">
                        <div class="contenedor_items">
                            <div class="visible">
                                @forelse($places as $place)
                                    <a href="/Sitio/{{ $place->id }}">
                                        <div class="place-card">
                                            <img src="{{ url('/api/files/' . $place->cover) }}" alt="{{ $place->name }}">
                                            <div>
                                                <h5>{{ $place->name }}</h5>
                                                <h5>{{ $place->slogan }}</h5>
                                            </div>
                                            <p>{{ $place->localization }}</p>
                                        </div>
                                    </a>
                                @empty
                                    <div class="no-results">
                                        <p>No se encontraron sitios que coincidan con tu búsqueda.</p>
                                        <a href="/Coleccion" class="btn-back">Ver todos los sitios</a>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    
                    <button class="adelante_btn"><span class="flecha">&rsaquo;</span></button>
                </div>
            </div>
        </section>
    </main>

    <!-- footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-col">
                <h2>Conexion</h2>
                <p>EcoRisaralda</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
                <div class="language-selector">
                    🌐
                    <select>
                        <option>Español</option>
                        <option>English</option>
                    </select>
                </div>
            </div>
            
            <div class="footer-col">
                <h4>Información</h4>
                <ul>
                    <li><a href="/">Conexión EcoRisaralda</a></li>
                    <li><a href="/">Descripción</a></li>
                    <li><a href="/">Lema</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>Navegación rápida</h4>
                <ul>
                    <li><a href="/">Inicio</a></li>
                    <li><a href="/sobre-nosotros">Sobre nosotros</a></li>
                    <li><a href="/privacidad">Políticas</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>Contacto y soporte</h4>
                <ul>
                    <li><a href="mailto:ecorisaralda@contacto.com">ecorisaralda@contacto.com</a></li>
                    <li><a href="#">300 445 80055</a></li>
                    <li><a href="#">Preguntas</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p><em>Conectando viajeros con la naturaleza. Explora, guarda y comparte experiencias únicas.</em></p>
            <p>© 2025 Conexión EcoRisaralda – Todos los derechos reservados.</p>
        </div>
    </footer>

    <script type="text/javascript" src="{{ asset('js/main_Coleccion.js') }}"></script>
</body>
</html>