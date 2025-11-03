<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to PostgreSQL properly
$conn = pg_connect("host=localhost dbname=myresume user=postgres password=122604");

if (!$conn) {
    die("❌ Connection failed: " . pg_last_error());
}

echo "✅ Connected to PostgreSQL successfully!<br>";

//Run query
$result = pg_query($conn, "SELECT id, username FROM users");

if ($result) {
    echo "✅ Query successful!<br>";
    while ($row = pg_fetch_assoc($result)) {
        echo "ID: " . htmlspecialchars($row['id']) . " | Username: " . htmlspecialchars($row['username']) . "<br>";
    }
} else {
    echo "❌ Query failed: " . pg_last_error($conn);
}

pg_close($conn);
?>
