<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['recrutador_id'])) {
    http_response_code(403);
    echo json_encode(['erro' => 'Acesso negado']);
    exit;
}

$acao = $_POST['acao'] ?? '';

try {
    // 1. CADASTRAR NOVA VAGA
    if ($acao === 'nova_vaga') {
        $titulo = $_POST['titulo'];
        $local = $_POST['localizacao'];
        $tipo = $_POST['tipo'];
        $tags = $_POST['tags'];

        $stmt = $pdo->prepare("INSERT INTO vagas (titulo, localizacao, tipo, tags, status) VALUES (?, ?, ?, ?, 'aberta')");
        $stmt->execute([$titulo, $local, $tipo, $tags]);
        
        echo json_encode(['sucesso' => true, 'msg' => 'Vaga criada com sucesso!']);
    }

    // 2. ALTERAR STATUS DO CANDIDATO (Ajax)
    elseif ($acao === 'mudar_status_candidato') {
        $id = $_POST['id'];
        $status = $_POST['status'];
        
        $stmt = $pdo->prepare("UPDATE candidaturas SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        
        echo json_encode(['sucesso' => true]);
    }

    // 3. ALTERAR STATUS DA VAGA (Encerrar/Abrir)
    elseif ($acao === 'mudar_status_vaga') {
        $id = $_POST['id'];
        $statusAtual = $_POST['status_atual'];
        $novoStatus = ($statusAtual === 'aberta') ? 'encerrada' : 'aberta';
        
        $stmt = $pdo->prepare("UPDATE vagas SET status = ? WHERE id = ?");
        $stmt->execute([$novoStatus, $id]);
        
        echo json_encode(['sucesso' => true, 'novo_status' => $novoStatus]);
    }

} catch (PDOException $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}
?>