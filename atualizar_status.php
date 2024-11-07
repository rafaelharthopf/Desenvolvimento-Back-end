<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['processo_id'], $_POST['status'])) {
    $processoId = $_POST['processo_id'];
    $novoStatus = $_POST['status'];

    $stmt = $pdo->prepare('UPDATE processos SET status = ? WHERE id = ?');
    $stmt->execute([$novoStatus, $processoId]);

    $_SESSION['mensagem'] = "O status do processo foi atualizado para '{$novoStatus}'.";

    header("Location: detalhes_processo.php?id=" . urlencode($processoId));
    exit;
}
