<?php
require_once 'config.php';

// Verificar que el usuario esté autenticado con GitHub
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];

// Inicializar arreglo de reservas en sesión si no existe
if (!isset($_SESSION['reservas'])) {
    $_SESSION['reservas'] = [];
}

// Lista fija de ciudades permitidas
$ciudades = ['Bogotá', 'Medellín', 'Cali'];
$mensaje = '';
$tipoMensaje = '';

// --- PROCESAR FORMULARIO: CREAR RESERVA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reservar') {
    $origen = $_POST['origen'] ?? '';
    $destino = $_POST['destino'] ?? '';
    $fecha = $_POST['fecha'] ?? '';

    if ($origen === $destino) {
        $mensaje = 'El origen y el destino no pueden ser la misma ciudad.';
        $tipoMensaje = 'error';
    } elseif (!in_array($origen, $ciudades) || !in_array($destino, $ciudades)) {
        $mensaje = 'Por favor selecciona un destino válido.';
        $tipoMensaje = 'error';
    } elseif (empty($fecha)) {
        $mensaje = 'Debes seleccionar una fecha para el vuelo.';
        $tipoMensaje = 'error';
    } else {
        // Crear nuevo vuelo
        $nuevoVuelo = [
            'id' => uniqid('FL-'),
            'origen' => $origen,
            'destino' => $destino,
            'fecha' => $fecha,
            'estado' => 'Confirmado',
            'creado_en' => date('Y-m-d H:i')
        ];
        $_SESSION['reservas'][] = $nuevoVuelo;
        $mensaje = '¡Vuelo reservado con éxito!';
        $tipoMensaje = 'exito';
    }
}

// --- PROCESAR ACCIÓN: CANCELAR RESERVA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancelar') {
    $idVuelo = $_POST['vuelo_id'] ?? '';
    foreach ($_SESSION['reservas'] as &$reserva) {
        if ($reserva['id'] === $idVuelo) {
            $reserva['estado'] = 'Cancelado';
            $mensaje = 'La reserva ' . htmlspecialchars($idVuelo) . ' ha sido cancelada.';
            $tipoMensaje = 'info';
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva de Vuelos - Sistema de Vuelos</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        .header { background: #1a73e8; color: white; padding: 15px 20px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; }
        .header img { border-radius: 50%; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 20px; }
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
        label { font-weight: bold; margin-bottom: 5px; }
        select, input[type="date"] { padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-primary { background: #1a73e8; color: white; }
        .btn-danger { background: #d93025; color: white; }
        .alerta { padding: 12px; border-radius: 5px; margin-top: 15px; font-weight: bold; }
        .alerta-exito { background: #e6f4ea; color: #137333; }
        .alerta-error { background: #fce8e6; color: #c5221f; }
        .alerta-info { background: #e8f0fe; color: #1a73e8; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-confirmado { background: #ceead6; color: #0d652d; }
        .badge-cancelado { background: #fad2cf; color: #a50e0e; }
    </style>
</head>
<body>

<div class="container">
    <!-- Encabezado de Sesión -->
    <div class="header">
        <div>
            <h2>Sistema de Vuelos ✈️</h2>
            <span>Bienvenido, <?php echo htmlspecialchars($user['name']); ?></span>
        </div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <img src="<?php echo htmlspecialchars($user['avatar']); ?>" width="45" height="45">
            <a href="logout.php" style="color: white; font-weight: bold; text-decoration: none;">Cerrar sesión</a>
        </div>
    </div>

    <!-- Mensajes de Notificación -->
    <?php if ($mensaje): ?>
        <div class="alerta alerta-<?php echo $tipoMensaje; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <!-- Formulario de Pedir Vuelos -->
    <div class="card">
        <h3>Pedir Nuevo Vuelo</h3>
        <form method="POST" action="dashboard.php">
            <input type="hidden" name="action" value="reservar">
            
            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label for="origen">Origen:</label>
                    <select name="origen" id="origen" required>
                        <option value="">-- Seleccionar --</option>
                        <?php foreach ($ciudades as $ciudad): ?>
                            <option value="<?php echo $ciudad; ?>"><?php echo $ciudad; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="destino">Destino:</label>
                    <select name="destino" id="destino" required>
                        <option value="">-- Seleccionar --</option>
                        <?php foreach ($ciudades as $ciudad): ?>
                            <option value="<?php echo $ciudad; ?>"><?php echo $ciudad; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="fecha">Fecha del Vuelo:</label>
                    <input type="date" name="fecha" id="fecha" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Reservar Vuelo</button>
        </form>
    </div>

    <!-- Gestión de Reservas -->
    <div class="card">
        <h3>Mis Reservas</h3>
        <?php if (empty($_SESSION['reservas'])): ?>
            <p>No tienes vuelos reservados en este momento.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Ruta</th>
                        <th>Fecha Vuelo</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['reservas'] as $reserva): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($reserva['id']); ?></strong></td>
                            <td><?php echo htmlspecialchars($reserva['origen']); ?> ➔ <?php echo htmlspecialchars($reserva['destino']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['fecha']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($reserva['estado']); ?>">
                                    <?php echo htmlspecialchars($reserva['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($reserva['estado'] === 'Confirmado'): ?>
                                    <form method="POST" action="dashboard.php" onsubmit="return confirm('¿Estás seguro de cancelar esta reserva?');">
                                        <input type="hidden" name="action" value="cancelar">
                                        <input type="hidden" name="vuelo_id" value="<?php echo $reserva['id']; ?>">
                                        <button type="submit" class="btn btn-danger">Cancelar Vuelo</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #888;">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
