<?php
include 'sidebar.php';
include 'db.php';
session_start();
if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
}
date_default_timezone_set('Asia/Manila');

$current_customer = isset($_SESSION['current_customer']) ? $_SESSION['current_customer'] : null;

if ($current_customer) {
  // Define variables here
  $customer_name = htmlspecialchars($current_customer['customer_name']);
  $contact = htmlspecialchars($current_customer['contact']);
  $services = $current_customer['services']; // Array of services
  $note = htmlspecialchars($current_customer['note']);
  $total_price = htmlspecialchars($current_customer['total_price']);
  $date = date("M, j, Y"); // Current date
  $time_queued = htmlspecialchars($current_customer['time_queued']); // Assuming this is already set when the customer was queued
  $time_processed = htmlspecialchars($current_customer['time_processed']); // Assuming this is already set when processing started
  $time_finished = date("h:i A"); // Current timestamp

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $services_str = implode(", ", $services); // Convert array to comma-separated string
    $sql = "INSERT INTO records (customer_name, contact, services, note, price, date, time_queued, time_processed, time_finished) 
                VALUES ('$customer_name', '$contact', '$services_str', '$note', '$total_price', '$date', '$time_queued', '$time_processed', '$time_finished')";

    if ($conn->query($sql) === TRUE) {
      echo "Operation Success, Added to Records";
    } else {
      echo "Error: Could not add to records. " . $conn->error;
    }

    unset($_SESSION['current_customer']); // Clear the current customer
    header('Location: queue.php'); // Redirect back to queue.php
    exit();
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
  <link rel="stylesheet" href="styles/current.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
</head>

<body>
  <div id="main-content" class="main-content">
    <div id="header" class="header">
      <img class="logo" src="icons/logo1.png" alt="">
      <p><span class="brand_name">Leah's M Queue</span> &nbsp; Current Customer</p>
    </div>
    <div id="content" class="content">
      <?php if ($current_customer): ?>
        <h1>Current Customer Details</h1>
        <p>Customer Name: <?= $customer_name ?></p>
        <p>Contact: <?= $contact ?></p>
        <p>Note: <?= $note ?></p>
        <p>Total Price: <?= $total_price ?> PHP</p>
        <p>Estimated Processing Time: <?= htmlspecialchars($current_customer['total_time']) ?></p>
        <p>Services:</p>
        <ul class="task-list">
          <?php foreach ($services as $service): ?>
            <li>
              <label class="task-item">
                <input type="checkbox"> <?= htmlspecialchars($service) ?>
              </label>
            </li>
          <?php endforeach; ?>
        </ul>
        <form method="POST" action="">
          <button type="submit" class="submit-btn" onclick="checkAll(event)">Done</button>
        </form>
      <?php else: ?>
        <p>There is no current customer.</p>
      <?php endif; ?>
    </div>
  </div>
  <script>
    function checkAll(event) {
      event.preventDefault(); // Prevent the default form submission

      Swal.fire({
        title: "Are you sure?",
        text: "Have you finished serving this customer?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        confirmButtonText: "Yes, all done!",
        cancelButtonText: "No",

      }).then((result) => {
        if (result.isConfirmed) {
          // If confirmed, submit the form
          document.querySelector('form').submit();
        } else {
          Swal.fire("Cancelled", "Finish serving the customer first", "info");
        }
      });

    }
  </script>
  <script src="script/sidebar.js"></script>
</body>

</html>