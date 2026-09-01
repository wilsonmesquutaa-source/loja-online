<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Em Construção</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            width: 100%;
        }

        .icon {
            font-size: 64px;
            margin-bottom: 20px;
            display: inline-block;
            animation: pulse 2s infinite ease-in-out;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: #ffffff;
        }

        p {
            font-size: 1.1rem;
            color: #94a3b8;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .form-container {
            display: flex;
            gap: 10px;
            max-width: 450px;
            margin: 0 auto;
        }

        input[type="email"] {
            flex: 1;
            padding: 14px 18px;
            border-radius: 8px;
            border: 1px solid #334155;
            background-color: #1e293b;
            color: #ffffff;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s;
        }

        input[type="email"]:focus {
            border-color: #3b82f6;
        }

        button {
            padding: 14px 24px;
            border-radius: 8px;
            border: none;
            background-color: #3b82f6;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2563eb;
        }

        .footer {
            margin-top: 48px;
            font-size: 0.875rem;
            color: #64748b;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        @media (max-width: 480px) {
            .form-container {
                flex-direction: column;
            }
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="icon">🚀</div>
        <h1>Estamos preparando algo incrível!</h1>
        <p>Nosso novo site está em desenvolvimento. Deixe seu e-mail abaixo para ser notificado assim que lançarmos.</p>
        
        <form class="form-container" onsubmit="event.preventDefault(); alert('Obrigado! Avisaremos você em breve.');">
            <input type="email" placeholder="Digite seu e-mail" required>
            <button type="submit">Notificar-me</button>
        </form>

        <div class="footer">
            &copy; 2026 Seu Nome ou Empresa. Todos os direitos reservados.
        </div>
    </div>

</body>
</html>