<?php
// index.php
session_start();

// Incluir configurações e models
require_once 'config/database.php';
require_once 'models/Database.php';
require_once 'models/Usuario.php';
require_once 'models/Item.php';
require_once 'models/Consumo.php';

// Incluir controllers
require_once 'controllers/UsuarioController.php';
require_once 'controllers/ItemController.php';
require_once 'controllers/ConsumoController.php';
require_once 'controllers/ResumoController.php';

// Conectar ao banco
$database = new Database();
$db = $database->getConnection();

// Inicializar controllers
$usuarioController = new UsuarioController($db);
$itemController = new ItemController($db);
$consumoController = new ConsumoController($db);
$resumoController = new ResumoController($db);

// Determinar página atual
$page = isset($_GET['page']) ? $_GET['page'] : 'usuarios';
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Processar ações
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch($page) {
        case 'usuarios':
            if ($action === 'create') {
                $result = $usuarioController->criarUsuario($_POST);
                $message = $result['message'];
                if($result['success']) {
                    $_SESSION['message'] = $message;
                    header('Location: index.php?page=usuarios');
                    exit;
                }
            }
            
            // NOVO: Ação de exclusão de usuário
            if ($action === 'delete') {
                $id = $_POST['id'];
                $result = $usuarioController->excluirUsuario($id);
                $message = $result['message'];
                if($result['success']) {
                    $_SESSION['message'] = $message;
                    header('Location: index.php?page=usuarios');
                    exit;
                } else {
                    $_SESSION['message'] = $message;
                    header('Location: index.php?page=usuarios');
                    exit;
                }
            }
            
            // NOVO: Ação de atualização de usuário
            if ($action === 'update') {
                $id = $_POST['id'];
                $result = $usuarioController->atualizarUsuario($id, $_POST);
                $message = $result['message'];
                if($result['success']) {
                    $_SESSION['message'] = $message;
                    header('Location: index.php?page=usuarios');
                    exit;
                } else {
                    $_SESSION['message'] = $message;
                    header('Location: index.php?page=usuarios');
                    exit;
                }
            }
            break;
            
        case 'itens':
            if ($action === 'create') {
                $result = $itemController->criarItem($_POST);
                $message = $result['message'];
                if($result['success']) {
                    $_SESSION['message'] = $message;
                    header('Location: index.php?page=itens');
                    exit;
                }
            }

            if ($action === 'delete') {
                $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
                if ($id > 0) {
                    $result = $itemController->excluirItem($id);
                    $message = $result['message'];
                    $_SESSION['message'] = $message;
                } else {
                    $_SESSION['message'] = 'Item inválido.';
                }

                header('Location: index.php?page=itens');
                exit;
            }
            break;
            
        case 'consumos':
            if ($action === 'create') {
                // NOVO: Recebe array de usuários para divisão automática
                $dados = $_POST;
                $dados['id_usuarios'] = isset($_POST['id_usuarios']) ? $_POST['id_usuarios'] : [];
                $result = $consumoController->registrarConsumo($dados);
                $message = $result['message'];
                if($result['success']) {
                    $_SESSION['message'] = $message;
                    header('Location: index.php?page=consumos');
                    exit;
                }
            }

            if ($action === 'delete') {
                $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
                if ($id > 0) {
                    $result = $consumoController->excluirConsumo($id);
                    $_SESSION['message'] = $result['message'];
                } else {
                    $_SESSION['message'] = 'Consumo inválido.';
                }

                header('Location: index.php?page=consumos');
                exit;
            }
            break;
    }
}

// Carregar dados para as views
$usuarios = $usuarioController->listarUsuarios();
$itens = $itemController->listarItens();
$consumos = $consumoController->listarConsumos();

// Processar resumo
$resumo = null;
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

if ($page === 'resumo' && $data_inicio && $data_fim) {
    $resumo = $resumoController->gerarResumo($data_inicio, $data_fim);
}

// Mostrar mensagens
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Incluir header
$current_page = $page;
include 'views/header.php';

// Mostrar mensagem se existir
if ($message): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php
// Incluir a view apropriada
switch($page) {
    case 'usuarios':
        include 'views/usuarios.php';
        break;
    case 'itens':
        include 'views/itens.php';
        break;
    case 'consumos':
        include 'views/consumos.php';
        break;
    case 'resumo':
        include 'views/resumo.php';
        break;
    default:
        include 'views/usuarios.php';
}

// Incluir footer
include 'views/footer.php';
?>