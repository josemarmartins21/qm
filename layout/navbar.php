<!--nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="../dashboard/index.php">ISP- QManager</a>
    <div class="navbar-nav">
      <a class="nav-link" href="../clients/index.php">Clientes</a>
      <a class="nav-link" href="../plans/index.php">Planos</a>
      <a class="nav-link" href="../subscriptions/index.php">Assinaturas</a>
    </div>
  </div>
</nav-->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php

include "../config/database.php";


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
include "sessao.php"; 
?>
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISP-QManager</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed: 80px;
            --transition-base: all 0.3s ease-in-out;
        }
      
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
    
        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }
        
   
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            background: linear-gradient(180deg, #1a1d29 0%, #212529 100%);
            transition: var(--transition-base);
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
        }
        
    
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background-color: #f8f9fa;
            transition: var(--transition-base);
            display: flex;
            flex-direction: column;
        }
        
  
        .content-area {
            flex: 1;
            padding: 1.5rem;
        }
        
        /* Menu navegação */
        .sidebar .nav {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem;
        }
        
        .sidebar .nav-link {
            border-radius: 4px;
            margin-bottom: 0.5rem;
            padding: 0.75rem 1rem;
            color: rgba(255,255,255,0.8) !important;
            transition: var(--transition-base);
            font-weight: 500;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(13, 110, 253, 0.9);
            color: #fff !important;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
        }
        
        
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
       
        .user-section {
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.2);
        }
        
      
        .theme-toggle-wrapper {
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .form-check-input:checked {
            background-color: #ffc107;
            border-color: #ffc107;
        }
        
       
        .mobile-header {
            display: none;
            background: linear-gradient(90deg, #1a1d29 0%, #212529 100%);
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
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
            
            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1035;
            }
            
            .overlay.show {
                display: block;
            }
            
        }
        
      
        [data-bs-theme="dark"] .main-content {
            background-color: #212529;
        }
        
        
        .sidebar ::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar ::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar ::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    
    <!-- Overlay para mobile -->
    <div class="overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <!-- SIDEBAR -->
    <nav class="sidebar" id="sidebar">
        
        <!-- Logo -->
        <div class="sidebar-brand">
            <a href="../dashboard/index.php" class="d-flex align-items-center text-white text-decoration-none">
                <i class="bi bi-router-fill text-primary fs-3 me-2"></i>
                <span class="fs-5 fw-bold">ISP-QManager</span>
            </a>
        </div>

        <!-- Menu -->
        <ul class="nav nav-pills flex-column">
            
            <li class="nav-item">
                <a href="../dashboard/index.php" class="nav-link <?php echo ($current_page == 'index' && $current_dir == 'dashboard') ? 'active' : ''; ?>">
                <i class="bi bi-house-gear-fill"></i>
                    <span class="ms-2">ISP-Panel</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="../clients/index.php" class="nav-link <?php echo ($current_dir == 'clients') ? 'active' : ''; ?>">
                    <i class="bi bi-people-fill"></i>
                    <span class="ms-2">Clientes</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="../plans/index.php" class="nav-link <?php echo ($current_dir == 'plans') ? 'active' : ''; ?>">
                    <i class="bi bi-tags-fill"></i>
                    <span class="ms-2">Planos</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="../subscriptions/index.php" class="nav-link <?php echo ($current_dir == 'subscriptions') ? 'active' : ''; ?>">
                    <i class="bi bi-credit-card-fill"></i>
                    <span class="ms-2">Assinaturas</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="../reports/index.php" class="nav-link <?php echo ($current_dir == 'reports') ? 'active' : ''; ?>">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span class="ms-2">Relatórios</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="../configuracoes/index.php" class="nav-link <?php echo ($current_dir == 'settings') ? 'active' : ''; ?>">
                    <i class="bi bi-gear-fill"></i>
                    <span class="ms-2">Configurações</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="../auth/logout.php" class="nav-link <?php echo ($current_dir == 'support') ? 'active' : ''; ?>">
                
                    <span class="ms-2 btn btn-danger" > <i class="bi bi-box-arrow-right me-2"></i> Sair</span>
                </a>
            </li>
        </ul>
        

        <!-- Usuário -->
        <div class="user-section">
            <div class="dropdown">
                <a href="perfil.php" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle">  </i>
                     <span>
                     <?=   $nome ?> • <?= ucfirst($role) ?> 
                 
                
                </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark shadow">
                    
                    <li><a class="dropdown-item" href="../settings/index.php"><i class="bi bi-gear me-2"></i>Configurações</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                </ul>
            </div>
        </div>

        <!-- Toggle Tema -->
        <div class="theme-toggle-wrapper">
            <div class="d-flex align-items-center justify-content-between text-white">
                <small class="text-secondary">Tema</small>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input bg-warning border-warning" type="checkbox" id="themeToggle" style="width: 2.5em; height: 1.2em;">
                    <label class="form-check-label ms-2" for="themeToggle">
                        <i class="bi bi-moon-stars-fill text-white" id="themeIcon"></i>
                    </label>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="main-content">
        
        <!-- Header Mobile -->
        <div class="mobile-header d-md-none">
            <button class="btn btn-dark btn-sm" onclick="toggleSidebar()">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="text-white fw-bold">
                <i class="bi bi-router-fill text-primary me-2"></i>ISP-QManager
            </span>
            <div style="width: 40px;"></div> <!-- Spacer para centralização -->
        </div>
       
        <!-- ÁREA DE CONTEÚDO - Aqui entra o conteúdo das páginas -->
        <div class="content-area"> 

    