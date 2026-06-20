<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - QManager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

<form action="process_login.php" method="POST" class="card p-4 shadow" style="width: 350px;">
   <img src="../assets/logo.jpg" alt="" srcset="" style="width:200px; margin: auto; display: block; margin-bottom:25px;">
    <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
    <input type="password" name="password" class="form-control mb-3" placeholder="Senha" required>

    <button class="btn btn-primary w-100">Entrar</button>

    <br>
<a href="../client_portal/index.php" style="text-decoration: none; color: blue; font-weight: bold; ">Acessar Portal de Cliente</a>
</form>


</body>
</html>
