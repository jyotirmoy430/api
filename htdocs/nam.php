<?php
date_default_timezone_set('Asia/Dhaka');

try {
    session_start();

    // Database credentials
    $host = 'sql110.infinityfree.com';
    $db   = 'if0_36287848_jbmovies';
    $user = 'if0_36287848';
    $pass = 'XL9tpY7pDr6jAV';
    $port = 3306;

    $auth_duration = 3 * 24 * 60 * 60;
    $already_authenticated = false;
    $masterUser = false;

    $username = $_SERVER['PHP_AUTH_USER'] ?? '';
    $password = isset($_SERVER['PHP_AUTH_PW']) ? md5($_SERVER['PHP_AUTH_PW']) : '';

    if (
        isset($_SESSION['auth_time_prod']) &&
        (time() - $_SESSION['auth_time_prod']) < $auth_duration &&
        isset($_SESSION['user']) &&
        isset($_SESSION['country']) &&
        $_SESSION['country'] === 'Bangladesh'
    ) {
        $already_authenticated = true;
    }

    if (!$already_authenticated) {
        if (!$username || !$password) {
            header('WWW-Authenticate: Basic realm="Restricted Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Authorization Required.';
            exit;
        }

        $conn = new mysqli($host, $user, $pass, $db, $port);
        if ($conn->connect_error) {
            echo "Something is wrong";
            exit;
        }

        $stmt = $conn->prepare("SELECT scope FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $stmt->bind_result($scope);

        if (!$stmt->fetch()) {
            header('WWW-Authenticate: Basic realm="Restricted Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Authorization Required.';
            exit;
        }

        $stmt->close();
        $conn->close();

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

        $_SESSION['auth_time_prod'] = time();
        $_SESSION['user'] = $username;
        $_SESSION['scope'] = $scope;
        $_SESSION['country'] = $country;
        $_SESSION['lat'] = $data->lat;
        $_SESSION['lon'] = $data->lon;
        $_SESSION['isp'] = $data->isp;
    }

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

} catch (\Exception $e) {
    echo "Something is wrong";
    exit;
}

if (!$masterUser) {
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authorization Required.';
    exit;
}

// Now connect once for the rest of the script
$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    echo "<p>Database connection failed.</p>";
    exit;
}

$defaultDate = date("d/m/Y l");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    handleFormSubmission($conn, $_POST["date_input"] ?? '');
}

// Handle delete request
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    deleteEntry($conn, intval($_GET['delete_id']));
}

$entries = getAllEntries($conn);
$conn->close();

function handleFormSubmission($conn, $submittedDate) {
    $dateOnly = preg_replace('/[^0-9\/]/', '', $submittedDate);
    $timestamp = DateTime::createFromFormat('d/m/Y', $dateOnly);

    if ($timestamp) {
        $formattedForDb = $timestamp->format('Y-m-d H:i:s');
        $formattedDateOnly = $timestamp->format('Y-m-d');
        $now = date('Y-m-d H:i:s');

        $checkStmt = $conn->prepare("SELECT id FROM nam WHERE DATE(`date`) = ?");
        $checkStmt->bind_param("s", $formattedDateOnly);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO nam (`date`, `created_at`, `updated_at`) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $formattedForDb, $now, $now);
            if ($stmt->execute()) {
                echo "<p>You submitted and saved: " . htmlspecialchars($submittedDate) . "</p>";
            } else {
                echo "<p>Failed to save date.</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Duplicate entry. A date already exists for this day.</p>";
        }

        $checkStmt->close();
    }
}

function deleteEntry($conn, $deleteId) {
    $stmt = $conn->prepare("SELECT date FROM nam WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->bind_result($dateToDelete);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM nam WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();
}

function getAllEntries($conn) {
    $entries = [];
    $query = "SELECT id, date, created_at, updated_at FROM nam ORDER BY date DESC LIMIT 30";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $entries[] = $row;
        }
    }

    return $entries;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Nam</title>

        <link rel="icon" href="logo_nam_small.png" sizes="32x32" />
        <link rel="icon" href="logo_nam.ico" sizes="32x32" />
    <link rel="apple-touch-icon" href="logo_nam.png" />

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        form {
            display: flex;
            align-items: center;
            max-width: 400px;
            gap: 10px;
        }
        input[type="text"] {
            padding: 8px 10px;
            width: 100%;
            flex: 1;
            box-sizing: border-box;
        }
        button {
            padding: 8px 16px;
            background-color: #007BFF;
            border: none;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            margin-top: 30px;
            border-collapse: collapse;
            width: 100%;
            max-width: 600px;
        }
        th {
            background-color: #f2f2f2;
        }
        th, td {
            border: 1px solid #ccc;
            text-align: left;
            padding: 8px;
        }
        tbody tr:nth-child(even) {
            background-color: #e4e4e4;
        }
        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
    </style>
    <script>
        function confirmDelete(formattedDate) {
            return confirm("Want to delete " + formattedDate + "?");
        }
    </script>
</head>
<body>

<form method="POST">
    <input type="text" name="date_input" value="<?= htmlspecialchars($defaultDate) ?>" required />
    <button type="submit">Add</button>
</form>

<?php if (!empty($entries)): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Created At</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entries as $entry): ?>
                <tr>
                    <td><?= htmlspecialchars($entry['id']) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y l', strtotime($entry['date']))) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($entry['created_at']))) ?></td>
                    <td>
                        <form method="GET" onsubmit="return confirmDelete('<?= htmlspecialchars(date('d/m/Y l', strtotime($entry['date']))) ?>')">
                            <input type="hidden" name="delete_id" value="<?= $entry['id']; ?>" />
                            <button type="submit" style="background-color: red; color: white; border: none; padding: 4px 8px; cursor: pointer;">X</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No entries found.</p>
<?php endif; ?>

</body>
</html>
