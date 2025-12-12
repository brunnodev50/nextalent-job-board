# NexTalent - Plataforma de Recrutamento & Seleção (ATS) 🚀

![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

Um sistema completo de **Job Board** (Quadro de Vagas) e **ATS** (Sistema de Rastreamento de Candidatos) desenvolvido utilizando **PHP Puro (Vanilla)**, sem o uso de frameworks. O foco do projeto é performance, segurança e responsividade (Mobile First).

O projeto simula um ambiente real onde candidatos buscam vagas e enviam currículos PDF, e recrutadores gerenciam o processo seletivo através de um painel administrativo completo.

---

## 📸 Galeria do Projeto

### 1. Área Pública (Candidatos)
Visualização de vagas com filtros avançados (Localização, Cargo, Data) e modal de candidatura.

![Tela Inicial - Busca de Vagas](https://github.com/user-attachments/assets/fb71d5c2-6ff8-48c9-98dd-fb4b2606602a)

### 2. Acesso Administrativo
Tela de login segura com tratamento de erros.

![Login do Recrutador](https://github.com/user-attachments/assets/cfd825c0-8ab3-422d-93a1-bcbbd9313082)

### 3. Painel de Controle (Dashboard)
Gestão completa: status dos candidatos (colorido dinamicamente), download de currículos e filtros.

![Painel Administrativo](https://github.com/user-attachments/assets/d26fd835-e951-4b06-96a2-46dff80a4bab)

### 4. Gestão de Vagas
Criação de novas oportunidades e opção de encerrar/reabrir vagas existentes.

![Nova Vaga](https://github.com/user-attachments/assets/deb7a6e5-4fe9-4a7b-a1a3-c4337807df0c)

### 5. Perfil do Recrutador
Edição de dados e upload de foto de perfil (com pré-visualização e armazenamento seguro).

![Perfil e Configurações](https://github.com/user-attachments/assets/4617c499-e524-4816-98f7-3ae06ae2c3ab)

---

## ✨ Funcionalidades Principais

### 🌍 Para Candidatos
- **Busca Inteligente:** Filtre vagas por palavras-chave, local ou recência (ex: "Últimos 7 dias").
- **Candidatura Drag & Drop:** Envio facilitado de currículos em PDF.
- **Feedback:** Notificações visuais de sucesso (Toasts).

### 🔒 Para Recrutadores (Admin)
- **Gestão de Candidaturas:**
  - Alteração de status: *Recebido*, *Em Processo*, *Aprovado*, *Reprovado*.
  - Visualização e Download direto do PDF.
  - Botão "Copiar E-mail" para agilizar o contato.
- **Gestão de Vagas:** Publique novas vagas ou encerre as preenchidas (elas somem do site automaticamente).
- **Segurança:**
  - Senhas criptografadas (Hash).
  - Proteção contra SQL Injection e XSS.
  - Sistema de Login com Sessões PHP.
- **Personalização:** Upload de foto de perfil.
- **Responsividade:** O painel se adapta ao celular, transformando tabelas em "Cartões" para fácil leitura.

---

## 🛠️ Tecnologias

- **Backend:** PHP (PDO, Sessions, File System).
- **Banco de Dados:** MySQL.
- **Frontend:** HTML5, CSS3 (CSS Variables, Flexbox, Grid, Media Queries).
- **Javascript:** Vanilla JS (Fetch API, DOM Manipulation).
- **Design:** Phosphor Icons.

---

## 🚀 Instalação e Configuração

### Pré-requisitos
- Servidor Local (XAMPP, WAMP, Laragon) ou Docker.
- PHP 7.4+ e MySQL.

### Passo 1: Clone o Repositório
```bash
git clone [https://github.com/brunnodev50/nextalent-job-board.git](https://github.com/brunnodev50/nextalent-job-board.git)
