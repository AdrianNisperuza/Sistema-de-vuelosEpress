<?php
session_start();

define('GITHUB_CLIENT_ID', 'TU_CLIENT_ID_AQUI');
define('GITHUB_CLIENT_SECRET', 'TU_CLIENT_SECRET_AQUI');
define('REDIRECT_URI', 'http://localhost/sistema_vuelos/callback.php');

// Función auxiliar para realizar peticiones HTTP a la API de GitHub usando cURL
function callGitHubAPI($url, $accessToken = null, $postFields = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // GitHub EXIGE un User-Agent explícito en todas sus peticiones API
    $headers = [
        'User-Agent: Sistema-Vuelos-PHPApp',
        'Accept: application/json'
    ];
    
    if ($accessToken) {
        $headers[] = 'Authorization: Bearer ' . $accessToken;
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($postFields) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
