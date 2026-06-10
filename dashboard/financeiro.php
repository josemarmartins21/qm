<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/database.php";

$user = $_SESSION['user'];

if (!in_array($user['role'], ['admin','financeiro'])) {
    header("Location: index.php");
    exit;
}

$nome = $user['primeiro_nome']." ".$user['ultimo_nome'];
$hora = date("d/m/Y H:i");

function total($sql, $qmanager) {
    $r = $qmanager->query($sql);
    return $r ? $r->fetch_assoc()['t'] : 0;
}

$faturas_total   = total("SELECT COUNT(*) t FROM faturas", $qmanager);
$faturas_pagas   = total("SELECT COUNT(*) t FROM faturas WHERE status='paga'", $qmanager);
$faturas_pend    = total("SELECT COUNT(*) t FROM faturas WHERE status!='paga'", $qmanager);
$clientes        = total("SELECT COUNT(*) t FROM client", $qmanager);

include "../layout/header.php";
?>

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold">Painel Financeiro</h3>
        <small class="text-muted"><?= $nome ?> • Financeiro</small>
    </div>
    <div class="text-end">
        <span class="badge bg-success">
            <i class="bi bi-circle-fill"></i> Online
        </span><br>
        <small><?= $hora ?></small>
    </div>
</div>

<!-- RESUMO -->
<div class="row g-3 mb-4">
<div class="col-md-3">
<div class="card shadow p-3 text-center">
<h4><?= $faturas_total ?></h4>
<span>Total de Faturas</span>
</div>
</div>

<div class="col-md-3">
<div class="card shadow p-3 text-center text-success">
<h4><?= $faturas_pagas ?></h4>
<span>Pagas</span>
</div>
</div>

<div class="col-md-3">
<div class="card shadow p-3 text-center text-danger">
<h4><?= $faturas_pend ?></h4>
<span>Pendentes</span>
</div>
</div>

<div class="col-md-3">
<div class="card shadow p-3 text-center">
<h4><?= $clientes ?></h4>
<span>Clientes</span>
</div>
</div>
</div>

<!-- AÇÕES -->
<div class="row g-3 mb-4">
<div class="col-md-3">
<a href="../billing/index.php" class="btn btn-warning w-100">📄 Gerir Faturas</a>
</div>
<div class="col-md-3">
<a href="../payments/index.php" class="btn btn-success w-100">💰 Pagamentos</a>
</div>
<div class="col-md-3">
<a href="../clients/index.php" class="btn btn-outline-primary w-100">👥 Ver Clientes</a>
</div>
<div class="col-md-3">
<a href="index.php" class="btn btn-dark w-100">⬅ Dashboard</a>
</div>
</div>

<!-- GRÁFICO -->
<div class="card shadow p-4">
<h5 class="fw-bold mb-3">Resumo Financeiro</h5>
<canvas id="grafFinanceiro"></canvas>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('grafFinanceiro'),{
    type:'doughnut',
    data:{
    labels:['Pagas','Pendentes'],
    datasets:[{
        data:[<?= $faturas_pagas ?>,<?= $faturas_pend ?>],
        backgroundColor:['#198754','#dc3545']
    }]
    }
    });
</script>

<?php include "../layout/footer.php"; ?>
