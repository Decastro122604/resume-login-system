<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// PostgreSQL connection
$conn = pg_connect("host=localhost dbname=myresume user=postgres password=122604");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$username = $_SESSION['username'] ?? '';
$message = "";

// Always start with empty fields
$user = [
    'fullname' => '',
    'contact' => '',
    'address' => '',
    'education' => '',
    'experience' => '',
    'skills' => ''
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Capture form inputs
    $fullname = trim($_POST['fullname']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $education = trim($_POST['education']);
    $experience = trim($_POST['experience']);
    $skills = trim($_POST['skills']);

    // Save or update to database
    $update = "UPDATE users 
               SET fullname=$1, contact=$2, address=$3, education=$4, experience=$5, skills=$6
               WHERE username=$7";

    $res = pg_query_params($conn, $update, [$fullname, $contact, $address, $education, $experience, $skills, $username]);

    if ($res) {
        $message = "<p class='success'>✅ Resume updated successfully!</p>";

        // Clear form after saving
        $user = [
            'fullname' => '',
            'contact' => '',
            'address' => '',
            'education' => '',
            'experience' => '',
            'skills' => ''
        ];
    } else {
        $message = "<p class='error'>❌ Update failed: " . pg_last_error($conn) . "</p>";
    }
}

pg_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Resume</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #a8edea, #fed6e3);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            width: 400px;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            background: #5c67f2;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #4a54e1;
        }
        .success { color: green; text-align: center; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Edit Your Resume</h2>
        <?= $message ?>

        <label>Full Name</label>
        <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>

        <label>Contact</label>
        <input type="text" name="contact" value="<?= htmlspecialchars($user['contact'] ?? '') ?>">

        <label>Address</label>
        <textarea name="address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>

        <label>Education</label>
        <textarea name="education"><?= htmlspecialchars($user['education'] ?? '') ?></textarea>

        <label>Experience</label>
        <textarea name="experience"><?= htmlspecialchars($user['experience'] ?? '') ?></textarea>

        <label>Skills</label>
        <textarea name="skills"><?= htmlspecialchars($user['skills'] ?? '') ?></textarea>

        <button type="submit">Save Changes</button>
        <br><br>
        <a href="logout.php">Logout</a> |
        
        <?php if (isset($_SESSION['username'])): ?>
            <a href="public_resume.php?username=<?= urlencode($_SESSION['username']) ?>" 
               target="_blank" 
               class="btn">View Public Resume</a>
        <?php endif; ?>
    </form>
</body>
</html>