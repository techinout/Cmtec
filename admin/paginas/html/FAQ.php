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
           <!-- FAQ Accordion -->
           <div class="accordion accordion-flush" id="accordionFlushExample">
                <!-- Navegação no Site -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseNavigation" aria-expanded="false" aria-controls="flush-collapseNavigation">
                            Navegação no Site
                        </button>
                    </h2>
                    <div id="flush-collapseNavigation" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <!-- FAQ Item 1 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                        Como faço para navegar no site?
                                    </button>
                                </h2>
                                <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#flush-collapseNavigation">
                                    <div class="accordion-body">Utilize a barra de navegação lateral para acessar as diferentes seções do site. Clique nos itens de menu para visualizar as páginas correspondentes.</div>
                                </div>
                            </div>

                            <!-- FAQ Item 2 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                        Como faço para ver as estatísticas?
                                    </button>
                                </h2>
                                <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#flush-collapseNavigation">
                                    <div class="accordion-body">Clique na aba "Estatísticas" na barra de navegação lateral para visualizar gráficos e dados sobre o desempenho do sistema.</div>
                                </div>
                            </div>

                            <!-- FAQ Item 3 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                        Como faço para acessar a tabela de usuários?
                                    </button>
                                </h2>
                                <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#flush-collapseNavigation">
                                    <div class="accordion-body">Clique na aba "Usuários" na barra de navegação lateral para acessar a tabela de usuários registrados no sistema.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações do Sistema -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSettings" aria-expanded="false" aria-controls="flush-collapseSettings">
                            Configurações do Sistema
                        </button>
                    </h2>
                    <div id="flush-collapseSettings" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <!-- FAQ Item 4 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                                        Como posso alterar as configurações do sistema?
                                    </button>
                                </h2>
                                <div id="flush-collapseFour" class="accordion-collapse collapse" data-bs-parent="#flush-collapseSettings">
                                    <div class="accordion-body">Acesse a aba "Configurações" na barra de navegação lateral para alterar as configurações do sistema, como preferências de usuário e ajustes gerais.</div>
                                </div>
                            </div>

                            <!-- FAQ Item 10 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTen" aria-expanded="false" aria-controls="flush-collapseTen">
                                        Como posso redefinir minha senha?
                                    </button>
                                </h2>
                                <div id="flush-collapseTen" class="accordion-collapse collapse" data-bs-parent="#flush-collapseSettings">
                                    <div class="accordion-body">Vá até a aba "Configurações" e clique em "Redefinir Senha". Siga as instruções para criar uma nova senha.</div>
                                </div>
                            </div>

                            <!-- FAQ Item 14 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFourteen" aria-expanded="false" aria-controls="flush-collapseFourteen">
                                        Como posso configurar notificações por e-mail?
                                    </button>
                                </h2>
                                <div id="flush-collapseFourteen" class="accordion-collapse collapse" data-bs-parent="#flush-collapseSettings">
                                    <div class="accordion-body">Acesse a seção "Notificações" nas configurações do sistema para ativar ou desativar notificações por e-mail e personalizar suas preferências.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Outras Perguntas Frequentes -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOther" aria-expanded="false" aria-controls="flush-collapseOther">
                            Outras Perguntas Frequentes
                        </button>
                    </h2>
                    <div id="flush-collapseOther" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <!-- FAQ Item 5 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFive" aria-expanded="false" aria-controls="flush-collapseFive">
                                        O que devo fazer se encontrar um erro?
                                    </button>
                                </h2>
                                <div id="flush-collapseFive" class="accordion-collapse collapse" data-bs-parent="#flush-collapseOther">
                                    <div class="accordion-body">Caso encontre um erro, entre em contato com o suporte técnico através da aba "Ajuda" para relatar o problema.</div>
                                </div>
                            </div>

                            <!-- FAQ Item 6 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSix" aria-expanded="false" aria-controls="flush-collapseSix">
                                        Como posso sugerir uma nova funcionalidade?
                                    </button>
                                </h2>
                                <div id="flush-collapseSix" class="accordion-collapse collapse" data-bs-parent="#flush-collapseOther">
                                    <div class="accordion-body">Para sugerir uma nova funcionalidade, vá até a aba "Sugestões" e envie sua proposta através do formulário disponível.</div>
                                </div>
                            </div>

                            <!-- FAQ Item 7 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSeven" aria-expanded="false" aria-controls="flush-collapseSeven">
                                        Qual é o horário de funcionamento do suporte?
                                    </button>
                                </h2>
                                <div id="flush-collapseSeven" class="accordion-collapse collapse" data-bs-parent="#flush-collapseOther">
                                    <div class="accordion-body">O suporte está disponível 24 horas por dia, 7 dias por semana para atender às suas necessidades.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contato e Suporte -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseContact" aria-expanded="false" aria-controls="flush-collapseContact">
                            Contato e Suporte
                        </button>
                    </h2>
                    <div id="flush-collapseContact" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <!-- FAQ Item 8 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseEight" aria-expanded="false" aria-controls="flush-collapseEight">
                                        Como posso entrar em contato com o suporte?
                                    </button>
                                </h2>
                                <div id="flush-collapseEight" class="accordion-collapse collapse" data-bs-parent="#flush-collapseContact">
                                    <div class="accordion-body">Você pode entrar em contato com o suporte através da aba "Contato" ou enviando um e-mail para suporte@exemplo.com.</div>
                                </div>
                            </div>

                            <!-- FAQ Item 9 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseNine" aria-expanded="false" aria-controls="flush-collapseNine">
                                        Existe um chat ao vivo disponível?
                                    </button>
                                </h2>
                                <div id="flush-collapseNine" class="accordion-collapse collapse" data-bs-parent="#flush-collapseContact">
                                    <div class="accordion-body">Sim, o chat ao vivo está disponível na página de suporte durante o horário comercial.</div>
                                </div>
                            </div>

                            <!-- FAQ Item 12 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwelve" aria-expanded="false" aria-controls="flush-collapseTwelve">
                                        Como posso enviar um feedback sobre o sistema?
                                    </button>
                                </h2>
                                <div id="flush-collapseTwelve" class="accordion-collapse collapse" data-bs-parent="#flush-collapseContact">
                                    <div class="accordion-body">Você pode enviar seu feedback através da aba "Feedback" ou completando o formulário disponível no site.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sugestões e Melhorias -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSuggestions" aria-expanded="false" aria-controls="flush-collapseSuggestions">
                            Sugestões e Melhorias
                        </button>
                    </h2>
                    <div id="flush-collapseSuggestions" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <!-- FAQ Item 11 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseEleven" aria-expanded="false" aria-controls="flush-collapseEleven">
                                        Como posso sugerir melhorias para o sistema?
                                    </button>
                                </h2>
                                <div id="flush-collapseEleven" class="accordion-collapse collapse" data-bs-parent="#flush-collapseSuggestions">
                                    <div class="accordion-body">Você pode sugerir melhorias acessando a aba "Sugestões" e preenchendo o formulário para enviar suas propostas.</div>
                                </div>
                            </div>

                            <!-- FAQ Item 13 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThirteen" aria-expanded="false" aria-controls="flush-collapseThirteen">
                                        Posso contribuir para o desenvolvimento do sistema?
                                    </button>
                                </h2>
                                <div id="flush-collapseThirteen" class="accordion-collapse collapse" data-bs-parent="#flush-collapseSuggestions">
                                    <div class="accordion-body">Sim, você pode contribuir para o desenvolvimento do sistema participando de programas de feedback ou colaborando com a equipe de desenvolvimento.</div>
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
   <script src="./js/usuario.js" type="module"></script>

  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>