<?php
// Facebook App Configuration
define('FB_APP_ID', 'YOUR_APP_ID');          // Replace with your Facebook App ID
define('FB_APP_SECRET', 'YOUR_APP_SECRET');  // Replace with your Facebook App Secret
define('FB_REDIRECT_URI', 'http://localhost:8000/callback.php');
define('FB_GRAPH_VERSION', 'v18.0');

// Error Logging Configuration
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');
error_reporting(E_ALL);

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
