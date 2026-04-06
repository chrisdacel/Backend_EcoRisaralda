@php
$frontendUrl = rtrim(config('app.frontend_url', config('app.url')), '/');
$logoUrl = $frontendUrl . '/images/Pagina_inicio/nature-svgrepo-com.svg';
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
        .message-text { color: #475569; font-size: 15px; line-height: 1.8; margin: 0 0 32px 0; }
        .cta-button { display: inline-block; background-color: #10b981; color: #ffffff; text-decoration: none; border-radius: 8px; padding: 14px 40px; font-weight: 600; font-size: 15px; margin: 28px 0; }
        .alert-box { background-color: #f0f9ff; border-left: 4px solid #0284c7; border-radius: 6px; padding: 16px; margin: 28px 0; }
        .alert-text { color: #0c4a6e; font-size: 13px; line-height: 1.6; margin: 0; }
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
        <h1>{{ $subjectLine }}</h1>
    </div>

    <div class="body">
        <p class="greeting">Hola,</p>

        <p class="message-text">{{ $messageText }}</p>

        <center>
            <a href="{{ $actionUrl }}" class="cta-button">{{ $actionLabel }}</a>
        </center>

        <div class="alert-box">
            <p class="alert-text">Si no realizaste esta solicitud o no reconoces esta actividad, por favor ignora este correo. Tu cuenta está segura.</p>
        </div>

        <p class="message-text">Si tienes dudas o necesitas ayuda, no dudes en contactarnos a través de nuestra plataforma.</p>
    </div>

    <div class="footer-section">
        <p class="footer-text">Este es un correo automático de Conexion EcoRisaralda. Por favor no respondas a este mensaje.</p>
        <p class="footer-link"><a href="#" style="color: #10b981; text-decoration: underline;">Contactar soporte</a></p>
        <p class="footer-bottom">© 2026 Conexion EcoRisaralda. Todos los derechos reservados.<br>
        Risaralda, Colombia</p>
    </div>
</div>
</body>
</html>
