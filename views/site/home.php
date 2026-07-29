<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sua Loja | Em Breve</title>
    <!-- Fonte Google Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --accent-color: #2563eb; /* Cor principal (Altere para a cor da sua marca) */
            --accent-hover: #1d4ed8;
            --bg-color: #0f172a;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            /* Efeito de luzes no fundo */
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.2) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(147, 51, 234, 0.15) 0px, transparent 50%);
        }

        .container {
            max-width: 620px;
            width: 100%;
            text-align: center;
            background: var(--glass-bg);
            padding: 48px 32px;
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 24px;
            color: var(--text-main);
            text-transform: uppercase;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(37, 99, 235, 0.15);
            color: #60a5fa;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 24px;
            border: 1px solid rgba(96, 165, 250, 0.25);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #60a5fa;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(96, 165, 250, 0.7);
            animation: pulse 1.6s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(96, 165, 250, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(96, 165, 250, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(96, 165, 250, 0); }
        }

        h1 {
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            color: var(--text-muted);
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .form-group {
            display: flex;
            gap: 10px;
            margin-bottom: 32px;
        }

        input[type="email"] {
            flex: 1;
            padding: 16px 20px;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            background: rgba(15, 23, 42, 0.6);
            color: #fff;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="email"]:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25);
        }

        button {
            padding: 16px 28px;
            border-radius: 12px;
            border: none;
            background: var(--accent-color);
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            white-space: nowrap;
        }

        button:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
        }

        .divider {
            height: 1px;
            background: var(--glass-border);
            margin-bottom: 24px;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 24px;
        }

        .social-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .social-links a:hover {
            color: var(--text-main);
        }

        @media (max-width: 520px) {
            .container { padding: 32px 20px; }
            .form-group { flex-direction: column; }
            h1 { font-size: 1.75rem; }
            button { width: 100%; }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Substitua pelo nome ou tag <img> da sua logo -->
        <div class="logo">SuaMarca</div>

        <div class="badge">
            <span class="pulse-dot"></span>
            Loja em Construção
        </div>

        <h1>Algo incrível está sendo preparado para você.</h1>
        <p>Nossa nova plataforma está em fase final de desenvolvimento. Cadastre seu e-mail e seja o primeiro a saber da inauguração para garantir **15% OFF no primeiro pedido**.</p>

        <!-- Formulário para captura -->
        <form class="form-group" action="#" method="POST">
            <input type="email" placeholder="Digite seu melhor e-mail..." required>
            <button type="submit">Garantir Desconto</button>
        </form>

        <div class="divider"></div>

        <!-- Canais de atendimento enquanto o site está fora -->
        <div class="social-links">
            <a href="https://instagram.com/sualoja" target="_blank">Instagram</a>
            <a href="https://wa.me/5500000000000" target="_blank">Atendimento WhatsApp</a>
        </div>
    </div>

</body>
</html>