<?php
session_start();
include "../config/database.php";


if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$session_user_id = $_SESSION['user'];
$session_is_adm = $_SESSION['is_adm'] ?? 0;

if ($session_is_adm == 1 && isset($_GET['user_id'])) {
    $view_user_id = intval($_GET['user_id']);
} else {
    $view_user_id = $session_user_id;
}


$pode_editar = ($session_is_adm == 1);


$dad = $qmanager->prepare("SELECT user_id, primeiro_nome, ultimo_nome, email, is_adm FROM users WHERE user_id = ?");
$dad->bind_param("i", $view_user_id);
$dad->execute();
$result = $dad->get_result();

if ($result->num_rows === 0) {
    die("Usuário não encontrado.");
}

$user = $result->fetch_assoc();
$dad->close();


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $pode_editar) {
    $primeiro_nome = trim($_POST['primeiro_nome']);
    $ultimo_nome = trim($_POST['ultimo_nome']);
    $email = trim($_POST['email']);
    $novo_is_adm = intval($_POST['is_adm']);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido.";
    } else {
        $check = $qmanager->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $check->bind_param("si", $email, $view_user_id);
        $check->execute();
        $check_result = $check->get_result();
        
        if ($check_result->num_rows > 0) {
            $erro = "Este email já está em uso por outro usuário.";
        } else {
            $update = $qmanager->prepare("UPDATE users SET primeiro_nome = ?, ultimo_nome = ?, email = ?, is_adm = ? WHERE user_id = ?");
            $update->bind_param("sssii", $primeiro_nome, $ultimo_nome, $email, $novo_is_adm, $view_user_id);
            
            if ($update->execute()) {
                $sucesso = "Dados atualizados com sucesso!";
                $user['primeiro_nome'] = $primeiro_nome;
                $user['ultimo_nome'] = $ultimo_nome;
                $user['email'] = $email;
                $user['is_adm'] = $novo_is_adm;
            } else {
                $erro = "Erro ao atualizar dados: " . $conn->error;
            }
            $update->close();
        }
        $check->close();
    }
}


$stats = [];
$da = $qmanager->prepare("SELECT COUNT(*) as total_assinaturas FROM historico_de_assinaturas WHERE client_client_id = ?");
$da->bind_param("i", $view_user_id);
$da->execute();
$result = $da->get_result();
$stats['assinaturas'] = $result->fetch_assoc()['total_assinaturas'];
$da->close();

$da = $qmanager->prepare("SELECT COUNT(*) as total_sessoes FROM user_sessions WHERE user_id = ?");
$da->bind_param("i", $view_user_id);
$da->execute();
$result = $da->get_result();
$stats['sessoes'] = $result->fetch_assoc()['total_sessoes'];
$da->close();


$is_adm = (int)$user['is_adm'];
if ($is_adm == 1) {
    $tipo_usuario = 'Administrador';
} elseif ($is_adm == 2) {
    $tipo_usuario = 'Tecnico';
} elseif ($is_adm == 0) {
    $tipo_usuario = 'Usuário';
} else {
    $tipo_usuario = 'Usuário';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 4px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(180deg, #1a1d29 0%, #212529 100%); color: white; padding: 30px; text-align: center; position: relative; }
        .header h1 { font-size: 1.8rem; margin-bottom: 5px; }
        .tipo-usuario { display: inline-block; background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 4px; font-size: 0.85rem; margin-top: 10px; }
        .modo-visualizacao { position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 4px; font-size: 0.8rem; }
        .content { padding: 30px; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: 500; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 0.9rem; }
        .form-group input, .form-group select { width: 100%; padding: 12px 15px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 1rem; transition: border-color 0.3s; background: white; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #667eea; }
        .form-group input:disabled, .form-group select:disabled { background: #e9ecef; cursor: not-allowed; color: #6c757d; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: #f8f9fa; padding: 20px; border-radius: 4px; text-align: center; }
        .stat-number { font-size: 1.8rem; font-weight: bold; color: #667eea; }
        .stat-label { font-size: 0.85rem; color: #6c757d; margin-top: 5px; }
        .btn { padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: all 0.3s; text-transform: uppercase; font-size: 0.9rem; text-decoration: none; display: inline-block; }
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #5568d3; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .acoes { display: flex; gap: 10px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6; }
        .info-box { background: #e7f3ff; border-left: 4px solid #667eea; padding: 15px; margin-bottom: 20px; border-radius: 0 6px 6px 0; }
        .info-box p { color: #495057; font-size: 0.9rem; margin: 0; }
        .readonly-field { background: #f8f9fa !important; border-color: #dee2e6 !important; color: #6c757d !important; }
        @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } .header h1 { font-size: 1.4rem; } .modo-visualizacao { position: static; margin-top: 15px; display: inline-block; } }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Perfil do Usuário</h1>
        <span class="tipo-usuario"><?php echo $tipo_usuario; ?></span>
        <?php if (!$pode_editar): ?>
            <div class="modo-visualizacao">Modo Visualização</div>
        <?php else: ?>
            <div class="modo-visualizacao">Modo Edição</div>
        <?php endif; ?>
    </div>
    <div class="content">
        <?php if (isset($sucesso)): ?>
            <div class="alert alert-success"><?php echo $sucesso; ?></div>
        <?php endif; ?>
        <?php if (isset($erro)): ?>
            <div class="alert alert-error"><?php echo $erro; ?></div>
        <?php endif; ?>
        <?php if (!$pode_editar): ?>
            <div class="info-box">
                <p><strong>Modo Visualização:</strong> Você está visualizando seu perfil. Apenas administradores podem editar dados.</p>
            </div>
        <?php endif; ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['assinaturas']; ?></div>
                <div class="stat-label">Assinaturas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['sessoes']; ?></div>
                <div class="stat-label">Sessões Ativas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">#<?php echo $user['user_id']; ?></div>
                <div class="stat-label">ID do Usuário</div>
            </div>
        </div>
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="primeiro_nome">Primeiro Nome</label>
                    <input type="text" id="primeiro_nome" name="primeiro_nome" value="<?php echo htmlspecialchars($user['primeiro_nome']); ?>" <?php echo $pode_editar ? '' : 'disabled class="readonly-field"'; ?> required>
                </div>
                <div class="form-group">
                    <label for="ultimo_nome">Último Nome</label>
                    <input type="text" id="ultimo_nome" name="ultimo_nome" value="<?php echo htmlspecialchars($user['ultimo_nome']); ?>" <?php echo $pode_editar ? '' : 'disabled class="readonly-field"'; ?> required>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" <?php echo $pode_editar ? '' : 'disabled class="readonly-field"'; ?> required>
            </div>
            <?php if ($pode_editar): ?>
                <div class="form-group">
                    <label for="is_adm">Tipo de Usuário</label>
                    <select id="is_adm" name="is_adm" required>
                        <option value="0" <?php echo $user['is_adm'] == 0 ? 'selected' : ''; ?>>Usuário Normal</option>
                        <option value="1" <?php echo $user['is_adm'] == 1 ? 'selected' : ''; ?>>Administrador</option>
                        <option value="2" <?php echo $user['is_adm'] == 2 ? 'selected' : ''; ?>>Técnico/Suporte</option>
                    </select>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label for="tipo_visualizacao">Tipo de Usuário</label>
                    <input type="text" id="tipo_visualizacao" value="<?php echo $tipo_usuario; ?>" disabled class="readonly-field">
                </div>
            <?php endif; ?>
            <div class="acoes">
                <?php if ($pode_editar): ?>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <?php endif; ?>
                <a href="<?php echo $session_is_adm == 1 ? 'index.php' : 'index.php'; ?>" class="btn btn-secondary">Voltar</a>
                <?php if ($session_is_adm == 1 && $view_user_id != $session_user_id): ?>
                    <a href="perfil.php" class="btn btn-secondary">Ver Meu Perfil</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
</body>
</html>