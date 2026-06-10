<?php
session_start();
/*if (isset($_SESSION['client'])) {
    header("Location: index.php");
    exit;
}*/
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Portal do Cliente | Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow p-4" style="max-width:400px;width:100%;">
    <h4 class="fw-bold text-center mb-3">
        <i class="bi bi-person-circle"></i> Portal do Cliente
    </h4>

    <?php if(isset($_GET['erro'])): ?>
        <div class="alert alert-danger">Email ou senha inválidos</div>
    <?php endif; ?>

    <form method="POST" action="process_login.php">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Senha</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button class="btn btn-primary w-100">
            Entrar
        </button>
    </form>

    <div class="text-center mt-3">
        <small class="text-muted">
            Acesso exclusivo para clientes
        </small>
    </div>
</div>

</body>
</html>
