<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado como admin
if (!isset($_SESSION['token']) || strpos($_SESSION['token'], '_admin') === false) {
    // Se não estiver logado como admin, redireciona para a página de login
    header('Location: login.html');
    exit;
}

// Remove todas as variáveis de sessão
session_unset();

// Destroi a sessão
session_destroy();

// Remove o cookie de sessão se estiver definido
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redireciona para a página de login
header('Location: login.html');
exit;
?>
