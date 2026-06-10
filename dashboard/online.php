<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/database.php";
include "../layout/header.php";
?>

<div class="container mt-4">
<h4 class="fw-bold mb-3">Usuários Online</h4>

<table class="table table-hover shadow bg-white">
<tr>
    <th>Nome</th>
    <th>Email</th>
    <th>Status</th>
    <th>Última Atividade</th>
</tr>

<?php
$sql = "SELECT u.primeiro_nome, 
               u.email, s.last_activity
        FROM user_sessions s
        JOIN users u ON u.user_id = s.user_id
        WHERE s.last_activity > NOW() - INTERVAL 10 MINUTE
";

$res = $qmanager->query($sql);

while ($row = $res->fetch_assoc()):
?>
<tr>
<td><?= $row['primeiro_nome'] ?></td>
<td><?= $row['email'] ?></td>
<td>
<span class="badge bg-success">
<i class="bi bi-circle-fill"></i> Online
</span>
</td>
<td><?= $row['last_activity'] ?></td>
</tr>
<?php endwhile; ?>

</table>
</div>

<?php include "../layout/footer.php"; ?>
