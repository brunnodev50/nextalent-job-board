<?php
session_start();
require_once '../config.php';

// 1. Verificação de Login
if (!isset($_SESSION['recrutador_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Lógica da Foto de Perfil (Session ou Fallback)
$fotoUsuario = isset($_SESSION['recrutador_foto']) && !empty($_SESSION['recrutador_foto']) 
    ? '../' . $_SESSION['recrutador_foto'] 
    : 'https://ui-avatars.com/api/?name='.urlencode($_SESSION['recrutador_nome']).'&background=random';

// 3. Filtros de Busca
$where = "WHERE 1=1";
$params = [];

$busca = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_SPECIAL_CHARS);
if ($busca) {
    $where .= " AND (candidato_nome LIKE ? OR candidato_email LIKE ?)";
    $params[] = "%$busca%"; $params[] = "%$busca%";
}

$filtroVaga = filter_input(INPUT_GET, 'vaga', FILTER_SANITIZE_SPECIAL_CHARS);
if ($filtroVaga) {
    $where .= " AND vaga_titulo = ?";
    $params[] = $filtroVaga;
}

$filtroStatus = filter_input(INPUT_GET, 'status_candidato', FILTER_SANITIZE_SPECIAL_CHARS);
if ($filtroStatus) {
    $where .= " AND status = ?";
    $params[] = $filtroStatus;
}

// 4. Consultas SQL
// Candidatos
$sql = "SELECT * FROM candidaturas $where ORDER BY data_envio DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$candidaturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vagas (Para Sidebar e Filtro)
$vagasTodas = $pdo->query("SELECT * FROM vagas ORDER BY criado_em DESC")->fetchAll(PDO::FETCH_ASSOC);
$titulosVagas = array_unique(array_column($vagasTodas, 'titulo'));
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Painel Admin | NexTalent</title>
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">

    <style>
        /* --- ESTILOS DO PAINEL --- */
        body { background-color: #F8FAFC; padding-bottom: 3rem; }
        
        /* Header Personalizado */
        .admin-header { background: white; padding: 0.8rem 0; border-bottom: 1px solid #E2E8F0; margin-bottom: 1.5rem; }
        
        .profile-area {
            display: flex; align-items: center; gap: 1rem;
            text-decoration: none; color: inherit;
        }
        .profile-info { text-align: right; line-height: 1.2; }
        .profile-name { display: block; font-weight: 600; font-size: 0.9rem; color: var(--color-text-main); }
        .profile-link-text { display: block; font-size: 0.75rem; color: var(--color-text-muted); }
        .profile-img { 
            width: 42px; height: 42px; 
            border-radius: 50%; object-fit: cover; 
            border: 2px solid #E2E8F0; 
            transition: 0.2s;
        }
        .profile-area:hover .profile-img { border-color: var(--color-primary); }

        /* Grid Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 1.5rem;
            align-items: start;
        }

        /* Sidebar Vagas */
        .vaga-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #F1F5F9; }
        .vaga-item:last-child { border: none; }
        .status-badge { font-size: 0.75rem; font-weight: 600; padding: 2px 8px; border-radius: 12px; }
        .badge-open { background: #DCFCE7; color: #166534; }
        .badge-closed { background: #F1F5F9; color: #64748B; }

        /* Filtros */
        .filter-bar { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; background: white; padding: 1rem; border-radius: 8px; border: 1px solid #E2E8F0; }
        .filter-input { flex: 1; padding: 0.6rem; border: 1px solid #CBD5E1; border-radius: 6px; min-width: 140px; }

        /* Status Colors */
        .status-select { width: 100%; padding: 0.4rem; border-radius: 6px; font-weight: 600; font-size: 0.85rem; border: 1px solid transparent; cursor: pointer; }
        .st-recebido { background: #F1F5F9; color: #475569; border-color: #E2E8F0; }
        .st-processo { background: #FFEDD5; color: #C2410C; border-color: #FED7AA; }
        .st-aprovado { background: #DCFCE7; color: #15803D; border-color: #BBF7D0; }
        .st-reprovado { background: #FEE2E2; color: #B91C1C; border-color: #FECACA; }

        /* --- RESPONSIVIDADE (MOBILE CARD VIEW) --- */
        @media (max-width: 900px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            
            /* Header Ajustes */
            .profile-info { display: none; } /* Esconde nome, mostra só foto */
            
            /* Filtros */
            .filter-bar { flex-direction: column; }
            .filter-input, .filter-bar button, .filter-bar a { width: 100%; }

            /* Tabela vira Cartões */
            .admin-table, .admin-table tbody, .admin-table tr, .admin-table td { display: block; width: 100%; }
            .admin-table thead { display: none; }
            
            .admin-table tr {
                background: white; margin-bottom: 1rem;
                border: 1px solid #E2E8F0; border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 1rem;
            }
            .admin-table td {
                padding: 0.6rem 0; text-align: right;
                border-bottom: 1px solid #F1F5F9;
                display: flex; justify-content: space-between; align-items: center;
            }
            .admin-table td::before {
                content: attr(data-label);
                font-weight: 600; color: #64748B; font-size: 0.85rem; text-transform: uppercase;
            }
            .admin-table td:last-child { border: none; justify-content: center; padding-top: 1rem; }
            
            /* Ajuste email mobile */
            .email-wrapper { max-width: 180px; }
            .email-wrapper span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        }
    </style>
</head>
<body>

    <header class="admin-header">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 1.25rem; font-weight: 700;">
                Nex<span style="color: #2563EB;">Talent</span>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem;">
                <a href="perfil.php" class="profile-area">
                    <div class="profile-info">
                        <span class="profile-name"><?php echo htmlspecialchars($_SESSION['recrutador_nome']); ?></span>
                        <span class="profile-link-text">Editar Perfil</span>
                    </div>
                    <img src="<?php echo htmlspecialchars($fotoUsuario); ?>" alt="Perfil" class="profile-img">
                </a>

                <a href="logout.php" class="btn btn--outline" style="padding: 0.5rem; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;" title="Sair">
                    <i class="ph ph-sign-out" style="font-size: 1.2rem;"></i>
                </a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="dashboard-grid">

            <aside>
                <div style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #E2E8F0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3>Vagas</h3>
                        <button onclick="modalVaga.showModal()" class="btn btn--primary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                            <i class="ph ph-plus"></i> Nova
                        </button>
                    </div>

                    <div id="lista-vagas">
                        <?php foreach($vagasTodas as $v): $aberta = ($v['status'] === 'aberta'); ?>
                        <div class="vaga-item">
                            <div style="overflow: hidden; padding-right: 0.5rem;">
                                <h4 style="font-size: 0.9rem; margin-bottom: 2px; <?php echo !$aberta ? 'text-decoration:line-through; opacity:0.6;' : ''; ?>">
                                    <?php echo htmlspecialchars($v['titulo']); ?>
                                </h4>
                                <span class="status-badge <?php echo $aberta ? 'badge-open' : 'badge-closed'; ?>">
                                    <?php echo $aberta ? 'Aberta' : 'Encerrada'; ?>
                                </span>
                            </div>
                            <button class="btn btn--outline" style="padding: 0.3rem;" 
                                    onclick="toggleVaga(<?php echo $v['id']; ?>, '<?php echo $v['status']; ?>')"
                                    title="Alterar Status">
                                <i class="ph <?php echo $aberta ? 'ph-lock-key' : 'ph-lock-key-open'; ?>"></i>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>

            <main>
                <div style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #E2E8F0;">
                    <h3 style="margin-bottom: 1rem;">Candidaturas</h3>

                    <form method="GET" class="filter-bar">
                        <input type="text" name="busca" class="filter-input" placeholder="Nome ou Email..." value="<?php echo htmlspecialchars($busca); ?>">
                        <select name="vaga" class="filter-input">
                            <option value="">Todas Vagas</option>
                            <?php foreach($titulosVagas as $t): ?>
                                <option value="<?php echo htmlspecialchars($t); ?>" <?php echo $filtroVaga == $t ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($t); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select name="status_candidato" class="filter-input">
                            <option value="">Status</option>
                            <option value="Recebido" <?php echo $filtroStatus=='Recebido'?'selected':'';?>>Recebido</option>
                            <option value="Em processo" <?php echo $filtroStatus=='Em processo'?'selected':'';?>>Em Processo</option>
                            <option value="Aprovado" <?php echo $filtroStatus=='Aprovado'?'selected':'';?>>Aprovado</option>
                            <option value="Não aprovado" <?php echo $filtroStatus=='Não aprovado'?'selected':'';?>>Reprovado</option>
                        </select>
                        <button type="submit" class="btn btn--primary" style="padding: 0.6rem 1rem;"><i class="ph ph-funnel"></i></button>
                        <?php if($busca || $filtroVaga || $filtroStatus): ?>
                            <a href="painel.php" class="btn btn--outline" style="padding: 0.6rem;">Limpar</a>
                        <?php endif; ?>
                    </form>

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Candidato</th>
                                <th>Status</th>
                                <th style="text-align: center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($candidaturas as $c): 
                                $css = 'st-recebido';
                                if($c['status'] == 'Em processo') $css = 'st-processo';
                                if($c['status'] == 'Aprovado') $css = 'st-aprovado';
                                if($c['status'] == 'Não aprovado') $css = 'st-reprovado';
                            ?>
                            <tr>
                                <td data-label="Candidato">
                                    <div style="text-align: left; width: 100%;">
                                        <div style="font-weight: 600; font-size: 1rem;"><?php echo htmlspecialchars($c['candidato_nome']); ?></div>
                                        <div style="font-size: 0.85rem; color: #64748B; margin-bottom: 5px;">
                                            <?php echo htmlspecialchars($c['vaga_titulo']); ?>
                                        </div>
                                        
                                        <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: #F1F5F9; padding: 0.2rem 0.6rem; border-radius: 4px;" class="email-wrapper">
                                            <span style="font-size: 0.8rem;"><?php echo htmlspecialchars($c['candidato_email']); ?></span>
                                            <button onclick="copiarEmail('<?php echo htmlspecialchars($c['candidato_email']); ?>', this)" 
                                                    style="background:none; border:none; cursor:pointer; color:#2563EB; display:flex;" title="Copiar">
                                                <i class="ph ph-copy"></i>
                                            </button>
                                        </div>
                                        
                                        <?php if($c['linkedin']): ?>
                                            <div style="margin-top: 5px;">
                                                <a href="<?php echo htmlspecialchars($c['linkedin']); ?>" target="_blank" style="font-size: 0.8rem; color: #0077B5; text-decoration: none;">
                                                    <i class="ph ph-linkedin-logo"></i> LinkedIn
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td data-label="Status">
                                    <select class="status-select <?php echo $css; ?>" onchange="alterarStatus(<?php echo $c['id']; ?>, this)">
                                        <option value="Recebido" <?php echo $c['status']=='Recebido'?'selected':'';?>>Recebido</option>
                                        <option value="Em processo" <?php echo $c['status']=='Em processo'?'selected':'';?>>Em Processo</option>
                                        <option value="Aprovado" <?php echo $c['status']=='Aprovado'?'selected':'';?>>Aprovado</option>
                                        <option value="Não aprovado" <?php echo $c['status']=='Não aprovado'?'selected':'';?>>Reprovado</option>
                                    </select>
                                </td>

                                <td data-label="Ações">
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="../<?php echo htmlspecialchars($c['arquivo_path']); ?>" target="_blank" class="btn btn--outline" style="padding: 0.5rem;" title="Ver PDF">
                                            <i class="ph ph-eye"></i>
                                        </a>
                                        <a href="../<?php echo htmlspecialchars($c['arquivo_path']); ?>" download class="btn btn--primary" style="padding: 0.5rem;" title="Baixar PDF">
                                            <i class="ph ph-download-simple"></i>
                                        </a>
                                        <a href="excluir.php?id=<?php echo $c['id']; ?>" class="btn--danger" style="padding: 0.5rem;" onclick="return confirm('ATENÇÃO: Isso apaga o registro e o arquivo PDF. Confirmar?')" title="Excluir">
                                            <i class="ph ph-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if(empty($candidaturas)): ?>
                        <div style="padding: 3rem; text-align: center; color: #64748B;">
                            Nenhum registro encontrado.
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <dialog id="modalVaga" class="modal">
        <div class="modal__content">
            <button class="modal__close" onclick="modalVaga.close()"><i class="ph ph-x"></i></button>
            <h3>Nova Vaga</h3>
            <form id="formVaga" style="margin-top: 1rem;">
                <input type="hidden" name="acao" value="nova_vaga">
                <div class="form-group">
                    <label>Título</label>
                    <input type="text" name="titulo" required class="filter-input" style="width: 100%;">
                </div>
                <div class="form-group">
                    <label>Local</label>
                    <input type="text" name="localizacao" required class="filter-input" style="width: 100%;">
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <select name="tipo" class="filter-input" style="width: 100%;">
                        <option>Full-time</option><option>PJ</option><option>Estágio</option><option>Freelance</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tags</label>
                    <input type="text" name="tags" class="filter-input" style="width: 100%;" placeholder="Separar por vírgula">
                </div>
                <button type="submit" class="btn btn--primary btn--full">Salvar</button>
            </form>
        </div>
    </dialog>

    <script>
        function copiarEmail(texto, btn) {
            navigator.clipboard.writeText(texto).then(() => {
                const icon = btn.querySelector('i');
                const original = icon.className;
                icon.className = 'ph ph-check';
                btn.style.color = '#166534';
                setTimeout(() => { icon.className = original; btn.style.color = '#2563EB'; }, 2000);
            });
        }

        async function alterarStatus(id, select) {
            const status = select.value;
            select.className = 'status-select';
            if(status==='Recebido') select.classList.add('st-recebido');
            else if(status==='Em processo') select.classList.add('st-processo');
            else if(status==='Aprovado') select.classList.add('st-aprovado');
            else if(status==='Não aprovado') select.classList.add('st-reprovado');

            const fd = new FormData();
            fd.append('acao','mudar_status_candidato'); fd.append('id',id); fd.append('status',status);
            await fetch('acoes.php', { method: 'POST', body: fd });
        }

        async function toggleVaga(id, status) {
            if(!confirm('Alterar status?')) return;
            const fd = new FormData();
            fd.append('acao','mudar_status_vaga'); fd.append('id',id); fd.append('status_atual',status);
            const res = await fetch('acoes.php', { method: 'POST', body: fd });
            if((await res.json()).sucesso) location.reload();
        }

        document.getElementById('formVaga').addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(e.target);
            const res = await fetch('acoes.php', { method: 'POST', body: fd });
            if((await res.json()).sucesso) location.reload();
        });
    </script>
</body>
</html>