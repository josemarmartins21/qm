<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/database.php";

$admin = $_SESSION['user'];
$nome = $admin['primeiro_nome']." ".$admin['ultimo_nome'];
$municipio_admin = $admin['municipio'] ?? '—';
$role = $admin['role'] ?? 'admin';
$hora = date("d/m/Y H:i");

function total($sql, $qmanager) {
    $r = $qmanager->query($sql);
    return $r ? $r->fetch_assoc()['t'] : 0;
}

$clientes     = total("SELECT COUNT(*) t FROM client", $qmanager);
$usuarios     = total("SELECT COUNT(*) t FROM users", $qmanager);
$planos       = total("SELECT COUNT(*) t FROM planos", $qmanager);
$assinaturas  = total("SELECT COUNT(*) t FROM client_has_plan", $qmanager);
$faturas_pagas= total("SELECT COUNT(*) t FROM faturas WHERE status='paga'", $qmanager);
$faturas_pend = total("SELECT COUNT(*) t FROM faturas WHERE status!='paga'", $qmanager);

$rel_municipio = $qmanager->query("
    SELECT municipio, COUNT(*) total 
    FROM client 
    GROUP BY municipio
");

$busca = $_GET['busca'] ?? '';
$result_clientes = $result_users = null;

if ($busca !== '') {
    $like = "%$busca%";

    $skua = $qmanager->prepare("SELECT primeiro_nome, email FROM client WHERE primeiro_nome LIKE ? OR email LIKE ?");
    $skua->bind_param("ss", $like, $like);
    $skua->execute();
    $result_clientes = $skua->get_result();

    $s2 = $qmanager->prepare("SELECT primeiro_nome, email FROM users WHERE primeiro_nome LIKE ? OR email LIKE ?");
    $s2->bind_param("ss", $like, $like);
    $s2->execute();
    $result_users = $s2->get_result();
}


?>

<div class="container-fluid py-3">
<?php include "../layout/header.php";?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold mb-0">Painel do Administrador</h3>
        <small class="text-muted">
            <a href="perfil.php" class="text-decoration-none"><i class="bi bi-person-circle"></i> <?= $nome ?> • <?= ucfirst($role) ?></a> • Município: <?= $municipio_admin ?>
        </small>
    </div>
    <div class="text-md-end">
        <span class="badge bg-success">
            <a href="online.php" class="text-white text-decoration-none">Ver <i class="bi bi-circle-fill small"></i> Online</a>
        </span><br>
        <small class="text-muted"><?= $hora ?></small>
    </div>
</div>


<div class="row g-3 mb-4">
<?php
$cards = [
 ["Clientes", $clientes, "primary", "../clients/index.php", "bi-people"],
 ["Funcionários", $usuarios, "info", "../users/index.php", "bi-person-gear"],
 ["Planos", $planos, "success", "../plans/index.php", "bi-journal-plus"],
 ["Assinaturas", $assinaturas, "warning", "../subscriptions/index.php", "bi-card-checklist"],
 ["Faturas Pendentes", $faturas_pend, "danger", "../billing/index.php", "bi-exclamation-triangle"]
];
foreach ($cards as $c):
?>
<div class="col-12 col-sm-6 col-lg">
    <a href="<?= $c[3] ?>" class="text-decoration-none">
        <div class="card shadow-sm border-0 h-100 text-center p-3 hover-lift">
            <div class="card-body">
                <i class="bi <?= $c[4] ?> display-6 text-<?= $c[2] ?> mb-2"></i>
                <h4 class="text-<?= $c[2] ?> fw-bold mb-1"><?= $c[1] ?></h4>
                <span class="text-muted small text-uppercase fw-semibold"><?= $c[0] ?></span>
            </div>
        </div>
    </a>
</div>
<?php endforeach; ?>
</div>


<div class="d-flex flex-wrap gap-2 mb-4 justify-content-center">
    <a class="btn btn-primary" href="../clients/create.php"><i class="bi bi-person-fill-check"></i> <span class="d-none d-sm-inline">Cliente</span></a>
    <a class="btn btn-info text-white" href="../users/create.php"><i class="bi bi-person-fill-gear"></i> <span class="d-none d-sm-inline">Funcionário</span></a>
    <a class="btn btn-success" href="../plans/create.php"><i class="bi bi-file-earmark-plus"></i> <span class="d-none d-sm-inline">Plano</span></a>
    <a class="btn btn-warning" href="../billing/index.php"><i class="bi bi-receipt"></i> <span class="d-none d-sm-inline">Faturas</span></a>
    <a class="btn btn-dark" target="_blank" href="../client_portal/index.php"><i class="bi bi-cloud-check"></i> <span class="d-none d-sm-inline">Portal</span></a>
    <a class="btn btn-danger" href="../auth/logout.php"><i class="bi bi-x-circle-fill"></i> <span class="d-none d-sm-inline">Sair</span></a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3"><i class="bi bi-search me-2"></i>Pesquisa Global</h5>
        <form method="GET" class="row g-2">
            <div class="col-12 col-md-10">
                <input class="form-control" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Buscar por nome ou email...">
            </div>
            <div class="col-12 col-md-2 d-grid">
                <button class="btn btn-dark"><i class="bi bi-search me-1"></i>Pesquisar</button>
            </div>
        </form>
    </div>
</div>

<?php if ($busca !== ''): ?>
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-people me-2 text-primary"></i>Clientes</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <tbody>
                            <?php while($c=$result_clientes->fetch_assoc()): ?>
                            <tr><td class="ps-3"><?= $c['primeiro_nome'] ?></td><td class="text-muted small"><?= $c['email'] ?></td></tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-person-gear me-2 text-info"></i>Funcionários</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <tbody>
                            <?php while($u=$result_users->fetch_assoc()): ?>
                            <tr><td class="ps-3"><?= $u['primeiro_nome'] ?></td><td class="text-muted small"><?= $u['email'] ?></td></tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<div class="row g-4 mb-4">

    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>Financeiro (Colunas)</h6>
            </div>
            <div class="card-body">
                <canvas id="graficoFinanceiroColuna" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="mb-0"><i class="bi bi-pie-chart me-2 text-success"></i>Financeiro (Pizza)</h6>
            </div>
            <div class="card-body">
                <canvas id="graficoFinanceiroPizza" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="mb-0"><i class="bi bi-geo-alt me-2 text-warning"></i>Clientes por Município</h6>
            </div>
            <div class="card-body">
                <canvas id="graficoMunicipioPizza" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="mb-0"><i class="bi bi-graph-up me-2 text-info"></i>Resumo Geral</h6>
            </div>
            <div class="card-body">
                <canvas id="graficoResumo" height="250"></canvas>
            </div>
        </div>
    </div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

new Chart(document.getElementById('graficoFinanceiroColuna'), {
 type:'bar',
 data:{
  labels:['Pagas','Pendentes'],
  datasets:[{
   label: 'Faturas',
   data:[<?= $faturas_pagas ?>,<?= $faturas_pend ?>],
   backgroundColor: ['#198754', '#dc3545'],
   borderRadius: 6
  }]
 },
 options: {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } }
 }
});


new Chart(document.getElementById('graficoFinanceiroPizza'), {
 type:'pie',
 data:{
  labels:['Pagas','Pendentes'],
  datasets:[{
   data:[<?= $faturas_pagas ?>,<?= $faturas_pend ?>],
   backgroundColor: ['#198754', '#dc3545']
  }]
 },
 options: {
  responsive: true,
  maintainAspectRatio: false
 }
});

new Chart(document.getElementById('graficoMunicipioPizza'), {
 type:'pie',
 data:{
  labels:[<?php while($m=$rel_municipio->fetch_assoc()){ echo "'".$m['municipio']."',"; } ?>],
  datasets:[{
   data:[<?php mysqli_data_seek($rel_municipio,0); while($m=$rel_municipio->fetch_assoc()){ echo $m['total'].","; } ?>]
  }]
 },
 options: {
  responsive: true,
  maintainAspectRatio: false
 }
});


new Chart(document.getElementById('graficoResumo'), {
 type:'bar',
 data:{
  labels:['Clientes','Funcionários','Planos','Assinaturas'],
  datasets:[{
   label: 'Total',
   data:[<?= $clientes ?>,<?= $usuarios ?>,<?= $planos ?>,<?= $assinaturas ?>],
   backgroundColor: ['#0d6efd', '#0dcaf0', '#198754', '#ffc107'],
   borderRadius: 6
  }]
 },
 options: {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } }
 }
});
</script>

<?php include "../layout/footer.php"; ?>