<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

function criarEmpresa($pdo, $dadosEmpresa) {
    $stmt = $pdo->prepare('INSERT INTO configuracoes (nome_sistema, endereco_sistema, email, telefone) VALUES (?, ?, ?, ?)');
    if ($stmt->execute([
        $dadosEmpresa['nome_sistema'],
        $dadosEmpresa['endereco_sistema'],
        $dadosEmpresa['email'],
        $dadosEmpresa['telefone']
    ])) {
        return "Empresa cadastrada com sucesso!";
    } else {
        return 'Erro ao cadastrar a empresa.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = criarEmpresa($pdo, $_POST);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Empresa</title>
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
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h2>Cadastrar Empresa</h2>
        <?php if (isset($mensagem)): ?>
            <div class="alert alert-info mt-3"><?= htmlspecialchars($mensagem) ?></div> 
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="nome_empresa" class="form-label">Nome Empresa</label>
                <input type="text" class="form-control" id="nome_sistema" name="nome_sistema" required>
            </div>
            <div class="mb-3">
                <label for="endereco_empresa" class="form-label">Endereço Empresa</label>
                <input type="text" class="form-control" id="endereco_sistema" name="endereco_sistema" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="telefone" name="telefone" required>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
            <a class="button" href="editar_configuracoes.php">Editar Empresa</a>
        </form>
    </div>
</div>
</body>
</html>
<?php include 'footer.php'; ?>
