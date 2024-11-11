<?php
session_start();

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
?>
<!DOCTYPE html>
<html lang="pt-br">

    <head>
        <meta charset="utf-8">
        <title>CMTEC - ABRA SEU CHAMADO AQUI</title>
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
        <link rel="stylesheet" href="lib/animate/animate.min.css"/>
        <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="css/style.css" rel="stylesheet">
    </head>
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


    </style>

    <body>
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
        <!-- Navbar & Hero Start -->
        <div class="container-fluid position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
                <a href="" class="navbar-brand p-0">
                    <h1 class="text-primary">CMTEC</h1>
                    <!-- <img src="img/logo.png" alt="Logo"> -->
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="index.php" class="nav-item nav-link active" style="color: #000; text-decoration: none; background-color: transparent;" 
       onmouseover="this.style.color='#3E5FFE'; this.style.backgroundColor='transparent';" 
       onmouseout="this.style.color='#000'; this.style.backgroundColor='transparent';">Home</a>
       <a href="chamados.php" class="nav-item nav-link" style="color: #000; text-decoration: none; background-color: transparent;" 
       onmouseover="this.style.color='#3E5FFE'; this.style.backgroundColor='transparent';" 
       onmouseout="this.style.color='#000'; this.style.backgroundColor='transparent';">Chamados</a>
       <a href="ver_chamados.php" class="nav-item nav-link" style="color: #000; text-decoration: none; background-color: transparent;" 
       onmouseover="this.style.color='#3E5FFE'; this.style.backgroundColor='transparent';" 
       onmouseout="this.style.color='#000'; this.style.backgroundColor='transparent';">Ver Chamados</a>
                        <a href="perfil.php" class="nav-item nav-link" style="color: #000; text-decoration: none; background-color: transparent;" 
       onmouseover="this.style.color='#3E5FFE'; this.style.backgroundColor='transparent';" 
       onmouseout="this.style.color='#000'; this.style.backgroundColor='transparent';">Perfil</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link" style="color: #000; text-decoration: none; background-color: transparent;" 
       onmouseover="this.style.color='#3E5FFE'; this.style.backgroundColor='transparent';" 
       onmouseout="this.style.color='#000'; this.style.backgroundColor='transparent';" data-bs-toggle="dropdown">
                                <span class="dropdown-toggle" >Paginas</span>
                            </a>
                            <div class="dropdown-menu m-0">
                                <a href="chamados.php" class="dropdown-item">Chamados</a>
                                <a href="ver_chamados.php" class="dropdown-item">Ver Chamados</a>
                                <a href="perfil.php" class="dropdown-item">Perfil</a>
                                <a href="FAQ.php" class="dropdown-item">FAQs</a>
                                <a href="../deslogar.php" class="dropdown-item active" style="background-color:#d9d9d9; color:#000;">Sair</a>
                            </div>
                        </div>
                    </div>
    
                </div>
            </nav>

        
            <!-- Carousel End -->
        </div>
        <!-- Navbar & Hero End -->

        <!-- Services Start -->
        <div class="container-fluid service pb-5">
            <div class="container pb-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h1 class="display-5 mb-4" style="margin-top:100px;">Ajude a manter nossa ETEC funcionando</h1>
                </div>
                <div class="row g-4" style="margin-top:-80px">
                    <a href="chamados.php">
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="service-item">
                            <div class="service-img" style="height:280px">
                                <img src="img/3.png" class="img-fluid rounded-top w-100" alt="Image">
                            </div>
                            <div class="rounded-bottom p-4">
                                <a href="chamados.php" class="h4 d-inline-block mb-4"  style="color: #000; text-decoration: none;" onmouseover="this.style.color='#3E5FFE'; this.style.backgroundColor='transparent';" onmouseout="this.style.color='#000';">Abrir um Chamado</a>
                                <p class="mb-4">Faça aqui sua reclamação
                                </p>
                                <a class="btn btn-primary rounded-pill py-2 px-4" href="chamados.php" >Abrir Chamado</a>
                            </div>
                        </div>
                    </div>
    </a>
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.4s">
                        <div class="service-item">
                            <div class="service-img" style="height:280px">
                            <img src="img/1.png" class="img-fluid rounded-top w-100" alt="Image" style="margin-top:20px">
                            </div>
                            <div class="rounded-bottom p-4">
                                <a href="perfil.php" class="h4 d-inline-block mb-4"  style="color: #000; text-decoration: none;" onmouseover="this.style.color='#3E5FFE'; this.style.backgroundColor='transparent';" onmouseout="this.style.color='#000';">Editar Perfil</a>
                                <p class="mb-4">Edite seu perfil de usuário
                                </p>
                                <a class="btn btn-primary rounded-pill py-2 px-4" href="perfil.php">editar perfil</a>
                            </div>
                        </div>
                    </div>
    
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.6s" >
                        <div class="service-item">
                            <div class="service-img" style="height:280px">
                                <img src="img/2.png" class="img-fluid rounded-top w-100" alt="Image">
                            </div>
                            <div class="rounded-bottom p-4">
                                <a href="ver_chamados.php" class="h4 d-inline-block mb-4"  style="color: #000; text-decoration: none;" onmouseover="this.style.color='#3E5FFE'; this.style.backgroundColor='transparent';" onmouseout="this.style.color='#000';">veja seu chamados</a>
                                <p class="mb-4">Aqui está os seus chamados em aberto
                                </p>
                                <a class="btn btn-primary rounded-pill py-2 px-4" href="ver_chamados.php">ver mais</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     
        <!-- Footer Start -->
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
                            <a href="#" style="color: #fffff; text-decoration: none;" 
       onmouseover="this.style.color='#FFF';" 
 ><i class="fas fa-angle-right me-2"></i>Politicas de Privacidade</a>
                            <a href="#" style="color: #fffff; text-decoration: none;" 
       onmouseover="this.style.color='#FFF';" 
      ><i class="fas fa-angle-right me-2"></i> Termos & Condições</a>
                            <a href="#" style="color: #fffff; text-decoration: none;" 
       onmouseover="this.style.color='#FFF';" 
      ><i class="fas fa-angle-right me-2"></i> Support</a>
                            <a href="./FAQ.php" style="color: #fffff; text-decoration: none;" 
       onmouseover="this.style.color='#FFF';" 
       ><i class="fas fa-angle-right me-2"></i> FAQ</a>
                            <a href="FAQ.php" style="color: #fffff; text-decoration: none;" 
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
        <script src="./js/main.js"></script>
    </body>

</html>