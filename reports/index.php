<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
include "../config/database.php";

// Processar filtros
$where = [];
$params = [];
$types = "";

if (!empty($_GET['nome'])) {
    $where[] = "(u.primeiro_nome LIKE ? OR u.ultimo_nome LIKE ?)";
    $nome = "%" . $_GET['nome'] . "%";
    $params[] = $nome;
    $params[] = $nome;
    $types .= "ss";
}

if (!empty($_GET['email'])) {
    $where[] = "u.email LIKE ?";
    $params[] = "%" . $_GET['email'] . "%";
    $types .= "s";
}

if (!empty($_GET['status'])) {
    $where[] = "h.status = ?";
    $params[] = $_GET['status'];
    $types .= "s";
}

if (!empty($_GET['is_adm'])) {
    $where[] = "u.is_adm = ?";
    $params[] = $_GET['is_adm'];
    $types .= "i";
}

if (!empty($_GET['data_inicio'])) {
    $where[] = "h.created_at >= ?";
    $params[] = $_GET['data_inicio'];
    $types .= "s";
}

if (!empty($_GET['data_fim'])) {
    $where[] = "h.created_at <= ?";
    $params[] = $_GET['data_fim'] . ' 23:59:59';
    $types .= "s";
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';


$sql = "SELECT 
            u.user_id,
            CONCAT(u.primeiro_nome, ' ', u.ultimo_nome) AS nome_completo,
            u.email,
            u.is_adm,
            h.historico_id,
            h.planos_plan_id,
            h.created_at AS data_assinatura,
            h.data_expiracao,
            h.status AS status_assinatura,
            s.session_id,
            s.last_activity AS ultima_atividade
        FROM users u
        LEFT JOIN historico_de_assinaturas h ON u.user_id = h.client_client_id
        LEFT JOIN user_sessions s ON u.user_id = s.user_id
        $whereClause
        ORDER BY u.user_id DESC, h.created_at DESC";


$relatorio =$qmanager ->prepare($sql);

if (!empty($params)) {
    $relatorio->bind_param($types, ...$params);
}

$relatorio->execute();
$result = $relatorio->get_result();
$dados = $result->fetch_all(MYSQLI_ASSOC);
$relatorio->close();


if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="relatorio_usuarios.csv"');
    echo "\xEF\xBB\xBF"; // BOM UTF-8
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID Usuário', 'Nome Completo', 'Email', 'Administrador', 'ID Histórico', 'Plano ID', 'Data Assinatura', 'Data Expiração', 'Status', 'Sessão ID', 'Última Atividade']);
    
    foreach ($dados as $row) {
        fputcsv($output, [
            $row['user_id'],
            $row['nome_completo'],
            $row['email'],
            $row['is_adm'] ? 'Sim' : 'Não',
            $row['historico_id'] ?: 'N/A',
            $row['planos_plan_id'] ?: 'N/A',
            $row['data_assinatura'] ? date('d/m/Y H:i', strtotime($row['data_assinatura'])) : 'N/A',
            $row['data_expiracao'] ? date('d/m/Y', strtotime($row['data_expiracao'])) : 'N/A',
            $row['status_assinatura'] ?: 'N/A',
            $row['session_id'] ?: 'N/A',
            $row['ultima_atividade'] ? date('d/m/Y H:i', strtotime($row['ultima_atividade'])) : 'N/A'
        ]);
    }
    fclose($output);
    exit;
}

$totalUsers = count(array_unique(array_column($dados, 'user_id')));
$totalAssinaturas = count(array_filter($dados, function($d) { return !empty($d['historico_id']); }));
$admins = count(array_filter($dados, function($d) { return $d['is_adm'] == 1; }));
$ativos = count(array_filter($dados, function($d) { return strtolower($d['status_assinatura'] ?? '') == 'ativo'; }));
//include "../layout/header.php";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Usuários e Assinaturas</title>
    
    <!-- DataTables CSS -->
     <!-- Bootstrap 5 CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    
    <style>


            
            
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:(0%, #764ba7 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; background: white; border-radius: 4px; box-shadow: 1 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header {background: linear-gradient(180deg, #1a1d29 0%, #212529 100%);box-shadow: 4px 0 20px rgba(0,0,0,0.15); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 2rem; margin-bottom: 10px; }
        .filtros { padding: 25px; border-bottom: 1px solid #dee2e6; }
        .filtros-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-weight: 600; margin-bottom: 5px; color: #495057; font-size: 0.9rem; }
        .form-group input, .form-group select { padding: 10px 12px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 0.95rem; transition: border-color 0.3s; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #667eea; }
        .btn { padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: all 0.3s; text-transform: uppercase; font-size: 0.85rem; text-decoration: none; display: inline-block; text-align: center; }
        .btn-primary { background: blue; color: white; }
        .btn-primary:hover { background: #5568d3; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        .acoes { padding: 20px 25px; display: flex; gap: 10px; flex-wrap: wrap; background: white; border-bottom: 1px solid #dee2e6; }
        .tabela-container { padding: 25px; overflow-x: auto; }
        table.dataTable { width: 100% !important; border-collapse: collapse; }
        table.dataTable thead th {  background: linear-gradient(180deg, #1a1d29 0%, #212529 100%);box-shadow: 4px 0 20px rgba(0,0,0,0.15); color: white; font-weight: 600; padding: 15px 10px; border: none; }
        table.dataTable tbody td { padding: 12px 10px; border-bottom: 1px solid #dee2e6; }
        table.dataTable tbody tr:hover { background-color: #f8f9fa; }
        .status-ativo { background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; }
        .status-inativo { background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; }
        .status-pendente { background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; }
        .badge-adm { background: blue; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; }
        .badge-user { background: #6c757d; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; }
        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 15px; }
        .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { margin-top: 15px; }
        @media print { body { background: white; padding: 0; } .container { box-shadow: none; max-width: 100%; } .filtros, .acoes, .dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate, .dt-buttons { display: none !important; } .header { background: #333 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; } table.dataTable thead th { background: #333 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-card {  background: linear-gradient(180deg, #1a1d29 0%, #212529 100%);box-shadow: 4px 0 20px rgba(0,0,0,0.15); color: white; padding: 20px; border-radius: 4px; text-align: center; }
        .stat-number { font-size: 2rem; font-weight: bold; margin-bottom: 5px; }
        .stat-label { font-size: 0.9rem; opacity: 0.9; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
    <div class="sidebar-brand">
            <a href="../dashboard/index.php" class="d-flex align-items-center text-white text-decoration-none">
                <i class="bi bi-router-fill text-primary fs-3 me-2"></i>
                <span class="fs-5 fw-bold">ISP-QManager</span>
            </a>
        </div>
        <h1>Relatório de Usuários e Assinaturas</h1>
        <hr style="width:100%; height:4px; background-color:blue;">
        <div class="form-group d-flex">
                <a href="../dashboard/index.php" class="btn btn-primary ms-auto" > <i class="bi bi-house-gear-fill"></i> ISP-Panel</a>
            </div>
    </div>
    
    <!-- Estatísticas -->
    <div class="filtros">
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalUsers; ?></div>
                <div class="stat-label">Total Usuários</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalAssinaturas; ?></div>
                <div class="stat-label">Assinaturas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $admins; ?></div>
                <div class="stat-label">Administradores</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $ativos; ?></div>
                <div class="stat-label">Assinaturas Ativas</div>
            </div>
        </div>
    </div>
    
    <!-- Filtros -->
    <div class="filtros">
        <form method="GET" class="filtros-form">
            <div class="form-group">
                <label for="nome">Nome do Usuário</label>
                <input type="text" id="nome" name="nome" value="<?php echo isset($_GET['nome']) ? htmlspecialchars($_GET['nome']) : ''; ?>" placeholder="Buscar por nome...">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" placeholder="Buscar por email...">
            </div>
            
            <div class="form-group">
                <label for="status">Status Assinatura</label>
                <select id="status" name="status">
                    <option value="">Todos</option>
                    <option value="ativo" <?php echo (isset($_GET['status']) && $_GET['status'] == 'ativo') ? 'selected' : ''; ?>>Ativo</option>
                    <option value="inativo" <?php echo (isset($_GET['status']) && $_GET['status'] == 'inativo') ? 'selected' : ''; ?>>Inativo</option>
                    <option value="pendente" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pendente') ? 'selected' : ''; ?>>Pendente</option>
                    <option value="expirado" <?php echo (isset($_GET['status']) && $_GET['status'] == 'expirado') ? 'selected' : ''; ?>>Expirado</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="is_adm">Tipo Usuário</label>
                <select id="is_adm" name="is_adm">
                    <option value="">Todos</option>
                    <option value="1" <?php echo (isset($_GET['is_adm']) && $_GET['is_adm'] == '1') ? 'selected' : ''; ?>>Administrador</option>
                    <option value="0" <?php echo (isset($_GET['is_adm']) && $_GET['is_adm'] == '0') ? 'selected' : ''; ?>>Usuário Normal</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="data_inicio">Data Início</label>
                <input type="date" id="data_inicio" name="data_inicio" value="<?php echo isset($_GET['data_inicio']) ? htmlspecialchars($_GET['data_inicio']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="data_fim">Data Fim</label>
                <input type="date" id="data_fim" name="data_fim" value="<?php echo isset($_GET['data_fim']) ? htmlspecialchars($_GET['data_fim']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
            
            <div class="form-group">
                <a href="index.php" class="btn btn-secondary">Limpar</a>
            </div>

            <div class="form-group">
                <a href="../dashboard/index.php" class="btn btn-primary"> <i class="bi bi-house-gear-fill"></i> ISP-Panel</a>
            </div>
        </form>
    </div>
    
    <!-- Ações -->
    <div class="acoes">
        <?php
        $queryParams = $_GET;
        $queryParams['export'] = 'csv';
        $csvUrl = '?' . http_build_query($queryParams);
        ?>
        <a href="<?php echo $csvUrl; ?>" class="btn btn-success"> <i class="bi bi-file-earmark-arrow-down-fill"></i> Download</a>
        <button onclick="exportarExcel()" class="btn btn-success"> <i class="bi bi-file-excel"></i> Exportar Excel</button>
        <button onclick="exportarPDF()" class="btn btn-success"> <i class="bi bi-file-pdf-fill"></i> Exportar PDF</button>
        <button onclick="window.print()" class="btn btn-primary"> <i class="bi bi-printer"></i> Imprimir</button>
    </div>
    
    <!-- Tabela -->
    <div class="tabela-container">
    
        <table id="tabelaRelatorio" class="display nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome Completo</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Plano ID</th>
                    <th>Data Assinatura</th>
                    <th>Data Expiração</th>
                    <th>Status</th>
                    <th>Última Atividade</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dados as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['nome_completo']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <?php if ($row['is_adm']): ?>
                            <span class="badge-adm">Admin</span>
                        <?php else: ?>
                            <span class="badge-user">Usuário</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $row['planos_plan_id'] ? htmlspecialchars($row['planos_plan_id']) : '-'; ?></td>
                    <td><?php echo $row['data_assinatura'] ? date('d/m/Y H:i', strtotime($row['data_assinatura'])) : '-'; ?></td>
                    <td><?php echo $row['data_expiracao'] ? date('d/m/Y', strtotime($row['data_expiracao'])) : '-'; ?></td>
                    <td>
                        <?php 
                        $status = strtolower($row['status_assinatura'] ?? '');
                        if ($status == 'ativo') {
                            $classe = 'status-ativo';
                        } elseif (in_array($status, ['inativo', 'expirado'])) {
                            $classe = 'status-inativo';
                        } elseif ($status == 'pendente') {
                            $classe = 'status-pendente';
                        } else {
                            $classe = 'badge-user';
                        }
                        ?>
                        <span class="<?php echo $classe; ?>"><?php echo ucfirst(htmlspecialchars($row['status_assinatura'] ?: 'N/A')); ?></span>
                    </td>
                    <td><?php echo $row['ultima_atividade'] ? date('d/m/Y H:i', strtotime($row['ultima_atividade'])) : 'N/A'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#tabelaRelatorio').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        },
        pageLength: 25,
        order: [[0, 'desc']],
        dom: 'Blfrtip',
        buttons: [
            {
                extend: 'excel',
                text: 'Excel (DataTables)',
                title: 'Relatório de Usuários',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text: 'PDF (DataTables)',
                title: 'Relatório de Usuários',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: 'Imprimir (DataTables)',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ]
    });
});

function exportarExcel() {
    var tabela = document.getElementById('tabelaRelatorio');
    var html = tabela.outerHTML;
    var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
    var downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    downloadLink.href = url;
    downloadLink.download = 'relatorio_usuarios.xls';
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

function exportarPDF() {
    var tabela = document.getElementById('tabelaRelatorio');
    var janela = window.open('', '', 'height=600,width=800');
    var dataAtual = new Date().toLocaleString('pt-BR');
    
    janela.document.write('<html><head><title>Relatório de Usuários</title>');
    janela.document.write('<style>');
    janela.document.write('table { border-collapse: collapse; width: 100%; font-family: Arial; font-size: 12px; }');
    janela.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }');
    janela.document.write('th { background-color: #667eea; color: white; }');
    janela.document.write('tr:nth-child(even) { background-color: #f2f2f2; }');
    janela.document.write('h2 { color: #333; font-family: Arial; }');
    janela.document.write('p { color: #666; font-family: Arial; }');
    janela.document.write('</style>');
    janela.document.write('</head><body>');
    janela.document.write('<h2>Relatório de Usuários e Assinaturas</h2>');
    janela.document.write('<p>Gerado em: ' + dataAtual + '</p>');
    janela.document.write(tabela.outerHTML);
    janela.document.write('</body></html>');
    
    janela.document.close();
    janela.focus();
    setTimeout(function() {
        janela.print();
        janela.close();
    }, 250);
}
</script>

</body>
</html>