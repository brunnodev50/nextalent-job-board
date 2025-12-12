<?php
// api/vagas.php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

try {
    // Inicializa a query base: APENAS VAGAS ABERTAS
    $sql = "SELECT * FROM vagas WHERE status = 'aberta'";
    $params = [];

    // 1. Filtro por Termo (Cargo ou Tags)
    if (!empty($_GET['busca'])) {
        $termo = $_GET['busca'];
        $sql .= " AND (titulo LIKE ? OR tags LIKE ?)";
        $params[] = "%$termo%";
        $params[] = "%$termo%";
    }

    // 2. Filtro por Localização (Cidade ou Estado)
    if (!empty($_GET['local'])) {
        $local = $_GET['local'];
        $sql .= " AND localizacao LIKE ?";
        $params[] = "%$local%";
    }

    // 3. Filtro por Data (Recência)
    if (!empty($_GET['dias'])) {
        $dias = (int)$_GET['dias'];
        if ($dias > 0) {
            $sql .= " AND criado_em >= DATE_SUB(NOW(), INTERVAL ? DAY)";
            $params[] = $dias;
        }
    }

    $sql .= " ORDER BY criado_em DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $vagas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formata as tags e calcula "dias atrás"
    foreach ($vagas as &$vaga) {
        // Tags
        if (!empty($vaga['tags'])) {
            $vaga['tags'] = array_map('trim', explode(',', $vaga['tags']));
        } else {
            $vaga['tags'] = [];
        }

        // Formatação amigável de data (Ex: "Há 2 dias")
        $dataVaga = new DateTime($vaga['criado_em']);
        $hoje = new DateTime();
        $intervalo = $hoje->diff($dataVaga);
        
        if ($intervalo->days == 0) {
            $vaga['publicado_em'] = "Hoje";
        } elseif ($intervalo->days == 1) {
            $vaga['publicado_em'] = "Ontem";
        } else {
            $vaga['publicado_em'] = "Há " . $intervalo->days . " dias";
        }
    }

    echo json_encode($vagas);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>