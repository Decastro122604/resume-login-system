<?php
session_start();

$message = "";

// PostgreSQL connection
$conn = pg_connect("host=localhost dbname=myresume user=postgres password=122604");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $message = "<p class='error'>All fields are required!</p>";
    } else {
        // Query user from database
        $result = pg_query_params($conn, 
            "SELECT * FROM users WHERE username = $1 AND password = $2", 
            [$username, $password]
        );

        if (pg_num_rows($result) > 0) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;

            // ✅ Check if user has a resume
            $checkResume = pg_query_params($conn, 
                "SELECT * FROM users WHERE username = $1 AND (fullname IS NOT NULL OR email IS NOT NULL)", 
                [$username]
            );

            // ✅ If no resume data yet, initialize blank resume fields
            if (pg_num_rows($checkResume) === 0) {
                pg_query_params($conn, 
                    "UPDATE users SET fullname='', contact='', email='', linkedin='', profile_picture='', 
                    address='', education='', experience='', skills='' WHERE username=$1", 
                    [$username]
                );
            }

            $message = "<p class='success'>Login Successful. Redirecting...</p>";
            header("refresh:2;url=resume_edit.php");
        } else {
            $message = "<p class='error'>Invalid Username or Password</p>";
        }
    }
}

// ✅ Safe variable for current username (prevents undefined warnings)
$currentUser = $_SESSION['username'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login Page</title>
  <style>
  * { box-sizing: border-box; }
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: radial-gradient(circle at top left, #ff9ecd, #e78bff, #fcb3b3, #ff6f91);
    background-attachment: fixed;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    color: #333;
  }
  .login-box {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px 30px;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(255, 123, 172, 0.4);
    width: 320px;
    text-align: center;
  }
  h2 {
    margin-bottom: 30px;
    font-weight: 700;
    color: #d63384;
  }
  input[type="text"], input[type="password"] {
    width: 100%;
    padding: 14px 15px;
    margin: 12px 0;
    border: 1.5px solid #f2c2d4;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s ease;
  }
  input[type="text"]:focus, input[type="password"]:focus {
    border-color: #ff8ac0;
    outline: none;
  }
  button {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #ff6f91, #ff8abf, #e78bff);
    color: white;
    font-size: 18px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 20px;
    transition: opacity 0.3s ease;
  }
  button:hover {
    opacity: 0.9;
  }
  .error {
    color: #d32f2f;
    margin-top: 15px;
    font-weight: 600;
  }
  .success {
    color: #388e3c;
    margin-top: 15px;
    font-weight: 600;
  }
  .public-btn {
    display: inline-block;
    background: linear-gradient(135deg, #e78bff, #ff8abf);
    color: white;
    padding: 12px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    font-size: 15px;
    margin-top: 18px;
    transition: all 0.3s ease;
  }
  .public-btn:hover {
    opacity: 0.9;
  }
</style>

</head>
<body>
  <div class="login-box">
    <h2>Login</h2>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" />
      <input type="password" name="password" placeholder="Password" />
      <button type="submit">Login</button>
    </form>

    <?= $message ?>

    <!-- View Public Resume Buttons -->
    <a class="public-btn" href="resume_gallery.php" target="_blank">View All Public Resumes</a>
    <a class="public-btn" href="public_resume.php?username=<?= urlencode($currentUser) ?>" target="_blank">View My Resume</a>

  </div>
</body>
</html>
