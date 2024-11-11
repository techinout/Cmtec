<?php
session_start();
include "../Controller/conexao.php";

// Verifica a validade do token da sessão
if (!isset($_SESSION['token'])) {
    // Se o token não estiver presente, redireciona para a página de login
    header('Location: ../login.html');
    exit;
}

// Divide o token em sua parte principal e tipo (admin ou aluno)
list($token, $type) = explode('_', $_SESSION['token'], 2);

// Verifica se o tipo do token é 'aluno'
if ($type !== 'aluno') {
    // Se o tipo não for 'aluno', redireciona para a página de login
    header('Location: ../login.html');
    exit;
}

$user = $_SESSION['user'];

$sql = "SELECT codAmbiente, descricao FROM Ambiente";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$ambientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT codEquipamentos, descricao FROM Equipamentos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$equipamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Obtém os dados do aluno logado
$codAluno = $_SESSION['user']['codAluno'];

$sql = "SELECT * FROM Aluno WHERE codAluno = :codAluno";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':codAluno', $codAluno, PDO::PARAM_INT);
$stmt->execute();
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="pt-br">

    <head>
        <meta charset="utf-8">
        <title>CMTEC - CHAMADOS</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">
        <link rel="icon" href="img/c.png" type="image/png">
                <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link href="lib/animate/animate.min.css" rel="stylesheet">
        <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="css/style.css" rel="stylesheet">

   
            <!-- ... outros links e meta tags ... -->
            <style>
                .nav-link:hover,
                .footer-item a:hover {
                    color: #3E5FFE; /* Azul para links */
                    text-decoration: underline; /* Adiciona um sublinhado para melhor visualização */
                }
        
                /* Botões */
            
                .text-primary {
                    color: #3E5FFE !important;
                }
        
                .btn-primary {
                    background-color: #3E5FFE;
                    border-color: #3E5FFE;
                }
        
                .navbar-light .navbar-brand {
                    color: #3E5FFE;
                }
        
                .navbar-nav .nav-link.active {
                    color: #3E5FFE;
                }
        
                .footer-item h4 {
                    color: #3E5FFE;
                }
        
                .footer-item a {
                    color: #3E5FFE;
                }
        
                .btn-light {
                    background-color: #3E5FFE;
                    color: #fff;
                }
        
                .btn-light:hover {
                    background-color: #3457D0;
                    color: #fff;
                }
        
                :root {
                    --bs-primary: #3E5FFE; /* Azul */
                    --bs-secondary: #F0F0F0; /* Exemplo de cor secundária */
                    --bs-dark: #000000; /* Exemplo de cor escura */
                    --bs-light: #FFFFFF; /* Exemplo de cor clara */
                    --bs-white: #FFFFFF; /* Branco */
                    --bs-body: #6c757d; /* Exemplo de cor do texto */
                }
        
                .footer .footer-item .bg-primary {
                    background-color: #000000; /* Fundo preto */
                    color: #ffffff; /* Texto branco */
                }
        
                .footer .footer-item .bg-primary:hover {
                    background-color: #333333; /* Fundo preto mais escuro ao passar o mouse */
                    color: #ffffff; /* Texto branco ao passar o mouse */
                }
        
                /* Estilo do footer */
                .footer {
                    background-color: #212529; /* Cor de fundo escura */
                    color: #ffffff; /* Cor do texto branco */
                }
        
                .footer .footer-item h4,
                .footer .footer-item a {
                    color: #ffffff; /* Cor do texto branco */
                }
        
                .footer .footer-item a:hover {
                    color: #3E5FFE; /* Azul para links ao passar o mouse */
                }
        
                /* Estilo do copyright */
                .copyright {
                    background-color: #000000; /* Cor de fundo preta */
                    color: #ffffff; /* Cor do texto branca */
                }
        
                .copyright a {
                    color: #ffffff; /* Cor do texto branca para links */
                }
        
                .copyright a:hover {
                    color: #3E5FFE; /* Azul para links ao passar o mouse */
                }
                /* Cor e borda dos botões do FAQ ao serem clicados */
                .form-container {
            max-width: 600px;
            margin: 0 auto;
        }

        /* Estilo personalizado para foco */
        .form-control:focus, .form-select:focus {
            border-color: #3E5FFE;
            box-shadow: 0 0 5px rgba(62, 95, 254, 0.5);
        }

        /* Estilo moderno para o dropdown */
        .form-select {
            appearance: none;
            background-image: url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/svgs/solid/chevron-down.svg');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 15px;
            padding-right: 40px;
        }

        /* Ajustar a largura do select para parecer mais moderno */
        .form-select {
            padding: 0.75rem 1.25rem;
            border-radius: 0.375rem;
            border: 2px solid #ced4da;
            transition: border-color 0.3s ease-in-out;
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


        <!-- Topbar Start -->
        <div class="container-fluid topbar bg-light px-5 d-none d-lg-block">
            <div class="row gx-0 align-items-center">
                <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                    <div class="d-flex flex-wrap">
                    <a href="mailto:example@gmail.com" class="text-muted small me-0"><i class="fas fa-envelope text-primary me-2"></i><?php echo htmlspecialchars($user['email']); ?></a>
                    </div>
                </div>
                <div class="col-lg-4 text-center text-lg-end">
                    <div class="d-inline-flex align-items-center" style="height: 45px;">
                       
                    </div>
                </div>
            </div>
        </div>
        <!-- Topbar End -->

        <!-- Navbar & Hero Start -->
        <div class="container-fluid position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
                <a href="index.php" class="navbar-brand p-0">
                    <h1 class="text-primary"></i>CMTEC</h1>
                    <!-- <img src="img/logo.png" alt="Logo"> -->
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="index.php" class="nav-item nav-link" style="color: #000; text-decoration: none; background-color: transparent;" 
                        onmouseover="this.style.color='#000'; this.style.backgroundColor='transparent';" >Home</a>

                        <a href="chamados.php" class="nav-item nav-link" style="color: #000; text-decoration: none; background-color: transparent;" 
                        onmouseover="this.style.color='#000'; this.style.backgroundColor='transparent';" >Chamados</a>
                        <a href="ver_chamados.php" class="nav-item nav-link" style="color: #000; text-decoration: none; background-color: transparent;" 
       onmouseover="this.style.color='#3E5FFE'; this.style.backgroundColor='transparent';" 
       onmouseout="this.style.color='#000'; this.style.backgroundColor='transparent';">Ver Chamados</a>
                        <a href="perfil.php" class="nav-item nav-link" style="color: #000; text-decoration: none; background-color: transparent;" 
                        onmouseover="this.style.color='#000'; this.style.backgroundColor='transparent';" >Perfil</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link active" data-bs-toggle="dropdown"  style="color: #000; text-decoration: none; background-color: transparent;" 
                            onmouseover="this.style.color='#3E5FFE'; this.style.backgroundColor='transparent';" 
                            onmouseout="this.style.color='#000'; this.style.backgroundColor='transparent';">
                                <span class="dropdown-toggle" >Paginas</span>
                            </a>
                            <div class="dropdown-menu m-0">
                            <a href="chamados.php" class="dropdown-item"  style="background-color:#3E5FFE;  color:#fff;">Chamados</a>
                            <a href="ver_chamados.php" class="dropdown-item">Ver Chamados</a>
                                <a href="perfil.php" class="dropdown-item">Perfil</a>
                                <a href="FAQ.php" class="dropdown-item">FAQs</a>
                                <a href="../deslogar.php" class="dropdown-item active" style="background-color:#d9d9d9; color:#000;">Sair</a>

                            </div>
                        </div>
                    </div>
                </div>
            </nav>

        


        <!-- conteudo Start -->
        <div class="container py-5 overflow-hidden">
    <div class="row g-5 align-items-center">
        <div class="col-12">
            <div class="form-container">
                <h2 class="text-center mb-4" style="margin-top:40px">Formulário de Chamado</h2>
                <form action="../DAO/chamado/chamadoPost.php" method="POST" enctype="multipart/form-data">
                    <!-- Campo de Descrição -->
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="4" placeholder="Escreva sua mensagem" required></textarea>
                    </div>

                    <!-- Dropdown Local -->
                    <div class="mb-3">
                        <label for="local" class="form-label">Local</label>
                        <select class="form-select" id="local" name="local" required>
                            <option value="" selected disabled>Escolha um local</option>
                            <?php
                            // Itera sobre os ambientes e gera as opções do dropdown
                            foreach ($ambientes as $ambiente) {
                                echo "<option value='{$ambiente['codAmbiente']}'>{$ambiente['descricao']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Dropdown Equipamento -->
                    <div class="mb-3">
                        <label for="equipamento" class="form-label">Equipamento</label>
                        <select class="form-select" id="equipamento" name="equipamento" required>
                            <option value="" selected disabled>Escolha um equipamento</option>
                            <?php
                            // Itera sobre os equipamentos e gera as opções do dropdown
                            foreach ($equipamentos as $equipamento) {
                                echo "<option value='{$equipamento['codEquipamentos']}'>{$equipamento['descricao']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Campo de Upload de Imagens -->
                    <div class="mb-3">
                        <label for="imagens" class="form-label">Adicionar Imagens</label>
                        <input class="form-control" type="file" id="imagens" name="imagens[]" accept="image/*" multiple>
                    </div>

                    <!-- Botão de Enviar -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Enviar Chamado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



        <!-- conteudo End -->

        <div class="container-fluid footer py-5 wow fadeIn" data-wow-delay="0.2s">
            <div class="container py-5 border-start-0 border-end-0" style="border: 1px solid; border-color: rgb(255, 255, 255, 0.08);">
                <div class="row g-5">
                    <div class="col-md-6 col-lg-6 col-xl-4">
                        <div class="footer-item">
                            <a href="index.html" class="p-0">
                                <h4 class="text-white"></i>CMTEC</h4>
                     
                            </a>
                            <p class="mb-4">Em breve estrá disponivel uma aplicação mobile</p>
                          <div class="d-flex justify-content-center">
    <a href="#" style="background-color: #3E5FFE; color: #fff; display: flex; align-items: center; justify-content: center; border-radius: 0.25rem; padding: 0.75rem 1.5rem; margin-right: 0.5rem; text-decoration: none; font-size: 1.25rem;">
        <i class="fas fa-apple-alt" style="color: #fff; margin-right: 0.75rem; font-size: 1.5rem;"></i>
        <div>
            <h6 style="margin: 0; font-size: 1rem;">App Store</h6>
        </div>
    </a>
    <a href="#" style="background-color: #3E5FFE; color: #fff; display: flex; align-items: center; justify-content: center; border-radius: 0.25rem; padding: 0.75rem 1.5rem; margin-left: 0.5rem; text-decoration: none; font-size: 1.25rem;">
        <i class="fas fa-play" style="color: #fff; margin-right: 0.75rem; font-size: 1.5rem;"></i>
        <div>
            <h6 style="margin: 0; font-size: 1rem;" style="color: #fff;">Google Play</h6>
        </div>
    </a>
</div>


                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-2">
                        <div class="footer-item">
                            <h4 class="text-white mb-4" >Links Rápidos</h4>
                            <a href="#" style="color: #ffffff; text-decoration: none;" 
   onmouseover="this.style.color='#FFF';">
   <i class="fas fa-angle-right me-2"></i> About Us
</a>
<a href="chamados.php" style="color: #ffffff; text-decoration: none;" 
   onmouseover="this.style.color='#FFF';">
   <i class="fas fa-angle-right me-2"></i> Chamados
</a>
<a href="FAQ.php" style="color: #ffffff; text-decoration: none;" 
   onmouseover="this.style.color='#FFF';">
   <i class="fas fa-angle-right me-2"></i> FAQ
</a>
<a href="perfil.php" style="color: #ffffff; text-decoration: none;" 
   onmouseover="this.style.color='#FFF';">
   <i class="fas fa-angle-right me-2"></i> Perfil
</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="footer-item">
                            <h4 class="text-white mb-4">Suporte</h4>
                            <a href="#" style="color: #fff; text-decoration: none;" 
       onmouseover="this.style.color='#FFF';" 
 ><i class="fas fa-angle-right me-2"></i>Politicas de Privacidade</a>
                            <a href="#" style="color: #fff; text-decoration: none;" 
       onmouseover="this.style.color='#FFF';" 
      ><i class="fas fa-angle-right me-2"></i> Termos & Condições</a>
                            <a href="#" style="color: #fff; text-decoration: none;" 
       onmouseover="this.style.color='#FFF';" 
      ><i class="fas fa-angle-right me-2"></i> Support</a>
                            <a href="FAQ.php" style="color: #fff; text-decoration: none;" 
       onmouseover="this.style.color='#FFF';" 
       ><i class="fas fa-angle-right me-2"></i> FAQ</a>
                            <a href="#" style="color: #fff; text-decoration: none;" 
       onmouseover="this.style.color='#FFF';" 
 ><i class="fas fa-angle-right me-2" ></i> Help</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="footer-item">
                            <h4 class="text-white mb-4">Entre em contato</h4>
                            
                            <div class="d-flex align-items-center">
                                <i class="fas fa-envelope text-primary me-3"></i>
                                <p class="text-white mb-0">techinoutpro@gmail..com</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-phone-alt text-primary me-3"></i>
                                <p class="text-white mb-0">(+011) 98775 7890</p>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <i class="fab fa-firefox-browser text-primary me-3"></i>
                                <p class="text-white mb-0">techinout.com</p>
                            </div>
                            <div class="d-flex">
                                <a class="btn btn-primary btn-sm-square rounded-circle me-3" href="#"><i class="fab fa-facebook-f text-white"></i></a>
                                <a class="btn btn-primary btn-sm-square rounded-circle me-3" href="#"><i class="fab fa-twitter text-white"></i></a>
                                <a class="btn btn-primary btn-sm-square rounded-circle me-3" href="#"><i class="fab fa-instagram text-white"></i></a>
                                <a class="btn btn-primary btn-sm-square rounded-circle me-0" href="#"><i class="fab fa-linkedin-in text-white"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->
        
        <!-- Copyright Start -->
        <div class="container-fluid copyright py-4">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-md-0">
                        <span class="text-body"><a href="#" class="border-bottom text-white"><i class="fas fa-copyright text-light me-2"></i>TECHIN-OUT</a>, All right reserved.</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright End -->



        
        <!-- JavaScript Libraries -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="lib/wow/wow.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/waypoints/waypoints.min.js"></script>
        <script src="lib/counterup/counterup.min.js"></script>
        <script src="lib/lightbox/js/lightbox.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>
        

        <!-- Template Javascript -->
        <script src="js/main.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>

</html>