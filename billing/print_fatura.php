<?php
include "../config/database.php";

$fatura_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($fatura_id <= 0) {
    die("ID da fatura inválido");
}

// Verificar se a conexão existe e está funcionando
if (!isset($qmanager) || !$qmanager) {
    die("Erro: Conexão com o banco de dados não estabelecida");
}

$sql = "SELECT f.*, c.primeiro_nome, c.ultimo_nome, c.email, c.telefone, c.municipio,
        p.nome as plano_nome
        FROM faturas f
        JOIN client c ON c.client_id = f.client_id
        LEFT JOIN planos p ON p.plan_id = f.client_id
        WHERE f.fatura_id = ?";

$dados= $qmanager->prepare($sql);


if ($dados === false) {
    die("Erro ao preparar a consulta: " . $qmanager->error);
}

$dados->bind_param("i", $fatura_id);
$dados->execute();
$result = $dados->get_result();
$f = $result->fetch_assoc();

if (!$f) {
    die("Fatura não encontrada");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
    <meta charset="UTF-8">
    <title>Fatura #<?= str_pad($f['fatura_id'], 6, '0', STR_PAD_LEFT) ?></title>
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 20px; }
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            color: #333;
        }
        .header {
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-info { text-align: right; }
        .fatura-title {
            font-size: 2rem;
            color: #001bbb;
            margin: 0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
        }
        .info-box h4 {
            margin-top: 0;
            color: #0d6efd;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background:   #001bbb;
            color: white;
        }
        .total {
            text-align: right;
            font-size: 1.5rem;
            font-weight: bold;
            color: #001bbb;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 3px solid #0d6efd;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paga { background: #d1edff;  color: #001bbb; }
        .status-pendente { background: #fff3cd; color: #856404; }
        .status-vencida { background: #f8d7da; color: #721c24; }
        .no-print {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            margin: 0 5px;
        }
        .btn-primary { background: #0d6efd; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>

<div class="no-print">
    <button class="btn btn-primary" onclick="window.print()">
        🖨️ Imprimir Fatura
    </button>
    <a href="pdf_fatura.php?id=<?= $fatura_id ?>" class="btn btn-secondary">
        📄 Baixar PDF
    </a>
    <a href="index.php" class="btn btn-secondary">
        ← Voltar
    </a>
</div>

<div class="header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="fatura-title">FATURA</h1>
            <p style="margin: 5px 0 0 0; color: #6c757d;">
                #<?= str_pad($f['fatura_id'], 6, '0', STR_PAD_LEFT) ?>
            </p>
        </div>
        <div class="company-info">
            <h2 style="margin: 0; color: #0d6efd;">ISP-QManager</h2>
            <p style="margin: 5px 0 0 0; color: #6c757d;">
                Sistema de Gestão ISP<br>
                suporte@isp-qmanager.com
            </p>
        </div>
    </div>
</div>

<div class="info-grid">
    <div class="info-box">
        <h4>📋 Faturado Para</h4>
        <strong><?= htmlspecialchars($f['primeiro_nome'] . ' ' . $f['ultimo_nome']) ?></strong><br>
        <?= htmlspecialchars($f['email']) ?><br>
        <?= htmlspecialchars($f['telefone']) ?><br>
        <?= nl2br(htmlspecialchars($f['municipio'])) ?>
    </div>
    <div class="info-box">
        <h4>📅 Detalhes da Fatura</h4>
        <strong>Data Emissão:</strong> <?= date('d/m/Y', strtotime($f['data_emissao'])) ?><br>
        <strong>Vencimento:</strong> <?= date('d/m/Y', strtotime($f['data_vencimento'])) ?><br>
        <strong>Status:</strong> 
        <span class="status status-<?= $f['status'] ?>">
            <?= ucfirst($f['status']) ?>
        </span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Descrição</th>
            <th style="text-align: right;">Valor</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <strong><?= htmlspecialchars($f['plano_nome'] ? $f['plano_nome'] : 'Serviço de Internet') ?></strong><br>
                <small style="color: #6c757d;">Referência: <?= date('m/Y', strtotime($f['data_emissao'])) ?></small>
            </td>
            <td style="text-align: right;">
                <?= number_format($f['valor'], 2, ',', '.') ?> Kz
            </td>
        </tr>
    </tbody>
</table>

<div class="total">
    Total: <?= number_format($f['valor'], 2, ',', '.') ?> Kz
</div>

<div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #dee2e6; text-align: center; color: #6c757d; font-size: 0.9rem;">
    <p>Obrigado por sua preferência!<br>
    Para dúvidas, entre em contato: suporte@isp-qmanager.com</p>
</div>

</body>
</html>