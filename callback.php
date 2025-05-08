<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

try {
    $fb = new Facebook\Facebook([
        'app_id' => FB_APP_ID,
        'app_secret' => FB_APP_SECRET,
        'default_graph_version' => FB_GRAPH_VERSION,
    ]);

    $helper = $fb->getRedirectLoginHelper();

    // Handle OAuth 2.0 callback
    try {
        $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        error_log('Graph returned an error: ' . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
        header('Location: index.php?error=' . urlencode('Error en la respuesta de Facebook'));
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        error_log('Facebook SDK returned an error: ' . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
        header('Location: index.php?error=' . urlencode('Error en el SDK de Facebook'));
        exit;
    }

    if (!isset($accessToken)) {
        if ($helper->getError()) {
            header('Location: index.php?error=' . urlencode('Error: ' . $helper->getError()));
            exit;
        }
        header('Location: index.php?error=' . urlencode('Error desconocido'));
        exit;
    }

    // Exchange short-lived token for a long-lived one
    $oAuth2Client = $fb->getOAuth2Client();
    
    try {
        $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        error_log('Error getting long-lived token: ' . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
        header('Location: index.php?error=' . urlencode('Error al obtener token de larga duración'));
        exit;
    }

    // Store the token in the session
    $_SESSION['fb_access_token'] = (string) $longLivedAccessToken;

    // Get user information
    try {
        $response = $fb->get('/me?fields=id,name', $longLivedAccessToken);
        $user = $response->getGraphUser();
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_id'] = $user['id'];
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        error_log('Graph returned an error: ' . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        error_log('Facebook SDK returned an error: ' . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
    }

    // Redirect to dashboard
    header('Location: dashboard.php');
    exit;

} catch(Exception $e) {
    error_log('General error: ' . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
    header('Location: index.php?error=' . urlencode('Error general del sistema'));
    exit;
}
