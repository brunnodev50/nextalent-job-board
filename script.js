// script.js

const jobsContainer = document.getElementById('jobs-container');
const modal = document.getElementById('apply-modal');
const form = document.getElementById('application-form');
const modalTitle = document.getElementById('modal-job-title');

// 1. Buscar Vagas com Filtros
async function fetchJobs(filtros = {}) {
    try {
        // Cria query string (ex: ?busca=java&local=sp)
        const params = new URLSearchParams(filtros).toString();
        const response = await fetch(`api/vagas.php?${params}`);
        const jobs = await response.json();
        renderJobs(jobs);
    } catch (error) {
        jobsContainer.innerHTML = '<p>Erro ao carregar vagas.</p>';
    }
}

// 2. Renderizar no HTML
function renderJobs(jobs) {
    if (jobs.length === 0) {
        jobsContainer.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #64748B;">Nenhuma vaga encontrada com esses filtros.</p>';
        return;
    }

    jobsContainer.innerHTML = jobs.map(job => `
        <article class="job-card">
            <div style="display:flex; justify-content:space-between; align-items:start;">
                <h3>${job.titulo}</h3>
                <small style="color: #2563EB; font-weight:600;">${job.publicado_em || 'Recente'}</small>
            </div>
            <div class="meta">
                <span><i class="ph ph-map-pin"></i> ${job.localizacao}</span>
                <span><i class="ph ph-briefcase"></i> ${job.tipo}</span>
            </div>
            <div class="tags">
                ${job.tags.map(tag => `<span>${tag}</span>`).join('')}
            </div>
            <button class="btn btn--outline btn--full" onclick="openModal('${job.titulo}')">
                Candidatar-se
            </button>
        </article>
    `).join('');
}

// 3. Botão de Busca
function aplicarFiltros() {
    const busca = document.getElementById('busca-termo').value;
    const local = document.getElementById('busca-local').value;
    const dias = document.getElementById('busca-data').value;
    
    // Feedback visual
    jobsContainer.innerHTML = '<p style="text-align:center;">Buscando...</p>';
    fetchJobs({ busca, local, dias });
}

// 4. Modal
window.openModal = (title) => {
    modalTitle.textContent = title;
    modal.showModal();
}

// 5. Envio do Form
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = form.querySelector('button');
    btn.disabled = true; btn.innerText = 'Enviando...';

    const formData = new FormData();
    formData.append('name', document.getElementById('name').value);
    formData.append('email', document.getElementById('email').value);
    formData.append('linkedin', document.getElementById('linkedin').value);
    formData.append('job_title', modalTitle.textContent);
    formData.append('resume', document.getElementById('resume').files[0]);

    try {
        const res = await fetch('api/candidatar.php', { method: 'POST', body: formData });
        const data = await res.json();
        
        if(data.success) {
            alert('Sucesso! Boa sorte.');
            modal.close();
            form.reset();
        } else {
            alert('Erro: ' + data.message);
        }
    } catch(err) {
        alert('Erro de conexão.');
    } finally {
        btn.disabled = false; btn.innerText = 'Enviar Candidatura';
    }
});

// Inicializa
document.addEventListener('DOMContentLoaded', () => fetchJobs());