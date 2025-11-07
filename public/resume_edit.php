<?php
session_start();

// ✅ Redirect if not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// ✅ Connect to PostgreSQL
$conn = pg_connect("host=localhost dbname=myresume user=postgres password=122604");
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$username = $_SESSION['username'] ?? '';
$message = "";

// ✅ Check if user exists in database
$fetch = pg_query_params($conn, "SELECT * FROM users WHERE username = $1", [$username]);
if ($fetch && pg_num_rows($fetch) > 0) {
    $user = pg_fetch_assoc($fetch);
} else {
    // If user exists but no resume yet, create a blank record
    $insert = pg_query_params($conn, "
        INSERT INTO users (username, fullname, contact, email, linkedin, profile_picture, address, education, experience, skills)
        VALUES ($1, '', '', '', '', '', '', '', '', '')
    ", [$username]);
    
    if ($insert) {
        $user = [
            'fullname' => '',
            'contact' => '',
            'email' => '',
            'linkedin' => '',
            'profile_picture' => '',
            'address' => '',
            'education' => '',
            'experience' => '',
            'skills' => ''
        ];
    } else {
        die("<p style='color:red;'>❌ Error creating new blank resume: " . pg_last_error($conn) . "</p>");
    }
}

// ✅ Handle deleting attachments
if (isset($_POST['delete_attachment'])) {
    $fileName = $_POST['delete_attachment'];
    pg_query_params($conn, "DELETE FROM attachments WHERE username=$1 AND file_name=$2", [$username, $fileName]);
    $message = "<p class='success'>🗑️ Attachment deleted successfully!</p>";
}

// ✅ Handle form updates and file uploads
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['delete_attachment'])) {
    $fullname = trim($_POST['fullname']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $linkedin = trim($_POST['linkedin']);
    $profile_picture = trim($_POST['profile_picture']);
    $address = trim($_POST['address']);
    $education = trim($_POST['education']);
    $experience = trim($_POST['experience']);
    $skills = trim($_POST['skills']);

    $update = "
        UPDATE users 
        SET fullname=$1, contact=$2, email=$3, linkedin=$4, profile_picture=$5,
            address=$6, education=$7, experience=$8, skills=$9
        WHERE username=$10
    ";
    $res = pg_query_params($conn, $update, [
        $fullname, $contact, $email, $linkedin, $profile_picture,
        $address, $education, $experience, $skills, $username
    ]);

    // ✅ File uploads (same logic)
    if (!empty($_FILES['attachments']['name'][0])) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['attachments']['tmp_name'] as $key => $tmpName) {
            $fileName = basename($_FILES['attachments']['name'][$key]);
            $fileType = $_FILES['attachments']['type'][$key];
            $filePath = $uploadDir . $fileName;

            $checkQuery = pg_query_params($conn,
                "SELECT 1 FROM attachments WHERE username=$1 AND file_name=$2",
                [$username, $fileName]
            );

            if (pg_num_rows($checkQuery) === 0 && move_uploaded_file($tmpName, $filePath)) {
                pg_query_params($conn,
                    "INSERT INTO attachments (username, file_name, file_type, file_path)
                     VALUES ($1, $2, $3, $4)",
                    [$username, $fileName, $fileType, "uploads/" . $fileName]
                );
            }
        }
    }

    if ($res) {
        $message = "<p class='success'>✅ Resume saved successfully!</p>";
        $fetch = pg_query_params($conn, "SELECT * FROM users WHERE username = $1", [$username]);
        $user = pg_fetch_assoc($fetch);
    } else {
        $message = "<p class='error'>❌ Update failed: " . pg_last_error($conn) . "</p>";
    }
}

// ✅ Fetch existing attachments
$attachments = pg_query_params($conn, "SELECT * FROM attachments WHERE username=$1", [$username]);

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
            background: radial-gradient(circle at top left, #ff9ecd, #e78bff, #fcb3b3, #ff6f91);
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 0;
        }

        form {
            background: white;
            padding: 35px 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            width: 620px;
            box-sizing: border-box;
        }

        input, textarea {
            width: 100%;
            padding: 8px;
            margin: 6px 0 14px 0;
            border: 1px solid #6A1452;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .attachments {
            margin-top: 20px;
            padding: 15px;
            background: #fff8fa;
            border-radius: 10px;
            border: 1px solid #6A1452;
            box-sizing: border-box;
        }

        .attachments h4 {
            margin-bottom: 12px;
            color: #333;
        }

        .attachment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .delete-btn {
            background: linear-gradient(135deg, #ff6f91, #ff8abf, #e78bff);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: #ff9a9e;
        }

        button {
            width: 100%;
            background: linear-gradient(135deg, #ff6f91, #ff8abf, #e78bff);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 25px;
        }

        button:hover {
            background: #ff9a9e;
        }

        .success { color: green; text-align: center; }
        .error { color: red; text-align: center; }

        .bottom-links {
            text-align: center;
            margin-top: 18px;
        }

    </style>
</head>
<body>
    <div class="container">
        <form method="POST" enctype="multipart/form-data">
            <h2 style="text-align:center; color:#ff4081;">Edit Your Resume</h2>
            <?= $message ?>

            <label>Full Name</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>

            <label>Contact</label>
            <input type="text" name="contact" value="<?= htmlspecialchars($user['contact'] ?? '') ?>">

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">

            <label>LinkedIn</label>
            <input type="url" name="linkedin" placeholder="https://linkedin.com/in/username" value="<?= htmlspecialchars($user['linkedin'] ?? '') ?>">

            <label>Profile Picture URL</label>
            <input type="url" name="profile_picture" placeholder="https://example.com/image.jpg" value="<?= htmlspecialchars($user['profile_picture'] ?? '') ?>">

            <label>Address</label>
            <textarea name="address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>

            <label>Education</label>
            <textarea name="education"><?= htmlspecialchars($user['education'] ?? '') ?></textarea>

            <label>Experience</label>
            <textarea name="experience"><?= htmlspecialchars($user['experience'] ?? '') ?></textarea>

            <label>Skills</label>
            <textarea name="skills"><?= htmlspecialchars($user['skills'] ?? '') ?></textarea>

            <label>📎 Upload Attachments (Certificates, Awards, etc.)</label>
            <input type="file" name="attachments[]" multiple>

            <?php if ($attachments && pg_num_rows($attachments) > 0): ?>
                <div class="attachments">
                    <h4>📂 Existing Attachments</h4>
                    <?php while ($file = pg_fetch_assoc($attachments)): ?>
                        <div class="attachment-item">
                            <a href="<?= htmlspecialchars($file['file_path']) ?>" target="_blank">
                                <?= htmlspecialchars($file['file_name']) ?> (<?= htmlspecialchars($file['file_type']) ?>)
                            </a>
                            <button type="submit" name="delete_attachment" value="<?= htmlspecialchars($file['file_name']) ?>" class="delete-btn">Delete</button>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <button type="submit">Save Changes</button>

            <div class="bottom-links">
                <a href="logout.php">Logout</a> |
                <?php if (isset($_SESSION['username'])): ?>
                    <a href="public_resume.php?username=<?= urlencode($_SESSION['username']) ?>" target="_blank">View Public Resume</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</body>
</html>
