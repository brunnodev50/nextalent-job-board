<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['recrutador_id'])) {
    die("Acesso negado.");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Pegar o caminho do arquivo para deletar o físico também
    $stmt = $pdo->prepare("SELECT arquivo_path FROM candidaturas WHERE id = ?");
    $stmt->execute([$id]);
    $candidatura = $stmt->fetch();

    if ($candidatura) {
        // Deletar arquivo físico
        $arquivo = $candidatura['arquivo_path'];
        if (file_exists($arquivo)) {
            unlink($arquivo);
        }

        // 2. Deletar do banco
        $stmtDelete = $pdo->prepare("DELETE FROM candidaturas WHERE id = ?");
        $stmtDelete->execute([$id]);
    }
}

header("Location: painel.php");
exit;
?>
