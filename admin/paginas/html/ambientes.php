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

$sql = "SELECT codAmbiente, descricao FROM Ambiente";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$ambientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">


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
        <div class="container-fluid">
          <!--  Row 1 -->
          <div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-xs-5">
                    <h2>Gerenciamento de <b>Ambientes</b></h2>
                </div>
            </div>
        </div>
        <table id="ambientesTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ambientes as $ambiente): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ambiente['codAmbiente']); ?></td>
                        <td><?php echo htmlspecialchars($ambiente['descricao']); ?></td>
                        <td class="actions">
                            <!-- Botão de Editar -->
                            <a href="#" class="fa fa-edit" title="Editar" data-toggle="modal" data-target="#editModal<?php echo $ambiente['codAmbiente']; ?>"></a>

                            <!-- Botão de Deletar -->
                            <a href="../../../DAO/ambiente/ambienteDelete.php?id=<?php echo htmlspecialchars($ambiente['codAmbiente']); ?>" class="fa fa-trash" title="Excluir" data-toggle="tooltip" onclick="return confirm('Tem certeza que deseja excluir este ambiente?');"></a>
                            
                            <!-- Modal de Edição -->
                            <div class="modal fade" id="editModal<?php echo $ambiente['codAmbiente']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $ambiente['codAmbiente']; ?>" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?php echo $ambiente['codAmbiente']; ?>">Editar Ambiente</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="../../../DAO/ambiente/ambientePatch.php" method="post">
                                                <div class="form-group mb-4">
                                                    <label for="descricao<?php echo $ambiente['codAmbiente']; ?>">Descrição</label>
                                                    <input type="text" class="form-control" id="descricao<?php echo $ambiente['codAmbiente']; ?>" name="descricao" value="<?php echo htmlspecialchars($ambiente['descricao']); ?>">
                                                </div>
                                                <input type="hidden" name="codAmbiente" value="<?php echo $ambiente['codAmbiente']; ?>">
                                                <button type="submit" class="btn btn-primary w-100">Atualizar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="clearfix">
            <button type="button" class="btn btn-primary mt-3 w-100" data-toggle="modal" data-target="#addAmbienteModal">Adicionar Ambiente</button>
            <div class="hint-text mt-2">Mostrando <b><?php echo count($ambientes); ?></b> registros</div>
        </div>
    </div>
</div>


<!-- Modal de Adição -->
<div class="modal fade" id="addAmbienteModal" tabindex="-1" role="dialog" aria-labelledby="addAmbienteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAmbienteModalLabel">Adicionar Novo Ambiente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="../../../DAO/ambiente/ambientePost.php" method="post">
                    <div class="form-group mb-4">
                        <label for="descricaoNovo">Nome do Ambiente</label>
                        <input type="text" class="form-control" id="descricaoNovo" name="descricao" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Adicionar</button>
                </form>
            </div>
        </div>
    </div>
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
   <script src="assets/js/ambientes.js" type="module"></script>

  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <!-- Adicione isso na seção <head> -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
        $('#ambientesTable').DataTable({
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "Nenhum registro encontrado",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "Nenhum registro disponível",
                "infoFiltered": "(filtrado de _MAX_ registros no total)",
                "search": "Buscar:",
                "paginate": {
                    "next": "Próximo",
                    "previous": "Anterior"
                }
            }
        });
    });
</script>

</body>

</html>