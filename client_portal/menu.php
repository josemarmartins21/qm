<?php


session_start();

if (!isset($_SESSION['client'])) {
    header("Location: login.php");
    exit;
}

include "../config/database.php";


$client = $_SESSION['client'];
$client_id = (int)$client['client_id'];
$nome = $client['nome'];
$municipio = $client['municipio'] ?? '—';


$sqlPlano = "
    SELECT p.nome, p.preco, h.data_expiracao
    FROM historico_de_assinaturas h
    INNER JOIN planos p ON p.plan_id = h.planos_plan_id
    WHERE h.client_client_id = ?
    ORDER BY h.created_at DESC
    LIMIT 1
";

$stmtPlano = $qmanager->prepare($sqlPlano);
if (!$stmtPlano) {
    die("Erro SQL (PLANO): " . $qmanager->error);
}
$stmtPlano->bind_param("i", $client_id);
$stmtPlano->execute();
$plano = $stmtPlano->get_result()->fetch_assoc();

$sqlFat = "
    SELECT fatura_id, valor, status, data_vencimento
    FROM faturas
    WHERE client_id = ?
    ORDER BY data_vencimento DESC
";

$stmtFat = $qmanager->prepare($sqlFat);
if (!$stmtFat) {
    die("Erro SQL (FATURAS): " . $qmanager->error);
}
$stmtFat->bind_param("i", $client_id);
$stmtFat->execute();
$faturas = $stmtFat->get_result();


$nome_completo = $client['nome'] ?? 'Cliente';
$email = $client['email'] ?? '';
$status = strtolower($client['status'] ?? 'ativo');
$status_label = ($status === 'ativo') ? 'Ativo' : 'Suspenso';
$status_badge = ($status === 'ativo') ? 'bg-success' : 'bg-danger';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_dir  = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Cliente - ISP-QManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* ===== SIDEBAR FIXO ===== */
        .sidebar-client {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            z-index: 1040;
            background: linear-gradient(180deg, #1a1d29 0%, #212529 100%);
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        /* ===== CONTEÚDO DESLOCADO ===== */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            background: #f8f9fa;
        }
        
    
        .sidebar-client::-webkit-scrollbar { width: 4px; }
        .sidebar-client::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
        
        
        .sidebar-client .nav-link {
            color: rgba(255,255,255,0.7) !important;
            border-radius: 8px;
            padding: 0.6rem 1rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s;
            font-size: 0.9rem;
        }
        .sidebar-client .nav-link:hover,
        .sidebar-client .nav-link.active {
            background: #0d6efd;
            color: #fff !important;
        }
        .sidebar-client .nav-link i {
            width: 22px;
            text-align: center;
            margin-right: 0.5rem;
        }
        
   
        @media (max-width: 991.98px) {
            .sidebar-client {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar-client.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .mobile-header { display: flex !important; }
        }
        
     
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1035;
        }
        .sidebar-overlay.show { display: block; }

        .content-scroll {
            overflow-y: auto;
            max-height: calc(100vh - 60px);
        }
    </style>
</head>
<body>


<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>


<nav class="sidebar-client d-flex flex-column" id="sidebarClient">
    
    <!-- Logo -->
    <div class="p-3 border-bottom border-secondary border-opacity-25">
        <a href="menu.php" class="d-flex align-items-center text-white text-decoration-none gap-2">
            <i class="bi bi-router-fill text-primary fs-3"></i>
            <div>
                <span class="fw-bold fs-5">ISP-QManager</span><br>
                <span class="badge bg-success" style="font-size:0.6rem;">ÁREA DO CLIENTE</span>
            </div>
        </a>
    </div>

    <!-- Menu -->
    <div class="flex-grow-1 p-2">
        <small class="text-secondary text-uppercase ms-2" style="font-size:0.65rem;letter-spacing:1px;">Menu Principal</small>
        <ul class="nav nav-pills flex-column mt-1">
            <li class="nav-item">
                <a href="menu.php" class="nav-link active">
                    <i class="bi bi-grid-fill"></i>
                    <span>Painel</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Usuário -->
    <div class="p-3 border-top border-secondary border-opacity-25 bg-dark bg-opacity-25">
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width:36px;height:36px;font-size:0.9rem;">
                <?= strtoupper(substr($nome_completo, 0, 1)) ?>
            </div>
            <div class="text-white overflow-hidden">
                <div class="fw-semibold text-truncate" style="font-size:0.85rem;"><?= htmlspecialchars($nome_completo) ?></div>
                <div class="d-flex align-items-center gap-1" style="font-size:0.75rem;">
                    <span class="rounded-circle <?= $status_badge ?>" style="width:6px;height:6px;"></span>
                    <span class="text-secondary"><?= $status_label ?></span>
                </div>
            </div>
        </div>
        <a href="logout.php" class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sair</span>
        </a>
    </div>
</nav>

<div class="main-content">
    
    <!-- Header Mobile -->
    <div class="mobile-header d-none bg-dark text-white p-2 align-items-center justify-content-between sticky-top">
        <button class="btn btn-dark btn-sm border-secondary" onclick="toggleSidebar()">
            <i class="bi bi-list fs-5"></i>
        </button>
        <span class="fw-bold"><i class="bi bi-router-fill text-primary me-1"></i>ISP-QManager</span>
        <div style="width:36px;"></div>
    </div>

    <!-- ÁREA DE CONTEÚDO COM SCROLL -->
    <div class="content-scroll p-3 p-md-4">

        <!-- ===== CONTEÚDO ORIGINAL DA menu.php - NÃO ALTERADO ===== -->
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold">Portal do Cliente</h3>
                    <small class="text-muted">
                        <?= htmlspecialchars($nome) ?> • Município: <?= htmlspecialchars($municipio) ?>
                    </small>
                </div>
                <div class="text-end">
                    <span class="badge bg-success">
                        <i class="bi bi-circle-fill"></i> Online
                    </span><br>
                    <a href="logout.php" class="btn btn-sm btn-outline-danger mt-1">Sair</a>
                </div>
            </div>

            <!-- PLANO -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card shadow p-3 h-100">
                        <h6 class="fw-bold">Plano Atual</h6>

                        <?php if($plano): ?>
                        <p class="mb-1"><strong><?= htmlspecialchars($plano['nome']) ?></strong></p>
                        <p class="mb-1">Preço: <?= number_format($plano['preco'],2,",",".") ?></p>
                        <p class="mb-0">
                            Expira em: <?= date("d/m/Y", strtotime($plano['data_expiracao'])) ?>
                        </p>
                        <?php else: ?>
                        <span class="text-muted">Nenhum plano ativo</span>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow p-3 h-100">
                        <h6 class="fw-bold">Status do Serviço</h6>

                        <?php
                        if ($plano && strtotime($plano['data_expiracao']) >= time()) {
                            echo '<span class="badge bg-success">Ativo</span>';
                        } else {
                            echo '<span class="badge bg-danger">Suspenso</span>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- FATURAS -->
            <div class="card shadow p-4">
                <h5 class="fw-bold mb-3">Minhas Faturas</h5>

                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Vencimento</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php while($f = $faturas->fetch_assoc()): ?>
                        <tr>
                            <td><?= date("d/m/Y", strtotime($f['data_vencimento'])) ?></td>
                            <td><?= number_format($f['valor'],2,",",".") ?></td>
                            <td>
                                <?= $f['status'] === 'paga'
                                    ? '<span class="badge bg-success">Paga</span>'
                                    : '<span class="badge bg-warning text-dark">Pendente</span>' ?>
                            </td>
                            <td>
                                <a href="fatura_pdf.php?id=<?= $f['fatura_id'] ?>" class="btn btn-sm btn-outline-primary">
                                    PDF
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>

                        </tbody>
                    </table>
                    <?php   include "../layout/navbarClient.php";?>
                </div>

            </div>

        </div>
     

    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebarClient').classList.toggle('show');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    }
</script>

</body>
</html>