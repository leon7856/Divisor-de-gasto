
# Sistema de Gestão de Consumos (Gastor)

Este repositório contém uma aplicação web em PHP desenvolvida para controlar usuários, itens e seus consumos, gerando relatórios e um resumo das despesas ou uso de itens. O projeto segue uma arquitetura simples

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


