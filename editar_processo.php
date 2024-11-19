<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';
include 'header.php';

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$processoId = $_GET['id'];
$processo = $pdo->prepare('SELECT * FROM processos WHERE id = ?');
$processo->execute([$processoId]);
$processo = $processo->fetch();

if (!$processo) {
    header('Location: dashboard.php');
    exit;
}

if ($_POST) {
    $dadosProcesso = [
        'numero' => $_POST['numero'],
        'tipo' => $_POST['tipo'],
        'status' => $_POST['status'],
        'autor' => $_POST['autor'],
        'reu' => $_POST['reu'],
        'data_abertura' => $_POST['data_abertura'],
        'prazo' => $_POST['prazo'],
        'descricao' => $_POST['descricao']
    ];

    $stmt = $pdo->prepare('UPDATE processos SET numero = ?, tipo = ?, status = ?, autor = ?, reu = ?, data_abertura = ?, prazo = ?, descricao = ? WHERE id = ?');
    $stmt->execute([
        $dadosProcesso['numero'],
        $dadosProcesso['tipo'],
        $dadosProcesso['status'],
        $dadosProcesso['autor'],
        $dadosProcesso['reu'],
        $dadosProcesso['data_abertura'],
        $dadosProcesso['prazo'],
        $dadosProcesso['descricao'],
        $processoId
    ]);

    $_SESSION['mensagem'] = "Processo atualizado com sucesso.";

    echo "<script>
        window.location.href = 'editar_processo.php?id=" . $processoId . "';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Processo - Sistema Advocacia</title>
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
        #loading {
            display: none;
            text-align: center;
            margin: 20px 0;
            color: red;
        }
        #loading img {
            width: 30px;
            height: 30px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h2>Editar Processo: <?php echo htmlspecialchars($processo['numero']); ?></h2>

        <?php
        if (isset($_SESSION['mensagem'])) {
            echo "<div class='alert alert-info'>" . $_SESSION['mensagem'] . "</div>";
            unset($_SESSION['mensagem']);
        }
        ?>

        <form method="POST" enctype="multipart/form-data" onsubmit="showLoading()">
            <div id="loading">
                <img src="https://i.gifer.com/YCZH.gif" alt="Carregando..."> Carregando, por favor aguarde...
            </div>
            <div class="mb-3">
                <label for="numero" class="form-label">Número do Processo</label>
                <input type="text" class="form-control" id="numero" name="numero" value="<?php echo htmlspecialchars($processo['numero']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo</label>
                <select class="form-select" id="tipo" name="tipo" required>
                    <option value="" disabled>Escolha um tipo</option>
                    <option value="Civil" <?php echo ($processo['tipo'] == 'Civil') ? 'selected' : ''; ?>>Civil</option>
                    <option value="Trabalhista" <?php echo ($processo['tipo'] == 'Trabalhista') ? 'selected' : ''; ?>>Trabalhista</option>
                    <option value="Previdenciário" <?php echo ($processo['tipo'] == 'Previdenciário') ? 'selected' : ''; ?>>Previdenciário</option>
                    <option value="Tribunal de Ética" <?php echo ($processo['tipo'] == 'Tribunal de Ética') ? 'selected' : ''; ?>>Tribunal de Ética</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="Em Andamento" <?php echo $processo['status'] == 'Em Andamento' ? 'selected' : ''; ?>>Em Andamento</option>
                    <option value="Concluído" <?php echo $processo['status'] == 'Concluído' ? 'selected' : ''; ?>>Concluído</option>
                    <option value="Suspenso" <?php echo $processo['status'] == 'Suspenso' ? 'selected' : ''; ?>>Suspenso</option>
                    <option value="Arquivado" <?php echo $processo['status'] == 'Arquivado' ? 'selected' : ''; ?>>Arquivado</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="autor" class="form-label">Autor</label>
                <input type="text" class="form-control" id="autor" name="autor" value="<?php echo htmlspecialchars($processo['autor']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="reu" class="form-label">Réu</label>
                <input type="text" class="form-control" id="reu" name="reu" value="<?php echo htmlspecialchars($processo['reu']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="data_abertura" class="form-label">Data de Abertura</label>
                <input type="date" class="form-control" id="data_abertura" name="data_abertura" value="<?php echo htmlspecialchars($processo['data_abertura']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="prazo" class="form-label">Prazo</label>
                <input type="text" class="form-control" id="prazo" name="prazo" value="<?php echo htmlspecialchars($processo['prazo']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="4" required><?php echo htmlspecialchars($processo['descricao']); ?></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="detalhes_processo.php?id=<?php echo $processoId; ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showLoading() {
    document.getElementById('loading').style.display = 'block';
}
</script>
</body>
</html>
<?php include 'footer.php'; ?>
