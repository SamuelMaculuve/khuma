<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khuma - Inteligente com Chatbot para WhatsApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#1E40AF',
                        accent: '#10B981',
                        dark: '#1a1a1a',
                        light: '#F8FAFC',
                        gray: {
                            50: '#f9fafb',
                            100: '#f3f4f6',
                            200: '#e5e7eb',
                            300: '#d1d5db',
                            400: '#9ca3af',
                            500: '#6b7280',
                            600: '#4b5563',
                            700: '#374151',
                            800: '#1f2937',
                            900: '#111827',
                        }
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                        'inter': ['Inter', 'sans-serif']
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(0, 0, 0, 0.1)',
                        'card': '0 20px 50px -12px rgba(0, 0, 0, 0.08)',
                        'hover': '0 25px 50px -12px rgba(0, 0, 0, 0.15)',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .feature-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #f1f5f9;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            border-color: #25D366;
        }

        .pricing-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid #e5e7eb;
        }

        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.12);
        }

        .pricing-card.popular {
            border: 2px solid #25D366;
            position: relative;
            overflow: hidden;
        }

        .pricing-card.popular::before {
            content: 'MAIS POPULAR';
            position: absolute;
            top: 20px;
            right: -35px;
            background: #25D366;
            color: white;
            padding: 5px 40px;
            font-size: 12px;
            font-weight: 600;
            transform: rotate(45deg);
        }

        .table-header {
            background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
        }

        .table-row:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .sticky-nav {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-light text-dark font-inter">
<!-- Header/Navigation -->
<header class="sticky top-0 z-50 sticky-nav">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-md">
                🐾
            </div>
            <div>
                <span class="text-xl font-bold text-gray-900">Khuma</span>
                <span class="text-xs text-primary font-semibold bg-green-50 px-2 py-1 rounded-full ml-2">Beta</span>
            </div>
        </div>

        <nav class="hidden md:flex space-x-8">
            <a href="#features" class="text-gray-600 hover:text-primary transition font-medium">Funcionalidades</a>
            <a href="#pricing" class="text-gray-600 hover:text-primary transition font-medium">Pacotes</a>
            <a href="#comparison" class="text-gray-600 hover:text-primary transition font-medium">Comparação</a>
            <a href="#contact" class="text-gray-600 hover:text-primary transition font-medium">Contacto</a>
        </nav>

        <div class="flex items-center space-x-4">
            <a href="{{ route('login') }}" class="text-gray-700 font-medium hover:text-primary transition hidden md:block">Entrar</a>
            <a href="https://wa.me/258878700088?text=Olá%20👋%2C%20tenho%20interesse%20no%20Khuma!" target="_blank" class="bg-primary text-white px-6 py-2 rounded-lg font-medium hover:bg-secondary transition shadow-md">
                <i class="fab fa-whatsapp mr-2"></i>Demonstração
            </a>
        </div>

        <button class="md:hidden text-gray-600">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>
</header>

<!-- Hero Section -->
<section class="hero-gradient text-white py-16 md:py-24 relative">
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl">
            <span class="badge badge-success mb-4">Novo • Versão 2.0</span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">Transforme o WhatsApp no seu maior canal de vendas</h1>
            <p class="text-xl mb-8 opacity-90 leading-relaxed">CRM completo com chatbot inteligente para WhatsApp. Automatize atendimento, capture leads 24/7 e converta conversas em vendas.</p>
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="https://wa.me/258878700088?text=Olá%20👋%2C%20quero%20uma%20demonstração%20do%20Khuma%20CRM!" target="_blank" class="bg-white text-primary px-8 py-4 rounded-xl font-semibold text-center hover:bg-gray-50 transition shadow-lg hover:shadow-xl">
                    <i class="fab fa-whatsapp mr-2"></i>Começar Agora
                </a>
                <a href="#pricing" class="border-2 border-white text-white px-8 py-4 rounded-xl font-semibold text-center hover:bg-white hover:bg-opacity-10 transition">
                    <i class="fas fa-chart-line mr-2"></i>Ver Planos
                </a>
            </div>
            <div class="mt-12 flex items-center space-x-6">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-sm">Atendimento 24/7</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-sm">Integração WhatsApp</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-sm">Setup em 24h</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Solução Completa para WhatsApp Business</h2>
            <p class="text-gray-600 max-w-2xl mx-auto text-lg">Tudo que você precisa para transformar o WhatsApp em uma máquina de vendas e atendimento automatizado</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="feature-card bg-white p-8 rounded-2xl shadow-card">
                <div class="w-14 h-14 bg-gradient-to-r from-green-100 to-green-50 rounded-xl flex items-center justify-center mb-6">
                    <i class="fab fa-whatsapp text-primary text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Chatbot Inteligente</h3>
                <p class="text-gray-600 mb-4">Atendimento automático 24/7 com respostas inteligentes e fluxos conversacionais avançados.</p>
                <ul class="space-y-2">
                    <li class="flex items-center text-sm">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>Respostas automáticas</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>Fluxos conversacionais</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>Qualificação de leads</span>
                    </li>
                </ul>
            </div>

            <div class="feature-card bg-white p-8 rounded-2xl shadow-card">
                <div class="w-14 h-14 bg-gradient-to-r from-blue-100 to-blue-50 rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">CRM Integrado</h3>
                <p class="text-gray-600 mb-4">Gerencie todos os seus contatos e interações em um só lugar com nosso CRM integrado ao WhatsApp.</p>
                <ul class="space-y-2">
                    <li class="flex items-center text-sm">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>Base de contatos centralizada</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>Histórico completo</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>Segmentação avançada</span>
                    </li>
                </ul>
            </div>

            <div class="feature-card bg-white p-8 rounded-2xl shadow-card">
                <div class="w-14 h-14 bg-gradient-to-r from-purple-100 to-purple-50 rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-shopping-cart text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Vendas Automatizadas</h3>
                <p class="text-gray-600 mb-4">Venda produtos e serviços diretamente pelo WhatsApp com pagamentos integrados.</p>
                <ul class="space-y-2">
                    <li class="flex items-center text-sm">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>Catálogo digital</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>Múltiplos métodos de pagamento</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>Checkout no WhatsApp</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Escolha o Plano Perfeito</h2>
            <p class="text-gray-600 max-w-2xl mx-auto text-lg">Planos flexíveis para negócios de todos os tamanhos. Comece simples, cresça conosco.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <!-- UBUNTU -->
            <div class="pricing-card bg-white p-8 rounded-2xl shadow-soft flex flex-col h-full">
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-gray-100 to-gray-50 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-seedling text-gray-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">UBUNTU</h3>
                            <p class="text-sm text-gray-500">Pequenos negócios</p>
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-gray-900 mb-2">1.800<span class="text-sm font-normal text-gray-500"> MZN/mês + IVA</span></div>
                    <span class="text-sm font-normal text-red-500"> Antes <del>3.000</del> <small>MZN/mês</small></span>
                    <p class="text-gray-600">Ideal para quem está começando com automação no WhatsApp.</p>
                </div>

                <ul class="mb-8 space-y-4 flex-grow">
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>2 Membros da equipa</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>500 linhas no fluxo do chatbot</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>1 instância WhatsApp (QR Code)</span>
                    </li>
                    <li class="flex items-start text-gray-400">
                        <i class="fas fa-times mt-1 mr-3"></i>
                        <span>Venda de produtos/serviços</span>
                    </li>
                    <li class="flex items-start text-gray-400">
                        <i class="fas fa-times mt-1 mr-3"></i>
                        <span>Mensagens em massa</span>
                    </li>
                </ul>

                <a href="#" target="_blank" class="bg-gray-100 text-gray-800 text-center py-4 rounded-xl font-semibold hover:bg-gray-200 transition">Escolher UBUNTU</a>
            </div>

            <!-- BAOBÁ -->
            <div class="pricing-card popular bg-white p-8 rounded-2xl shadow-card flex flex-col h-full">
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-100 to-green-50 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-tree text-primary text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">BAOBÁ</h3>
                            <p class="text-sm text-gray-500">Empresas em crescimento</p>
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-gray-900 mb-2">8.000<span class="text-sm font-normal text-gray-500"> MZN/mês  + IVA</span></div>
                    <p class="text-gray-600">Perfeito para empresas que querem escalar vendas pelo WhatsApp.</p>
                </div>

                <ul class="mb-8 space-y-4 flex-grow">
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span><strong>Tudo do UBUNTU +</strong></span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>5 Membros da equipa</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>1.500 linhas no fluxo do chatbot</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Venda de produtos/serviços ✅</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Múltiplos métodos de pagamento</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Integração Google Sheets</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>2 instâncias WhatsApp</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Mensagens em massa</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Suporte básico</span>
                    </li>
                </ul>

                <a href="#" target="_blank" class="bg-gradient-to-r from-primary to-secondary text-white text-center py-4 rounded-xl font-semibold hover:opacity-90 transition shadow-md">Escolher BAOBÁ</a>
            </div>

            <!-- LEÃO -->
            <div class="pricing-card bg-white p-8 rounded-2xl shadow-soft flex flex-col h-full">
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-yellow-100 to-yellow-50 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-crown text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">LEÃO</h3>
                            <p class="text-sm text-gray-500">Empresas estabelecidas</p>
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-gray-900 mb-2">Personalizado</div>
                    <p class="text-gray-600">Solução enterprise com recursos avançados e suporte prioritário.</p>
                </div>

                <ul class="mb-8 space-y-4 flex-grow">
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span><strong>Tudo do BAOBÁ +</strong></span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Membros ilimitados</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Fluxo do chatbot ilimitado</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Instâncias WhatsApp customizáveis</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>WhatsApp Templates API</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Formulários WhatsApp</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Suporte prioritário</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Cloud API & QR Code</span>
                    </li>
                </ul>

                <a href="#" target="_blank" class="bg-gray-900 text-white text-center py-4 rounded-xl font-semibold hover:bg-gray-800 transition">Falar com Especialista</a>
            </div>
        </div>

        <div class="mt-12 text-center">
            <p class="text-gray-600 mb-6">Todos os planos incluem:</p>
            <div class="flex flex-wrap justify-center gap-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span class="text-sm">Cancele quando quiser</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span class="text-sm">Suporte técnico</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span class="text-sm">Demonstração gratuita</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span class="text-sm">Atualizações gratuitas</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Comparison Table Section -->
<section id="comparison" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Comparação Detalhada de Funcionalidades</h2>
            <p class="text-gray-600 max-w-2xl mx-auto text-lg">Veja todas as funcionalidades de cada plano e escolha o ideal para o seu negócio</p>
        </div>

        <div class="overflow-x-auto rounded-2xl shadow-card border border-gray-100">
            <table class="w-full">
                <thead>
                <tr class="table-header">
                    <th class="text-left p-6 text-white font-semibold text-lg">Funcionalidades</th>
                    <th class="p-6 text-center">
                        <div class="inline-block bg-white text-gray-900 px-6 py-2 rounded-lg font-bold">UBUNTU</div>
                    </th>
                    <th class="p-6 text-center">
                        <div class="inline-block bg-white text-gray-900 px-6 py-2 rounded-lg font-bold">BAOBÁ</div>
                    </th>
                    <th class="p-6 text-center">
                        <div class="inline-block bg-white text-gray-900 px-6 py-2 rounded-lg font-bold">LEÃO</div>
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                <!-- Equipa -->
                <tr class="table-row">
                    <td class="p-6 font-medium">
                        <div class="flex items-center">
                            <i class="fas fa-users text-gray-500 mr-3"></i>
                            <span>Membros da equipa</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="font-bold text-gray-900">2</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="font-bold text-gray-900">5</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="badge badge-success">Ilimitado</span>
                    </td>
                </tr>

                <!-- Chatbot -->
                <tr class="table-row">
                    <td class="p-6 font-medium">
                        <div class="flex items-center">
                            <i class="fas fa-robot text-gray-500 mr-3"></i>
                            <span>Fluxo do Chatbot</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="font-bold text-gray-900">500 linhas</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="font-bold text-gray-900">1.500 linhas</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="badge badge-success">Ilimitado</span>
                    </td>
                </tr>

                <!-- Vendas -->
                <tr class="table-row">
                    <td class="p-6 font-medium">
                        <div class="flex items-center">
                            <i class="fas fa-shopping-cart text-gray-500 mr-3"></i>
                            <span>Venda de produtos/serviços</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="text-gray-400">-</span>
                    </td>
                    <td class="p-6 text-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </td>
                    <td class="p-6 text-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </td>
                </tr>

                <!-- Pagamentos -->
                <tr class="table-row">
                    <td class="p-6 font-medium">
                        <div class="flex items-center">
                            <i class="fas fa-credit-card text-gray-500 mr-3"></i>
                            <span>Métodos de pagamento</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="text-gray-400">-</span>
                    </td>
                    <td class="p-6 text-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </td>
                    <td class="p-6 text-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </td>
                </tr>

                <!-- Google Sheets -->
                <tr class="table-row">
                    <td class="p-6 font-medium">
                        <div class="flex items-center">
                            <i class="fab fa-google text-gray-500 mr-3"></i>
                            <span>Integração com Google Sheets</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="text-gray-400">-</span>
                    </td>
                    <td class="p-6 text-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </td>
                    <td class="p-6 text-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </td>
                </tr>

                <!-- Interacção -->
                <tr class="table-row">
                    <td class="p-6 font-medium">
                        <div class="flex items-center">
                            <i class="fas fa-comments text-gray-500 mr-3"></i>
                            <span>Interação pelo lead</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="text-gray-400">-</span>
                    </td>
                    <td class="p-6 text-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </td>
                    <td class="p-6 text-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </td>
                </tr>

                <!-- WhatsApp Instâncias -->
                <tr class="table-row">
                    <td class="p-6 font-medium">
                        <div class="flex items-center">
                            <i class="fab fa-whatsapp text-gray-500 mr-3"></i>
                            <span>Instâncias de WhatsApp</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="font-bold text-gray-900">1</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="font-bold text-gray-900">2</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="badge badge-info">Customizável</span>
                    </td>
                </tr>

                <!-- Mensagem em Massa -->
                <tr class="table-row">
                    <td class="p-6 font-medium">
                        <div class="flex items-center">
                            <i class="fas fa-bullhorn text-gray-500 mr-3"></i>
                            <span>Mensagem em Massa</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="text-gray-400">-</span>
                    </td>
                    <td class="p-6 text-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </td>
                    <td class="p-6 text-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </td>
                </tr>

                <!-- Qualificação -->
                <tr class="table-row">
                    <td class="p-6 font-medium">
                        <div class="flex items-center">
                            <i class="fas fa-filter text-gray-500 mr-3"></i>
                            <span>Qualificação de clientes no WB</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="text-gray-400">-</span>
                    </td>
                    <td class="p-6 text-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </td>
                    <td class="p-6 text-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </td>
                </tr>

                <!-- Conexão -->
                <tr class="table-row">
                    <td class="p-6 font-medium">
                        <div class="flex items-center">
                            <i class="fas fa-plug text-gray-500 mr-3"></i>
                            <span>Tipo de conexão do WhatsApp</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="font-medium">QR Code</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="font-medium">QR Code / Cloud API</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="font-medium">QR Code / Cloud API</span>
                    </td>
                </tr>

                <!-- Templates -->
                <tr class="table-row">
                    <td class="p-6 font-medium">
                        <div class="flex items-center">
                            <i class="fas fa-layer-group text-gray-500 mr-3"></i>
                            <span>WhatsApp Templates</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="text-gray-400">-</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="badge badge-info">Cloud API</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="badge badge-info">Cloud API</span>
                    </td>
                </tr>

                <!-- Formulários -->
                <tr class="table-row">
                    <td class="p-6 font-medium">
                        <div class="flex items-center">
                            <i class="fas fa-edit text-gray-500 mr-3"></i>
                            <span>Formulários do WhatsApp</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="text-gray-400">-</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="badge badge-info">Cloud API</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="badge badge-info">Cloud API</span>
                    </td>
                </tr>

                <!-- Suporte -->
                <tr class="table-row bg-gray-50">
                    <td class="p-6 font-medium">
                        <div class="flex items-center">
                            <i class="fas fa-headset text-gray-500 mr-3"></i>
                            <span>Suporte</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="text-gray-400">-</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="font-medium">Básico</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="badge badge-warning">Prioritário</span>
                    </td>
                </tr>
                </tbody>
                <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="4" class="p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-center">
                            <div class="mb-4 sm:mb-0">
                                <p class="font-semibold text-gray-900">Precisa de ajuda para escolher?</p>
                                <p class="text-gray-600 text-sm">Fale com nosso especialista</p>
                            </div>
                            <a href="https://wa.me/258878700088?text=Olá%20👋%2C%20preciso%20de%20ajuda%20para%20escolher%20o%20plano%20ideal!" target="_blank" class="bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-secondary transition">
                                <i class="fab fa-whatsapp mr-2"></i>Falar com Especialista
                            </a>
                        </div>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 hero-gradient text-white relative">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Pronto para transformar seu WhatsApp?</h2>
            <p class="text-xl mb-8 opacity-90">Junte-se a mais de 500 empresas que já automatizaram seus atendimentos e aumentaram vendas com o Khuma CRM.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white bg-opacity-10 p-6 rounded-2xl backdrop-blur-sm">
                    <div class="text-3xl font-bold mb-2">24h</div>
                    <p class="text-sm opacity-90">Setup completo</p>
                </div>
                <div class="bg-white bg-opacity-10 p-6 rounded-2xl backdrop-blur-sm">
                    <div class="text-3xl font-bold mb-2">98%</div>
                    <p class="text-sm opacity-90">Taxa de satisfação</p>
                </div>
                <div class="bg-white bg-opacity-10 p-6 rounded-2xl backdrop-blur-sm">
                    <div class="text-3xl font-bold mb-2">500+</div>
                    <p class="text-sm opacity-90">Clientes ativos</p>
                </div>
            </div>

            <a href="https://wa.me/258872000726?text=Olá%20👋%2C%20quero%20começar%20com%20o%20Khuma%20hoje!" target="_blank" class="inline-flex items-center bg-white text-primary px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition shadow-2xl hover:shadow-3xl">
                <i class="fab fa-whatsapp text-xl mr-3"></i>
                Começar Demonstração Grátis
            </a>
            <p class="text-sm mt-4 opacity-80">Sem compromisso • 7 dias de suporte grátis</p>
        </div>
    </div>
</section>

<!-- Footer -->
<footer id="contact" class="bg-gray-900 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            <div class="md:col-span-2">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-md">
                        🐾
                    </div>
                    <div>
                        <span class="text-2xl font-bold">Khuma</span>
                        <p class="text-gray-400 text-sm">Automação Inteligente para WhatsApp</p>
                    </div>
                </div>
                <p class="text-gray-400 mb-6 max-w-md">Solução completa de CRM com chatbot integrado para WhatsApp. Automatize atendimento, capture leads e aumente suas vendas 24 horas por dia.</p>
                <div class="flex space-x-4">
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-pink-600 transition">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-400 transition">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-6 text-white">Links Rápidos</h3>
                <ul class="space-y-3">
                    <li><a href="#features" class="text-gray-400 hover:text-white transition flex items-center">
                            <i class="fas fa-chevron-right text-xs mr-2 text-primary"></i>
                            Funcionalidades
                        </a></li>
                    <li><a href="#pricing" class="text-gray-400 hover:text-white transition flex items-center">
                            <i class="fas fa-chevron-right text-xs mr-2 text-primary"></i>
                            Planos e Preços
                        </a></li>
                    <li><a href="#comparison" class="text-gray-400 hover:text-white transition flex items-center">
                            <i class="fas fa-chevron-right text-xs mr-2 text-primary"></i>
                            Comparação
                        </a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition flex items-center">
                            <i class="fas fa-chevron-right text-xs mr-2 text-primary"></i>
                            Casos de Sucesso
                        </a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-6 text-white">Contacte-nos</h3>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <i class="fab fa-whatsapp text-primary mt-1 mr-3"></i>
                        <div>
                            <p class="font-medium">WhatsApp Comercial</p>
                            <p class="text-gray-400">+258 87 870 0088</p>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-envelope text-primary mt-1 mr-3"></i>
                        <div>
                            <p class="font-medium">Email</p>
                            <p class="text-gray-400">comercial@khuma.co.mz</p>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt text-primary mt-1 mr-3"></i>
                        <div>
                            <p class="font-medium">Localização</p>
                            <p class="text-gray-400">Maputo, Moçambique</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-12 pt-8 text-center">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 mb-4 md:mb-0">&copy; 2026 Khuma. Todos os direitos reservados.</p>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition text-sm">Política de Privacidade</a>
                    <a href="#" class="text-gray-400 hover:text-white transition text-sm">Termos de Serviço</a>
                    <a href="#" class="text-gray-400 hover:text-white transition text-sm">Cookies</a>
                </div>
            </div>
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
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Sticky header background on scroll
    window.addEventListener('scroll', function() {
        const header = document.querySelector('header');
        if (window.scrollY > 100) {
            header.classList.add('sticky-nav');
        } else {
            header.classList.remove('sticky-nav');
        }
    });
</script>
</body>
</html>
