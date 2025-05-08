<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

// Check if user is logged in
if (!isset($_SESSION['fb_access_token'])) {
    header('Location: index.php');
    exit;
}

try {
    $fb = new Facebook\Facebook([
        'app_id' => FB_APP_ID,
        'app_secret' => FB_APP_SECRET,
        'default_graph_version' => FB_GRAPH_VERSION,
    ]);

    $accessToken = $_SESSION['fb_access_token'];

    // Fetch user's groups
    try {
        $response = $fb->get('/me/groups?fields=id,name,administrator', $accessToken);
        $groups = $response->getGraphEdge()->asArray();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        error_log('Graph returned an error: ' . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
        $groups = [];
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        error_log('Facebook SDK returned an error: ' . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
        $groups = [];
    }

} catch(Exception $e) {
    error_log('General error: ' . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
    header('Location: index.php?error=' . urlencode('Error al cargar los grupos'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Facebook Group Poster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <i class="fab fa-facebook text-blue-600 text-2xl mr-2"></i>
                    <span class="font-bold text-xl">FB Group Poster</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">
                        <i class="fas fa-user mr-2"></i>
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?>
                    </span>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Status Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p class="font-bold">¡Éxito!</p>
                <p><?php echo htmlspecialchars($_GET['success']); ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Error</p>
                <p><?php echo htmlspecialchars($_GET['error']); ?></p>
            </div>
        <?php endif; ?>

        <!-- Posting Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Publicar en Grupos</h2>
            
            <form action="post.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <!-- Group Selection -->
                <div>
                    <label for="group" class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccionar Grupo
                    </label>
                    <select name="group" id="group" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Selecciona un grupo --</option>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?php echo htmlspecialchars($group['id']); ?>">
                                <?php echo htmlspecialchars($group['name']); ?>
                                <?php echo $group['administrator'] ? ' (Administrador)' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Message Input -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                        Mensaje
                    </label>
                    <textarea name="message" id="message" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Escribe tu mensaje aquí..."></textarea>
                </div>

                <!-- Image Upload -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        Imagen (Opcional)
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                        <div class="space-y-1 text-center">
                            <i class="fas fa-image text-gray-400 text-3xl mb-3"></i>
                            <div class="flex text-sm text-gray-600">
                                <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                    <span>Subir una imagen</span>
                                    <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF hasta 10MB</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                        <i class="fas fa-paper-plane mr-2"></i>Publicar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Script -->
    <script>
        document.getElementById('image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.className = 'mt-3 rounded-lg max-h-48 mx-auto';
                    
                    const container = event.target.closest('div').querySelector('.space-y-1');
                    const existingPreview = container.querySelector('img');
                    if (existingPreview) {
                        existingPreview.remove();
                    }
                    container.appendChild(preview);
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
