@php
$frontendUrl = rtrim(config('app.frontend_url', config('app.url')), '/');
$logoUrl = $frontendUrl . '/images/Pagina_inicio/nature-svgrepo-com.svg';
$placeUrl = $frontendUrl . '/turista/sitio/' . $place->id . '#evento-' . $event->id;
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
        .event-section { background-color: #ffffff; border-left: 5px solid #10b981; padding: 24px; margin: 28px 0; border-radius: 8px; }
        .event-title { color: #1e293b; font-size: 20px; font-weight: 700; margin: 0 0 8px 0; }
        .event-site { color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; }
        .event-description { color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 20px 0; }
        .event-details { color: #475569; font-size: 14px; line-height: 1.8; }
        .event-details p { margin: 0 0 8px 0; }
        strong { color: #10b981; font-weight: 600; }
        .benefits-section { margin: 32px 0; }
        .benefits-title { color: #1e293b; font-size: 16px; font-weight: 700; margin: 0 0 16px 0; }
        .benefits-list { list-style: none; }
        .benefits-list li { color: #475569; font-size: 15px; margin: 0 0 12px 0; padding-left: 24px; position: relative; }
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
        <h1>Nuevo evento disponible</h1>
    </div>

    <div class="body">
        <p class="greeting">Hola {{ $user->name }},</p>

        <p class="intro-text">Te comunicamos que se ha publicado un nuevo evento en uno de tus sitios favoritos. Este podría ser de tu interés.</p>

        <div class="event-section">
            <p class="event-site">Sitio: {{ $place->name }}</p>
            <p class="event-title">{{ $event->title }}</p>
            
            @if($event->description)
            <p class="event-description">{{ $event->description }}</p>
            @endif

            <div class="event-details">
                <p><strong>Inicio:</strong> {{ $event->starts_at->format('d \\d\\e M \\d\\e Y \\a \\l\\a\\s H:i') }}</p>
                @if($event->ends_at)
                <p><strong>Fin:</strong> {{ $event->ends_at->format('d \\d\\e M \\d\\e Y \\a \\l\\a\\s H:i') }}</p>
                @endif
            </div>
        </div>

        <div class="benefits-section">
            <p class="benefits-title">¿Por qué deberías estar atento a este evento?</p>
            <ul class="benefits-list">
                <li>Experiencias únicas en la naturaleza</li>
                <li>Actividades verificadas y seguras</li>
                <li>Información detallada y reseñas de otros usuarios</li>
            </ul>
        </div>

        <center>
            <a href="{{ config('app.frontend_url', config('app.url')) }}/turista/sitio/{{ $place->id }}#evento-{{ $event->id }}" class="cta-button">
                Ver evento completo
            </a>
        </center>

        <p class="intro-text">No te pierdas esta oportunidad de vivir una aventura en la provincia de Risaralda.</p>
    </div>

    <div class="footer-section">
        <p class="footer-text">Recibiste este correo porque tienes activadas las notificaciones en Conexion EcoRisaralda.</p>
        <p class="footer-link"><a href="#" style="color: #10b981; text-decoration: underline;">Administrar preferencias de notificaciones</a></p>
        <p class="footer-bottom">© 2026 Conexion EcoRisaralda. Todos los derechos reservados.<br>
        Risaralda, Colombia</p>
    </div>
</div>
</body>
</html>

        <div class="event-details">
            <p class="place-name">{{ $place->name }}</p>
            <h3>{{ $event->title }}</h3>

            @if($event->description)
            <p class="desc">{{ $event->description }}</p>
            @endif

            <div class="info-row">
                <span class="info-label">Inicio:</span>
                <span class="info-value">{{ $event->starts_at->format('d/m/Y \a \l\a\s H:i') }}</span>
            </div>
            @if($event->ends_at)
            <div class="info-row">
                <span class="info-label">Fin:</span>
                <span class="info-value">{{ $event->ends_at->format('d/m/Y \a \l\a\s H:i') }}</span>
            </div>
            @endif
        </div>

        <div class="button-wrapper">
            <a href="{{ $placeUrl }}" class="button">Ver evento completo</a>
        </div>
    </div>

    <div class="footer">
        <p>Recibiste este correo porque tienes notificaciones activas.</p>
        <p style="margin-top: 8px;">© {{ date('Y') }} Conexión EcoRisaralda</p>
    </div>
</div>
</body>
</html>
