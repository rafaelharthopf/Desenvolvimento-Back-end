<?php
session_start();
include 'db.php'; 
include 'header.php';

function buscarTodosClientes($pdo) {
    $stmt = $pdo->query("SELECT id, nome, cpf_cnpj FROM clientes");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$clientes = buscarTodosClientes($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = cadastrarProcesso($pdo, $_POST);
}

function cadastrarProcesso($pdo, $dadosProcesso) {
    if (empty($dadosProcesso['tipo']) || empty($dadosProcesso['numero']) || empty($dadosProcesso['autor']) || empty($dadosProcesso['reu']) || empty($dadosProcesso['cliente_id'])) {
        return "Erro: Tipo, Número, Autor, Réu e Cliente são campos obrigatórios.";
    }

    $stmt = $pdo->prepare('INSERT INTO processos (tipo, numero, autor, reu, data_abertura, descricao, status, advogado_responsavel, vara, prazo, cliente_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    if ($stmt->execute([
        $dadosProcesso['tipo'],
        $dadosProcesso['numero'],
        $dadosProcesso['autor'],
        $dadosProcesso['reu'],
        $dadosProcesso['data_abertura'] ?? null,
        $dadosProcesso['descricao'] ?? null,
        $dadosProcesso['status'] ?? 'Em andamento',
        $dadosProcesso['advogado_responsavel'] ?? null,
        $dadosProcesso['vara'] ?? null,
        $dadosProcesso['prazo'] ?? null,
        $dadosProcesso['cliente_id'] 
    ])) {
        return "Processo cadastrado com sucesso!";
    } else {
        return "Erro ao cadastrar processo.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Processo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa; /* Cor de fundo suave */
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 10px; /* Bordas arredondadas */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Sombra suave */
        }
        h2 {
            color: #007bff; /* Cor do título */
            margin-bottom: 30px;
        }
        .alert {
            border-radius: 10px; /* Bordas arredondadas para alertas */
        }
        .btn-primary {
            background-color: #007bff; /* Azul padrão */
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3; /* Azul escuro ao passar o mouse */
        }
    </style>
    <script>
        async function buscarClientes() {
            const input = document.getElementById('cliente');
            const datalist = document.getElementById('clientes');
            datalist.innerHTML = ''; 

            if (input.value.length < 2) return; 

            const response = await fetch(`cadastrar_processo.php?search=${input.value}`);
            const clientes = await response.json();

            clientes.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.nome; 
                option.dataset.id = cliente.id; 
                option.dataset.cpf = cliente.cpf_cnpj; 
                datalist.appendChild(option);
            });
        }

        function adicionarClienteId() {
            const input = document.getElementById('cliente');
            const datalist = document.getElementById('clientes');
            const selectedOption = datalist.querySelector(`option[value="${input.value}"]`);
            if (selectedOption) {
                const clienteIdInput = document.createElement('input');
                clienteIdInput.type = 'hidden';
                clienteIdInput.name = 'cliente_id';
                clienteIdInput.value = selectedOption.dataset.id; 
                input.form.appendChild(clienteIdInput);
            }
        }
    </script>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h2>Cadastrar Processo</h2>
        <?php if (isset($mensagem)): ?>
            <div class="alert alert-info mt-3"><?= $mensagem ?></div>
        <?php endif; ?>
        <form method="post" onsubmit="adicionarClienteId()">
            <div class="mb-3">
                <label for="cliente" class="form-label">Cliente</label>
                <input list="clientes" class="form-control" id="cliente" name="cliente" oninput="buscarClientes()" required>
                <datalist id="clientes"></datalist>
            </div>
            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo</label>
                <select class="form-select" id="tipo" name="tipo" required>
                    <option value="" disabled selected>Escolha um tipo</option>
                    <option value="civil">Civil</option>
                    <option value="trabalhista">Trabalhista</option>
                    <option value="previdenciario">Previdenciário</option>
                    <option value="tribunal_de_etica">Tribunal de Ética</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="numero" class="form-label">Número</label>
                <input type="text" class="form-control" id="numero" name="numero" required>
            </div>
            <div class="mb-3">
                <label for="autor" class="form-label">Autor</label>
                <input type="text" class="form-control" id="autor" name="autor" required>
            </div>
            <div class="mb-3">
                <label for="reu" class="form-label">Réu</label>
                <input type="text" class="form-control" id="reu" name="reu" required>
            </div>
            <div class="mb-3">
                <label for="data_abertura" class="form-label">Data de Abertura</label>
                <input type="date" class="form-control" id="data_abertura" name="data_abertura">
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao"></textarea>
            </div>
            <div class="mb-3">
                <label for="advogado_responsavel" class="form-label">Advogado Responsável</label>
                <input type="text" class="form-control" id="advogado_responsavel" name="advogado_responsavel">
            </div>
            <div class="mb-3">
                <label for="vara" class="form-label">Vara</label>
                <input type="text" class="form-control" id="vara" name="vara">
            </div>
            <div class="mb-3">
                <label for="prazo" class="form-label">Prazo</label>
                <input type="date" class="form-control" id="prazo" name="prazo">
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
