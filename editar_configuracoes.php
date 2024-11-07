<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'db.php';
include 'header.php';

$empresaId = 1;
if ($empresaId == 1) {
    $stmt = $pdo->prepare("SELECT * FROM configuracoes WHERE id = ?");
    $stmt->execute([$empresaId]);
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$empresa) {
        die("Empresa não encontrada.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('UPDATE configuracoes SET nome_sistema = ?, endereco_sistema = ?, email = ?, telefone = ?, dias_prazo = ? WHERE id = ?');
    if ($stmt->execute([
        $_POST['nome_sistema'],
        $_POST['endereco_sistema'],
        $_POST['email'],
        $_POST['telefone'],
        $_POST['dias_prazo'],
        $empresaId
    ])) {
        $mensagem = "Empresa atualizada com sucesso!";
        
        $stmt = $pdo->prepare("SELECT * FROM configuracoes WHERE id = ?");
        $stmt->execute([$empresaId]);
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $mensagem = "Erro ao atualizar a empresa.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Empresa</title>
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
        <h2>Editar Empresa</h2>
        <?php if (isset($mensagem)): ?>
            <div class="alert alert-info mt-3"><?= htmlspecialchars($mensagem) ?></div> 
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="nome_empresa" class="form-label">Nome Empresa</label>
                <input type="text" class="form-control" id="nome_sistema" name="nome_sistema" value="<?= htmlspecialchars($empresa['nome_sistema']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="endereco_empresa" class="form-label">Endereço Empresa</label>
                <input type="text" class="form-control" id="endereco_sistema" name="endereco_sistema" value="<?= htmlspecialchars($empresa['endereco_sistema']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($empresa['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($empresa['telefone']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="telefone" class="form-label">Dias para Ver Meus Lembretes</label>
                <input type="text" class="form-control" id="dias_prazo" name="dias_prazo" value="<?= htmlspecialchars($empresa['dias_prazo']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>
</div>
</body>
</html>
<?php include 'footer.php'; ?>
