<?php
require_once 'config.php';

// Validar parámetros recibidos
if (!isset($_GET['code']) || !isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth2state']) {
    exit('Error: Estado no válido o falta el código de autorización.');
}

// 1. Intercambiar 'code' por 'access_token'
$tokenData = callGitHubAPI('https://github.com/login/oauth/access_token', null, [
    'client_id' => GITHUB_CLIENT_ID,
    'client_secret' => GITHUB_CLIENT_SECRET,
    'code' => $_GET['code'],
    'redirect_uri' => REDIRECT_URI
]);

if (!isset($tokenData['access_token'])) {
    exit('Error al obtener el Token de Acceso de GitHub.');
}

$accessToken = $tokenData['access_token'];

// 2. Obtener los datos del perfil de usuario
$userData = callGitHubAPI('https://api.github.com/user', $accessToken);

// 3. Obtener el email principal del usuario si no es público
if (empty($userData['email'])) {
    $emails = callGitHubAPI('https://api.github.com/user/emails', $accessToken);
    foreach ($emails as $email) {
        if ($email['primary']) {
            $userData['email'] = $email['email'];
            break;
        }
    }
}

// 4. Guardar usuario en la sesión
$_SESSION['user'] = [
    'id'        => $userData['id'],
    'username'  => $userData['login'],
    'name'      => $userData['name'] ?? $userData['login'],
    'email'     => $userData['email'] ?? 'No disponible',
    'avatar'    => $userData['avatar_url']
];

header('Location: dashboard.php');
exit;<?php
require_once 'config.php';

// Validar parámetros recibidos
if (!isset($_GET['code']) || !isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth2state']) {
    exit('Error: Estado no válido o falta el código de autorización.');
}

// 1. Intercambiar 'code' por 'access_token'
$tokenData = callGitHubAPI('https://github.com/login/oauth/access_token', null, [
    'client_id' => GITHUB_CLIENT_ID,
    'client_secret' => GITHUB_CLIENT_SECRET,
    'code' => $_GET['code'],
    'redirect_uri' => REDIRECT_URI
]);

if (!isset($tokenData['access_token'])) {
    exit('Error al obtener el Token de Acceso de GitHub.');
}

$accessToken = $tokenData['access_token'];

// 2. Obtener los datos del perfil de usuario
$userData = callGitHubAPI('https://api.github.com/user', $accessToken);

// 3. Obtener el email principal del usuario si no es público
if (empty($userData['email'])) {
    $emails = callGitHubAPI('https://api.github.com/user/emails', $accessToken);
    foreach ($emails as $email) {
        if ($email['primary']) {
            $userData['email'] = $email['email'];
            break;
        }
    }
}

// 4. Guardar usuario en la sesión
$_SESSION['user'] = [
    'id'        => $userData['id'],
    'username'  => $userData['login'],
    'name'      => $userData['name'] ?? $userData['login'],
    'email'     => $userData['email'] ?? 'No disponible',
    'avatar'    => $userData['avatar_url']
];

header('Location: dashboard.php');
exit;
