<?php
session_start();
include "../config/database.php";

$cid = $_SESSION['client']['client_id'];

$faturas = $qmanager->query(
"SELECT * FROM faturas WHERE client_id=$cid"
);
?>

<h4>Minhas Faturas</h4>

<table class="table">
<tr><th>Valor</th><th>Status</th></tr>
<?php while($f=$faturas->fetch_assoc()): ?>
<tr>
<td><?= $f['valor'] ?></td>
<td><?= $f['status'] ?></td>
</tr>
<?php endwhile; ?>
</table>
