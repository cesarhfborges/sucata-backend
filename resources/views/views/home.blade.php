<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Controle de Sucata | API Gateway</title>
    <!--suppress JSUnresolvedLibraryURL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--suppress JSUnresolvedLibraryURL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!--suppress JSUnresolvedLibraryURL -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!--suppress CssUnresolvedCustomProperty -->
    <style>
        :root { --bs-primary: #2c3e50; }

        body {
            background-color: var(--bs-body-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color 0.3s ease;
        }

        /* Ajuste do gradiente para o modo escuro */
        [data-bs-theme='dark'] .hero-section {
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
        }

        .hero-section {
            background: linear-gradient(135deg, #2c3e50 0%, #000000 100%);
            color: white;
            padding: 60px 0;
        }

        .api-terminal {
            background: #121212;
            color: #00ff00;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.85rem;
            border: 1px solid #333;
        }

        /* Ajuste fino para cards no modo dark */
        [data-bs-theme='dark'] .card {
            border: 1px solid #333 !important;
        }

        .hero-section {
            background: linear-gradient(135deg, #2c3e50 0%, #000000 100%);
            color: white;
            padding: 60px 0;
        }

        .status-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s;
        }

        .status-card:hover {
            transform: translateY(-5px);
        }

        .badge-up {
            background-color: #27ae60;
        }

        .badge-down {
            background-color: #e74c3c;
        }

        .api-terminal {
            background: #1e1e1e;
            color: #00ff00;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.85rem;
        }

        #theme-toggle {
            border-radius: 50px;
            padding: 5px 12px;
            border: 1px solid rgba(255,255,255,0.3);
        }

        #theme-toggle:hover {
            background-color: rgba(255,255,255,0.1);
        }

        /* Garante que o accordion acompanhe o tema */
        [data-bs-theme='dark'] .accordion-button {
            background-color: #212529;
            color: #fff;
        }
    </style>
</head>
<body>

<header class="hero-section shadow-lg position-relative">
    <div class="container text-end pt-2">
        <button class="btn btn-outline-light btn-sm" id="theme-toggle" title="Alternar tema">
            <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
        </button>
    </div>
    <div class="container text-center">
        <h1 class="display-4 fw-bold"><i class="bi bi-recycle me-2"></i>Sistema Sucata</h1>
        <p class="lead">Backend API Gateway & Controle de Materiais Recicláveis</p>
        <div class="mt-4">
            <span id="main-status-badge" class="badge rounded-pill bg-secondary px-4 py-2">Verificando sistema...</span>
        </div>
    </div>
</header>

<main class="container my-5">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="card-title fw-bold mb-4">Sobre o Projeto</h3>
                    <p>Este sistema é o núcleo de processamento para gestão de resíduos e sucatas. Construído sobre a
                        arquitetura <strong>Lumen (Laravel)</strong>, ele fornece serviços de alta performance para o
                        frontend em <strong>Angular</strong>.</p>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item bg-transparent"><i class="bi bi-check2-circle text-success me-2"></i>
                            Gestão de usuários
                        </li>
                        <li class="list-group-item bg-transparent"><i class="bi bi-check2-circle text-success me-2"></i>
                            Autenticação via JWT
                        </li>
                        <li class="list-group-item bg-transparent"><i class="bi bi-check2-circle text-success me-2"></i>
                            Database MariaDB/MySQL
                        </li>
                        <li class="list-group-item bg-transparent"><i class="bi bi-check2-circle text-success me-2"></i>
                            Relatorios
                        </li>
                    </ul>
                    <a href="/docs" class="btn btn-outline-primary">Documentação da API</a>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="card-title fw-bold mb-4">API Health Check</h3>
                    <div class="d-flex flex-column small text-muted">
                        <span>Versão: <span id="app-version">---</span></span>
                        <span>PHP: <span id="php-version">---</span></span>
                        <span>Data/Hora: <span id="php-date">---</span></span>
                    </div>
                    <hr>
                    <div id="health-checks" class="row row-cols-2 g-3">
                        <div class="col text-center py-3">Carregando...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h5 class="text-muted mb-3"><i class="bi bi-code-slash me-2"></i>JSON Response Preview</h5>

        <div class="accordion accordion-flush" id="accordionFlushExample">
            <div class="accordion-item">
                <h2 class="accordion-header" id="flush-headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                        Detalhar
                    </button>
                </h2>
                <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                        <div id="json-preview" class="api-terminal">
                            Aguardando resposta da API...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="container text-center mb-5 text-muted small">
    <p>&copy; 2026 César Borges - Sistema Sucata. Desenvolvido com Lumen & Bootstrap 5.</p>
</footer>

<script>
    async function checkHealth() {
        const statusBadge = document.getElementById('main-status-badge');
        const checksContainer = document.getElementById('health-checks');
        const jsonPreview = document.getElementById('json-preview');

        try {
            const response = await fetch('/api/health');
            const data = await response.json();

            // Atualiza badge principal
            statusBadge.className = `badge rounded-pill px-4 py-2 ${data.server === 'ok' ? 'bg-success' : 'bg-danger'}`;
            statusBadge.innerText = data.server === 'ok' ? 'SISTEMA OPERACIONAL' : 'SISTEMA DEGRADADO';

            // Informações gerais
            document.getElementById('app-version').innerText = data.version;
            document.getElementById('php-version').innerText = data.php;
            const currentDate = new Date(data.timestamp);
            document.getElementById('php-date').innerText = `${currentDate.toLocaleDateString('pt-BR')} às ${currentDate.toLocaleTimeString('pt-BR')}`;

            // Renderiza os checks individuais
            checksContainer.innerHTML = '';
            for (const [key, value] of Object.entries(data.checks)) {
                const isUp = value === 'up';
                checksContainer.innerHTML += `
                    <div class="col">
                        <div class="p-2 border rounded text-center">
                            <div class="small text-uppercase text-muted" style="font-size: 0.7rem">${key}</div>
                            <span class="badge ${isUp ? 'bg-success' : 'bg-danger'}">${value.toUpperCase()}</span>
                        </div>
                    </div>
                `;
            }

            // Preview do JSON
            jsonPreview.innerHTML = `<pre class="mb-0">${JSON.stringify(data, null, 2)}</pre>`;

        } catch (error) {
            statusBadge.className = "badge rounded-pill bg-danger px-4 py-2";
            statusBadge.innerText = "ERRO DE CONEXÃO COM API";
            jsonPreview.innerText = "Não foi possível conectar ao endpoint /api/health";
        }
    }

    // Executa ao carregar
    checkHealth();
    // Atualiza a cada 30 segundos
    setInterval(checkHealth, 30000);
</script>
<script>
    const htmlElement = document.documentElement;
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');

    // 1. Define tema inicial baseado no sistema
    const getSystemTheme = () => window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

    const updateUI = (theme) => {
        htmlElement.setAttribute('data-bs-theme', theme);
        themeIcon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
    };

    // Aplica o inicial
    updateUI(getSystemTheme());

    // 2. Lógica do Toggle simples
    themeToggle.addEventListener('click', () => {
        const currentTheme = htmlElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        updateUI(newTheme);
    });

    // Opcional: Escuta mudanças no sistema enquanto a página está aberta
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        updateUI(e.matches ? 'dark' : 'light');
    });
</script>

</body>
</html>
