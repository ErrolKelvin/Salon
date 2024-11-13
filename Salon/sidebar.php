<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leah's M Queue</title>
  <link rel="stylesheet" href="styles/sidebar.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div id="sidebar" class="sidebar">
    <div id="toggle-btn" class="toggle-btn" onclick="toggleSidebar()"> &#9776;</div>
    
    <p class="sb_label">LEAH'S M SALON</p>
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="add.php">Add Customer</a></li>
      <li><a href="queue.php">Queue</a></li>
      <li><a href="current.php">Current Customer</a></li>
      <li><a href="records.php">Records</a></li>
      <br><br><br><br><br><br><br><br><br>
      <li><a href="#" onclick="confirmLogout()">Logout</a></li>
    </ul>
  </div>
  <script src="script/sidebar.js"></script>
  <script>
    function confirmLogout() {
      Swal.fire({
        title: 'Log out?',
        text: "You will be logged out.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, logout'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'logout.php';
        }
      })
    }
  </script>
</body>

</html>
