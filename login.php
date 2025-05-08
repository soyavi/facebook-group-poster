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
    
    // Required permissions for posting to groups
    $permissions = [
        'groups_access_member_info',  // To access group information
        'publish_to_groups',          // To post content to groups
        'user_managed_groups'         // To get list of groups user manages
    ];

    // Generate Facebook login URL
    $loginUrl = $helper->getLoginUrl(FB_REDIRECT_URI, $permissions);

    // Redirect to Facebook login dialog
    header("Location: " . $loginUrl);
    exit;
    
} catch(Exception $e) {
    // Log error
    error_log("Facebook Login Error: " . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
    
    // Redirect to index with error
    header("Location: index.php?error=" . urlencode('Error al conectar con Facebook'));
    exit;
}
