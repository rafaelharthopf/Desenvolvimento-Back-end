<?php 
session_start();
include 'db.php'; 

if (isset($_SESSION['empresa_id'])) {
    $empresaId = $_SESSION['empresa_id'];
    $stmt = $pdo->prepare("SELECT nome_empresa FROM configuracoes WHERE id = ?");
    $stmt->execute([$empresaId]);
    $configuracao = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($configuracao) {
        $nomeEmpresa = htmlspecialchars($configuracao['nome_empresa']);
    } else {
        $nomeEmpresa = 'Nome da Empresa Não Encontrado';
    }
} else {
    $nomeEmpresa = 'Sistema Advocacia';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($title) ? $nomeEmpresa : 'Sistema Advocacia'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php"><?= $nomeEmpresa ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cadastrar_usuario.php">Cadastrar Usuário</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="usuarios.php">Usuários</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cadastrar_cliente.php">Cadastrar Cliente</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cadastrar_processo.php">Cadastrar Processo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ver_lembretes.php">Lembretes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Sair</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="configuracao.php">Configuração</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
