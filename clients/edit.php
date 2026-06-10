<?php
include "../config/database.php";
include "../layout/header.php";

$id = $_GET['id'];
$c = $qmanager->query("SELECT * FROM client WHERE client_id=$id")->fetch_assoc();
?>

<form action="update.php" method="POST" class="card p-4 shadow col-md-6">
<input type="hidden" name="id" value="<?= $id ?>">
<input name="primeiro_nome" value="<?= $c['primeiro_nome'] ?>" class="form-control mb-2">
<input name="ultimo_nome" value="<?= $c['ultimo_nome'] ?>" class="form-control mb-2">
<input name="telefone" value="<?= $c['telefone'] ?>" class="form-control mb-3">
<button class="btn btn-warning">Atualizar</button>
</form>

<?php include "../layout/footer.php"; ?>
