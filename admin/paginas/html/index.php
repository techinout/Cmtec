<?php
session_start();
include '../../../Controller/conexao.php'; 
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

$sql = "SELECT codAluno, nome, email FROM Aluno";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT codChamado FROM Chamado";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT COUNT(*) AS total_enviados FROM Chamado WHERE codStatus = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
$total_enviados = $resultado['total_enviados'];

$sql = "SELECT COUNT(*) AS total_chamados FROM Chamado WHERE codStatus != 4";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
$total_chamados = $resultado['total_chamados'];

$sql = "SELECT COUNT(*) AS total_inativo FROM Chamado WHERE codStatus = 4";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
$total_inativo = $resultado['total_inativo'];

$sql = "SELECT COUNT(*) AS total_respondidos FROM Chamado WHERE codStatus = 3"; // 3 = Concluído
$stmt = $pdo->prepare($sql);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
$total_respondidos = $resultado['total_respondidos'];

$sql = "SELECT COUNT(*) AS total_ambientes FROM Ambiente";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
$total_ambientes = $resultado['total_ambientes'];

$sql = "SELECT COUNT(*) AS total_equipamentos FROM Equipamentos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
$total_equipamentos = $resultado['total_equipamentos'];

?>

<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CMTEC - ADMIN</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
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
  <div class="container-fluid" >
  <div class="row" style="margin-bottom : 50px;">
  <div class="col-lg-4">
    <div class="card overflow-hidden hover-img" style="border: none; margin: 0; padding: 0; background-color: #E0F7FA; text-align: center;"> <!-- Cor de fundo para Chamados -->
      <a href="chamados.php" style="display: block; text-decoration: none; color: inherit; padding: 20px;"> <!-- Adicionando padding -->
        <div class="position-relative">
          <i class="fa fa-ticket fa-3x" style="color: #00796B;"></i> <!-- Ícone de Chamados -->
        </div>
        <div class="card-body" style="padding: 10px;"> <!-- Margem entre o card e os itens -->
          <p class="d-block my-2 fs-5 text-dark fw-semibold link-primary" style="margin: 0;">Chamados</p> <!-- Reduzindo a margem -->
        </div>
      </a>
    </div>
  </div>
  
  <div class="col-lg-4">
    <div class="card overflow-hidden hover-img" style="border: none; margin: 0; padding: 0; background-color: #FFEBEE; text-align: center;"> <!-- Cor de fundo para Usuários -->
      <a href="usuarios.php" style="display: block; text-decoration: none; color: inherit; padding: 20px;"> <!-- Adicionando padding -->
        <div class="position-relative">
          <i class="fa fa-users fa-3x" style="color: #D32F2F;"></i> <!-- Ícone de Usuários -->
        </div>
        <div class="card-body" style="padding: 10px;"> <!-- Margem entre o card e os itens -->
          <p class="d-block my-2 fs-5 text-dark fw-semibold link-primary" style="margin: 0;">Usuários</p> <!-- Reduzindo a margem -->
        </div>
      </a>
    </div>
  </div>
  
  <div class="col-lg-4">
    <div class="card overflow-hidden hover-img" style="border: none; margin: 0; padding: 0; background-color: #FFF3E0; text-align: center;"> <!-- Cor de fundo para Ambientes -->
      <a href="ambientes.php" style="display: block; text-decoration: none; color: inherit; padding: 20px;"> <!-- Adicionando padding -->
        <div class="position-relative">
          <i class="fa fa-building fa-3x" style="color: #FF9800;"></i> <!-- Ícone de Ambientes -->
        </div>
        <div class="card-body" style="padding: 10px;"> <!-- Margem entre o card e os itens -->
          <p class="d-block my-2 fs-5 text-dark fw-semibold link-primary" style="margin: 0;">Ambientes</p> <!-- Reduzindo a margem -->
        </div>
      </a>
    </div>
  </div>
</div>

    <div class="row">
      <!-- Card Total Usuários -->
      <div class="col-md-6" style=margin-top:-15px;>
        <div class="card bg-primary-subtle shadow-none w-100"> <!-- Cor de fundo azul claro -->
          <div class="card-body">
            <div class="d-flex mb-10 pb-1 justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-6">
                <div class="rounded-circle-shape bg-primary px-3 py-2 rounded-pill d-inline-flex align-items-center justify-content-center"> <!-- Azul -->
                  <iconify-icon icon="carbon:user-avatar-filled-alt" class="fs-7 text-white"></iconify-icon> <!-- Ícone de usuários -->
                </div>
                <h6 class="mb-0 fs-4 fw-medium text-muted">
                  Total Usuários
                </h6>
              </div>
            </div>
            <div class="row align-items-end justify-content-between">
              <div class="col-5">
                <h2 class="mb-6 fs-8"><?php echo count($alunos); ?></h2>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card Total Chamados -->
      <div class="col-md-6" style=margin-top:-15px;>
        <div class="card bg-success-subtle shadow-none w-100"> <!-- Cor de fundo verde claro -->
          <div class="card-body">
            <div class="d-flex mb-10 pb-1 justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-6">
                <div class="rounded-circle-shape bg-success px-3 py-2 rounded-pill d-inline-flex align-items-center justify-content-center"> <!-- Verde -->
                  <iconify-icon icon="carbon:document" class="fs-7 text-white"></iconify-icon> <!-- Ícone de documento -->
                </div>
                <h6 class="mb-0 fs-4 fw-medium text-muted">
                  Total Chamados
                </h6>
              </div>
            </div>
            <div class="row align-items-end justify-content-between">
              <div class="col-5">
                <h2 class="mb-6 fs-8"><?php echo $total_chamados; ?></h2>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card Total Chamados Novos -->
      <div class="col-md-6" style=margin-top:-15px;>
        <div class="card bg-warning-subtle shadow-none w-100"> <!-- Cor de fundo laranja claro -->
          <div class="card-body">
            <div class="d-flex mb-10 pb-1 justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-6">
                <div class="rounded-circle-shape bg-warning px-3 py-2 rounded-pill d-inline-flex align-items-center justify-content-center"> <!-- Laranja -->
                  <iconify-icon icon="carbon:document-add" class="fs-7 text-white"></iconify-icon> <!-- Ícone de novo documento -->
                </div>
                <h6 class="mb-0 fs-4 fw-medium text-muted">
                  Total Chamados Novos
                </h6>
              </div>
            </div>
            <div class="row align-items-end justify-content-between">
              <div class="col-5">
                <h2 class="mb-6 fs-8"><?php echo $total_enviados; ?></h2>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card Total Chamados Respondidos -->
      <div class="col-md-6" style=margin-top:-15px;>
        <div class="card bg-info-subtle shadow-none w-100"> <!-- Cor de fundo azul claro -->
          <div class="card-body">
            <div class="d-flex mb-10 pb-1 justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-6">
                <div class="rounded-circle-shape bg-info px-3 py-2 rounded-pill d-inline-flex align-items-center justify-content-center"> <!-- Azul claro -->
                  <iconify-icon icon="carbon:reply" class="fs-7 text-white"></iconify-icon> <!-- Ícone de resposta -->
                </div>
                <h6 class="mb-0 fs-4 fw-medium text-muted">
                  Total Chamados Respondidos
                </h6>
              </div>
            </div>
            <div class="row align-items-end justify-content-between">
              <div class="col-5">
                <h2 class="mb-6 fs-8"><?php echo $total_respondidos; ?></h2> <!-- Mostra o total de chamados respondidos -->
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6" style="margin-top: -15px;">
    <div class="card bg-danger-subtle shadow-none w-100"> <!-- Cor de fundo vermelho claro -->
        <div class="card-body">
            <div class="d-flex mb-10 pb-1 justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-6">
                    <div class="rounded-circle-shape bg-danger px-3 py-2 rounded-pill d-inline-flex align-items-center justify-content-center"> <!-- Vermelho -->
                        <iconify-icon icon="carbon:warning" class="fs-7 text-white"></iconify-icon> <!-- Ícone de aviso -->
                    </div>
                    <h6 class="mb-0 fs-4 fw-medium text-muted">
                        Total Chamados Inativos
                    </h6>
                </div>
            </div>
            <div class="row align-items-end justify-content-between">
                <div class="col-5">
                    <h2 class="mb-6 fs-8"><?php echo $total_inativo; ?></h2> <!-- Mostra o total de chamados inativos -->
                </div>
            </div>
        </div>
    </div>
</div>


      <!-- Card Ambiente -->
      <div class="col-md-6" style=margin-top:-15px;>
        <div class="card bg-secondary-subtle shadow-none w-100"> <!-- Cor de fundo cinza claro -->
          <div class="card-body">
            <div class="d-flex mb-10 pb-1 justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-6">
                <div class="rounded-circle-shape bg-secondary px-3 py-2 rounded-pill d-inline-flex align-items-center justify-content-center"> <!-- Cinza -->
                  <iconify-icon icon="carbon:building" class="fs-7 text-white"></iconify-icon> <!-- Ícone de ambiente -->
                </div>
                <h6 class="mb-0 fs-4 fw-medium text-muted">
                  Ambiente
                </h6>
              </div>
            </div>
            <div class="row align-items-end justify-content-between">
              <div class="col-5">
                <h2 class="mb-6 fs-8"><?php echo $total_ambientes; ?></h2> <!-- Mostra o total de ambientes -->
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card Equipamento -->
      <div class="col-md-6" style=margin-top:-15px;>
        <div class="card bg-dark-subtle shadow-none w-100"> <!-- Cor de fundo cinza escuro claro -->
          <div class="card-body">
            <div class="d-flex mb-10 pb-1 justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-6">
                <div class="rounded-circle-shape bg-dark px-3 py-2 rounded-pill d-inline-flex align-items-center justify-content-center"> <!-- Cinza escuro -->
                  <iconify-icon icon="carbon:tools" class="fs-7 text-white"></iconify-icon> <!-- Ícone de equipamento -->
                </div>
                <h6 class="mb-0 fs-4 fw-medium text-muted">
                  Equipamento
                </h6>
              </div>
            </div>
            <div class="row align-items-end justify-content-between">
              <div class="col-5">
                <h2 class="mb-6 fs-8"><?php echo $total_equipamentos; ?></h2> <!-- Mostra o total de equipamentos -->
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div> <!-- .row -->

    


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
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>