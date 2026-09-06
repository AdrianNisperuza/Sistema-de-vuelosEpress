<?php
require_once 'config.php';

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

// Generamos un estado 'state' para prevenir ataques CSRF
$_SESSION['oauth2state'] = bin2hex(random_bytes(16));

$githubAuthUrl = 'https://github.com/login/oauth/authorize?' . http_build_query([
    'client_id' => GITHUB_CLIENT_ID,
    'redirect_uri' => REDIRECT_URI,
    'scope' => 'user:email',
    'state' => $_SESSION['oauth2state']
]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Vuelos - Login</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f6f9; margin: 0; }
        .login-card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; }
        .btn-github { background-color: #24292e; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; display: inline-block; margin-top: 15px; }
        .btn-github:hover { background-color: #000; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Sistema de Vuelos ✈️</h2>
    <p>Inicia sesión para gestionar tus pasajes</p>
    <a href="<?php echo htmlspecialchars($githubAuthUrl); ?>" class="btn-github">
        Iniciar sesión con GitHub
    </a>
</div>

</body>
</html>
