<?php
include 'sidebar.php';
session_start();
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
}

// Define services with prices and estimated times
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

function formatTime($minutes)
{
  $hours = floor($minutes / 60);
  $remainingMinutes = $minutes % 60;

  if ($hours > 0) {
    return "{$hours}hr " . ($remainingMinutes > 0 ? "{$remainingMinutes}m" : "");
  } else {
    return "{$remainingMinutes}m";
  }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $customer_name = $_POST['customer_name'];
  $contact = $_POST['contact'];
  $selected_services = $_POST['services'];
  $note = $_POST['note'];

  // Calculate total price and estimated time
  $total_price = 0;
  $total_time = 0;
  foreach ($selected_services as $service) {
    $total_price += $services[$service]['price'];
    $total_time += $services[$service]['time'];
  }

  // Store customer data in session
  $_SESSION['queue'][] = [
    'customer_name' => $customer_name,
    'contact' => $contact,
    'services' => $selected_services,
    'note' => $note,
    'total_price' => $total_price,
    'total_time' => formatTime($total_time),
    'date' => date("M, j, Y"),
    'time' => date("h:i A"),
    'time_queued' => date("h:i A")
  ];

  // Redirect to queue.php
  header("Location: queue.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leah's M Queue</title>
  <link rel="stylesheet" href="styles/add.css">
  <link rel="stylesheet" href="styles/sidebar1.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div id="main-content" class="main-content">
    <div id="header" class="header">
      <img class="logo" src="icons/logo1.png" alt="">
      <p><span class="brand_name">Leah's M Queue</span> &nbsp; Add Customer</p>
    </div>
    <div id="content" class="content">
      <div id="form_container" class="form_container">
        <form name="add_form" method="POST" action="">
          <div class="form-content">
            <label for="customer_name">Customer Name</label>
            <input type="text" name="customer_name" required><br>
            <label for="contact">Contact #</label>
            <input type="text" name="contact" required><br>
            <div class="service-container">
              <label for="services">Service</label><br>
              <label for="search-bar">Search Service</label>
              <input type="text" class="search-bar" id="serviceSearch" placeholder="Search services..."
                onkeyup="filterServices()">
              <div class="service-list" id="serviceList">
                <?php foreach ($services as $service => $details): ?>
                  <label>
                  <input type="checkbox" name="services[]" value="<?= $service ?>"> <?= $service ?> (<?= $details['price'] ?> PHP)
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
            <label for="customer_name">Note/ Special Request</label><br>
            <textarea name="note" id="note" rows="5" cols="60"></textarea>
          </div>
          <div class="form-submit-container">
            <button class="form-submit" type="submit">Add to Queue</button>
          </div>
        </form>
      </div>
    </div>
    <script src="script/sidebar.js"></script>
    <script src="script/scripting.js"></script>
    <script>
      function validateForm() {
        let services = document.querySelectorAll('input[name="services[]"]:checked');
        let contact = document.querySelector('input[name="contact"]').value;

        if (services.length === 0) {
          Swal.fire({
            icon: 'error',
            title: 'No Service Selected',
            text: 'Please select at least one service!'
          });
          return false;
        }

        let contactPattern = /^09\d{9}$/;
        if (!contactPattern.test(contact)) {
          Swal.fire({
            icon: 'error',
            title: 'Invalid Contact Number',
            text: 'Please enter a valid contact number (09XXXXXXXXX)!'
          });
          return false;
        }

        return true;
      }

      document.querySelector('form[name="add_form"]').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        if (!validateForm()) {
          return; // Stop if validation fails
        }

        const customerName = document.querySelector('input[name="customer_name"]').value;
        const contact = document.querySelector('input[name="contact"]').value;
        const services = Array.from(document.querySelectorAll('input[name="services[]"]:checked'))
          .map(checkbox => checkbox.value).join(', ');
        const note = document.querySelector('textarea[name="note"]').value;

        // Show confirmation dialog
        Swal.fire({
          title: 'Add to Queue?',
          text: 'Are you sure you want to add to queue?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Confirm',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            // Submit the form if confirmed
            event.target.submit();
          }
        });
      });
    </script>
  </div>
</body>

</html>
