# Limites Arquiteturais do Pacote Multi-Tenant

Este documento descreve **explicitamente o escopo e os limites** do pacote de infraestrutura multi-tenant multi-database para Laravel.

O objetivo é deixar claro **o que o pacote resolve** e, principalmente, **o que ele deliberadamente NÃO resolve**, garantindo baixo acoplamento, alta reutilização e longevidade do projeto.

---

## 1. Propósito do pacote

O pacote tem como finalidade fornecer **infraestrutura técnica** para aplicações Laravel que necessitam de:

- Multi-tenant baseado em subdomínio
- Isolamento total de dados por banco de dados
- Tenant central (root) separado dos tenants de negócio
- Troca dinâmica de conexão em tempo de execução
- Contexto de execução previsível e seguro

Este pacote **não é um framework de aplicação**, nem um gerador de sistemas completos.

---

## 2. Princípio fundamental

> **Infraestrutura não deve conter regras de aplicação.**

O pacote existe para **viabilizar** a aplicação, não para **decidir como ela funciona**.

---

## 3. O que o pacote NÃO resolve (decisão consciente)

As exclusões abaixo são **intencionais** e fazem parte do design do pacote.

---

### 3.1 Autenticação

**Não faz parte do escopo do pacote:**
- Login / Logout
- Guards
- Providers
- Sessions
- Tokens
- Sanctum, Passport, JWT
- MFA
- OAuth / SSO

**Motivo:**
A estratégia de autenticação varia enormemente entre projetos (web, API, mobile, SSO, etc.).  
Qualquer tentativa de padronizar isso dentro do pacote criaria acoplamento e limitaria sua adoção.

**Responsabilidade do pacote:**
Garantir que o **contexto do tenant e a conexão correta** estejam ativos antes da autenticação ocorrer.

---

### 3.2 Autorização

**Não faz parte do escopo do pacote:**
- Policies
- Gates
- RBAC / ABAC
- Papéis e permissões
- Regras de acesso funcional

**Motivo:**
Autorização é **regra de negócio**.  
Ela depende do domínio da aplicação, do modelo de dados e das decisões do produto.

**Responsabilidade do pacote:**
Disponibilizar helpers e contexto (`is_tenant()`, `is_central()`, `tenant()`) que possam ser usados pelas Policies da aplicação.

---

### 3.3 CRUD (Create, Read, Update, Delete)

**Não faz parte do escopo do pacote:**
- Controllers
- Services
- Repositories
- Models de negócio
- Validações
- Fluxos de cadastro

**Motivo:**
Cada aplicação possui entidades, relacionamentos e regras próprias.  
Um CRUD genérico dentro do pacote não seria reutilizável nem seguro.

**Responsabilidade do pacote:**
Garantir que qualquer CRUD implementado pela aplicação funcione dentro do banco de dados correto, de forma isolada.

---

### 3.4 Painel Administrativo

**Não faz parte do escopo do pacote:**
- Filament
- Laravel Nova
- Backpack
- Painéis customizados
- Dashboards

**Motivo:**
UI é uma decisão de produto e impõe fortes opiniões técnicas.  
O pacote deve ser consumível por aplicações com qualquer tipo de interface ou até mesmo sem interface (API-only).

**Responsabilidade do pacote:**
Emitir eventos e fornecer hooks para que painéis administrativos possam ser construídos externamente.

---

### 3.5 Views / Front-end

**Não faz parte do escopo do pacote:**
- Blade
- Vue
- React
- Inertia
- Quasar
- Mobile UI

**Motivo:**
Front-end varia conforme o tipo de aplicação e o público-alvo.  
Incluir qualquer camada de apresentação tornaria o pacote inutilizável fora de um cenário específico.

**Responsabilidade do pacote:**
Nenhuma.  
O pacote é totalmente agnóstico de front-end.

---

### 3.6 Regras de Negócio

**Não faz parte do escopo do pacote:**
- Planos
- Limites
- Cobrança
- Bloqueios
- Assinaturas
- Lógica comercial
- Regras por país ou cliente

**Motivo:**
Regras de negócio mudam com frequência e são específicas do domínio da aplicação.

**Responsabilidade do pacote:**
Fornecer eventos, contratos e pontos de extensão para que a aplicação implemente suas próprias regras.

---

## 4. Divisão clara de responsabilidades

| Camada | Responsável |
|------|------------|
| Resolução de tenant | Pacote |
| Contexto de execução | Pacote |
| Troca de conexão | Pacote |
| Isolamento de dados | Pacote |
| Autenticação | Aplicação |
| Autorização | Aplicação |
| CRUD | Aplicação |
| UI / Front-end | Aplicação |
| Regras de negócio | Aplicação |

---

## 5. Benefícios dessas decisões

- Baixo acoplamento
- Alta reutilização
- Fácil manutenção
- Testabilidade
- Compatibilidade com qualquer stack
- Longevidade do pacote

---

## 6. Conclusão

Este pacote é **deliberadamente pequeno, coeso e opinado apenas onde faz sentido**: na infraestrutura multi-tenant.

Qualquer funcionalidade fora desse escopo deve ser implementada pela aplicação que consome o pacote, utilizando os contratos, eventos e helpers fornecidos.

Essa separação é fundamental para garantir a qualidade técnica e a sustentabilidade do projeto a longo prazo.
