<?php
// Connect to PostgreSQL
$conn = pg_connect("host=localhost dbname=myresume user=postgres password=122604");
if (!$conn) {
    die("❌ Database connection failed: " . pg_last_error());
}

// Fetch users who have at least filled out fullname or email
$query = "
    SELECT username, fullname 
    FROM users 
    WHERE TRIM(fullname) <> '' OR TRIM(email) <> ''
    ORDER BY fullname ASC
";
$result = pg_query($conn, $query);
$users = pg_fetch_all($result);

pg_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Resume Gallery</title>
<style>
    * { box-sizing: border-box; }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: radial-gradient(circle at top left, #ff9ecd, #e78bff, #fcb3b3, #ff6f91);
        background-attachment: fixed;
        background-repeat: no-repeat;
        background-size: cover;
        min-height: 100vh;
        margin: 0;
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #333;
        position: relative;
    }

    h1 {
        color: #d63384;
        font-weight: 700;
        margin-bottom: 25px;
        text-shadow: 1px 1px rgba(255, 255, 255, 0.6);
    }

    /* Scrollable Gallery Container */
    .gallery-wrapper {
        position: relative;
        width: 100%;
        max-width: 900px;
        height: 500px;
        overflow-y: auto;
        padding-right: 10px;
        scroll-behavior: smooth;
    }

    /* Fading shadow at bottom */
    .gallery-wrapper::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 70px;
        background: linear-gradient(to top, rgba(255,255,255,0.8), rgba(255,255,255,0));
        pointer-events: none;
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
    }

    .gallery-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        padding-bottom: 70px; /* space before fade */
    }

    .card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(255, 123, 172, 0.3);
        padding: 25px 20px;
        text-align: center;
        transition: transform 0.2s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(255, 123, 172, 0.4);
    }

    .fullname {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }

    .view-btn {
        display: inline-block;
        background: linear-gradient(135deg, #ff6f91, #ff8abf, #e78bff);
        color: white;
        padding: 10px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .view-btn:hover {
        opacity: 0.9;
    }

    .no-resume {
        color: #555;
        font-style: italic;
        background: rgba(255,255,255,0.8);
        padding: 15px 25px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        margin-top: 100px;
    }

    .back-btn {
        margin-top: 25px;
        background: linear-gradient(135deg, #e78bff, #ff8abf);
        color: white;
        padding: 12px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .back-btn:hover {
        opacity: 0.9;
    }

    /* Custom scrollbar for consistency */
    .gallery-wrapper::-webkit-scrollbar {
        width: 8px;
    }

    .gallery-wrapper::-webkit-scrollbar-thumb {
        background: #ff8abf;
        border-radius: 10px;
    }

    .gallery-wrapper::-webkit-scrollbar-thumb:hover {
        background: #d63384;
    }

</style>
</head>
<body>

    <h1>🎓 Resume Gallery</h1>

    <?php if ($users && count($users) > 0): ?>
        <div class="gallery-wrapper">
            <div class="gallery-container">
                <?php foreach ($users as $user): ?>
                    <div class="card">
                        <div class="fullname"><?= htmlspecialchars($user['fullname'] ?: $user['username']) ?></div>
                        <a class="view-btn" href="public_resume.php?username=<?= urlencode($user['username']) ?>" target="_blank">
                            View Resume
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <p class="no-resume">No public resumes available yet.</p>
    <?php endif; ?>

    <a class="back-btn" href="login.php">⬅ Back to Login</a>

</body>
</html>
