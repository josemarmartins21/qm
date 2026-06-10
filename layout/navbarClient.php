<?php

if (session_status() === PHP_SESSION_NONE) {
    //session_start();
}


if (!isset($_SESSION['user'])) {

    exit();
}


require "../config/database.php";


$client_id = $_SESSION['user']['client_id'] ?? $_SESSION['user']['user_id'] ?? 0;

$s = $qmanager->prepare("SELECT client_id, primeiro_nome, ultimo_nome, telefone, email, municipio, status FROM client WHERE client_id = ?");
$s->bind_param("i", $client_id);
$s->execute();
$result = $s->get_result();
$client = $result->fetch_assoc();


if (!$client) {
    $client = [
        'client_id' => 0,
        'primeiro_nome' => 'Cliente',
        'ultimo_nome' => '',
        'telefone' => '',
        'email' => '',
        'municipio' => '',
        'status' => 'ativo'
    ];
}

// Define variáveis para exibição
$nome_completo = trim($client['primeiro_nome'] . ' ' . $client['ultimo_nome']);
$email = $client['email'];
$telefone = $client['telefone'] ?? '';
$municipio = $client['municipio'] ?? '';
$status = strtolower($client['status'] ?? 'ativo');
$status_label = ($status === 'ativo') ? 'Ativo' : 'Suspenso';
$status_class = ($status === 'ativo') ? 'status-ativo' : 'status-suspenso';

// Navegação ativa
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_dir  = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISP-QManager - Área do Cliente</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed: 70px;
            --transition-base: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-color: #10b981;
            --danger-color: #ef4444;
        }
      
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow: hidden; /* Previne scroll no body */
        }
        
        /* ============================================================
           WRAPPER PRINCIPAL - Layout fixo sem empurrar elementos
           ============================================================ */
        .dashboard-wrapper {
            display: flex;
            height: 100vh;
            width: 100%;
            overflow: hidden;
        }
        
        /* ============================================================
           SIDEBAR - Fixo à esquerda
           ============================================================ */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            transition: var(--transition-base);
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 30px rgba(0,0,0,0.3);
            flex-shrink: 0;
        }
        
        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background:blue;
        }
        
        /* ============================================================
           MAIN CONTENT - À direita do sidebar com scroll
           ============================================================ */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            transition: var(--transition-base);
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Contém o scroll interno */
            min-width: 0; /* Previne overflow em flex */
        }
        
        /* Área de conteúdo com scroll próprio */
        .content-scroll {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.5rem;
        }
        
        /* Scrollbar personalizada do conteúdo */
        .content-scroll::-webkit-scrollbar {
            width: 8px;
        }
        
        .content-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .content-scroll::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.15);
            border-radius: 10px;
        }
        
        .content-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(0,0,0,0.25);
        }
        
       
        .top-navbar {
            background: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.06);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            z-index: 1020;
        }
        
        .top-navbar .page-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .top-navbar .page-info h5 {
            margin: 0;
            font-weight: 700;
            color: #1e293b;
            font-size: 1.1rem;
        }
        
        .top-navbar .page-info small {
            color: #94a3b8;
            font-size: 0.8rem;
        }
        
        .top-navbar .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .top-navbar .nav-actions .btn-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid rgba(0,0,0,0.06);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            transition: all 0.2s;
            position: relative;
        }
        
        .top-navbar .nav-actions .btn-icon:hover {
            background: #f1f5f9;
            color: #334155;
        }
        
        .top-navbar .nav-actions .btn-icon .badge {
            position: absolute;
            top: -2px;
            right: -2px;
            font-size: 0.65rem;
            padding: 0.15rem 0.35rem;
        }
        
        
        .sidebar-brand {
            padding: 1.25rem;
            border-bottom: 1px solid blue;
            flex-shrink: 0;
        }
        
        .sidebar-brand a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #fff;
        }
        
        .brand-icon {
            width: 40px;
            height: 40px;
            background: blue;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            flex-shrink: 0;
        }
        
        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            min-width: 0;
        }
        
        .brand-title {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
        }
        
        .brand-subtitle {
            font-size: 0.65rem;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .client-badge {
            display: inline-block;
            background: blue;
            color: #fff;
            font-size: 0.6rem;
            padding: 0.1rem 0.4rem;
            border-radius: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-top: 0.15rem;
            width: fit-content;
        }
        
        
        .sidebar .nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.75rem;
        }
        
        .sidebar .nav::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar .nav::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar .nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        
        .sidebar .nav-item {
            margin-bottom: 0.2rem;
        }
        
        .sidebar .nav-link {
            border-radius: 10px;
            padding: 0.7rem 0.9rem;
            color: rgba(255,255,255,0.6) !important;
            transition: var(--transition-base);
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            position: relative;
            overflow: hidden;
            white-space: nowrap;
        }
        
        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary-gradient);
            border-radius: 0 4px 4px 0;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.06);
            color: #fff !important;
            transform: translateX(3px);
        }
        
        .sidebar .nav-link:hover::before {
            opacity: 1;
        }
        
        .sidebar .nav-link.active {
            background: rgba(102, 126, 234, 0.15);
            color: #fff !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15);
        }
        
        .sidebar .nav-link.active::before {
            opacity: 1;
        }
        
        .sidebar .nav-link i {
            width: 22px;
            text-align: center;
            font-size: 1rem;
            transition: transform 0.3s;
            flex-shrink: 0;
        }
        
        .sidebar .nav-link:hover i {
            transform: scale(1.1);
        }
        
        /* SEPARADOR DE MENU */
        .menu-divider {
            margin: 0.75rem;
            height: 1px;
            background: rgba(255,255,255,0.06);
        }
        
        .menu-label {
            padding: 0 0.9rem;
            font-size: 0.65rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 0.4rem;
            margin-top: 0.5rem;
        }
        
        /* ============================================================
           SEÇÃO USUÁRIO
           ============================================================ */
        .user-section {
            padding: 0.875rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            background: rgba(0,0,0,0.15);
            flex-shrink: 0;
        }
        
        .user-card {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.625rem;
            border-radius: 10px;
            background: rgba(255,255,255,0.03);
            transition: var(--transition-base);
        }
        
        .user-card:hover {
            background: rgba(255,255,255,0.06);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            color: #fff;
            font-weight: 700;
            flex-shrink: 0;
        }
        
        .user-info {
            flex: 1;
            min-width: 0;
        }
        
        .user-name {
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .user-role {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.7rem;
            font-weight: 500;
        }
        
        /* Status badges */
        .status-ativo {
            color: var(--success-color);
        }
        
        .status-suspenso {
            color: var(--danger-color);
        }
        
        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .status-dot.ativo {
            background: var(--success-color);
            box-shadow: 0 0 6px var(--success-color);
            animation: pulse 2s infinite;
        }
        
        .status-dot.suspenso {
            background: var(--danger-color);
            box-shadow: 0 0 6px var(--danger-color);
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        
        /* BOTÃO SAIR */
        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.5rem;
            border-radius: 8px;
            background: rgba(239, 68, 68, 0.08);
            color: #ef4444 !important;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: var(--transition-base);
            margin-top: 0.5rem;
        }
        
        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171 !important;
        }
        
        /* ============================================================
           TOGGLE TEMA
           ============================================================ */
        .theme-toggle-wrapper {
            padding: 0.875rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
        }
        
        .theme-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #94a3b8;
            font-size: 0.8rem;
        }
        
        .form-check-input {
            width: 2.4em !important;
            height: 1.2em !important;
            cursor: pointer;
        }
        
        .form-check-input:checked {
            background-color: #f59e0b;
            border-color: #f59e0b;
        }
        
        /* ============================================================
           HEADER MOBILE
           ============================================================ */
        .mobile-header {
            display: none;
            background: linear-gradient(90deg, #0f172a 0%, #1e293b 100%);
            padding: 0.875rem 1rem;
            position: sticky;
            top: 0;
            z-index: 1030;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
        }
        
        /* ============================================================
           OVERLAY MOBILE
           ============================================================ */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            z-index: 1035;
        }
        
        .overlay.show {
            display: block;
        }
        
        /* ============================================================
           RESPONSIVIDADE
           ============================================================ */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 260px;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
        }
        
        /* ============================================================
           DARK MODE
           ============================================================ */
        [data-bs-theme="dark"] .main-content {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        
        [data-bs-theme="dark"] .top-navbar {
            background: #1e293b;
            border-color: rgba(255,255,255,0.06);
        }
        
        [data-bs-theme="dark"] .top-navbar .page-info h5 {
            color: #f1f5f9;
        }
        
        [data-bs-theme="dark"] .top-navbar .btn-icon {
            background: #334155;
            border-color: rgba(255,255,255,0.06);
            color: #94a3b8;
        }
        
        [data-bs-theme="dark"] .content-card {
            background: #1e293b;
            border-color: rgba(255,255,255,0.06);
        }
        
        [data-bs-theme="dark"] .page-title {
            color: #f1f5f9;
        }
        
        [data-bs-theme="dark"] .page-subtitle {
            color: #94a3b8;
        }
        
        /* ============================================================
           CONTEÚDO - CARDS
           ============================================================ */
        .content-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.25rem;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        
        .content-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(0,0,0,0.08);
        }
        
        .page-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        
        .page-subtitle {
            color: #64748b;
            font-size: 0.875rem;
        }
        
        .welcome-banner {
            background: var(--gradient);
            border-radius: 14px;
            padding: 1.5rem;
            color: #999;
            margin-bottom: 1.5rem;
        }
        
        .welcome-banner h4 {
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .welcome-banner p {
            opacity: 0.85;
            margin: 0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    
    <!-- Overlay mobile -->
    <div class="overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <!-- ==========================================
         SIDEBAR
         ========================================== -->
    <nav class="sidebar" id="sidebar">
        
        <!-- Logo -->
        <div class="sidebar-brand">
            <a href="../dashboard/index.php">
                <div class="brand-icon">
                    <i class="bi bi-router-fill"></i>
                </div>
                <div class="brand-text">
                    <span class="brand-title">ISP-QManager</span>
                    <span class="brand-subtitle">Sistema de Gestão</span>
                    <span class="client-badge">Área do Cliente</span>
                </div>
            </a>
        </div>

        <!-- Menu -->
        <ul class="nav nav-pills flex-column">
            
            <div class="menu-label">Menu Principal</div>
            
            <li class="nav-item">
                <a href="../client_portal/menu.php" class="nav-link <?php echo ($current_page == 'index' && $current_dir == 'dashboard') ? 'active' : ''; ?>">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Painel</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="../client_portal/plano.php" class="nav-link <?php echo ($current_dir == 'plans') ? 'active' : ''; ?>">
                    <i class="bi bi-tags-fill"></i>
                    <span>Planos</span>
                </a>
            </li>

            <div class="menu-divider"></div>
            <div class="menu-label">Minha Conta</div>

            <li class="nav-item">
                <a href="../profile/index.php" class="nav-link <?php echo ($current_dir == 'profile') ? 'active' : ''; ?>">
                    <i class="bi bi-person-circle"></i>
                    <span>Meu Perfil</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="../client_portal/menu.php" class="nav-link <?php echo ($current_dir == 'settings') ? 'active' : ''; ?>">
                    <i class="bi bi-gear-fill"></i>
                    <span>Configurações</span>
                </a>
            </li>
        </ul>

        <!-- Usuário Logado -->
        <div class="user-section">
            <div class="user-card">
                <div class="user-avatar">
                    <?= strtoupper(substr($client['primeiro_nome'], 0, 1)) ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?= htmlspecialchars($nome_completo) ?></div>
                    <div class="user-role">
                        <span class="status-dot <?= $status ?>"></span>
                        <span class="<?= $status_class ?>"><?= $status_label ?></span>
                    </div>
                </div>
            </div>
            <a href="../logout.php" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sair</span>
            </a>
        </div>

        <!-- Toggle Tema -->
        <div class="theme-toggle-wrapper">
            <div class="theme-toggle">
                <span><i class="bi bi-moon-stars-fill me-1"></i> Tema Escuro</span>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="themeToggle">
                </div>
            </div>
        </div>
    </nav>

    
    <main class="main-content">
        
        <!-- Header Mobile -->
        <div class="mobile-header d-md-none">
            <button class="btn btn-dark btn-sm border-0" onclick="toggleSidebar()" style="background: rgba(255,255,255,0.1);">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="text-white fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-router-fill text-primary"></i>
                ISP-QManager
            </span>
            <div style="width: 36px;"></div>
        </div>
        
        <!-- TOP NAVBAR - Fixo no topo do conteúdo -->
        <div class="top-navbar d-none d-md-flex">
            <div class="page-info">
                <button class="btn btn-sm btn-light border-0 d-lg-none" onclick="toggleSidebar()" style="padding: 0.4rem 0.6rem;">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <h5>Painel do Cliente</h5>
                    <small><?= date('d/m/Y') ?> • <?= htmlspecialchars($municipio ?: 'Sem localização') ?></small>
                </div>
            </div>
            <div class="nav-actions">
                <button class="btn-icon" title="Notificações">
                    <i class="bi bi-bell-fill"></i>
                    <span class="badge bg-danger rounded-pill">3</span>
                </button>
                <button class="btn-icon" title="Mensagens">
                    <i class="bi bi-chat-dots-fill"></i>
                </button>
                <div class="dropdown">
                    <button class="btn-icon" data-bs-toggle="dropdown" title="<?= htmlspecialchars($nome_completo) ?>">
                        <i class="bi bi-person-fill"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size: 0.85rem;">
                        <li><span class="dropdown-item-text text-muted small"><?= htmlspecialchars($email) ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../profile/index.php"><i class="bi bi-person me-2"></i>Perfil</a></li>
                        <li><a class="dropdown-item" href="../settings/index.php"><i class="bi bi-gear me-2"></i>Configurações</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
       
 
        <div class="content-scroll" id="contentScroll">
            
         
            <div class="welcome-banner">
                
                <p>Caro  cliente <h4> <?= htmlspecialchars($client['primeiro_nome']) ?>! 👋</h4>, gerencie seus planos e desfrute da sua conta.</p>
            </div>

            <!-- Cards de Resumo -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="content-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:rgba(102,126,234,0.1);">
                                <i class="bi bi-wifi text-primary fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted" style="font-size:0.8rem;">Plano Atual</h6>
                                <h5 class="mb-0 fw-bold">Premium 100MB</h5>
                            </div>
                        </div>
                        <div class="progress" style="height:5px;">
                            <div class="progress-bar bg-primary" style="width:75%"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">75% do período utilizado</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="content-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:rgba(16,185,129,0.1);">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted" style="font-size:0.8rem;">Status da Conta</h6>
                                <h5 class="mb-0 fw-bold text-success"><?= $status_label ?></h5>
                            </div>
                        </div>
                        <p class="text-muted mb-0" style="font-size:0.85rem;">Sua conexão está funcionando normalmente.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="content-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:rgba(245,158,11,0.1);">
                                <i class="bi bi-calendar-event-fill text-warning fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted" style="font-size:0.8rem;">Próximo Vencimento</h6>
                                <h5 class="mb-0 fw-bold">15/05/2026</h5>
                            </div>
                        </div>
                        <a href="../client_portal/plano.php" class="btn btn-sm btn-outline-primary w-100">Ver Planos</a>
                    </div>
                </div>
            </div>

            <!-- Conteúdo adicional para demonstrar scroll -->
            <div class="content-card mb-3">
                <h6 class="fw-bold mb-3">Informações da Conta</h6>
                <div class="table-responsive">
                    <table class="table table-borderless table-sm" style="font-size:0.9rem;">
                        <tr>
                            <td class="text-muted" style="width:140px;">Nome Completo</td>
                            <td class="fw-medium"><?= htmlspecialchars($nome_completo) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td class="fw-medium"><?= htmlspecialchars($email) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Telefone</td>
                            <td class="fw-medium"><?= htmlspecialchars($telefone ?: 'Não informado') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Município</td>
                            <td class="fw-medium"><?= htmlspecialchars($municipio ?: 'Não informado') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                <span class="badge bg-<?= $status === 'ativo' ? 'success' : 'danger' ?> bg-opacity-10 text-<?= $status === 'ativo' ? 'success' : 'danger' ?>" style="font-size:0.8rem;">
                                    <?= $status_label ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Mais conteúdo para scroll -->
            <div class="content-card mb-3">
                <h6 class="fw-bold mb-3">Últimas Atividades</h6>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background:rgba(0,0,0,0.02);">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(102,126,234,0.1);">
                            <i class="bi bi-arrow-down-circle text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-medium" style="font-size:0.9rem;">Pagamento confirmado</div>
                            <small class="text-muted">Hoje, 10:30</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background:rgba(0,0,0,0.02);">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(16,185,129,0.1);">
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                        <div>
                            <div class="fw-medium" style="font-size:0.9rem;">Plano renovado</div>
                            <small class="text-muted">Ontem, 14:15</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background:rgba(0,0,0,0.02);">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(245,158,11,0.1);">
                            <i class="bi bi-exclamation-circle text-warning"></i>
                        </div>
                        <div>
                            <div class="fw-medium" style="font-size:0.9rem;">Alerta de consumo</div>
                            <small class="text-muted">22/04/2026, 09:00</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center text-muted py-4" style="font-size:0.8rem;">
                <i class="bi bi-shield-check me-1"></i> ISP-QManager v1.0 • © 2026
            </div>

        </div>
    </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Toggle Sidebar Mobile
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    }

    // Toggle Tema Claro/Escuro
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;

    // Verifica tema salvo
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        themeToggle.checked = true;
        html.setAttribute('data-bs-theme', 'dark');
    }

    themeToggle.addEventListener('change', function() {
        if (this.checked) {
            html.setAttribute('data-bs-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            html.setAttribute('data-bs-theme', 'light');
            localStorage.setItem('theme', 'light');
        }
    });
</script>

</body>
</html>