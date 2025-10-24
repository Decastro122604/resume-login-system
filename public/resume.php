<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$name = "Angel Lyka Mae De Castro";
$title = "Computer Science Student";
$contact = [
    "Phone" => "09990071803 / 0968 669 0443",
    "Address" => "567, Purok 5, Ayao Iyao, Lemery Batangas",
    "Email" => "23-09904@g.batstate-u.edu.ph"
];
$skills = [
    ["name" => "PHP", "percent" => 50],
    ["name" => "JavaScript", "percent" => 63],
    ["name" => "Dart", "percent" => 72],
    ["name" => "HTML", "percent" => 83],
    ["name" => "CSS", "percent" => 88],
    ["name" => "MySQL", "percent" => 84],
    ["name" => "C++", "percent" => 91],
];
$experience = [
    "Served an average of 20 customers per day at our own convenience store.",
    "Maintained efficient records of stock inventory levels ensuring sufficient weekly product replenishment.",
    "Founded a Facebook Thrift Boutique focusing on sustainable and ethical fashion.",
    "Worked with various employees during OJT at Municipal Environment and Natural Resources Office within 80 hours.",
    "Service Crew / Cashier / Inventory Clerk / Bartender at Horizone Bar & Grill."
];
$education = [
    [
        "school" => "Batangas State University",
        "degree" => "Bachelor of Science Computer Science",
        "years" => "2023 - Present"
    ],
    [
        "school" => "Lemery Senior High",
        "degree" => "Science, Technology, Engineering, and Mathematics (STEM)",
        "years" => "2021 - 2023"
    ]
];

$profileImage = "profile.jpg";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($name) ?> — Resume</title>
  <style>
    :root{
      --peach:#f7d7cf;     
      --accent:#f1a07d;    
      --text:#111;
      --muted:#6b6b6b;
      --card:#ffffff;
      --container-bg:#f4efec;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background:var(--container-bg);
      color:var(--text);
      -webkit-font-smoothing:antialiased;
    }

    .resume-wrap{
      max-width:1000px;
      margin:32px auto;
      display:grid;
      grid-template-columns:320px 1fr;
      gap:0;
      background:var(--card);
      border-radius:10px;
      overflow:hidden;
      box-shadow:0 10px 30px rgba(18,18,18,0.12);
    }
    .sidebar{
      background:var(--peach);
      padding:28px;
      display:flex;
      flex-direction:column;
      align-items:center;
      gap:22px;
    }
    .photo-wrap{
      width:170px;
      height:170px;
      border-radius:50%;
      padding:6px;
      background:linear-gradient(180deg, rgba(0,0,0,0.06), rgba(0,0,0,0.02));
      display:flex;
      align-items:center;
      justify-content:center;
      box-shadow:0 6px 16px rgba(0,0,0,0.08) inset;
    }
    .photo-wrap img{
      width:100%;
      height:100%;
      object-fit:cover;
      border-radius:50%;
      display:block;
      background:#fff;
    }
    .skills-title{
      align-self:flex-start;
      font-weight:700;
      letter-spacing:1px;
      margin-left:6px;
      color: #2b2b2b;
    }
    /* skills list (horizontal bars) */
    .skills-list {
      display: flex;
      flex-direction: column;
      gap: 14px;
      width: 100%;
    }
    .skill .label {
      font-size: 14px;
      font-weight: 600;
      color: #4a2b20;
      display: flex;
      justify-content: space-between;
      margin-bottom: 4px;
    }
    .skill .bar {
      height: 10px;
      border-radius: 10px;
      background: #ffe1d6;
      overflow: hidden;
    }
    .skill .fill {
      height: 100%;
      border-radius: 10px;
      background: linear-gradient(90deg, #ff9d76, #ff6f61);
    }
    .skill .pct {
      font-weight: 600;
      font-size: 13px;
      color: #333;
    }


    /* RIGHT CONTENT */
    .content{
      padding:28px 38px;
    }
    .header-top{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:10px;
    }
    .name-block{
      margin-bottom:8px;
    }
    .name-block h1{
      margin:0;
      font-size:28px;
      letter-spacing:0.5px;
      color:#1f1f1f;
      font-weight:800;
    }
    .name-block .subtitle{
      margin-top:6px;
      color:var(--muted);
      font-weight:600;
      letter-spacing:1.2px;
    }

    .contact{
      color:var(--muted);
      font-size:14px;
      display:flex;
      flex-direction:column;
      gap:6px;
      text-align:right;
    }

    .section{
      margin-top:18px;
    }
    .section h3{
      margin:0 0 10px 0;
      font-size:18px;
      letter-spacing:0.8px;
      color:#242424;
    }
    .bullets{ margin:0; padding-left:20px; }
    .bullets li { margin-bottom:10px; color:#2b2b2b; line-height:1.45; }

    .education .school{
      margin-bottom:12px;
    }
    .school .school-name{
      font-weight:700;
      color:#222;
    }
    .school .meta{
      color:var(--muted);
      font-size:13px;
    }

    .logout-wrap{
      position: fixed;
      bottom: 20px;
      right: 20px;
    }
    .btn-logout{
      background:#d9534f;
      color:#fff;
      padding:8px 14px;
      border-radius:6px;
      text-decoration:none;
      font-weight:700;
      letter-spacing:0.6px;
    }
    .btn-logout:hover{ background:#c43f3a; }

    /* responsive */
    @media (max-width:880px){
      .resume-wrap{ grid-template-columns:1fr; }
      .contact{ text-align:left; margin-top:10px; }
      .logout-wrap{ justify-content:flex-start; }
    }
  </style>
</head>
<body>
  <div class="resume-wrap" role="main">
    <!-- LEFT -->
    <aside class="sidebar" aria-label="Profile sidebar">
      <div class="photo-wrap">
        <?php if (file_exists($profileImage)): ?>
          <img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile photo of <?= htmlspecialchars($name) ?>">
        <?php else: ?>
          <!-- placeholder circle -->
          <img src="data:image/svg+xml;utf8,
            <svg xmlns='http://www.w3.org/2000/svg' width='300' height='300'>
              <rect width='100%' height='100%' fill='%23f7d7cf'/>
            </svg>" alt="profile placeholder">
        <?php endif; ?>
      </div>

      <div style="text-align:center;">
        <div style="font-weight:800; font-size:18px; color:#3d2b29;"><?= htmlspecialchars($name) ?></div>
        <div style="color:var(--muted); font-weight:600; margin-top:6px;"><?= htmlspecialchars($title) ?></div>
      </div>

      <section class="skills">
        <h2>Skills</h2>
        <div class="skills-title">Skills</div>
        <div class="skills-list">
          <?php foreach ($skills as $s): 
              $p = (int)$s['percent']; ?>
            <div class="skill">
              <div class="label">
                <?= htmlspecialchars($s['name']) ?>
                <span class="pct"><?= $p ?>%</span>
              </div>
              <div class="bar">
                <div class="fill" style="width: <?= $p ?>%"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    </aside>

    <!-- RIGHT -->
    <section class="content" aria-label="Resume content">
      <div class="header-top">
        <div class="name-block">
          <h1><?= htmlspecialchars($name) ?></h1>
          <div class="subtitle"><?= htmlspecialchars($title) ?></div>
        </div>

        <div class="contact" aria-label="Contact information">
          <div>📞 <?= htmlspecialchars($contact['Phone']) ?></div>
          <div>📍 <?= htmlspecialchars($contact['Address']) ?></div>
          <div>✉️ <?= htmlspecialchars($contact['Email']) ?></div>
        </div>
      </div>

      <div class="section work">
        <h3>WORK EXPERIENCE</h3>
        <ul class="bullets">
          <?php foreach ($experience as $exp): ?>
            <li><?= htmlspecialchars($exp) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="section education">
        <h3>EDUCATION</h3>
        <?php foreach ($education as $edu): ?>
          <div class="school">
            <div class="school-name"><?= htmlspecialchars($edu['school']) ?></div>
            <div class="meta"><?= htmlspecialchars($edu['degree']) ?> — <?= htmlspecialchars($edu['years']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="logout-wrap">
        <a href="logout.php" class="btn-logout" onclick="return confirm('Are you sure you want to log out?');">LOGOUT</a>
      </div>
    </section>
  </div>

  <script>
    
  </script>
</body>
</html>
