<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/database.php";

$user = $_SESSION['user'];

if (!in_array($user['role'], ['admin','tecnico'])) {
    header("Location: index.php");
    exit;
}

$nome = $user['primeiro_nome']." ".$user['ultimo_nome'];
$hora = date("d/m/Y H:i");

function total($sql, $qmanager) {
    $r = $qmanager->query($sql);
    return $r ? $r->fetch_assoc()['t'] : 0;
}

$clientes    = total("SELECT COUNT(*) t FROM client", $qmanager);
$planos      = total("SELECT COUNT(*) t FROM planos", $qmanager);
$assinaturas = total("SELECT COUNT(*) t FROM client_has_plan", $qmanager);

include "../layout/header.php";
?>

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold">Painel Técnico</h3>
        <small class="text-muted"><?= $nome ?> • Técnico</small>
    </div>
    <div class="text-end">
        <span class="badge bg-success">
            <i class="bi bi-circle-fill"></i> Online
        </span><br>
        <small><?= $hora ?></small>
    </div>
</div>


<div class="row g-3 mb-4">

<div class="col-md-4">
<div class="card shadow p-3 text-center">
<h4><?= $clientes ?></h4>
<span>Clientes</span>
</div>
</div>

<div class="col-md-4">
<div class="card shadow p-3 text-center">
<h4><?= $planos ?></h4>
<span>Planos</span>
</div>
</div>

<div class="col-md-4">
<div class="card shadow p-3 text-center">
<h4><?= $assinaturas ?></h4>
<span>Assinaturas</span>
</div>
</div>

</div>


<div class="row g-3 mb-4">
<div class="col-md-3">
<a href="../clients/index.php" class="btn btn-primary w-100">👥 Clientes</a>
</div>
<div class="col-md-3">
<a href="../plans/index.php" class="btn btn-success w-100">📡 Planos</a>
</div>
<div class="col-md-3">
<a href="../subscriptions/index.php" class="btn btn-warning w-100">🔄 Assinaturas</a>
</div>
<div class="col-md-3">
<a href="index.php" class="btn btn-dark w-100">⬅ Dashboard</a>
</div>
</div>

</div>

<?php include "../layout/footer.php"; ?>
