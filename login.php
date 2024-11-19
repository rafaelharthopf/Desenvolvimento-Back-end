<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $erro = "Usuário ou senha incorretos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema Advocacia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f4f7fa; 
            font-family: 'Arial', sans-serif;
        }
        .container {
            flex: 1;
            max-width: 800px; /
            margin: auto;
            padding: 2rem;
            background-color: #ffffff; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
        }
        h2 {
            margin-bottom: 1.5rem;
            color: #007bff; 
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            width: 150px; 
            text-align: center;
        }
        .btn-primary:hover {
            background-color: #0056b3; 
            width: 150px; 
            text-align: center;
        }
        footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 1rem 0;
        }
        footer .text-center {
            color: #6c757d; 
        }
        .alert-danger {
            margin-top: 1rem;
        }
        .btn-secondary {
            background-color: #6c757d; 
            border: none;
            width: 200px; 
            text-align: center;
        }
        .btn-secondary:hover {
            background-color: #5a6268; 
            width: 200px; 
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container mt-5">
        <div class="d-flex justify-content-center">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="btn btn-primary btn-sm d-flex align-items-center w-auto">
                    <i class="fas fa-tachometer-alt me-2"></i> Ir para o Dashboard
                </a>
            <?php else: ?>
                <a href="index.php" class="btn btn-secondary btn-sm d-flex align-items-center w-auto">
                     Página Inicial
                </a>
            <?php endif; ?>
        </div>
    <h2 class="text-center">Login</h2>
    <form method="post" class="mt-3">
    <?php if (isset($erro)) echo "<div class='alert alert-danger mt-2'>$erro</div>"; ?>
        <div class="mb-3">
            <label for="username" class="form-label">Usuário</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" required>
        </div>
        <div class="btn mb-3 w-100">
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
            <hr>
            <a href="cadastrar_usuario.php" class="btn btn-primary mb-3 w-100">Cadastrar Usuário</a>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>