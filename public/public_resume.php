<?php
// Connect to PostgreSQL
$conn = pg_connect("host=localhost dbname=myresume user=postgres password=122604");
if (!$conn) {
    die("❌ Database connection failed: " . pg_last_error());
}

// Get username from URL
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

// Fetch attachments
$attachments = [];
$att_query = pg_query_params($conn, "SELECT * FROM attachments WHERE username = $1", [$username]);
if ($att_query && pg_num_rows($att_query) > 0) {
    while ($row = pg_fetch_assoc($att_query)) {
        $attachments[] = $row;
    }
}

pg_close($conn);

// Split experience and skills into separate pink bars
$experiences = array_filter(array_map('trim', explode("\n", $user['experience'] ?? '')));
$skills = array_filter(array_map('trim', explode("\n", $user['skills'] ?? '')));

// Detect and fix Google Drive links (convert to direct image URL)
$profilePic = trim($user['profile_picture'] ?? '');
if (preg_match('/drive\.google\.com\/file\/d\/(.*?)\//', $profilePic, $matches)) {
    $fileId = $matches[1];
    $profilePic = "https://drive.google.com/uc?export=view&id=" . $fileId;
}

// Default fallback image if profile_picture is empty or invalid
if (empty($profilePic)) {
    $profilePic = "https://via.placeholder.com/120?text=No+Image";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($user['fullname'] ?? 'Public Resume') ?></title>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: radial-gradient(circle at top left, #ff9ecd, #e78bff, #fcb3b3, #ff6f91);
            background-attachment: fixed;
            display:flex;
            background-repeat: no-repeat;
            background-size: cover;
            padding: 40px 20px;
            color: #333;
        }

        .resume {
            background: white;
            max-width: 700px;
            margin: auto;
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(255, 182, 193, 0.4);
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .profile-header img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ffb6c1;
            box-shadow: 0 0 15px rgba(255, 192, 203, 0.6);
            background-color: #fff;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #444;
            margin-bottom: 8px;
            text-align: left;
        }

        .contact-info {
            font-size: 15px;
            text-align: left;
        }

        .contact-info p {
            margin: 3px 0;
        }

        .contact-info a {
            color: #ff5c8d;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        h3 {
            color: #ff5c8d;
            text-transform: uppercase;
            font-size: 16px;
            margin-top: 30px;
            margin-bottom: 10px;
        }

        .bar {
            background: #F3C8DD;
            border-radius: 25px;
            padding: 10px 15px;
            margin-bottom: 10px;
            border: 1px solid #D183A9;
            box-shadow: 0 3px 6px rgba(0,0,0,0.08);
        }

        a.attachment-link {
            display: block;
            color: #444;
            background: #ffe4ee;
            border-radius: 20px;
            padding: 8px 15px;
            margin: 6px 0;
            text-decoration: none;
            border: 1px solid #f4a6b4;
            transition: all 0.2s;
        }

        a.attachment-link:hover {
            background: #ffb6c1;
            color: white;
        }

        @media (max-width: 600px) {
            .resume {
                padding: 25px;
            }
            h1 {
                font-size: 22px;
            }
            .profile-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

    </style>
</head>
<body>
    <div class="resume">
        <div class="profile-header">
            <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile Picture">

            <div>
                <h1><?= htmlspecialchars($user['fullname']) ?></h1>
                <div class="contact-info">
                    <p>📞 <?= htmlspecialchars($user['contact']) ?></p>
                    <p>✉️ <?= htmlspecialchars($user['email']) ?></p>
                    <p>🏠 <?= htmlspecialchars($user['address']) ?></p>
                    <?php if (!empty($user['linkedin'])): ?>
                        <p>🔗 <a href="<?= htmlspecialchars($user['linkedin']) ?>" target="_blank">LinkedIn Profile</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <h3>Education</h3>
        <div class="bar"><?= htmlspecialchars($user['education']) ?></div>

        <h3>Experience</h3>
        <?php foreach ($experiences as $exp): ?>
            <div class="bar"><?= htmlspecialchars($exp) ?></div>
        <?php endforeach; ?>

        <h3>Skills</h3>
        <?php foreach ($skills as $skill): ?>
            <div class="bar"><?= htmlspecialchars($skill) ?></div>
        <?php endforeach; ?>

        <?php if (!empty($attachments)): ?>
            <h3>Attachments</h3>
            <?php foreach ($attachments as $file): ?>
                <a class="attachment-link" href="<?= htmlspecialchars($file['file_path']) ?>" target="_blank">
                    📄 <?= htmlspecialchars($file['file_name']) ?> (<?= htmlspecialchars($file['file_type']) ?>)
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
