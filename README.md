
# Sistema de Gestão de Consumos (Gastor)

Este repositório contém uma aplicação web em PHP desenvolvida para controlar usuários, itens e seus consumos, gerando relatórios e um resumo das despesas ou uso de itens. O projeto segue uma arquitetura simples inspirada no padrão MVC (Model-View-Controller). Abaixo você encontra uma explicação detalhada do sistema, sua estrutura e como utilizá-lo.

---

## 🔍 Visão Geral do Sistema

O aplicativo permite que um administrador ou usuário autorizado:

- Cadastre e gerencie usuários.
- Registre itens ou produtos que serão consumidos.
- Lance consumos desses itens por usuários em datas específicas.
- Visualize um resumo consolidado dos consumos e valores totais.

Essa solução é adequada para controles internos, gastos de equipe, oficinas ou pequenos estabelecimentos que precisam acompanhar o consumo de recursos.

## 📁 Estrutura de Pastas

```
index.php              # Ponto de entrada da aplicação

config/
  database.php         # Configurações de conexão com o banco de dados

controllers/
  *Controller.php      # Controladores principais (Usuário, Item, Consumo, Resumo)

models/
  *.php                # Classes de modelo para manipular dados e interagir com o BD

views/
  *.php                # Templates de visualização (HTML com PHP integrado)

assets/
  css/style.css        # Estilos visuais
  js/script.js         # Scripts front-end
```

### 👥 Controllers
Responsáveis por receber requisições HTTP, interagir com os modelos e redirecionar para as views adequadas:

- `UsuarioController.php` – Operações de listagem e cadastro de usuários.
- `ItemController.php` – Gerenciamento de itens/unidades de consumo.
- `ConsumoController.php` – Inserção e listagem de consumos realizados.
- `ResumoController.php` – Cálculo de totais e exibição de relatórios sumarizados.

### 🧱 Models
Classes que representam entidades e encapsulam lógica de acesso ao banco:

- `Usuario.php`, `Item.php`, `Consumo.php` – cada uma com métodos CRUD.
- `Database.php` – Conexão PDO reutilizável com o banco de dados.

### 🖼 Views
Páginas que exibem formulários, tabelas e informações ao usuário:

- `usuarios.php`, `itens.php`, `consumos.php`, `resumo.php` – telas principais.
- `header.php`, `footer.php` – componentes comuns de layout.

## ⚙️ Configuração e Instalação

1. **Requisitos**:
   - Servidor web com suporte a PHP 7.4 ou superior (Apache, Nginx, etc.).
   - Banco de dados MySQL/MariaDB.

2. **Banco de dados**:
   - Crie um banco com nome de sua escolha.
   - Importe o script SQL fornecido (caso exista) ou crie as tabelas `usuarios`, `itens`, `consumos` com campos adequados.
   - Ajuste as credenciais em `config/database.php`:
     ```php
     $host = 'localhost';
     $db   = 'nome_do_banco';
     $user = 'usuario';
     $pass = 'senha';
     ```

3. **Deploy**:
   - Coloque os arquivos da aplicação no diretório-raiz do seu servidor.
   - Garanta permissões adequadas para `index.php` e demais pastas.

4. **Dependências**:
   - Não há bibliotecas externas; a aplicação usa apenas PHP nativo.

## 🚀 Uso

1. Acesse `http://seu-servidor/` ou `localhost` no navegador.
2. Navegue pelo menu para gerenciar usuários, itens ou registrar consumos.
3. Clique em "Resumo" para ver totais e análises por período.

**Observação:** não há autenticação implementada; recomenda-se adicionar se for expor publicamente.

## 🛠️ Customização e Extensões

Algumas ideias para evolução:

- Implementar autenticação e controle de acesso.
- Adicionar filtros por data nos relatórios.
- Exportar dados para CSV/Excel.
- Melhorar interface com frameworks JS/CSS.

## 📂 Arquivo de Exemplo do Banco

Caso você precise de um ponto de partida para as tabelas, um esboço SQL seria:

```sql
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100)
);

CREATE TABLE itens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  descricao VARCHAR(255) NOT NULL,
  valor DECIMAL(10,2) NOT NULL
);

CREATE TABLE consumos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  item_id INT NOT NULL,
  quantidade INT NOT NULL,
  data DATE NOT NULL,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (item_id) REFERENCES itens(id)
);
```

## 📘 Licença

Este projeto é fornecido como exemplo de sistema e pode ser adaptado livremente.

---

Se precisar de ajuda adicional ou quiser adicionar funcionalidades, fique à vontade para perguntar! 😊