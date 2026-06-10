
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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

<div class="container-fluid">

<!-- CABEÇALHO -->
<div class="d-flex justify-content-between align-items-center mb-4">
<?php include "../layout/header.php";?>
    <div>
        
        <h3 class="fw-bold mb-0">Painel do Administrador</h3>
        <small class="text-muted">
        <a href="perfil.php"><i class="bi bi-person-circle"></i>    <?= $nome ?> • <?= ucfirst($role) ?>  </a>  • Município: <?= $municipio_admin ?>
        </small>
    </div>
    <div class="text-end">
        <span class="badge bg-success">
            <a href="online.php" class="text-white text-decoration-none">Ver</a>
            <i class="bi bi-circle-fill"></i> Online
        </span><br>
        <small><?= $hora ?></small>
    </div>
</div>

<!-- CARDS -->
<div class="row g-3 mb-4" title="Clique para visualizar">

<?php
$cards = [
 ["Clientes",$clientes,"primary","../clients/index.php"],
 ["Funcionários",$usuarios,"info","../users/index.php"],
 ["Planos",$planos,"success","../plans/index.php"],
 ["Assinaturas",$assinaturas,"warning","../subscriptions/index.php"],
 ["Faturas Pendentes",$faturas_pend,"danger","../billing/index.php"]
];
foreach ($cards as $c):
?>
<div class="col-md-3 col-sm-6">
<a href="<?= $c[3] ?>" class="text-decoration-none">
<div class="card shadow text-center p-3 h-100">
<h4 class="text-<?= $c[2] ?>"><?= $c[1] ?></h4>
<span><?= $c[0] ?></span>
</div>
</a>
</div>
<?php endforeach; ?>
</div>

<!-- AÇÕES -->
<div class="row g-1 mb-4 justify-content-center">
<a class="col-md-2 btn btn-primary me-3" href="../clients/create.php"><i class="bi bi-person-fill-check"></i> Cliente</a>
<a class="col-md-2 btn btn-info me-3 " href="../users/create.php"><i class="bi bi-person-fill-gear"></i> Funcionário</a>
<a class="col-md-2 btn btn-success me-3" href="../plans/create.php"><i class="bi bi-file-earmark-plus"></i> Plano</a>
<a class="col-md-2 btn btn-warning me-3" href="../billing/index.php">📄 Faturas</a>
<a class="col-md-2 btn btn-dark me-3" target="_blank" href="../client_portal/index.php"> <i class="bi bi-cloud-check"></i> Portal Cliente</a>
<a class="col-md-1 btn btn-danger" href="../auth/logout.php"><i class="bi bi-x-circle-fill"></i> Sair</a>
</div>

<!-- PESQUISA -->
<div class="card shadow p-4 mb-4">
<h5>Pesquisa Global</h5>
<form method="GET" class="row g-2">
<input class="col-md-10 form-control" name="busca" value="<?= htmlspecialchars($busca) ?>">
<button class="col-md-2 btn btn-dark"> <i class="bi bi-search"></i>Pesquisar</button>
</form>
</div>

<?php if ($busca !== ''): ?>
<div class="row mb-4">
<div class="col-md-6">

<h6>Clientes</h6>

<table class="table table-sm">
<?php while($c=$result_clientes->fetch_assoc()): ?>
<tr><td><?= $c['primeiro_nome'] ?></td><td><?= $c['email'] ?></td></tr>
<?php endwhile; ?>
</table>
</div>
<div class="col-md-6 me-2">
<h6>Funcionários</h6>
<table class="table table-sm">
<?php while($u=$result_users->fetch_assoc()): ?>
<tr><td><?= $u['primeiro_nome'] ?></td><td><?= $u['email'] ?></td></tr>
<?php endwhile; ?>
</table>
</div>
</div>
<?php endif; ?>

<!-- GRÁFICOS -->
<div class="row g-4 mb-6 w-150 d-flex justify-content-center">

<div class="col-md-5 me-2 card shadow p-2">
<h6>Financeiro (Colunas)</h6>
<canvas id="graficoFinanceiroColuna"></canvas>
</div>

<div class="col-md-6 card shadow p-2">
<h6>Financeiro (Pizza)</h6>
<canvas id="graficoFinanceiroPizza"></canvas>
</div>

<div class="col-md-5 me-2 card shadow p-2">
<h6>Clientes por Município (Pizza)</h6>
<canvas id="graficoMunicipioPizza"></canvas>
</div>

<div class="col-md-6 card shadow p-2">
<h6>Resumo Geral (Colunas)</h6>
<canvas id="graficoResumo"></canvas>
</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Financeiro coluna
new Chart(document.getElementById('graficoFinanceiroColuna'), {
 type:'bar',
 data:{
  labels:['Pagas','Pendentes'],
  datasets:[{
   data:[<?= $faturas_pagas ?>,<?= $faturas_pend ?>]
  }]
 }
});

// Financeiro pizza
new Chart(document.getElementById('graficoFinanceiroPizza'), {
 type:'pie',
 data:{
  labels:['Pagas','Pendentes'],
  datasets:[{
   data:[<?= $faturas_pagas ?>,<?= $faturas_pend ?>]
  }]
 }
});

// Município pizza
new Chart(document.getElementById('graficoMunicipioPizza'), {
 type:'pie',
 data:{
  labels:[<?php while($m=$rel_municipio->fetch_assoc()){ echo "'".$m['municipio']."',"; } ?>],
  datasets:[{
   data:[<?php mysqli_data_seek($rel_municipio,0); while($m=$rel_municipio->fetch_assoc()){ echo $m['total'].","; } ?>]
  }]
 }
});

// Resumo geral
new Chart(document.getElementById('graficoResumo'), {
 type:'bar',
 data:{
  labels:['Clientes','Funcionários','Planos','Assinaturas'],
  datasets:[{
   data:[<?= $clientes ?>,<?= $usuarios ?>,<?= $planos ?>,<?= $assinaturas ?>]
  }]
 }
});
</script>

<?php include "../layout/footer.php"; ?>
