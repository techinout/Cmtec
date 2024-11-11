<?php 
session_start();
include "../Controller/conexao.php";
include "../Model/Chamado.php";
include "../Model/Status.php";
include "../Model/Resposta.php"; // Incluindo o modelo Resposta

// Verifica a validade do token da sessão
if (!isset($_SESSION['token'])) {
    header('Location: ../login.html');
    exit;
}

// Divide o token em sua parte principal e tipo (admin ou aluno)
list($token, $type) = explode('_', $_SESSION['token'], 2);

// Verifica se o tipo do token é 'aluno'
if ($type !== 'aluno') {
    header('Location: ../login.html');
    exit;
}

$user = $_SESSION['user'];
$codAluno = $_SESSION['user']['codAluno'];

// Cria uma instância da classe Chamado
$chamado = new Chamado($pdo);

// Busca os chamados do aluno
$chamados = $chamado->listarPorAluno($codAluno);

usort($chamados, function($a, $b) {
    if ($a['codStatus'] == 2 && $b['codStatus'] != 2) {
        return -1;
    } elseif ($a['codStatus'] != 2 && $b['codStatus'] == 2) {
        return 1;
    }
    return 0;
});

// Cria uma instância da classe Status
$statusModel = new Status($pdo);
$status = $statusModel->listarTodos();

// Obtém os detalhes de ambientes e equipamentos
$sql = "SELECT codAmbiente, descricao FROM Ambiente";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$ambientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT codEquipamentos, descricao FROM Equipamentos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$equipamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cria uma instância da classe Resposta e busca as respostas do aluno
$resposta = new Resposta($pdo);
$respostas = $resposta->listarPorAluno($codAluno);
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

        <!-- Template Stylesheet -->
        <link href="css/style.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


   
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
        
/* Estilos para telas menores que 768px */
@media (max-width: 768px) {
  .card {
    flex-direction: column;
    text-align: center; /* Centralizar o conteúdo */
  }

  .card-img {
    margin: 0 auto 10px; /* Centralizar a imagem e adicionar margem inferior */
    width: 100%; /* Aumentar a imagem para ocupar toda a largura do card */
    height: auto; /* Manter a proporção da imagem */
  }

  .btn-group {
    position: relative; /* Reposicionar o dropdown de ações */
    margin-top: 10px;
    justify-content: center; /* Centralizar o botão de ações */
  }

  .btn-view-image {
    width: 100%; /* Botão de imagem ocupando 100% da largura */
  }
}
/* Estilo para o card com box shadow e margem reduzida */
.card {
    position: relative;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Sombra suave ao redor do card */
    margin-bottom: 10px; /* Margem inferior entre os cards */
    border-radius: 0 0px 10px 0;
    transition: box-shadow 0.3s ease; /* Transição suave para a sombra */
    padding: 5px; /* Espaçamento interno do card */
   
}

/* Efeito de hover no card */
.card:hover {
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Aumenta a sombra ao passar o mouse */
}

/* Estilo para a div de status verde */
.status-div {
    position: absolute;
    top: 0;
    left: 0;
    background-color: #28a745; /* Cor verde */
    color: #ffffff; /* Cor do texto branco */
    padding: 5px 10px;
    border-radius: 0 0px 5px 0;
    font-weight: bold;
    z-index: 1; /* Garante que a div fique acima dos outros elementos */
}

/* Estilo básico do botão pequeno */
.btn-small {
    background-color: #3E5FFE; /* Azul de fundo */
    color: #ffffff; /* Texto branco */
    border: none; /* Remove a borda padrão */
    border-radius: 4px; /* Borda arredondada */
    font-size: 14px; /* Tamanho da fonte menor */
    padding: 6px 12px; /* Padding menor para botões pequenos */
    transition: background-color 0.3s ease, transform 0.3s ease; /* Transição suave para cor e transformação */
    
}

/* Estado hover do botão pequeno */
.btn-small:hover,
.btn-small:focus {
    background-color: #3457D0; /* Azul mais escuro ao passar o mouse ou quando o botão está em foco */
    color: #ffffff; /* Texto branco */
    outline: none; /* Remove o contorno padrão de foco */
    transform: scale(1.05); /* Leve aumento de tamanho */
}

/* Estado ativo do botão pequeno */
.btn-small:active {
    background-color: #2a46c9; /* Azul ainda mais escuro quando o botão é clicado */
    transform: scale(0.98); /* Leve redução de tamanho ao clicar */
    border: none; /* Remove a borda padrão */
}

/* Adicionando um foco personalizado (opcional) */
.btn-small:focus {
    box-shadow: 0 0 0 3px rgba(62, 95, 254, 0.5); /* Sombra ao redor do botão para indicar o foco */
    border: none; /* Remove a borda padrão */
}

/* Estilo para o botão secundário pequeno */
.btn-secondary-small {
    background-color: #3E5FFE; /* Cor de fundo padrão */
    border-color: #3E5FFE; /* Cor da borda padrão */
    color: #ffffff; /* Cor do texto padrão */
    font-size: 14px; /* Tamanho da fonte menor */
    padding: 6px 12px; /* Padding menor para botões pequenos */
}

.btn-secondary-small:hover,
.btn-secondary-small:focus {
    background-color: #3E5FFE; /* Cor de fundo ao passar o mouse */
    border-color: #3E5FFE; /* Cor da borda ao passar o mouse */
    color: #ffffff; /* Cor do texto ao passar o mouse */
}

.btn-secondary-small:active {
    background-color: #3E5FFE; /* Cor de fundo ao clicar */
    border-color: #3E5FFE; /* Cor da borda ao clicar */
    color: #ffffff; /* Cor do texto ao clicar */
}

/* Centralizar conteúdo do modal */
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

/* Estilo do botão de fechar modal personalizado */
.btn-custom {
    background-color: #00a1ff;
    color: white;
}

.btn-custom:hover {
    background-color: #007bb5;
}

/* Estilo do hover e ativo no card */
.custom-card:hover {
    background-color: #e9ecef;
    transform: scale(1.02);
    transition: all 0.3s ease-in-out;
}
.container{
    margin-top:30px; 
    justify-content:center;
     display:flex; 
     align-items:center; 
     flex-direction:column;
}

/* Container para os botões */
.button-container {
    text-align: center;
    margin-bottom: 20px;
}

/* Estilo dos botões */
.button-container .btn {
    border-radius: 5px; /* Border-radius reduzido */
    padding: 8px 16px; /* Ajuste do padding para botões menores */
    font-size: 14px; /* Fonte menor para um visual mais compactado */
    font-weight: bold;
    text-transform: uppercase;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(0, 1, 1, 0.5); /* Box-shadow reduzido */
}

.btn-resolved {
    background-color: #3E5FFE;
    color: white;
}

.btn-resolved:hover {
    background-color: #556ee7;
    color: white;
}

.btn-open {
    background-color: #808080;
    color: white;
}

.btn-open:hover {
    background-color: #847c7c;
    color: white;
}

/* Responsividade para dispositivos móveis */
@media (max-width: 768px) {
    .button-container {
        padding: 0 10px;
    }

    .button-container .btn {
        width: 100%;
        box-sizing: border-box;
        margin: 5px 0;
    }
}

#divResolvidos, #divAberto {
    width: 100%;
    padding: 0;
    margin: 0;
}

/* Alinhamento dos cartões */
.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    
}
.status-enviado {
    background-color: #3E5FFE; /* Cor para "enviado" */
}

.status-em-andamento {
    background-color: #FFA500; /* Cor para "em andamento", ajuste conforme necessário */
}

            </style>
     
        
    </head>

    <body>
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
                            <a href="chamados.php" class="dropdown-item" >Chamados</a>
                            <a href="ver_chamados.php" class="dropdown-item"  style="background-color:#3E5FFE;  color:#fff;">Ver Chamados</a>
                                <a href="perfil.php" class="dropdown-item">Perfil</a>
                                <a href="FAQ.php" class="dropdown-item">FAQs</a>
                                <a href="../deslogar.php" class="dropdown-item" style="background-color:#d9d9d9; color:#000;">Sair</a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

        
        <!-- conteudo Start -->
           
        <div class="container-fluid py-5">
    <div class="container">
        <h1 class="text-center" style="margin-top:10px">Meus Chamados</h1>
        
        <!-- Botões de Filtro -->
        <div class="button-container">
            <p>Filtrar por:</p>
    <button class="btn btn-open" onclick="filtrarChamados('em-aberto')">Em Aberto</button>
    <button class="btn btn-resolved" onclick="filtrarChamados('resolvidos')">Resolvidos</button>
</div>

        
        <!-- Div de Chamados Resolvidos -->
        <div id="divResolvidos">
        <ul>
        <?php foreach ($respostas as $resposta): ?>
            <!-- Card de Resposta Resolvida -->
            <div class="card position-relative d-flex flex-row align-items-center p-3 mb-3 resposta resolvida" style="margin-top:50px;">
                <div class="card-body d-flex flex-column">
                    
                    <!-- Dados da Resposta -->
                     <div class="status-div">Concluido</div>
                    <p class="card-text" style="margin-top:10px;">Resposta: <?php echo htmlspecialchars($resposta['respostaTexto']); ?></p>
                    
                    <!-- Dados Adicionais -->
                    <p class="card-text">Descrição: <?php echo htmlspecialchars($resposta['descricaoChamado']); ?></p>
                    <p class="card-text">Ambiente: <?php echo htmlspecialchars($resposta['ambienteChamado']); ?></p>
                    <p class="card-text">Equipamento: <?php echo htmlspecialchars($resposta['equipamentoChamado']); ?></p>
                    <p class="card-text">Respondido em: <?php echo htmlspecialchars(date('d/m/Y', strtotime($resposta['dataResposta']))); ?></p>

                    <!-- Verificação e Exibição da Imagem -->
                    <?php 
                    $imagemExiste = !empty($resposta['imagem']) && file_exists("../uploads/resposta/" . $resposta['imagem']);
                    ?>
   
   <?php if ($imagemExiste): ?>
    <button type="button" class="btn btn-primary btn-sm btn-view-image" data-bs-toggle="modal" data-bs-target="#modalImagem-<?php echo htmlspecialchars($resposta['codResposta']); ?>">Ver Imagem</button>

    <!-- Modal para Imagem -->
    <div class="modal fade" id="modalImagem-<?php echo htmlspecialchars($resposta['codResposta']); ?>" tabindex="-1" aria-labelledby="modalImagemLabel-<?php echo htmlspecialchars($resposta['codResposta']); ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImagemLabel-<?php echo htmlspecialchars($resposta['codResposta']); ?>">Imagem da Resposta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="../uploads/resposta/<?php echo htmlspecialchars($resposta['imagem']); ?>" alt="Imagem da resposta" class="img-fluid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-custom" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

                </div>
            </div>
        <?php endforeach; ?>
</ul>
        </div>

        <!-- Div de Chamados em Aberto -->
        <div id="divAberto">
            
            <?php foreach ($chamados as $chamado): ?>
                <?php if ($chamado['codStatus'] == 1 || $chamado['codStatus'] == 2): // Verifica se o status é 1 ou 2 ?>
                <div class="card position-relative d-flex flex-row align-items-center p-3 mb-3 chamado" data-status="<?php echo htmlspecialchars($chamado['codStatus']); ?>" style="margin-top:50px;">
                    <?php
                    $imagemExiste = file_exists("../uploads/chamados/" . $chamado['imagem']) && !empty($chamado['imagem']);
                    ?>

<div class="status-div <?php echo ($chamado['codStatus'] == 1) ? 'status-enviado' : (($chamado['codStatus'] == 2) ? 'status-em-andamento' : ''); ?>">
    <?php 
    $statusItem = array_filter($status, function($item) use ($chamado) {
        return $item['id'] == $chamado['codStatus'];
    });
    echo !empty($statusItem) ? htmlspecialchars(array_shift($statusItem)['descricao']) : 'Não disponível';
    ?>
</div>


                    <div class="card-body d-flex flex-column" >
                        <p class="card-text" style="margin-top:10px;">Descrição: <?php echo htmlspecialchars($chamado['descricao']); ?></p>
                        <p class="card-text">Ambiente: 
                            <?php 
                            $ambiente = array_filter($ambientes, function($item) use ($chamado) {
                                return $item['codAmbiente'] == $chamado['codAmbiente'];
                            });
                            echo !empty($ambiente) ? htmlspecialchars(array_shift($ambiente)['descricao']) : 'Não disponível';
                            ?>
                        </p>
                        <p class="card-text">Equipamento: 
                            <?php 
                            $equipamento = array_filter($equipamentos, function($item) use ($chamado) {
                                return $item['codEquipamentos'] == $chamado['codEquipamentos'];
                            });
                            echo !empty($equipamento) ? htmlspecialchars(array_shift($equipamento)['descricao']) : 'Não disponível';
                            ?>
                        </p>
                        <p class="card-text">Data: <?php echo htmlspecialchars(date('d/m/Y', strtotime($chamado['data']))); ?></p>
                        
                        <?php if ($imagemExiste): ?>
                            <button type="button" class="btn btn-primary btn-sm btn-view-image" data-bs-toggle="modal" data-bs-target="#modalImagem-<?php echo $chamado['codChamado']; ?>">Ver Imagem</button>
                        <?php endif; ?>

                        <a href="../DAO/chamado/chamadoSeDelete.php?id=<?php echo htmlspecialchars($chamado['codChamado']); ?>" class="btn btn-danger btn-sm mt-2" onclick="return confirm('Tem certeza que deseja excluir este chamado?');">Deletar</a>

                        <?php if ($imagemExiste): ?>
                            <div class="modal fade" id="modalImagem-<?php echo $chamado['codChamado']; ?>" tabindex="-1" aria-labelledby="modalImagemLabel-<?php echo $chamado['codChamado']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalImagemLabel-<?php echo $chamado['codChamado']; ?>">Imagem do Chamado</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="../uploads/chamados/<?php echo htmlspecialchars($chamado['imagem']); ?>" alt="Imagem do chamado" class="img-fluid">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-custom" data-bs-dismiss="modal">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

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
        <script>
    function filtrarChamados(filtro) {
        const divAberto = document.getElementById('divAberto');
        const divResolvidos = document.getElementById('divResolvidos');

        if (filtro === 'todos') {
            divAberto.style.display = 'block';
            divResolvidos.style.display = 'none';
        } else if (filtro === 'resolvidos') {
            divAberto.style.display = 'none';
            divResolvidos.style.display = 'block';
        } else if (filtro === 'em-aberto') {
            divAberto.style.display = 'block';
            divResolvidos.style.display = 'none';
        }
    }

    // Inicialmente mostrar todos os chamados
    filtrarChamados('todos');
</script>
    </body>

</html>