<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* 🔑 CONEXÃO COM O BANCO (OBRIGATÓRIO) */
include "../config/database.php";

/* 📊 CONSULTAS */
$clientes = $qmanager->query(
    "SELECT COUNT(*) AS total FROM client"
)->fetch_assoc();

$faturas_pagas = $qmanager->query(
    "SELECT COUNT(*) AS total FROM faturas WHERE status='paga'"
)->fetch_assoc();

$faturas_pend = $qmanager->query(
    "SELECT COUNT(*) AS total FROM faturas WHERE status='pendente'"
)->fetch_assoc();

/* 🎨 LAYOUT */
include "../layout/header.php";
?>

<h3 class="mb-2">Bem-vindo ao QManager</h3>
<p class="text-muted mb-4">Sistema de Gestão de ISP</p>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow p-4 text-center">
            <h5>Clientes</h5>
            <h2 class="fw-bold"><?= $clientes['total'] ?></h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow p-4 text-center text-success">
            <h5>Faturas Pagas</h5>
            <h2 class="fw-bold"><?= $faturas_pagas['total'] ?></h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow p-4 text-center text-warning">
            <h5>Pendentes</h5>
            <h2 class="fw-bold"><?= $faturas_pend['total'] ?></h2>
        </div>
    </div>
</div>

<div class="card shadow p-4">
    <h5 class="mb-3">Resumo de Faturas</h5>
    <canvas id="grafico"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('grafico'), {
    type: 'doughnut',
    data: {
        labels: ['Pagas', 'Pendentes'],
        datasets: [{
            data: [<?= $faturas_pagas['total'] ?>, <?= $faturas_pend['total'] ?>],
            backgroundColor: ['#198754', '#ffc107']
        }]
    }
});
</script>

<?php include "../layout/footer.php"; ?>
