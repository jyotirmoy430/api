<?php

try{
    session_start();

    // Database credentials
    $host = 'sql110.infinityfree.com';
    $db   = 'if0_36287848_jbmovies';
    $user = 'if0_36287848';
    $pass = 'XL9tpY7pDr6jAV';
    $port = 3306;

    // Auth duration
    $auth_duration = 3 * 24 * 60 * 60; // 3 days in seconds
    $already_authenticated = false;
    $masterUser = false;

    // Get HTTP Basic Auth credentials
    $username = $_SERVER['PHP_AUTH_USER'] ?? '';
    $password = isset($_SERVER['PHP_AUTH_PW']) ? md5($_SERVER['PHP_AUTH_PW']) : '';

    // Check session for authentication
    if (
        isset($_SESSION['auth_time_prod']) &&
        (time() - $_SESSION['auth_time_prod']) < $auth_duration &&
        isset($_SESSION['user']) &&
        isset($_SESSION['country']) &&
        $_SESSION['country'] === 'Bangladesh'
    ) {
        $already_authenticated = true;
    }

    // If not authenticated, perform DB + country check
    if (!$already_authenticated) {
        if (!$username || !$password) {
            header('WWW-Authenticate: Basic realm="Restricted Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Authorization Required.';
            exit;
        }

        // Connect to DB
        $conn = new mysqli($host, $user, $pass, $db, $port);
        if ($conn->connect_error) {
            echo "Something is wrong";
            exit;
        }

        // Query for scope based on username/password
        $stmt = $conn->prepare("SELECT scope FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $stmt->bind_result($scope);

        if (!$stmt->fetch()) {
            // Invalid user
            header('WWW-Authenticate: Basic realm="Restricted Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Authorization Required.';
            exit;
        }

        $stmt->close();
        $conn->close();

        // Check country via IP
        $ip = $_SERVER['REMOTE_ADDR'];
        $response = @file_get_contents("http://ip-api.com/json/{$ip}");
        $data = json_decode($response);
        $country = ($data && $data->status === 'success') ? $data->country : 'Unknown';

        if ($country !== 'Bangladesh') {
            header('WWW-Authenticate: Basic realm="Restricted Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Authorization Required.';
            exit;
        }

        // Set session values after successful login and location check
        $_SESSION['auth_time_prod'] = time();
        $_SESSION['user'] = $username;
        $_SESSION['scope'] = $scope;
        $_SESSION['country'] = $country;
        $_SESSION['lat'] = $data->lat;
        $_SESSION['lon'] = $data->lon;
        $_SESSION['isp'] = $data->isp;

        
    }

    // Determine if master user
    if (
        $username === '430' ||
        (isset($_SESSION['user']) && $_SESSION['user'] === '430') ||
        isset($_GET["jb"])
    ) {
        $masterUser = true;
    }

    error_reporting(0);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");

}catch(\Exception $e){
    echo "Something is wrong";
    exit;
}


//add log - ip
if($masterUser){
logInfo('', 'log.txt');
logInfo('', 'login.txt');
}


function logInfo($message = '', $fileName = 'log.txt')
{
    try{
        $dt = new DateTime("now", new DateTimeZone("Asia/Dhaka"));
        $time = $dt->format("d M Y H:i:s"); // Format: 05 May 2025 14:10:59
        $mapLink = 'https://www.google.com/maps/@'.$_SESSION['lat'].','.$_SESSION['lon'];
        $formattedMsg = $time. ' -> '. $_SERVER['REMOTE_ADDR'] . ' -> ' . $_SESSION['isp'] . ' -> '.$_SESSION['user']. ' -> '. $_SESSION['country']. ' -> ' . $mapLink . (($message) ? ' -> '. $message : '');
        file_put_contents($fileName,  "");
    }catch(\Exception $e){}
}
?>