<?php
include 'sidebar.php';
session_start();

if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
}

date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['customer_index'])) {
    // Ensure $_SESSION['queue'] is an array
    if (!isset($_SESSION['queue']) || !is_array($_SESSION['queue'])) {
        $_SESSION['queue'] = [];
    }

    // Check if there is already a current customer being processed
    if (!isset($_SESSION['current_customer'])) {
        // Dequeue the first customer if the queue is not empty
        if (!empty($_SESSION['queue'])) {
            $current_customer = array_shift($_SESSION['queue']);
            // Add time_processed to the current customer
            $current_customer['time_processed'] = date("h:i A");
            // Save current customer to session
            $_SESSION['current_customer'] = $current_customer;
        }

        header('Location: current.php'); // Redirect to current.php
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
    <link rel="stylesheet" href="styles/queue.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div id="main-content" class="main-content">
        <div id="header" class="header">
            <img class="logo" src="icons/logo1.png" alt="">
            <span class="brand_name">Leah's M Queue</span> &nbsp; Queue
        </div>
        <div id="content" class="content">
            <table>
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Contact #</th>
                        <th>Services</th>
                        <th>Date</th>
                        <th>Time Queued</th>
                        <th>Estimated Processing Time</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($_SESSION['queue']) && !empty($_SESSION['queue'])): ?>
                        <?php foreach ($_SESSION['queue'] as $index => $customer): ?>
                            <tr>
                                <td><?= htmlspecialchars($customer['customer_name']) ?></td>
                                <td><?= htmlspecialchars($customer['contact']) ?></td>
                                <td><?= htmlspecialchars(implode(", ", $customer['services'])) ?></td>
                                <td><?= htmlspecialchars($customer['date']) ?></td>
                                <td><?= htmlspecialchars($customer['time']) ?></td>
                                <td><?= htmlspecialchars($customer['total_time']) ?></td>
                                <td><?= htmlspecialchars($customer['total_price']) ?> PHP</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No customers in the queue.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (isset($_SESSION['queue']) && !empty($_SESSION['queue'])): ?>
                <form id="process-form" method="POST" action="">
                    <input type="hidden" name="customer_index" value="0">
                    <button type="submit" class="process-button" id="process-button"><b>Serve Next Customer</b></button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <script src="script/sidebar.js"></script>
    <script src="script/scripting.js"></script>
    <script>
        // Check if a customer is currently being processed
        var isProcessingCustomer = <?php echo isset($_SESSION['current_customer']) ? 'true' : 'false'; ?>;

        document.getElementById('process-button').addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default form submission

            if (isProcessingCustomer) {
                Swal.fire({
                    icon: 'error',
                    title: 'Already Serving a Customer',
                    text: 'There is already a customer being served.',
                    confirmButtonText: 'OK'
                });
            } else {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to serve the next customer?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, submit the form
                        document.getElementById('process-form').submit();
                    }
                });
            }
        });
    </script>
</body>

</html>
