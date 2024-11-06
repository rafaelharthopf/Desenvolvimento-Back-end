<?php
session_start();
include 'db.php';
include 'header.php';

function buscarUsuarios($pdo) {
    $stmt = $pdo->query("SELECT id, username, nome_completo, cargo, cpf_cnpj, numero_oab FROM usuarios");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$usuarios = buscarUsuarios($pdo);
$usuario = null; // Inicialize a variável como null

// Se o ID do usuário for enviado via GET, carregar o formulário de edição
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $idUsuario = $_GET['edit'];
    $username = $_POST['username'];
    $nomeCompleto = $_POST['nome_completo'];
    $cargo = $_POST['cargo'];
    $cpfCnpj = $_POST['cpf_cnpj'];
    $numeroOAB = $_POST['numero_oab'];

    $stmt = $pdo->prepare('UPDATE usuarios SET username = ?, nome_completo = ?, cargo = ?, cpf_cnpj = ?, numero_oab = ? WHERE id = ?');
    $stmt->execute([$username, $nomeCompleto, $cargo, $cpfCnpj, $numeroOAB, $idUsuario]);

    if ($stmt->rowCount() > 0) {
        echo '<div class="alert alert-success mt-3">Usuário atualizado com sucesso!</div>';
        $usuarios = buscarUsuarios($pdo); // Atualiza a lista de usuários após a edição
        $usuario = null; // Limpa a variável após a atualização para não exibir o formulário
    } else {
        echo '<div class="alert alert-danger mt-3">Erro ao atualizar o usuário.</div>';
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['change_password'])) {
    $idUsuario = $_GET['change_password'];
    $novaSenha = $_POST['nova_senha'];
    $confirmarSenha = $_POST['confirmar_senha'];

    if ($novaSenha !== $confirmarSenha) {
        echo '<div class="alert alert-danger mt-3">Erro: As senhas não coincidem.</div>';
    } else {
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $stmt->execute([$senhaHash, $idUsuario]);

        if ($stmt->rowCount() > 0) {
            echo '<div class="alert alert-success mt-3">Senha alterada com sucesso!</div>';
        } else {
            echo '<div class="alert alert-danger mt-3">Erro ao alterar a senha.</div>';
        }
    }
}
if (isset($_GET['delete'])) {
    $idUsuario = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$idUsuario]);

    if ($stmt->rowCount() > 0) {
        echo '<div class="alert alert-success mt-3">Usuário excluído com sucesso!</div>';
        $usuarios = buscarUsuarios($pdo);
    } else {
        echo '<div class="alert alert-danger mt-3">Erro ao excluir o usuário.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 900px;
        }
        .table th, .table td {
            text-align: center;
        }
        .btn-warning {
            width: 100%;
        }
        .form-control, .btn {
            border-radius: 0.375rem;
        }
        .form-label {
            font-weight: bold;
        }
        .alert {
            font-size: 1rem;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Lista de Usuários</h2>
    
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Nome de Usuário</th>
                <th>Nome Completo</th>
                <th>Cargo</th>
                <th>CPF/CNPJ</th>
                <th>Número da OAB</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuarioRow): ?>
                <tr>
                    <td><?= htmlspecialchars($usuarioRow['username']) ?></td>
                    <td><?= htmlspecialchars($usuarioRow['nome_completo']) ?></td>
                    <td><?= htmlspecialchars($usuarioRow['cargo']) ?></td>
                    <td><?= htmlspecialchars($usuarioRow['cpf_cnpj']) ?></td>
                    <td><?= htmlspecialchars($usuarioRow['numero_oab']) ?></td>
                    <td>
                        <a href="?edit=<?= $usuarioRow['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="?delete=<?= $usuarioRow['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                        <a href="?change_password=<?= $usuarioRow['id'] ?>" class="btn btn-secondary btn-sm">Alterar Senha</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

    <?php if ($usuario): ?>
        <div class="mt-4">
            <h3 class="text-center">Editar Usuário</h3>
            <form method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Nome de Usuário</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($usuario['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nome_completo" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" id="nome_completo" name="nome_completo" value="<?= htmlspecialchars($usuario['nome_completo']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="cargo" class="form-label">Cargo</label>
                    <input type="text" class="form-control" id="cargo" name="cargo" value="<?= htmlspecialchars($usuario['cargo']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="cpf_cnpj" class="form-label">CPF/CNPJ</label>
                    <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj" value="<?= htmlspecialchars($usuario['cpf_cnpj']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="numero_oab" class="form-label">Número da OAB</label>
                    <input type="text" class="form-control" id="numero_oab" name="numero_oab" value="<?= htmlspecialchars($usuario['numero_oab']) ?>" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Atualizar</button>
            </form>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['change_password'])): ?>
        <div class="mt-4">
            <h3 class="text-center">Alterar Senha</h3>
            <form method="post">
                <div class="mb-3">
                    <label for="nova_senha" class="form-label">Nova Senha</label>
                    <input type="password" class="form-control" id="nova_senha" name="nova_senha" required>
                </div>
                <div class="mb-3">
                    <label for="confirmar_senha" class="form-label">Confirme a Nova Senha</label>
                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Alterar Senha</button>
            </form>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

<?php include 'footer.php'; ?>
