<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/database.php";


$clientes = $qmanager->query("
    SELECT client_id, primeiro_nome, ultimo_nome, email
    FROM client
    ORDER BY primeiro_nome
");


$planos = $qmanager->query("
    SELECT plan_id, nome, preco
    FROM planos
    ORDER BY nome
");

/* ======================
   PROCESSAR FORMULÁRIO
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 🔹 NOMES EXATOS DO FORMULÁRIO
    $client_id = (int) ($_POST['client_id'] ?? 0);
    $plan_id   = (int) ($_POST['plan_id'] ?? 0);

    // 🔹 DADOS DA FATURA
    $valor           = (float) ($_POST['valor'] ?? 0);
    $data_vencimento = $_POST['data_vencimento'] ?? date("Y-m-d", strtotime("+30 days"));

    // 🔹 DATA DA ASSINATURA
    $created_at = date("Y-m-d H:i:s");

    if ($client_id <= 0 || $plan_id <= 0) {
        die("Erro: selecione cliente e plano.");
    }

    /* ======================
       CRIAR ASSINATURA
       (APENAS 3 CAMPOS)
    ====================== */
    $stmtAss = $qmanager->prepare("
        INSERT INTO client_has_plan
        (client_client_id, planos_plan_id, created_at)
        VALUES (?, ?, ?)
    ");

    if (!$stmtAss) {
        die("Erro ao criar assinatura: " . $qmanager->error);
    }

    $stmtAss->bind_param(
        "iis",
        $client_id,
        $plan_id,
        $created_at
    );

    $stmtAss->execute();

    /* ======================
       CRIAR FATURA
    ====================== */
    $stmtFat = $qmanager->prepare("
        INSERT INTO faturas
        (client_id, valor, status, data_vencimento)
        VALUES (?, ?, 'pendente', ?)
    ");

    if (!$stmtFat) {
        die("Erro ao criar fatura: " . $qmanager->error);
    }

    $stmtFat->bind_param(
        "ids",
        $client_id,
        $valor,
        $data_vencimento
    );

    $stmtFat->execute();

    header("Location: index.php?ok=1");
    exit;
}

include "../layout/header.php";
?>

<div class="container mt-4">

<h4 class="fw-bold mb-3">
<i class="bi bi-repeat"></i> Criar Assinatura
</h4>

<form method="POST" class="card shadow p-4">

<div class="row g-3">

<!-- CLIENTE -->
<div class="col-md-6">
<label class="form-label fw-bold">Cliente</label>
<select name="client_id" class="form-select" required>
<option value="">Selecione o cliente</option>
<?php while ($c = $clientes->fetch_assoc()): ?>
<option value="<?= $c['client_id'] ?>">
<?= htmlspecialchars($c['primeiro_nome'].' '.$c['ultimo_nome'].' - '.$c['email']) ?>
</option>
<?php endwhile; ?>
</select>
</div>

<!-- PLANO -->
<div class="col-md-6">
<label class="form-label fw-bold">Plano</label>
<select name="plan_id" class="form-select" required>
<option value="">Selecione o plano</option>
<?php while ($p = $planos->fetch_assoc()): ?>
<option value="<?= $p['plan_id'] ?>" data-preco="<?= $p['preco'] ?>">
<?= htmlspecialchars($p['nome']) ?> (<?= number_format($p['preco'],2,",",".") ?>)
</option>
<?php endwhile; ?>
</select>
</div>

<!-- DATA VENCIMENTO -->
<div class="col-md-6">
<label class="form-label fw-bold">Data de Vencimento</label>
<input type="date" name="data_vencimento" class="form-control"
value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
</div>

<!-- VALOR -->
<div class="col-md-6">
<label class="form-label fw-bold">Valor da Fatura</label>
<input type="number" step="0.01" name="valor" class="form-control" required>
</div>

</div>

<hr>

<div class="d-flex justify-content-between">
<a href="../dashboard/index.php" class="btn btn-secondary">
← Voltar
</a>

<button class="btn btn-success">
<i class="bi bi-check-circle"></i> Criar Assinatura
</button>
</div>

</form>

</div>

<script>
document.querySelector('[name="plan_id"]').addEventListener('change', function(){
    let preco = this.options[this.selectedIndex].dataset.preco;
    if (preco) {
        document.querySelector('[name="valor"]').value = preco;
    }
});
</script>

<?php include "../layout/footer.php"; ?>
