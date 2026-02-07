<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khuma - CRM Inteligente com Chatbot para WhatsApp</title>
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
            <span class="text-xl font-bold text-primary">🐾 Khuma CRM</span>
        </div>

        <nav class="hidden md:flex space-x-8">
            <a href="#features" class="text-gray-600 hover:text-primary transition">Funcionalidades</a>
            <a href="#pricing" class="text-gray-600 hover:text-primary transition">Pacotes</a>
            <a href="#about" class="text-gray-600 hover:text-primary transition">Sobre</a>
            <a href="#contact" class="text-gray-600 hover:text-primary transition">Contacto</a>
        </nav>

        <div class="flex items-center space-x-4">
            <a href="#" class="text-primary font-medium hover:text-secondary transition">Entrar</a>
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
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Automatize seu Atendimento no WhatsApp</h1>
            <p class="text-lg md:text-xl mb-6 opacity-90">CRM completo com chatbot integrado para captar leads, qualificar clientes e automatizar respostas no WhatsApp.</p>
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="#" target="_blank" class="bg-white text-primary px-6 py-3 rounded-lg font-medium text-center hover:bg-gray-100 transition">Começar Agora</a>
                <a href="#pricing" class="border-2 border-white text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-white hover:bg-opacity-10 transition">Ver Planos</a>
            </div>
        </div>
        <div class="md:w-1/2 flex justify-center">
            <div class="relative">
                <div class="bg-white rounded-2xl shadow-2xl p-2 w-64 h-96 mx-auto">
                    <div class="bg-[#075E54] rounded-t-xl h-10 flex items-center px-4">
                        <div class="flex items-center space-x-2 text-white">
                            <i class="fab fa-whatsapp text-xl"></i>
                            <span class="font-medium">Chatbot Khuma</span>
                        </div>
                    </div>
                    <div class="p-4 h-full bg-gradient-to-b from-green-50 to-white rounded-b-xl">
                        <div class="mb-4">
                            <div class="bg-green-500 rounded-lg p-3 mb-2 text-sm">
                                Olá! Sou o assistente virtual da Khuma. Como posso ajudar?
                            </div>
                            <div class="bg-green-500 rounded-lg p-3 mb-2 text-sm">
                                Escolha uma opção:<br>
                                1️⃣ Informações<br>
                                2️⃣ Agendamento<br>
                                3️⃣ Falar com atendente
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center p-2 bg-gray-300 rounded-lg">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-green-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Lead Capturado</p>
                                    <p class="text-xs text-gray-500">João Silva • 5 min atrás</p>
                                </div>
                            </div>
                            <div class="flex items-center p-2 bg-blue-300 rounded-lg">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar text-blue-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Agendamento</p>
                                    <p class="text-xs text-gray-500">Marcado para amanhã</p>
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
            <h2 class="text-3xl font-bold mb-4">🚀 Funcionalidades Principais</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Tudo que você precisa para automatizar seu atendimento e gerenciar clientes no WhatsApp</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="feature-card bg-light p-6 rounded-xl shadow-md">
                <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                    <i class="fab fa-whatsapp text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Chatbot WhatsApp</h3>
                <p class="text-gray-600">Atendimento automático 24/24 no WhatsApp com respostas inteligentes e fluxos conversacionais.</p>
            </div>

            <div class="feature-card bg-green p-6 rounded-xl shadow-md">
                <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-users text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Captação de Leads</h3>
                <p class="text-gray-600">Capture e classifique automaticamente leads direto do WhatsApp no khuma CRM.</p>
            </div>

            <div class="feature-card bg-light p-6 rounded-xl shadow-md">
                <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-chart-line text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Qualificação de Clientes</h3>
                <p class="text-gray-600">Sistema inteligente para qualificar e segmentar clientes automaticamente.</p>
            </div>

            <div class="feature-card bg-light p-6 rounded-xl shadow-md">
                <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-check text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Agendamentos Automáticos</h3>
                <p class="text-gray-600">Clientes podem agendar serviços diretamente pelo chatbot do WhatsApp.</p>
            </div>

            <div class="feature-card bg-light p-6 rounded-xl shadow-md">
                <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-database text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Integração CRM</h3>
                <p class="text-gray-600">Conecte com Google Sheets, CRM simples ou outras ferramentas de gestão.</p>
            </div>

            <div class="feature-card bg-light p-6 rounded-xl shadow-md">
                <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-chart-bar text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Relatórios Customizáveis</h3>
                <p class="text-gray-600">Dashboard com relatórios detalhados de performance e conversões.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">📦 Nossos Melhores Pacotes</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Escolha o plano ideal para o seu negócio. Todos incluem integração completa com WhatsApp.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <!-- UBTNTU -->
            <div class="pricing-card bg-white p-8 rounded-xl shadow-md flex flex-col">
                <div class="mb-6">
                    <div class="flex items-center mb-2">
                        <h3 class="text-xl font-bold text-gray-800">UBUNTU</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Ideal para pequenos negócios e profissionais individuais</p>
                    <div class="text-3xl font-bold text-primary mb-2">3.000 MZN<span class="text-sm font-normal text-gray-500">/mês</span></div>
                </div>

                <ul class="mb-8 space-y-3 flex-grow">
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Chatbot básico WhatsApp ou site</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Respostas automáticas às perguntas frequentes</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Horário automático</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>1 fluxo de conversa</span>
                    </li>
                </ul>

                <a href="{{ route('register') }}" target="_blank" class="bg-primary text-white text-center py-3 rounded-lg font-medium hover:bg-secondary transition">INICIAR AGORA</a>

            </div>

            <!-- BAOBÁ -->
            <div class="pricing-card popular bg-white p-8 rounded-xl shadow-lg flex flex-col">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                    <span class="bg-primary text-white px-4 py-1 rounded-full text-sm font-medium">MAIS POPULAR</span>
                </div>

                <div class="mb-6">
                    <div class="flex items-center mb-2">
                        <h3 class="text-xl font-bold text-gray-800">BAOBÁ</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Ideal para empresas em crescimento</p>
                    <div class="text-3xl font-bold text-primary mb-2">8.000 MZN<span class="text-sm font-normal text-gray-500">/mês</span></div>
                </div>

                <ul class="mb-8 space-y-3">
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span><strong>Inclui tudo do Ubuntu +</strong></span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Atendimento 24/7</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Qualificação de clientes</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Agendamentos simples</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Integração com Google Sheets ou CRM simples</span>
                    </li>
                </ul>
                <a href="{{ route('register') }}" target="_blank" class="w-full bg-primary text-white text-center py-3 rounded-lg font-medium hover:bg-secondary transition">INICIAR AGORA</a>

            </div>

            <!-- LEÃO -->
            <div class="pricing-card bg-white p-8 rounded-xl shadow-md flex flex-col">
                <div class="mb-6">
                    <div class="flex items-center mb-2">
                        <h3 class="text-xl font-bold text-gray-800">LEÃO</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Ideal para empresas médias e marcas fortes</p>
                    <div class="text-3xl font-bold text-primary mb-2">Custom<span class="text-sm font-normal text-gray-500">/mês</span></div>
                </div>

                <ul class="mb-8 space-y-3 flex-grow">
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span><strong>Inclui tudo do Baobá +</strong></span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Respostas personalizadas por cliente</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Captação e classificação automática de leads</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Suporte dedicado</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Relatórios customizáveis</span>
                    </li>
                </ul>

                <a href="#" target="_blank" class="bg-dark text-white text-center py-3 rounded-lg font-medium hover:bg-gray-800 transition">CONTACTAR AGORA</a>

            </div>
        </div>

        <div class="mt-12 text-center">
            <div class="flex flex-col md:flex-row justify-center items-center space-y-4 md:space-y-0 md:space-x-8">
                <div class="flex items-center">
                    <i class="fas fa-check text-accent mr-2"></i>
                    <span>Cancele quando quiser</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-check text-accent mr-2"></i>
                    <span>24/7 suporte</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-check text-accent mr-2"></i>
                    <span>Demonstração gratuita</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-10 md:mb-0">
                <h2 class="text-3xl font-bold mb-6">Por que escolher o Khuma CRM?</h2>
                <p class="text-gray-600 mb-4">O WhatsApp é a ferramenta de comunicação mais usada em Moçambique, com milhões de utilizadores ativos diariamente. Nosso CRM integrado ao WhatsApp permite automatizar atendimento, captar leads e gerenciar clientes de forma eficiente.</p>
                <p class="text-gray-600 mb-4">Com o Khuma, você transforma conversas do WhatsApp em oportunidades de negócio organizadas e rastreáveis.</p>
                <p class="text-gray-600">Ideal para:</p>
                <ul class="mt-4 space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Pequenos negócios que querem automatizar atendimento</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Empresas que precisam captar leads pelo WhatsApp</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Profissionais que desejam qualificar clientes automaticamente</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-accent mt-1 mr-2"></i>
                        <span>Marcas que buscam integrar WhatsApp com seu CRM</span>
                    </li>
                </ul>
            </div>
            <div class="md:w-1/2 flex justify-center">
                <div class="bg-gradient-to-br from-primary to-secondary p-1 rounded-2xl shadow-xl">
                    <div class="bg-white p-6 rounded-2xl">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center mr-3">
                                <i class="fab fa-whatsapp text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-bold">Dashboard Khuma CRM</h3>
                                <p class="text-xs text-gray-500">Estatísticas do seu WhatsApp</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-green-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-500">Leads Capturados</p>
                                <p class="font-bold text-lg">48</p>
                            </div>
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-500">Conversas Ativas</p>
                                <p class="font-bold text-lg">23</p>
                            </div>
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-500">Taxa de Resposta</p>
                                <p class="font-bold text-lg">98%</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-500">Agendamentos</p>
                                <p class="font-bold text-lg">15</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs text-gray-500 mb-2">Leads por Origem</p>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">WhatsApp</span>
                                    <span class="font-medium">65%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 65%"></div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">Site</span>
                                    <span class="font-medium">25%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 25%"></div>
                                </div>
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
        <h2 class="text-3xl font-bold mb-4">Vamos fazer o seu negócio crescer juntos!</h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">Automatize seu WhatsApp, capture mais leads e aumente suas vendas com o Khuma CRM.</p>
        <a href="{{ route('register') }}" target="_blank" class="bg-white text-primary px-8 py-3 rounded-lg font-medium text-lg hover:bg-gray-100 transition">Começar Demonstração</a>
    </div>
</section>

<!-- Footer -->
<footer id="contact" class="bg-dark text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <div class="flex items-center space-x-2 mb-4">
                    <i class="fab fa-whatsapp text-primary text-2xl"></i>
                    <span class="text-xl font-bold">Khuma CRM</span>
                </div>
                <p class="text-gray-300 mb-4">CRM inteligente com chatbot integrado para WhatsApp. Automatize atendimento, capture leads e gerencie clientes de forma eficiente.</p>
                <div class="flex space-x-4">
                    <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary transition">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary transition">
                        <i class="fab fa-facebook-f"></i>
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
                        <i class="fab fa-whatsapp mt-1 mr-2 text-primary"></i>
                        <span>+258 87 870 0088</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-envelope mt-1 mr-2 text-primary"></i>
                        <span>info@khuma.co.mz</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-2 text-primary"></i>
                        <span>Maputo, Moçambique</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2024 Khuma CRM. Todos os direitos reservados.</p>
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
