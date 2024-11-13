<?php
session_start();
include 'sidebar.php';
include 'db.php';
$username = $_SESSION['username'];

if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
}

$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Define the services array
$services = [
  "Haircut" => ["price" => 70, "time" => 15],
  "Shampoo with Blowdry" => ["price" => 100, "time" => 20],
  "Hot Oil" => ["price" => 120, "time" => 45],
  "Hair Spa" => ["price" => 150, "time" => 45],
  "Cellophane" => ["price" => 350, "time" => 60],
  "Hair color" => ["price" => 350, "time" => 60],
  "Highlights" => ["price" => 400, "time" => 80],
  "Rebond with treatment" => ["price" => 1000, "time" => 240],
  "Rebond with color" => ["price" => 1500, "time" => 285],
  "Brazilian treatment" => ["price" => 700, "time" => 120],
  "Hair botox" => ["price" => 700, "time" => 120],
  "Traditional perm" => ["price" => 350, "time" => 60],
  "Digital perm" => ["price" => 1800, "time" => 120],
  "Manicure" => ["price" => 70, "time" => 20],
  "Pedicure" => ["price" => 80, "time" => 45],
  "Change polish" => ["price" => 50, "time" => 20],
  "Footspa regular" => ["price" => 200, "time" => 45],
  "Footspa with leg massage and pedicure" => ["price" => 270, "time" => 60],
  "Eyebrow shave" => ["price" => 50, "time" => 5],
  "Eyebrow threading" => ["price" => 100, "time" => 10],
  "Eyelash perm" => ["price" => 250, "time" => 30],
  "Eyelash extension" => ["price" => 400, "time" => 60]
];

// Function to convert minutes into hours and minutes format
// Function to convert minutes into hours and minutes format
function formatTime($minutes)
{
  $hours = floor($minutes / 60); // Get the hours
  $remainingMinutes = $minutes % 60; // Get the remaining minutes

  if ($hours > 0) {
    return "{$hours}hr " . ($remainingMinutes > 0 ? "{$remainingMinutes}m" : ""); // Include hours if greater than 0
  } else {
    return "{$remainingMinutes}m"; // Only show minutes if hours are 0
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leah's M Queue</title>
  <link rel="stylesheet" href="styles/sidebar1.css">
  <link rel="stylesheet" href="styles/home.css"> <!-- Add your CSS file for styling the services -->
</head>

<body>

  <div id="main-content" class="main-content">
    <div id="header" class="header">
      <img class="logo" src="icons/logo1.png" alt="">
      <p>Welcome to <span class="brand_name">Leah's M Queue</span>, <?php echo htmlspecialchars($user['full_name']); ?>!
      </p>
    </div>

    <!-- Services Menu Container -->
     
      <div class="content">
      <p class="title">What would you like to do?</p>
      <div class="buttons">
      <button onclick="window.location.href='add.php'">Add a customer</button>
        <button onclick="window.location.href='queue.php'">View Queue</button>
        <button onclick="window.location.href='current.php'">View Current Customer</button>
        <button onclick="window.location.href='records.php'">Records</button>

      </div>
      <div class="services-menu">
        <h2>Available Services</h2>
        <ul>
          <?php foreach ($services as $service => $details): ?>
            <li>
              <strong><?php echo htmlspecialchars($service); ?></strong><br>
              Price: <?php echo htmlspecialchars($details['price']); ?> PHP<br>
              Service duration: <?php echo formatTime($details['time']); ?> <!-- Convert minutes to hours and minutes -->
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

  </div>

  <script src="script/sidebar.js"></script>
</body>

</html>