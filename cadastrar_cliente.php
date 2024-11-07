<?php
session_start();
include 'db.php'; 
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'header.php';

require('fpdf/fpdf.php');

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
        return $pdo->lastInsertId();
    } else {
        return "Erro ao cadastrar cliente.";
    }
}

$empresaId = 1;
$empresa = $pdo->prepare('SELECT * FROM configuracoes WHERE id = ?');
$empresa->execute([$empresaId]);
$empresa = $empresa->fetch();

function gerarContratoPDF($dadosCliente, $clienteId) {
    try {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(190, 10, utf8_decode('Contrato de Trabalho e LGPD'), 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(100, 10, utf8_decode('Nome: ' . $dadosCliente['nome']), 0, 1);
        $pdf->Cell(100, 10, utf8_decode('CPF/CNPJ: ' . $dadosCliente['cpf_cnpj']), 0, 1);
        $pdf->Cell(100, 10, utf8_decode('E-mail: ' . $dadosCliente['email']), 0, 1);
        $pdf->Cell(100, 10, utf8_decode('Endereço: ' . $dadosCliente['endereco']), 0, 1);
        $pdf->Cell(100, 10, utf8_decode('Data de Nascimento: ' . $dadosCliente['data_nascimento']), 0, 1);
        $pdf->Ln(10);

        $textoContrato = "Cláusula 1: O cliente está ciente e concorda com o tratamento de seus dados pessoais conforme a Lei Geral de Proteção de Dados (LGPD). Todos os dados fornecidos são tratados de maneira confidencial e não serão divulgados sem o seu consentimento, exceto quando exigido por lei.\n\nCláusula 2: O cliente concorda com as condições do contrato de trabalho, incluindo as responsabilidades e direitos estabelecidos, que serão detalhados posteriormente. O presente contrato entra em vigor a partir da data de assinatura.";
        $pdf->MultiCell(0, 10, utf8_decode($textoContrato));
        $pdf->Ln(10);
        $pdf->Cell(80, 10, '_________________________', 0, 1, 'L');
        $pdf->Cell(80, 10, utf8_decode($dadosCliente['nome']), 0, 1, 'L');
        $pdf->Cell(80, 10, '_________________________', 0, 1, 'L');
        $pdf->Cell(80, 10, utf8_decode($empresa['nome_sistema']), 0, 1, 'L');

        $nomeArquivoPDF = 'contrato_cliente_' . $clienteId . '.pdf';
        $caminhoArquivo = 'uploads/' . $nomeArquivoPDF;
        
        $pdf->Output('F', $caminhoArquivo);
        return $nomeArquivoPDF;
    } catch (Exception $e) {
        return false;
    }
}
$mensagem = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['aceite']) && $_POST['aceite'] == 'on') {
        $clienteId = criarCliente($pdo, $_POST);
        
        if (is_numeric($clienteId)) {
            $nomeArquivoPDF = gerarContratoPDF($_POST, $clienteId);
            if ($nomeArquivoPDF) {
                $mensagem = "Cliente cadastrado com sucesso! <a href='uploads/{$nomeArquivoPDF}' download>Baixar Contrato</a>";
            } else {
                $mensagem = "<div class='alert alert-danger'>Erro ao gerar o contrato em PDF.</div>";
            }
        } else {
            $mensagem = "<div class='alert alert-danger'>{$clienteId}</div>";
        }
    } else {
        $mensagem = "<div class='alert alert-danger'>Você deve aceitar os termos antes de continuar.</div>";
    }
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
        <?php if ($mensagem): ?>
            <div class="alert alert-info mt-3"><?= $mensagem ?></div>
        <?php endif; ?>
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

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="on" id="aceite" name="aceite" required>
                <label class="form-check-label" for="aceite">
                    Eu aceito os termos da LGPD e do contrato.
                </label>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
</div>
</body>
</html>
<?php include 'footer.php'; ?>
