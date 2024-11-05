<?php
session_start();
if (isset($_SESSION['user_id'])) {
    include 'header.php';
}
include 'db.php';

function criarUsuario($pdo, $username, $senha) {
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        return "Erro: Nome de usuário já existe.";
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO usuarios (username, senha, nome_completo, cpf_cnpj, numero_oab, cargo) VALUES (?, ?, ?, ?, ?, ?)');
    if ($stmt->execute([$username, $senhaHash, $nomeCompleto, $cpfCnpj, $numeroOAB, 'Advogado'])) {
        return "Usuário criado com sucesso!";
    } else {
        return "Erro ao criar o usuário.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $senha = $_POST['senha'];
    $senhaConfirm = $_POST['senha_confirm'];

    if ($senha !== $senhaConfirm) {
        $mensagem = "Erro: As senhas não coincidem.";
    } else {
        $mensagem = criarUsuario($pdo, $username, $senha);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa; 
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 10px; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); 
        }
        h2 {
            color: #007bff; 
            margin-bottom: 30px;
        }
        .alert {
            border-radius: 10px;
        }
        .btn-primary {
            background-color: #007bff; 
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3; 
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h2>Cadastrar Usuário</h2>
        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Nome de Usuário</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <div class="mb-3">
                <label for="nome_completo" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nome_completo" name="nome_completo" required>
            </div>
            <div class="mb-3">
                <label for="cpf_cnpj" class="form-label">CPF/CNPJ</label>
                <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj" required>
            </div>
            <div class="mb-3">
                <label for="numero_oab" class="form-label">Número da OAB</label>
                <input type="text" class="form-control" id="numero_oab" name="numero_oab" required>
            </div>
            <button type="submit" class="btn btn-primary">Criar</button>
        </form>
        <?php if (isset($mensagem)): ?>
            <div class="alert alert-info mt-3"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
