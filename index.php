<?php
session_start();
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facebook Group Poster - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gradient-to-r from-blue-500 to-blue-600 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <i class="fab fa-facebook text-blue-600 text-6xl mb-4"></i>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Facebook Group Poster</h1>
            <p class="text-gray-600">Publica contenido en tus grupos de Facebook fácilmente</p>
        </div>

        <div class="space-y-4">
            <div class="bg-blue-50 p-4 rounded-lg text-sm text-blue-600">
                <ul class="list-disc list-inside">
                    <li>Publica mensajes en múltiples grupos</li>
                    <li>Comparte imágenes fácilmente</li>
                    <li>Gestiona tus publicaciones</li>
                </ul>
            </div>

            <a href="login.php" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg text-center transition duration-300 transform hover:scale-105">
                <i class="fab fa-facebook-f mr-2"></i>
                Iniciar Sesión con Facebook
            </a>
        </div>

        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Al continuar, aceptas que la aplicación acceda a tus grupos de Facebook</p>
        </div>
    </div>
</body>
</html>
