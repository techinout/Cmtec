<?php
session_start();
include '../../../Controller/conexao.php'; 
include "../../../Model/Status.php";
include "../../../Model/Resposta.php";

// Verifica se o token da sessão está presente
if (!isset($_SESSION['token'])) {
    header('Location: ../../login.html');
    exit;
}

// Divide o token em sua parte principal e tipo (admin ou aluno)
list($token, $type) = explode('_', $_SESSION['token'], 2);

// Verifica se o tipo do token é 'admin'
if ($type !== 'admin') {
    header('Location: ../../login.html');
    exit;
}

$codFuncionario = $_SESSION['user']['codFuncionario'];

$sql = "SELECT * FROM Funcionario WHERE codFuncionario = :codFuncionario";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':codFuncionario', $codFuncionario, PDO::PARAM_INT);
$stmt->execute();
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

$statusModel = new Status($pdo);
$status = $statusModel->listarTodos();

// Cria uma instância da classe Resposta e busca as respostas do aluno
$resposta = new Resposta($pdo);
$respostas = $resposta->listarTodos();

// Verifica o status selecionado no filtro (1 = Novos, 2 = Vistos, 3 = Concluídos)
$statusFiltro = isset($_GET['status']) ? intval($_GET['status']) : 1; // Padrão: 1 (Novos)

// Consulta SQL para selecionar os chamados com base no status e que estão ativos
$sql = "SELECT 
            c.codChamado, 
            c.descricao, 
            c.data, 
            c.imagem AS imagemChamado, 
            a.descricao AS ambiente, 
            e.descricao AS equipamentos, 
            r.respostaTexto, 
            r.imagem AS imagemResposta, 
            r.dataResposta,
            c.motivoInativacao  -- Adicionando a coluna motivoInativacao
        FROM Chamado c
        JOIN Ambiente a ON c.codAmbiente = a.codAmbiente
        JOIN Equipamentos e ON c.codEquipamentos = e.codEquipamentos
        LEFT JOIN Resposta r ON c.codChamado = r.codChamado
        WHERE c.codStatus = :statusFiltro
        ORDER BY c.data DESC";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':statusFiltro', $statusFiltro, PDO::PARAM_INT);
$stmt->execute();
$chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CMTEC - ADMIN</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <link rel="stylesheet" href="../assets/css/usuario.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
.card {
  width: 100%;
  max-width: 100%;
  position: relative;
  transition: transform 0.3s ease, background-color 0.3s ease; /* Adiciona transições suaves para transformação e cor */
}

.card:hover {

  background-color: #f0f0f0; /* Altera a cor de fundo do card ao passar o mouse */
}

.card-img {
  width: 150px;
  height: auto;
  object-fit: cover;
  box-shadow: 0 4px 8px rgba(0, 0, 0.2, 0.1); /* Adiciona uma sombra sutil à imagem */
}

.btn-group {
  position: absolute;
  top: 0;
  end: 0;
}

.square-btn {
  width: 40px; /* Ajuste o tamanho conforme necessário */
  height: 40px; /* Ajuste o tamanho conforme necessário */
  border-radius: 0 50% 0% 10%; /* Arredonda apenas o canto superior direito */
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0; /* Remove o padding interno */
  background-color: #00a1ff; /* Cor do botão */
  color: white; /* Cor do texto do botão */
  border: none; /* Remove a borda padrão do botão */
  transition: background-color 0.3s ease; /* Suaviza a transição de cor */
}

.square-btn:hover {
  background-color: #0088cc; /* Cor quando o botão é hover */
}

.square-btn:focus {
  background-color: #005f99; /* Cor quando o botão está focado */
  outline: none; /* Remove o contorno padrão de foco */
}

.square-btn:active {
  background-color: #004080; /* Cor quando o botão é clicado */
}

@media (max-width: 768px) {
  .card {
    flex-direction: column;
    text-align: center;
  }

  .card-img {
    margin: 0 auto;
    width: 100%;
    height: 200px;
  }

  .btn-group {
    position: relative;
    margin-top: 10px;
    justify-content: center;
  }
}
.btn-view-image {
  width: 15%;
  height:30px;
  font-size:14px;
  align-items:center;
  justify-content:center;
  display:flex;

}
.btn-custom {
      background-color: #00a1ff;
      color: white;
    }

    .btn-custom:hover {
      background-color: #007bb5;
      color: white;
    }

    .modal-body {
      text-align: center;
    }

    .modal-body .img-fluid {
      max-width: 100%;
      height: auto;
    }
    
    .modal-header {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .modal-title {
      margin: 0;
    }
    
    .modal-footer {
      justify-content: center;
    }

    /* Definindo a fonte personalizada para o card */


/* Charm extra: efeito de hover */
.custom-card:hover {
  background-color: #e9ecef;
  transform: scale(1.02);
  transition: all 0.3s ease-in-out;
}
.alert-card {
            position: fixed;
            top: 20px; /* Distância do topo */
            left: 50%;
            transform: translateX(-50%); /* Centraliza horizontalmente */
            z-index: 1050; /* Acima de outros elementos */
            width: 80%; /* Largura do card */
            max-width: 400px; /* Largura máxima do card */
            font-size: 18px;
            height: 80px;
            display: none; /* Inicialmente escondido */
        }
        .card-body {
            display: flex;
            align-items: center; /* Alinha verticalmente no centro */
            justify-content: center; /* Alinha horizontalmente no centro */
        }

  </style>
</head>

<body>
   <!-- Alerta de Sucesso -->
   <div class="card alert-card bg-success text-white" role="alert" id="successAlert">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-2x mr-2"></i> <!-- Ícone de sucesso -->
                <span>Chamado criado com sucesso!</span>
            </div>
            <button type="button" class="close text-white" aria-label="Fechar" onclick="fecharAlerta('successAlert')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>


    <!-- Alerta de Erro -->
    <div class="card alert-card bg-danger text-white" role="alert" id="errorAlertDominio">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x mr-2"></i> <!-- Ícone de erro -->
                <span>'Erro ao criar o chamado tente novamente.</span>
            </div>
            <button type="button" class="close text-white" aria-label="Fechar" onclick="fecharAlerta('errorAlertDominio')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        // Função para fechar o alerta
        function fecharAlerta(alertId) {
            $('#' + alertId).fadeOut(500); // Efeito fade out ao fechar
        }

        // Função para verificar a URL e mostrar o alerta se necessário
        function verificarAlertasNaURL() {
            const params = new URLSearchParams(window.location.search);
            if (params.has('success')) {
                $('#successAlert').fadeIn(800); // Exibe o alerta de sucesso
                setTimeout(function() {
                    fecharAlerta('successAlert'); // Fecha o alerta após 3 segundos
                }, 3000);
            }
            if (params.has('error')) {
                $('#errorAlert').fadeIn(800); // Exibe o alerta de erro
                setTimeout(function() {
                    fecharAlerta('errorAlert'); // Fecha o alerta após 3 segundos
                }, 3000);
            }
        }

        // Chama a função ao carregar a página
        $(document).ready(function() {
            verificarAlertasNaURL();
        });
    </script>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between" >
          <a href="index.php" class="text-nowrap logo-img" >
            <h2>CMTEC ADMIN</h2>
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="nav-small-cap">
              <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
              <span class="hide-menu">Home</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="index.php" aria-expanded="false">
                <iconify-icon icon="solar:widget-add-line-duotone"></iconify-icon>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li>
              <span class="sidebar-divider lg"></span>
            </li>
            <li class="nav-small-cap">
              <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
              <span class="hide-menu">Componentes</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="chamados.php" aria-expanded="false">
                <iconify-icon icon="solar:layers-minimalistic-bold-duotone"></iconify-icon>
                <span class="hide-menu">Chamados</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="usuarios.php" aria-expanded="false">
                <iconify-icon icon="solar:danger-circle-line-duotone"></iconify-icon>
                <span class="hide-menu">Usuarios</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="ambientes.php" aria-expanded="false">
                <iconify-icon icon="solar:bookmark-square-minimalistic-line-duotone"></iconify-icon>
                <span class="hide-menu">Ambientes</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="equipamentos.php" aria-expanded="false">
                <iconify-icon icon="solar:bookmark-square-minimalistic-line-duotone"></iconify-icon>
                <span class="hide-menu">Equipamentos</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="estatisticas.php" aria-expanded="false">
                <iconify-icon icon="solar:file-text-line-duotone"></iconify-icon>
                <span class="hide-menu">Estatisticas</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="FAQ.php" aria-expanded="false">
                <iconify-icon icon="solar:text-field-focus-line-duotone"></iconify-icon>
                <span class="hide-menu">FAQs</span>
              </a>
            </li>
            <li>
              <span class="sidebar-divider lg"></span>
            </li>           
            <li class="sidebar-item">
              <a class="sidebar-link" href="../../deslogar.php" aria-expanded="false">
                <iconify-icon icon="solar:login-3-line-duotone"></iconify-icon>
                <span class="hide-menu">Sair</span>
              </a>
            </li>         
                    
        </nav>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler " id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <li class="nav-item dropdown">
                <a class="nav-link " href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <img src="../assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                  <div class="message-body">
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">Meu perfil</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-list-check fs-6"></i>
                      <p class="mb-0 fs-3">Mensagens</p>
                    </a>
                    <a href="../../deslogar.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Sair</a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!--  Header End -->
      <div class="body-wrapper-inner">
        <div class="container-fluid">
        <div class="container mt-5" style="text-align: center;">
    <div class="card-body">
        <h3 class="card-title">Gerenciamento de Chamado</h3>

        <?php 
            // Verifica o status selecionado e ajusta o título
            $statusTitulo = 'Total de chamados novos';
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 1) {
                    $statusTitulo = 'Total de chamados novos';
                } elseif ($_GET['status'] == 2) {
                    $statusTitulo = 'Total de chamados vistos';
                } elseif ($_GET['status'] == 3) {
                    $statusTitulo = 'Total de chamados concluídos';
                } elseif ($_GET['status'] == 4) {
                  $statusTitulo = 'Total de chamados Inativos';
              }
                
            }
        ?>

        <!-- Exibe o título com base no filtro -->
        <h5 class="card-title"><?php echo $statusTitulo; ?>: <?php echo count($chamados); ?></h5>
    </div>
</div>

<div class="container mt-5" style="display: flex; justify-content: center;">
    <!-- Botões de filtro -->
    <div class="btn-group mb-3" role="group" aria-label="Filtro de Chamados">
        <a href="?status=1" class="btn" style="background-color: #007BFF; color: white; border-radius: 10px; margin: 0 5px; padding: 10px 20px; text-align: center;">Novos</a>
        <a href="?status=2" class="btn" style="background-color: #FFC107; color: black; border-radius: 10px; margin: 0 5px; padding: 10px 20px; text-align: center;">Em andamento</a>
        <a href="?status=3" class="btn" style="background-color: #28A745; color: white; border-radius: 10px; margin: 0 5px; padding: 10px 20px; text-align: center;">Concluídos</a>
        <a href="?status=4" class="btn" style="background-color: #FF0000; color: white; border-radius: 10px; margin: 0 5px; padding: 10px 20px; text-align: center;">Inativos</a>
    </div>
</div>



<?php foreach ($chamados as $chamado): ?>
<div class="card position-relative d-flex flex-row align-items-center p-3 mb-3">
    <!-- Exibição da imagem do chamado -->
    <?php if (!empty($chamado['imagemChamado'])): ?>
        <img src="../../../uploads/chamados/<?php echo htmlspecialchars($chamado['imagemChamado']); ?>" alt="Imagem do card" class="card-img me-3">
    <?php else: ?>
        <img src="../../../uploads/semImagem.jpeg" alt="Imagem Padrão" class="img-fluid" width="160px">
    <?php endif; ?>

    <div class="card-body d-flex flex-column">
        <h5 class="card-title">Código do Chamado: <?php echo htmlspecialchars($chamado['codChamado']); ?></h5>
        <p class="card-text">Descrição: <?php echo htmlspecialchars($chamado['descricao']); ?></p>
        <p class="card-text">Ambiente: <?php echo htmlspecialchars($chamado['ambiente']); ?></p>
        <p class="card-text">Equipamentos: <?php echo htmlspecialchars($chamado['equipamentos']); ?></p>
        <p class="card-text">Data: <?php echo htmlspecialchars(date('d/m/Y', strtotime($chamado['data']))); ?></p>

        <!-- Exibe a resposta, se houver e o status for 3 -->
        <?php if ($statusFiltro == 3): ?>
            <?php if (!empty($chamado['respostaTexto'])): ?>
                <div class="alert alert-info mt-2">
                    <strong>Resposta:</strong> <?php echo htmlspecialchars($chamado['respostaTexto']); ?>
                    <br>
                    <strong>Data:</strong> <?php echo htmlspecialchars($chamado['dataResposta']); ?>
                    <?php if (!empty($chamado['imagemResposta'])): ?>
                        <div class="mt-2">
                            <img src="../../../uploads/resposta/<?php echo htmlspecialchars($chamado['imagemResposta']); ?>" alt="Imagem da Resposta" class="img-fluid" width="160px">
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Exibe o motivo de inativação se o status for 4 -->
        <?php if ($statusFiltro == 4 && !empty($chamado['motivoInativacao'])): ?>
            <div class="alert alert-danger mt-2">
                <strong>Motivo da Inativação:</strong> <?php echo htmlspecialchars($chamado['motivoInativacao']); ?>
            </div>
        <?php endif; ?>

        <button type="button" class="btn btn-primary btn-sm btn-view-image" data-bs-toggle="modal" data-bs-target="#modalImagem-<?php echo $chamado['codChamado']; ?>">Ver Imagem</button>

        <!-- Modal de exibição de imagem -->
        <div class="modal fade" id="modalImagem-<?php echo $chamado['codChamado']; ?>" tabindex="-1" aria-labelledby="modalImagemLabel-<?php echo $chamado['codChamado']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalImagemLabel-<?php echo $chamado['codChamado']; ?>">Imagem do Chamado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                       <?php if (!empty($chamado['imagemChamado'])): ?>
                            <img src="../../../uploads/chamados/<?php echo htmlspecialchars($chamado['imagemChamado']); ?>" alt="Imagem do chamado" class="img-fluid">
                        <?php else: ?>
                            <img src="../../../uploads/semImagem.jpeg" alt="Imagem Padrão" class="img-fluid" width="160px">
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-custom" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($chamado['codChamado'] != 2 && $chamado['codChamado'] != 3): ?>
            <div class="btn-group position-absolute top-0 end-0" role="group">
                <button type="button" class="btn btn-secondary square-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <?php if ($statusFiltro == 4): ?>
                        <li>
                            <form action="../../../DAO/chamado/chamadoPatch.php" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar este chamado?');">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($chamado['codChamado']); ?>">
                                <input type="hidden" name="status" value="2">
                                <button type="submit" class="dropdown-item">Ativar Chamado</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <?php if ($statusFiltro != 3): ?>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#responderModal<?php echo $chamado['codChamado']; ?>">Responder</a></li>
                        <?php endif; ?>
                        <?php if ($statusFiltro != 2 && $statusFiltro != 3): ?>
                            <li>
                                <form action="../../../DAO/chamado/chamadoPatch.php" method="POST" onsubmit="return confirm('Tem certeza que deseja marcar como Visto este chamado?');">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($chamado['codChamado']); ?>">
                                    <input type="hidden" name="status" value="2">
                                    <button type="submit" class="dropdown-item">Visto</button>
                                </form>
                            </li>
                        <?php endif; ?>
                        <?php if ($statusFiltro == 3): ?>
                            <li>
                                <a href="../../../DAO/resposta/respostaDelete.php?id=<?php echo htmlspecialchars($chamado['codChamado']); ?>" class="dropdown-item" onclick="return confirm('Tem certeza que deseja excluir a resposta do chamado?');">Deletar Resposta</a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#motivoInativarModal<?php echo $chamado['codChamado']; ?>">Deletar Chamado</button>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Modal para Responder -->
        <div class="modal fade" id="responderModal<?php echo $chamado['codChamado']; ?>" tabindex="-1" aria-labelledby="responderModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="responderModalLabel">Responder ao Chamado #<?php echo $chamado['codChamado']; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="../../../DAO/resposta/respostaPost.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" name="codChamado" value="<?php echo $chamado['codChamado']; ?>">
                            <input type="hidden" name="descricaoChamado" value="<?php echo htmlspecialchars($chamado['descricao']); ?>">
                            <input type="hidden" name="ambienteChamado" value="<?php echo htmlspecialchars($chamado['ambiente']); ?>">
                            <input type="hidden" name="equipamentoChamado" value="<?php echo htmlspecialchars($chamado['equipamentos']); ?>">
                            <input type="hidden" name="dataChamado" value="<?php echo htmlspecialchars($chamado['data']); ?>">

                            <div class="mb-3">
                                <label for="respostaTexto" class="form-label">Resposta</label>
                                <textarea class="form-control" id="respostaTexto" name="respostaTexto" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="imagem" class="form-label">Anexar Imagem (opcional)</label>
                                <input type="file" class="form-control" id="imagem" name="imagem">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Enviar Resposta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para Motivo de Inativação -->
        <div class="modal fade" id="motivoInativarModal<?php echo $chamado['codChamado']; ?>" tabindex="-1" aria-labelledby="motivoInativarModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="motivoInativarModalLabel">Motivo para Inativar o Chamado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="../../../DAO/chamado/chamadoMotivo.php" method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($chamado['codChamado']); ?>">
                            <div class="mb-3">
                                <label for="motivoInativacao" class="form-label">Motivo</label>
                                <textarea class="form-control" id="motivoInativacao" name="motivoInativacao" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Inativar Chamado</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>




</div>


        </div>
        </div>
      </div>
    </div>
  </div>
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
  <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="../assets/js/dashboard.js"></script>
   <!-- Scripts -->
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>