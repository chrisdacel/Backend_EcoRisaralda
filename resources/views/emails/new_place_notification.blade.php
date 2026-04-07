@php
$frontendUrl = rtrim(config('app.frontend_url', config('app.url')), '/');
$logoUrl = $frontendUrl . '/images/Pagina_inicio/nature-svgrepo-com.svg';
$placeUrl = $frontendUrl . '/turista/sitio/' . $place->id;
$matched = is_array($matchedPreferences ?? null) ? $matchedPreferences : [];
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Albert Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background-color: #f8fafc; }
        .container { max-width: 650px; margin: 0 auto; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); }
        .logo-section { background-color: #ffffff; padding: 32px; text-align: center; border-bottom: 3px solid #10b981; }
        .logo-text { color: #10b981; font-size: 16px; font-weight: 700; letter-spacing: 1px; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 60px 32px; text-align: center; }
        .header h1 { margin: 0; color: #ffffff; font-size: 36px; font-weight: 700; line-height: 1.2; }
        .body { padding: 48px 32px; }
        .greeting { color: #1e293b; font-size: 15px; line-height: 1.6; margin: 0 0 24px 0; }
        .intro-text { color: #475569; font-size: 15px; line-height: 1.7; margin: 0 0 28px 0; }
        .place-image { width: 100%; max-width: 100%; border-radius: 8px; margin: 0 0 28px 0; }
        .place-info { background-color: #ffffff; border-left: 5px solid #10b981; padding: 24px; margin: 28px 0; border-radius: 8px; }
        .place-name { color: #1e293b; font-size: 24px; font-weight: 700; margin: 0 0 8px 0; }
        .place-slogan { color: #10b981; font-size: 15px; font-weight: 600; font-style: italic; margin: 0 0 16px 0; }
        .place-description { color: #475569; font-size: 15px; line-height: 1.7; margin: 0 0 20px 0; }
        .categories { margin: 20px 0; }
        .category-label { color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 12px 0; display: block; }
        .category-badge { display: inline-block; background-color: #dbeafe; color: #0369a1; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500; margin: 0 6px 6px 0; }
        .location-info { margin-top: 16px; color: #475569; font-size: 14px; line-height: 1.6; }
        .location-label { color: #10b981; font-weight: 600; }
        .benefits-section { margin: 32px 0; }
        .benefits-title { color: #1e293b; font-size: 16px; font-weight: 700; margin: 0 0 16px 0; }
        .benefits-list { list-style: none; }
        .benefits-list li { color: #475569; font-size: 14px; margin: 0 0 10px 0; padding-left: 24px; position: relative; }
        .benefits-list li:before { content: "✓"; position: absolute; left: 0; color: #10b981; font-weight: 700; }
        .cta-button { display: inline-block; background-color: #10b981; color: #ffffff; text-decoration: none; border-radius: 8px; padding: 14px 40px; font-weight: 600; font-size: 15px; margin: 28px 0; }
        .footer-section { padding: 40px 32px; text-align: center; }
        .footer-text { color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 16px 0; }
        .footer-link { color: #10b981; text-decoration: underline; }
        .footer-bottom { color: #94a3b8; font-size: 12px; line-height: 1.6; margin: 24px 0 0 0; padding-top: 24px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
<div class="container">
    <div class="logo-section">
        <p class="logo-text">Conexion EcoRisaralda</p>
    </div>

    <div class="header">
        <h1>Descubrimiento personalizado</h1>
    </div>

    <div class="body">
        <p class="greeting">Hola,</p>

        <p class="intro-text">Basándonos en tus preferencias, hemos identificado un lugar perfecto para ti. Creemos que te encantará explorarlo.</p>

        @if($place->cover)
        <img src="{{ asset('storage/' . $place->cover) }}" alt="{{ $place->name }}" class="place-image" />
        @endif

        <div class="place-info">
            <p class="place-name">{{ $place->name }}</p>
            
            @if($place->slogan)
            <p class="place-slogan">"{{ $place->slogan }}"</p>
            @endif

            <p class="place-description">{{ Illuminate\Support\Str::limit($place->description, 300) }}</p>

            @if(count($matched) > 0)
            <div class="categories">
                <span class="category-label">Categorías</span>
                @foreach($matched as $category)
                <span class="category-badge">{{ $category }}</span>
                @endforeach
            </div>
            @endif

            <div class="location-info">
                <span class="location-label">Ubicación:</span> {{ $place->localization }}
            </div>
        </div>

        <div class="benefits-section">
            <p class="benefits-title">Este sitio ofrece</p>
            <ul class="benefits-list">
                <li>Experiencias auténticas en la naturaleza</li>
                <li>Información verificada y reseñas de usuarios</li>
                <li>Oportunidades de aventura y relajación</li>
            </ul>
        </div>

        <center>
            <a href="{{ $placeUrl }}" class="cta-button">Explorar sitio completo</a>
        </center>
    </div>

    <div class="footer-section">
        <p class="footer-text">Recibiste este correo porque tienes activadas las notificaciones de descubrimientos.</p>
        <p class="footer-link"><a href="#" style="color: #10b981; text-decoration: underline;">Administrar preferencias</a></p>
        <p class="footer-bottom">© 2026 Conexion EcoRisaralda. Todos los derechos reservados.<br>
        Risaralda, Colombia</p>
    </div>
</div>
</body>
</html>