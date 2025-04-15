<?php

$valid_username = 'ami';
$valid_password = 'rishan';
$auth_duration = 2 * 24 * 60 * 60; // 2 days in seconds


$already_authenticated = false;

// Check if the cookie 'auth_time' is set and validate its time
if (isset($_COOKIE['auth_time'])) {
    // User authenticated within the last 2 days, no need to authenticate again
    $already_authenticated = true;
}

// If the cookie is not set or the time has expired, request authentication
if (!$already_authenticated) {
    if (
        !isset($_SERVER['PHP_AUTH_USER']) ||
        !isset($_SERVER['PHP_AUTH_PW']) ||
        $_SERVER['PHP_AUTH_USER'] !== $valid_username ||
        $_SERVER['PHP_AUTH_PW'] !== $valid_password
    ) {
        header('WWW-Authenticate: Basic realm="Restricted Area"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Authorization Required.';
        exit;
    }

    // If authentication is successful, set the cookie with the current timestamp
    if (!isset($_COOKIE['auth_time'])) {
        setcookie('auth_time', 'authenticated', time() + $auth_duration, "/");
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>বাজার দর</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .category {
            margin-bottom: 20px;
        }
        .category h2 {
            background: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .item {
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
            flex: 1 1 calc(33.333% - 20px);
            min-width: 200px;
            display: flex;
            justify-content: space-between;
        }
        .name {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>আজকের বাজার দর</h1>
    
    <div class="category">
        <h2>শাকসবজি</h2>
        <div class="container">
            <div class="item"><div class="name">শিম</div><div class="price">১৫ থেকে ৩০ টাকা</div></div>
            <div class="item"><div class="name">ফুলকপি (বড়)</div><div class="price">১০ থেকে ২০ টাকা</div></div>
            <div class="item"><div class="name">বাঁধাকপি (বড়)</div><div class="price">১০ থেকে ২০ টাকা</div></div>
            <div class="item"><div class="name">লাউ</div><div class="price">৩০ থেকে ৪০ টাকা</div></div>
            <div class="item"><div class="name">টমেটো</div><div class="price">২০ থেকে ৩০ টাকা</div></div>
            <div class="item"><div class="name">গাজর</div><div class="price">৩০ থেকে ৪০ টাকা</div></div>
            <div class="item"><div class="name">মুলা</div><div class="price">২০ টাকা</div></div>
        </div>
    </div>
    
    <div class="category">
        <h2>মসলা</h2>
        <div class="container">
            <div class="item"><div class="name">দেশি পেঁয়াজ</div><div class="price">৪০ টাকা</div></div>
            <div class="item"><div class="name">ইন্ডিয়ান পেঁয়াজ</div><div class="price">৫০ টাকা</div></div>
            <div class="item"><div class="name">আদা</div><div class="price">১২০ থেকে ২৮০ টাকা</div></div>
            <div class="item"><div class="name">রসুন</div><div class="price">২৩০ থেকে ২৪০ টাকা</div></div>
        </div>
    </div>
    
    <div class="category">
        <h2>মাংস</h2>
        <div class="container">
            <div class="item"><div class="name">গরুর মাংস</div><div class="price">৬৫০ থেকে ৭৮০ টাকা</div></div>
            <div class="item"><div class="name">খাসির মাংস</div><div class="price">১১৫০ থেকে ১২০০ টাকা</div></div>
            <div class="item"><div class="name">ব্রয়লার মুরগি</div><div class="price">১৯০ টাকা</div></div>
            <div class="item"><div class="name">সোনালি কক</div><div class="price">৩৩০ টাকা</div></div>
            <div class="item"><div class="name">দেশি মুরগি</div><div class="price">৫২০ টাকা</div></div>
        </div>
    </div>
    
    <div class="category">
        <h2>মাছ</h2>
        <div class="container">
            <div class="item"><div class="name">ইলিশ (৫০০ গ্রাম)</div><div class="price">১১০০ টাকা</div></div>
            <div class="item"><div class="name">ইলিশ (১ কেজি)</div><div class="price">২০০০ টাকা</div></div>
            <div class="item"><div class="name">রুই মাছ</div><div class="price">৩৮০ থেকে ৪৫০ টাকা</div></div>
            <div class="item"><div class="name">চিংড়ি</div><div class="price">৭৫০ থেকে ১২০০ টাকা</div></div>
            <div class="item"><div class="name">পাবদা</div><div class="price">৪০০ থেকে ৪৫০ টাকা</div></div>
            <div class="item"><div class="name">রুপচাঁদা</div><div class="price">১২০০ টাকা</div></div>
            <div class="item"><div class="name">বাইম</div><div class="price">১২০০ থেকে ১৪০০ টাকা</div></div>
            <div class="item"><div class="name">কাইক্ক্যা</div><div class="price">৬০০ টাকা</div></div>
        </div>
    </div>
</body>
</html>


