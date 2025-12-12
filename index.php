<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>NexTalent | Vagas</title>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="header">
        <div class="container header__container">
            <a href="index.php" class="logo">Nex<span>Talent</span></a>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <a href="admin/login.php" class="btn btn--outline" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                    Área do Recrutador
                </a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <span style="background: #DBEAFE; color: #2563EB; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Carreiras</span>
                <h1 class="hero__title">Encontre seu lugar no futuro.</h1>
                <p class="hero__text">Vagas selecionadas para profissionais de tecnologia.</p>

                <div class="search-bar">
                    <div class="search-input-group">
                        <i class="ph ph-magnifying-glass"></i>
                        <input type="text" id="busca-termo" placeholder="Cargo (ex: Java, Design)">
                    </div>
                    <div class="search-input-group">
                        <i class="ph ph-map-pin"></i>
                        <input type="text" id="busca-local" placeholder="Cidade ou Estado">
                    </div>
                    <div class="search-input-group" style="flex: 0 0 160px;">
                        <i class="ph ph-calendar-blank"></i>
                        <select id="busca-data">
                            <option value="">Qualquer data</option>
                            <option value="3">Últimos 3 dias</option>
                            <option value="7">Última semana</option>
                            <option value="15">Últimos 15 dias</option>
                        </select>
                    </div>
                    <button onclick="aplicarFiltros()" class="btn btn--primary">Buscar</button>
                </div>
            </div>
        </section>

        <section class="container" style="padding-bottom: 4rem;">
            <h2 style="margin-bottom: 1.5rem; font-size: 1.5rem;">Vagas Disponíveis</h2>
            <div id="jobs-container" class="jobs__grid">
                <p>Carregando oportunidades...</p>
            </div>
        </section>
    </main>

    <dialog id="apply-modal" class="modal">
        <div class="modal__content">
            <button class="modal__close" onclick="modal.close()"><i class="ph ph-x"></i></button>
            <h3 style="margin-bottom: 0.5rem;">Candidatar-se</h3>
            <p style="color: #64748B; margin-bottom: 1.5rem;">Vaga: <span id="modal-job-title" style="color: #2563EB; font-weight: 600;">--</span></p>

            <form id="application-form">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" id="name" required>
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" id="email" required>
                </div>
                <div class="form-group">
                    <label>LinkedIn</label>
                    <input type="url" id="linkedin" required>
                </div>
                <div class="form-group">
                    <label>Currículo (PDF)</label>
                    <input type="file" id="resume" accept=".pdf" required style="padding: 0.5rem; background: #F8FAFC;">
                </div>
                <button type="submit" class="btn btn--primary btn--full">Enviar Candidatura</button>
            </form>
        </div>
    </dialog>

    <script src="script.js"></script>
</body>
</html>