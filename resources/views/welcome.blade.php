<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>khuma - A tua app de chamadas inteligente</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#1E40AF',
                        accent: '#10B981',
                        dark: '#1F2937',
                        light: '#F9FAFB'
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }

        .pricing-card {
            transition: all 0.3s ease;
        }

        .pricing-card:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .pricing-card.popular {
            border: 2px solid #3B82F6;
        }
    </style>
</head>
<body class="bg-light text-dark font-poppins">
<!-- Header/Navigation -->
<header class="sticky top-0 z-50 bg-white shadow-md">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-2">

            <span class="text-xl font-bold text-primary">🐾 khuma</span>
        </div>

        <nav class="hidden md:flex space-x-8">
            <a href="#features" class="text-gray-600 hover:text-primary transition">Funcionalidades</a>
            <a href="#pricing" class="text-gray-600 hover:text-primary transition">Pacotes</a>
            <a href="#about" class="text-gray-600 hover:text-primary transition">Sobre</a>
            <a href="#contact" class="text-gray-600 hover:text-primary transition">Contacto</a>
        </nav>

        <div class="flex items-center space-x-4">
            <a href="{{ route("login") }}" class="text-primary font-medium hover:text-secondary transition">Entrar</a>
        </div>

        <button class="md:hidden text-gray-600">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>
</header>

<!-- Hero Section -->
<section class="hero-gradient text-white py-16 md:py-24">
    <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
        <div class="md:w-1/2 mb-10 md:mb-0">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">🐾 khuma - A tua app de chamadas inteligente</h1>
            <p class="text-lg md:text-xl mb-6 opacity-90">Registo automático de chamadas com dashboard online para acompanhar e organizar as tuas comunicações.</p>
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="https://wa.me/258878700088?text=Olá%20👋%2C%20tenho%20interesse%20em%20saber%20mais%20sobre%20o%20Khuma!" target="_blank" class="bg-white text-primary px-6 py-3 rounded-lg font-medium text-center hover:bg-gray-100 transition">Começar Agora</a>
                <a href="https://wa.me/258878700088?text=Olá%20👋%2C%20tenho%20interesse%20em%20saber%20mais%20sobre%20o%20Khuma!" target="_blank" class="border-2 border-white text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-white hover:bg-opacity-10 transition">Saber Mais</a>
            </div>
        </div>
        <div class="md:w-1/2 flex justify-center">
            <div class="relative">
                <div class="bg-white rounded-2xl shadow-2xl p-2 w-64 h-96 mx-auto">
                    <div class="bg-gray-800 rounded-t-xl h-8 flex items-center justify-center">
                        <div class="flex space-x-1">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                    </div>
                    <div class="p-4 h-full bg-gradient-to-b from-blue-50 to-white rounded-b-xl">
                        <div class="text-center mb-4">
                            <h3 class="font-bold text-gray-800">Dashboard khuma</h3>
                            <p class="text-xs text-gray-600">Últimas chamadas</p>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center p-2 bg-white rounded-lg shadow-sm">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-phone text-green-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Maria Silva</p>
                                    <p class="text-xs text-gray-500">5 min atrás • 12:45</p>
                                </div>
                                <div class="ml-auto text-green-600 text-sm font-medium">
                                    5:32
                                </div>
                            </div>
                            <div class="flex items-center p-2 bg-white rounded-lg shadow-sm">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-phone-slash text-red-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Número Desconhecido</p>
                                    <p class="text-xs text-gray-500">15 min atrás • 12:35</p>
                                </div>
                                <div class="ml-auto text-red-600 text-sm font-medium">
                                    Perdida
                                </div>
                            </div>
                            <div class="flex items-center p-2 bg-white rounded-lg shadow-sm">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-phone-alt text-blue-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">João Santos</p>
                                    <p class="text-xs text-gray-500">1 hora atrás • 11:50</p>
                                </div>
                                <div class="ml-auto text-blue-600 text-sm font-medium">
                                    2:15
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">🚀 Funcionalidades principais</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">O khuma recolhe automaticamente as informações das chamadas e envia tudo para um dashboard online onde podes acompanhar e organizar os teus registos.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="feature-card bg-light p-6 rounded-xl shadow-md">
                <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-mobile-alt text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Registo Automático</h3>
                <p class="text-gray-600">Regista automaticamente todas as chamadas (entrada, saída, perdidas) no teu telemóvel.</p>
            </div>

            <div class="feature-card bg-light p-6 rounded-xl shadow-md">
                <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-id-card text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Identificação Completa</h3>
                <p class="text-gray-600">Identifica número e nome (quando disponível) de cada chamada recebida ou efetuada.</p>
            </div>

            <div class="feature-card bg-light p-6 rounded-xl shadow-md">
                <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-clock text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Duração e Horário</h3>
                <p class="text-gray-600">Regista a duração, data e hora exata de cada chamada para referência futura.</p>
            </div>

            <div class="feature-card bg-light p-6 rounded-xl shadow-md">
                <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-chart-bar text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Dashboard Intuitivo</h3>
                <p class="text-gray-600">Acede ao teu histórico de chamadas através de um dashboard intuitivo em qualquer dispositivo.</p>
            </div>

            <div class="feature-card bg-light p-6 rounded-xl shadow-md">
                <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-history text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Histórico Organizado</h3>
                <p class="text-gray-600">Mantém um histórico organizado de todas as tuas comunicações para consulta rápida.</p>
            </div>

            <div class="feature-card bg-light p-6 rounded-xl shadow-md">
                <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-file-export text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Exportação de Dados</h3>
                <p class="text-gray-600">Exporta os teus dados de chamadas em formatos úteis como CSV, PDF ou Excel.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">📦 Pacotes Disponíveis</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Escolhe o plano que melhor se adapta às tuas necessidades. Com o khuma, tens sempre o controlo das tuas chamadas.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- khuma Essencial -->
            <div class="pricing-card bg-white p-8 rounded-xl shadow-md flex flex-col">
                <div class="mb-6">
                    <div class="flex items-center mb-2">
                        <span class="text-2xl font-bold">🐾</span>
                        <h3 class="text-xl font-bold ml-2">khuma Essencial</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Para quem precisa apenas de registar e acompanhar chamadas.</p>
                    <div class="text-3xl font-bold text-primary mb-2">5.000 MT<span class="text-sm font-normal text-gray-500">/mês</span></div>
                </div>

                <ul class="mb-8 space-y-3 flex-grow">
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Até 200 chamadas</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Dashboard simples e intuitivo</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Histórico de 3 meses</span>
                    </li>
                </ul>

                <a href="https://wa.me/258878700088?text=Olá%20👋%2C%20tenho%20interesse%20em%20saber%20mais%20sobre%20o%20Khuma!" target="_blank" class="bg-primary text-white text-center py-3 rounded-lg font-medium hover:bg-secondary transition">Escolher Plano</a>

            </div>

            <!-- khuma Premium -->
            <div class="pricing-card popular bg-white p-8 rounded-xl shadow-lg relative">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                    <span class="bg-primary text-white px-4 py-1 rounded-full text-sm font-medium">MAIS POPULAR</span>
                </div>

                <div class="mb-6">
                    <div class="flex items-center mb-2">
                        <span class="text-2xl font-bold">⚡</span>
                        <h3 class="text-xl font-bold ml-2">khuma Premium</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Para quem precisa de controlo total e estatísticas avançadas.</p>
                    <div class="text-3xl font-bold text-primary mb-2">17.000 MT<span class="text-sm font-normal text-gray-500">/mês</span></div>
                </div>

                <ul class="mb-8 space-y-3">
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Chamadas ilimitadas</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Dashboard com filtros avançados</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Estatísticas simples (totais, médias, chamadas perdidas)</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Histórico ilimitado</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Relatórios automáticos (PDF/Excel)</span>
                    </li>
                </ul>

                <a href="https://wa.me/258878700088?text=Olá%20👋%2C%20tenho%20interesse%20em%20saber%20mais%20sobre%20o%20Khuma!" target="_blank" class="bg-primary text-white text-center py-3 rounded-lg font-medium hover:bg-secondary transition" style="padding: 10px">Escolher Plano</a>

            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-10 md:mb-0">
                <h2 class="text-3xl font-bold mb-6">Ideal para profissionais e pequenos negócios</h2>
                <p class="text-gray-600 mb-4">Com o khuma, nunca mais perdes o rasto das tuas comunicações: cada chamada fica guardada com número, nome (se estiver registado), duração, data, hora e tipo de chamada.</p>
                <p class="text-gray-600 mb-4">Assim, tens sempre uma visão clara e organizada da tua actividade telefónica, seja para uso pessoal ou para o teu negócio.</p>
                <p class="text-gray-600">O khuma é especialmente útil para:</p>
                <ul class="mt-4 space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Profissionais que precisam de manter registo de chamadas com clientes</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Pequenos negócios que querem acompanhar a actividade telefónica</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Rádios comunitárias que precisam de registar chamadas de ouvintes</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Associações que necessitam de histórico de comunicações</span>
                    </li>
                </ul>
            </div>
            <div class="md:w-1/2 flex justify-center">
                <div class="bg-gradient-to-br from-primary to-secondary p-1 rounded-2xl shadow-xl">
                    <div class="bg-white p-6 rounded-2xl">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-chart-pie text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-bold">Estatísticas do Mês</h3>
                                <p class="text-xs text-gray-500">Resumo das tuas chamadas</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-500">Total Chamadas</p>
                                <p class="font-bold text-lg">147</p>
                            </div>
                            <div class="bg-green-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-500">Duração Média</p>
                                <p class="font-bold text-lg">4:32</p>
                            </div>
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-500">Chamadas Recebidas</p>
                                <p class="font-bold text-lg">89</p>
                            </div>
                            <div class="bg-red-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-500">Chamadas Perdidas</p>
                                <p class="font-bold text-lg">12</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Distribuição por Tipo</p>
                            <div class="flex h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="bg-green-500 w-3/5"></div>
                                <div class="bg-blue-500 w-1/4"></div>
                                <div class="bg-red-500 w-1/5"></div>
                            </div>
                            <div class="flex justify-between text-xs mt-1">
                                <span class="text-green-600">Recebidas</span>
                                <span class="text-blue-600">Efetuadas</span>
                                <span class="text-red-600">Perdidas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 hero-gradient text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Pronto para começar?</h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">Junte-se a milhares de utilizadores que já estão a usar o khuma para organizar as suas chamadas.</p>
        <a href="https://wa.me/258878700088?text=Olá%20👋%2C%20tenho%20interesse%20em%20saber%20mais%20sobre%20o%20Khuma!" target="_blank" class="bg-white text-primary px-8 py-3 rounded-lg font-medium text-lg hover:bg-gray-100 transition">Começar Agora</a>
    </div>
</section>

<!-- Footer -->
<footer id="contact" class="bg-dark text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <div class="flex items-center space-x-2 mb-4">
                    <span class="text-xl font-bold">🐾 khuma</span>
                </div>
                <p class="text-gray-300 mb-4">A aplicação simples e prática que recolhe automaticamente as informações das chamadas feitas e recebidas no teu telemóvel.</p>
                <div class="flex space-x-4">
                    <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary transition">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary transition">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary transition">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-4">Links Rápidos</h3>
                <ul class="space-y-2">
                    <li><a href="#features" class="text-gray-300 hover:text-white transition">Funcionalidades</a></li>
                    <li><a href="#pricing" class="text-gray-300 hover:text-white transition">Pacotes</a></li>
                    <li><a href="#about" class="text-gray-300 hover:text-white transition">Sobre</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition">FAQ</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-4">Contacto</h3>
                <ul class="space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-phone mt-1 mr-2 text-primary"></i>
                        <span>+258 87 870 0088</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-2 text-primary"></i>
                        <span>Maputo, Moçambique</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2025 khuma. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>

<script>
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            if(targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if(targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
</script>
</body>
</html>
