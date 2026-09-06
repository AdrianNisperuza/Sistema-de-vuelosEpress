<?php
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Principal - Sistema de Vuelos</title>
</head>
<body>

    <h1>Bienvenido al Sistema de Vuelos ✈️</h1>
    
    <div>
        <img src="<?php echo htmlspecialchars($user['avatar']); ?>" width="80" style="border-radius: 50%;">
        <h3>Hola, <?php echo htmlspecialchars($user['name']); ?> (@<?php echo htmlspecialchars($user['username']); ?>)</h3>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>ID GitHub:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
    </div>

    <hr>

    <p><a href="logout.php">Cerrar Sesión</a></p>

</body>
</html>
