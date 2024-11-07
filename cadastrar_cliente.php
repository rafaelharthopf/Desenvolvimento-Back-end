<?php
session_start();
include 'db.php'; 
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'header.php';

function criarCliente($pdo, $dadosCliente) {
    if (empty($dadosCliente['cpf_cnpj']) || empty($dadosCliente['nome']) || empty($dadosCliente['email'])) {
        return "Erro: CPF/CNPJ, Nome e E-mail são obrigatórios.";
    }

    $stmt = $pdo->prepare('INSERT INTO clientes (cpf_cnpj, nome, rg_ie, email, endereco, conjugue, nome_mae, data_nascimento, local_nascimento, pasep_pis, numero_beneficio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    if ($stmt->execute([
        $dadosCliente['cpf_cnpj'],
        $dadosCliente['nome'],
        $dadosCliente['rg_ie'] ?? null,
        $dadosCliente['email'],
        $dadosCliente['endereco'] ?? null,
        $dadosCliente['conjugue'] ?? null,
        $dadosCliente['nome_mae'] ?? null,
        $dadosCliente['data_nascimento'] ?? null,
        $dadosCliente['local_nascimento'] ?? null,
        $dadosCliente['pasep_pis'] ?? null,
        $dadosCliente['numero_beneficio'] ?? null
    ])) {
        return "Cliente cadastrado com sucesso!";
    } else {
        return "Erro ao cadastrar cliente.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = criarCliente($pdo, $_POST);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Cliente</title>
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
        <h2>Cadastrar Cliente</h2>
        <form method="post">
            <div class="mb-3">
                <label for="cpf_cnpj" class="form-label">CPF/CNPJ</label>
                <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj" required>
            </div>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="mb-3">
                <label for="rg_ie" class="form-label">RG/IE</label>
                <input type="text" class="form-control" id="rg_ie" name="rg_ie">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço</label>
                <input type="text" class="form-control" id="endereco" name="endereco">
            </div>
            <div class="mb-3">
                <label for="conjugue" class="form-label">Cônjuge</label>
                <input type="text" class="form-control" id="conjugue" name="conjugue">
            </div>
            <div class="mb-3">
                <label for="nome_mae" class="form-label">Nome da Mãe</label>
                <input type="text" class="form-control" id="nome_mae" name="nome_mae">
            </div>
            <div class="mb-3">
                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                <input type="date" class="form-control" id="data_nascimento" name="data_nascimento">
            </div>
            <div class="mb-3">
                <label for="local_nascimento" class="form-label">Local de Nascimento</label>
                <input type="text" class="form-control" id="local_nascimento" name="local_nascimento">
            </div>
            <div class="mb-3">
                <label for="pasep_pis" class="form-label">PASEP/PIS</label>
                <input type="text" class="form-control" id="pasep_pis" name="pasep_pis">
            </div>
            <div class="mb-3">
                <label for="numero_beneficio" class="form-label">Número de Benefício</label>
                <input type="text" class="form-control" id="numero_beneficio" name="numero_beneficio">
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>

        <?php if (isset($mensagem)): ?>
            <div class="alert alert-info mt-3"><?= $mensagem ?></div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
