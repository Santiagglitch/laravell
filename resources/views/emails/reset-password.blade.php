<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6fc;
            font-family: Arial, sans-serif;
        }
        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: white;
            margin: 16px 0 0 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            color: rgba(255,255,255,0.85);
            margin: 8px 0 0 0;
            font-size: 14px;
        }
        .body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 16px;
        }
        .text {
            font-size: 15px;
            color: #4b5563;
            line-height: 1.7;
            margin-bottom: 16px;
        }
        .btn-container {
            text-align: center;
            margin: 32px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .expiry-box {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 14px 18px;
            margin: 24px 0;
            font-size: 14px;
            color: #92400e;
        }
        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 28px 0;
        }
        .link-text {
            font-size: 13px;
            color: #6b7280;
            word-break: break-all;
        }
        .footer {
            background: #f9fafb;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            font-size: 13px;
            color: #9ca3af;
            margin: 4px 0;
        }
        .security-note {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 14px 18px;
            margin: 24px 0;
            font-size: 14px;
            color: #166534;
        }
    </style>
</head>
<body>
    <div class="wrapper">

        <div class="header">
            <h1>TECNICELL RM</h1>
            <p>Sistema de Gestión de Inventarios</p>
        </div>

        <div class="body">

            <p class="greeting">Hola, {{ $nombre }} 👋</p>

            <p class="text">
                Recibimos una solicitud para restablecer la contraseña de tu cuenta en
                <strong>TECNICELL RM</strong>.
            </p>

            <p class="text">
                Haz clic en el botón de abajo para crear una nueva contraseña:
            </p>

            <div class="btn-container">
                <a href="{{ $resetUrl }}" class="btn">
                    🔑 Restablecer mi contraseña
                </a>
            </div>

            <div class="expiry-box">
                <strong>Este enlace expirará en 60 minutos.</strong>
                Si no lo usas a tiempo, deberás solicitar uno nuevo.
            </div>

            <div class="security-note">
                <strong>¿No solicitaste este cambio?</strong>
                Si no fuiste tú, puedes ignorar este correo. Tu contraseña no cambiará.
            </div>

            <hr class="divider">

            <p class="text" style="font-size:13px; color:#6b7280;">
                Si el botón no funciona, copia y pega este enlace en tu navegador:
            </p>
            <p class="link-text">{{ $resetUrl }}</p>

        </div>

        <div class="footer">
            <p>© {{ date('Y') }} TECNICELL RM — Sistema de Gestión de Inventarios</p>
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        </div>

    </div>
</body>
</html>