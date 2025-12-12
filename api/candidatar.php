<?php
// api/candidatar.php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Receber dados do formulário
    $nome = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $linkedin = $_POST['linkedin'] ?? '';
    // O JS precisa enviar o nome da vaga. Se não vier, define um padrão.
    $vagaTitulo = $_POST['job_title'] ?? 'Candidatura Geral'; 

    // 2. Validação do Arquivo
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        
        $arquivo = $_FILES['resume'];
        
        // Verifica se é PDF
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        if ($extensao !== 'pdf') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Apenas arquivos PDF são aceitos.']);
            exit;
        }

        // 3. Configuração de Diretórios e Nomes
        // Caminho físico para salvar o arquivo (relativo a este arquivo PHP)
        $diretorioBase = "../documentos/curriculos/";
        
        // Cria a pasta se não existir (recursivo)
        if (!is_dir($diretorioBase)) {
            mkdir($diretorioBase, 0777, true);
        }

        // Gera nome aleatório seguro (Ex: a1b2c3d4e5... .pdf)
        $nomeAleatorio = bin2hex(random_bytes(16)) . '.' . $extensao;
        
        $caminhoFisico = $diretorioBase . $nomeAleatorio;
        
        // Caminho para salvar no banco (para ser acessível via link depois)
        // Removemos o "../" para salvar o caminho a partir da raiz do site
        $caminhoBanco = "documentos/curriculos/" . $nomeAleatorio;

        // 4. Mover arquivo e Salvar no Banco
        if (move_uploaded_file($arquivo['tmp_name'], $caminhoFisico)) {
            
            $sql = "INSERT INTO candidaturas (vaga_titulo, candidato_nome, candidato_email, linkedin, arquivo_path) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            try {
                $stmt->execute([$vagaTitulo, $nome, $email, $linkedin, $caminhoBanco]);
                echo json_encode(['success' => true, 'message' => 'Candidatura enviada com sucesso!']);
            } catch (PDOException $e) {
                // Se der erro no banco, tentamos apagar o arquivo que acabou de subir para não deixar lixo
                unlink($caminhoFisico);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao registrar no banco.']);
            }

        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar o arquivo no servidor.']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nenhum arquivo enviado ou erro no upload.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
}
?>
