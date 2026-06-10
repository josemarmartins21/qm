<?php
require_once __DIR__ . '/../config/database.php';
require 'lib/fpdf.php';
require 'phpqrcode/qrlib.php';
header('Content-Type:text/html; charset=UTF-8') ;

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die("Fatura inválida");
}


$sql = "
SELECT f.fatura_id, f.valor, f.status, f.data_vencimento,
       c.primeiro_nome, c.ultimo_nome, c.email
FROM faturas f
JOIN client c ON c.client_id = f.client_id
WHERE f.fatura_id = ?
LIMIT 1
";

$da = $qmanager->prepare($sql);
$da->bind_param("i", $id);
$da->execute();
$f = $da->get_result()->fetch_assoc();

if (!$f) {
    die("Fatura não encontrada");
}
header('Content-Type:text/html; charset=UTF-8') ;

$qrData = "FATURA:{$f['fatura_id']}|VALOR:{$f['valor']}|EMAIL:{$f['email']}";
$qrFile = sys_get_temp_dir() . "/qr_fatura_{$id}.png";
QRcode::png($qrData, $qrFile, QR_ECLEVEL_L, 4);


$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

$pdf->Image(__DIR__ . '/../assets/logo.jpg', 10, 10, 40);


$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'FATURA',0,1,'R');
$pdf->Ln(10);

$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,'Cliente: '.$f['primeiro_nome'].' '.$f['ultimo_nome'],0,1);
$pdf->Cell(0,8,'Email: '.$f['email'],0,1);
$pdf->Ln(5);

/* DADOS DA FATURA */
$pdf->SetFont('Arial','B',11);
$pdf->Cell(50,8,'Fatura Nº:',1);
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,$f['fatura_id'],1,1);

$pdf->SetFont('Arial','B',11);
$pdf->Cell(50,8,'Valor:',1);
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,number_format($f['valor'],2,",",".")." Kz",1,1);

$pdf->SetFont('Arial','B',11);
$pdf->Cell(50,8,'Vencimento:',1);
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,date("d/m/Y", strtotime($f['data_vencimento'])),1,1);

$pdf->SetFont('Arial','B',11);
$pdf->Cell(50,8,'Status:',1);
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,strtoupper($f['status']),1,1);

$pdf->Ln(10);

/* QR CODE */
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,'QR Code de Verificação',0,1);
$pdf->Image($qrFile, 10, $pdf->GetY(), 40);

$pdf->Ln(45);

/* RODAPÉ */
$pdf->SetFont('Arial','I',9);
$pdf->Cell(0,10,'Documento gerado automaticamente pelo sistema Qmanager ISP',0,1,'C');

/* LIMPAR QR TEMP */
@unlink($qrFile);

/* SAÍDA */
$pdf->Output("I", "fatura_{$id}.pdf");
