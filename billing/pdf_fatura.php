<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
include "../config/database.php";

require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$fatura_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($fatura_id <= 0) {
    die("ID da fatura inválido");
}

// Verificar se a conexão existe
if (!isset($qmanager) || !$qmanager) {
    die("Erro: Conexão com o banco de dados não estabelecida");
}

$sql = "SELECT f.*, c.primeiro_nome, c.ultimo_nome, c.email, c.telefone, c.municipio,
        p.nome as plano_nome
        FROM faturas f
        JOIN client c ON c.client_id = f.client_id
        LEFT JOIN planos p ON p.plan_id = f.client_id
        WHERE f.fatura_id = ?";

$da = $qmanager->prepare($sql);

// Verificar se prepare() falhou
if ($da === false) {
    die("Erro ao preparar a consulta: " . $qmanager->error);
}

$da->bind_param("i", $fatura_id);
$da->execute();
$result = $da->get_result();
$f = $result->fetch_assoc();

if (!$f) {
    die("Fatura não encontrada");
}

// Configurar Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);

// HTML do PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #333; }
        .header { border-bottom: 3px solid #0d6efd; padding-bottom: 15px; margin-bottom: 25px; }
        .fatura-title { font-size: 24px; color: #0d6efd; margin: 0; }
        .info-box { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 15px; }
        .info-box h4 { color: #0d6efd; font-size: 11px; text-transform: uppercase; margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background: #0d6efd; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #dee2e6; }
        .total { text-align: right; font-size: 16px; font-weight: bold; color: #0d6efd; margin-top: 15px; padding-top: 15px; border-top: 3px solid #0d6efd; }
        .status { padding: 3px 10px; border-radius: 15px; font-size: 10px; font-weight: bold; }
        .status-paga { background: #d1edff; color: #0d6efd; }
        .status-pendente { background: #fff3cd; color: #856404; }
        .status-vencida { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<div class="header">
    <table style="width: 100%; border: none;">
        <tr>
            <td style="border: none; padding: 0;">
                <h1 class="fatura-title">FATURA</h1>
                <p style="margin: 5px 0 0 0; color: #6c757d; font-size: 12px;">
                    #' . str_pad($f['fatura_id'], 6, '0', STR_PAD_LEFT) . '
                </p>
            </td>
            <td style="border: none; padding: 0; text-align: right;">
                <h2 style="margin: 0; color: #0d6efd; font-size: 18px;">ISP-QManager</h2>
                <p style="margin: 5px 0 0 0; color: #6c757d; font-size: 11px;">
                    Sistema de Gestão ISP<br>
                    suporte@isp-qmanager.com
                </p>
            </td>
        </tr>
    </table>
</div>

<table style="width: 100%; border: none;">
    <tr>
        <td style="width: 50%; border: none; padding: 0 10px 0 0;">
            <div class="info-box">
                <h4>Faturado Para</h4>
                <strong>' . htmlspecialchars($f['primeiro_nome'] . ' ' . $f['ultimo_nome']) . '</strong><br>
                ' . htmlspecialchars($f['email']) . '<br>
                ' . htmlspecialchars($f['telefone']) . '<br>
                ' . htmlspecialchars($f['municipio']) . '
            </div>
        </td>
        <td style="width: 50%; border: none; padding: 0 0 0 10px;">
            <div class="info-box">
                <h4>Detalhes da Fatura</h4>
                <strong>Data Emissão:</strong> ' . date('d/m/Y', strtotime($f['data_emissao'])) . '<br>
                <strong>Vencimento:</strong> ' . date('d/m/Y', strtotime($f['data_vencimento'])) . '<br>
                <strong>Status:</strong> 
                <span class="status status-' . $f['status'] . '">' . ucfirst($f['status']) . '</span>
            </div>
        </td>
    </tr>
</table>

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
                <strong>' . ($f['plano_nome'] ? htmlspecialchars($f['plano_nome']) : 'Serviço de Internet') . '</strong><br>
                <small style="color: #6c757d;">Referência: ' . date('m/Y', strtotime($f['data_emissao'])) . '</small>
            </td>
            <td style="text-align: right;">
                ' . number_format($f['valor'], 2, ',', '.') . ' Kz
            </td>
        </tr>
    </tbody>
</table>

<div class="total">
    Total: ' . number_format($f['valor'], 2, ',', '.') . ' Kz
</div>

<div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #dee2e6; text-align: center; color: #6c757d; font-size: 10px;">
    <p>Obrigado por sua preferência!<br>
    Para dúvidas, entre em contato: suporte@isp-qmanager.com</p>
</div>

</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Nome do arquivo
$filename = 'Fatura_' . str_pad($f['fatura_id'], 6, '0', STR_PAD_LEFT) . '_' . $f['primeiro_nome'] . '.pdf';

// Download
$dompdf->stream($filename, array("Attachment" => true));
?>