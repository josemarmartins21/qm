<?php
require "../config/database.php";

$id = intval($_GET['id']);

$f = $qmanager->query("
SELECT f.*, c.primeiro_nome, c.email
FROM faturas f
JOIN client c ON c.client_id=f.client_id
WHERE f.fatura_id=$id
")->fetch_assoc();

header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=fatura_$id.pdf");

echo "%PDF-1.4
1 0 obj<<>>endobj
2 0 obj<< /Type /Page /Parent 3 0 R /MediaBox [0 0 300 400]
/Contents 4 0 R /Resources<<>> >>endobj
3 0 obj<< /Type /Pages /Kids [2 0 R] /Count 1 >>endobj
4 0 obj<< /Length 120 >>stream
BT
/F1 12 Tf
50 350 Td (FATURA #$id) Tj
50 330 Td (Cliente: {$f['primeiro_nome']}) Tj
50 310 Td (Valor: {$f['valor']} Kz) Tj
50 290 Td (Vencimento: {$f['data_vencimento']}) Tj
ET
endstream
endobj
xref
0 5
0000000000 65535 f
trailer<< /Root 1 0 R >>
%%EOF";
