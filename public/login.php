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
            $message = "<p class='success'>Login Successful. Redirecting...</p>";
            header("refresh:2;url=resume_edit.php");
        } else {
            $message = "<p class='error'>Invalid Username or Password</p>";
        }
    }
}
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
      background: linear-gradient(135deg, #ff9a9e, #fad0c4);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      color: #333;
    }
    .login-box {
      background: #fff;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
      width: 320px;
      text-align: center;
    }
    h2 {
      margin-bottom: 30px;
      font-weight: 700;
      color: #ff8b8fff;
    }
    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 14px 15px;
      margin: 12px 0;
      border: 1.5px solid #ddd;
      border-radius: 8px;
      font-size: 16px;
      transition: border-color 0.3s ease;
    }
    input[type="text"]:focus, input[type="password"]:focus {
      border-color: #fad0c4;
      outline: none;
    }
    button {
      width: 100%;
      padding: 15px;
      background: #fea187ff;
      color: white;
      font-size: 18px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 20px;
      transition: background-color 0.3s ease;
    }
    button:hover {
      background: #ff9a9e;
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
  </div>
</body>
</html>
