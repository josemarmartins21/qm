<?php
session_start();

/* Se não estiver logado, manda para login */
if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit;
}

include "layout/header.php";
?>
 <meta name="viewport" content="width=device-width, initial-scale=1.0">

<div class="row mb-4">
    <div class="col-12">
        <div class="p-4 rounded shadow bg-primary text-white">
            <h2 class="fw-bold mb-1">QManager ISP</h2>
            <p class="mb-0">
                Sistema completo de gestão de provedores de internet
            </p>
        </div>
    </div>
</div>

<div class="row g-4">

    <!-- CLIENTES -->
    <div class="col-12 col-md-6 col-lg-4">
        <a href="clients/index.php" class="text-decoration-none">
            <div class="card shadow h-100 border-0 hover-card">
                <div class="card-body text-center">
                    <i class="bi bi-people-fill display-4 text-primary"></i>
                    <h5 class="mt-3 fw-bold">Clientes</h5>
                    <p class="text-muted">Gerir clientes e informações</p>
                </div>
            </div>
        </a>
    </div>

    <!-- PLANOS -->
    <div class="col-12 col-md-6 col-lg-4">
        <a href="plans/index.php" class="text-decoration-none">
            <div class="card shadow h-100 border-0 hover-card">
                <div class="card-body text-center">
                    <i class="bi bi-wifi display-4 text-success"></i>
                    <h5 class="mt-3 fw-bold">Planos</h5>
                    <p class="text-muted">Criar e gerenciar planos</p>
                </div>
            </div>
        </a>
    </div>

    <!-- ASSINATURAS -->
    <div class="col-12 col-md-6 col-lg-4">
        <a href="subscriptions/index.php" class="text-decoration-none">
            <div class="card shadow h-100 border-0 hover-card">
                <div class="card-body text-center">
                    <i class="bi bi-arrow-repeat display-4 text-warning"></i>
                    <h5 class="mt-3 fw-bold">Assinaturas</h5>
                    <p class="text-muted">Clientes e planos ativos</p>
                </div>
            </div>
        </a>
    </div>

    <!-- FATURAS (FUTURO / JÁ PREPARADO) -->
    <div class="col-12 col-md-6 col-lg-4">
        <a href="#" class="text-decoration-none">
            <div class="card shadow h-100 border-0 hover-card">
                <div class="card-body text-center">
                    <i class="bi bi-receipt display-4 text-danger"></i>
                    <h5 class="mt-3 fw-bold">Faturas</h5>
                    <p class="text-muted">Pagamentos e cobranças</p>
                    <span class="badge bg-secondary">Em breve</span>
                </div>
            </div>
        </a>
    </div>

    <!-- USUÁRIOS -->
    <div class="col-12 col-md-6 col-lg-4">
        <a href="users/index.php" class="text-decoration-none">
            <div class="card shadow h-100 border-0 hover-card">
                <div class="card-body text-center">
                    <i class="bi bi-person-badge-fill display-4 text-info"></i>
                    <h5 class="mt-3 fw-bold">Usuários</h5>
                    <p class="text-muted">Admins e operadores</p>
                </div>
            </div>
        </a>
    </div>

    <!-- CATEGORIAS -->
    <div class="col-12 col-md-6 col-lg-4">
        <a href="categories/index.php" class="text-decoration-none">
            <div class="card shadow h-100 border-0 hover-card">
                <div class="card-body text-center">
                    <i class="bi bi-tags-fill display-4 text-secondary"></i>
                    <h5 class="mt-3 fw-bold">Categorias</h5>
                    <p class="text-muted">Categorias de planos</p>
                </div>
            </div>
        </a>
    </div>

</div>

<?php include "layout/footer.php"; ?>
