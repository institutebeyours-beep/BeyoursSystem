
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚧 Sistema en mantenimiento</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
            padding: 1rem;
        }
        
        .container {
            background: white;
            padding: 3rem;
            border-radius: 1.5rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 500px;
            width: 100%;
            animation: fadeIn 0.6s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .icon {
            font-size: 4.5rem;
            margin-bottom: 1rem;
            display: block;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: #4f46e5;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        
        h1 {
            color: #1f2937;
            margin-bottom: 0.75rem;
            font-size: 1.75rem;
            font-weight: 700;
        }
        
        .message {
            color: #6b7280;
            margin-bottom: 1.5rem;
            line-height: 1.6;
            font-size: 1rem;
        }
        
        .status {
            background: #f3f4f6;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            color: #6b7280;
            display: inline-block;
        }
        
        .status span {
            font-weight: 600;
            color: #4f46e5;
        }
        
        .admin-badge {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.5rem 1.5rem;
            background: #10b981;
            color: white;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
            font-size: 0.75rem;
            color: #9ca3af;
        }
        
        @media (max-width: 640px) {
            .container {
                padding: 2rem 1.5rem;
            }
            
            .icon {
                font-size: 3.5rem;
            }
            
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🚀 Beyours</div>
        <span class="icon">🛠️</span>
        <h1>Estamos en mantenimiento</h1>
        <p class="message">{{ $message ?? 'Sistema en mantenimiento. Por favor, vuelve más tarde.' }}</p>
        <div class="status">
            ⏳ Estimado: <span>{{ now()->addHours(2)->format('H:i') }}</span>
        </div>
        
        @auth
            @if(auth()->user()->hasRole('super-admin'))
                <div class="admin-badge">🔑 Acceso Super-Admin disponible</div>
            @endif
        @endauth
        
        <div class="footer">
            © {{ date('Y') }} Beyours. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>