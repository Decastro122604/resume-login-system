<?php
// PostgreSQL connection
$conn = pg_connect("host=localhost dbname=myresume user=postgres password=122604");
if (!$conn) {
    die("❌ Database connection failed: " . pg_last_error());
}

// Get username from URL (e.g. ?username=angel)
$username = $_GET['username'] ?? null;

if (!$username) {
    die("<p style='color:red;'>❌ Missing username in URL.</p>");
}

// Fetch user data
$query = "SELECT * FROM users WHERE username = $1";
$result = pg_query_params($conn, $query, [$username]);
$user = pg_fetch_assoc($result);

if (!$user) {
    die("<p style='color:red;'>❌ No resume found for that username.</p>");
}

pg_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($user['fullname'] ?? 'Public Resume') ?></title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #c2e9fb, #e2f0cb);
            padding: 40px;
        }
        .resume {
            background: white;
            padding: 25px;
            border-radius: 12px;
            max-width: 700px;
            margin: 0 auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        h1 { color: #333; }
        h3 { margin-top: 20px; color: #5a5a5a; }
        p { color: #444; }
    </style>
</head>
<body>
    <div class="resume">
        <h1><?= htmlspecialchars($user['fullname'] ?? 'No Name Provided') ?></h1>
        <p><strong>Contact:</strong> <?= htmlspecialchars($user['contact'] ?? '') ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($user['address'] ?? '') ?></p>

        <h3>Education</h3>
        <p><?= nl2br(htmlspecialchars($user['education'] ?? '')) ?></p>

        <h3>Experience</h3>
        <p><?= nl2br(htmlspecialchars($user['experience'] ?? '')) ?></p>

        <h3>Skills</h3>
        <p><?= nl2br(htmlspecialchars($user['skills'] ?? '')) ?></p>
    </div>
</body>
</html>