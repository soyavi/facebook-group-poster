<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

// Check if user is logged in
if (!isset($_SESSION['fb_access_token'])) {
    header('Location: index.php');
    exit;
}

// Validate form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

if (!isset($_POST['group']) || !isset($_POST['message'])) {
    header('Location: dashboard.php?error=' . urlencode('Faltan datos requeridos'));
    exit;
}

try {
    $fb = new Facebook\Facebook([
        'app_id' => FB_APP_ID,
        'app_secret' => FB_APP_SECRET,
        'default_graph_version' => FB_GRAPH_VERSION,
    ]);

    $accessToken = $_SESSION['fb_access_token'];
    $groupId = $_POST['group'];
    $message = trim($_POST['message']);

    // Validate message
    if (empty($message)) {
        header('Location: dashboard.php?error=' . urlencode('El mensaje no puede estar vacío'));
        exit;
    }

    // Initialize data array for Facebook post
    $data = ['message' => $message];

    // Handle image upload if present
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $_FILES['image']['tmp_name']);
        finfo_close($fileInfo);

        if (!in_array($mimeType, $allowedTypes)) {
            header('Location: dashboard.php?error=' . urlencode('Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG y GIF.'));
            exit;
        }

        // Validate file size (10MB max)
        if ($_FILES['image']['size'] > 10 * 1024 * 1024) {
            header('Location: dashboard.php?error=' . urlencode('La imagen es demasiado grande. Máximo 10MB.'));
            exit;
        }

        try {
            // Create a photo post
            $data['source'] = $fb->fileToUpload($_FILES['image']['tmp_name']);
            $response = $fb->post('/' . $groupId . '/photos', $data, $accessToken);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            error_log('Error posting photo to Facebook: ' . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
            header('Location: dashboard.php?error=' . urlencode('Error al publicar la imagen: ' . $e->getMessage()));
            exit;
        }
    } else {
        try {
            // Create a text-only post
            $response = $fb->post('/' . $groupId . '/feed', $data, $accessToken);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            error_log('Error posting to Facebook: ' . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
            header('Location: dashboard.php?error=' . urlencode('Error al publicar: ' . $e->getMessage()));
            exit;
        }
    }

    // Get the post ID from the response
    $graphNode = $response->getGraphNode();
    $postId = $graphNode['id'] ?? null;

    if ($postId) {
        // Log successful post
        error_log(
            sprintf(
                "Successfully posted to group %s. Post ID: %s\n",
                $groupId,
                $postId
            ),
            3,
            __DIR__ . '/logs/success.log'
        );

        header('Location: dashboard.php?success=' . urlencode('¡Publicación realizada con éxito!'));
        exit;
    } else {
        throw new Exception('No se pudo obtener el ID de la publicación');
    }

} catch (Facebook\Exceptions\FacebookSDKException $e) {
    error_log('Facebook SDK Error: ' . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
    header('Location: dashboard.php?error=' . urlencode('Error del SDK de Facebook: ' . $e->getMessage()));
    exit;
} catch (Exception $e) {
    error_log('General Error: ' . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
    header('Location: dashboard.php?error=' . urlencode('Error general: ' . $e->getMessage()));
    exit;
}
