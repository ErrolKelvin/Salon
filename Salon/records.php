<?php
session_start();
include 'sidebar.php';
include 'db.php';

if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leah's M Queue</title>
  <link rel="stylesheet" href="styles/sidebar1.css">
  <link rel="stylesheet" href="styles/records2.css">
</head>

<body>
  <div id="main-content" class="main-content">
    <div id="header" class="header">
      <img class="logo" src="icons/logo1.png" alt="">
      <p><span class="brand_name">Leah M Queue</span> &nbsp; Records</p>
    </div>
    <div id="content" class="content">
      <div class="filter-container">
        <label for="showLast">Show Last:</label>
        <input type="number" id="showLast" name="showLast" min="1" oninput="filterRecords()"
          placeholder="Number of records">

        <label for="searchCustomer">Search Customer:</label>
        <input type="text" id="searchCustomer" name="searchCustomer" oninput="filterRecords()"
          placeholder="Customer Name">

        <label for="sortOrder">Sort By:</label>
        <select id="sortOrder" name="sortOrder" onchange="filterRecords()">
          <option value="mostRecent">Most Recent</option>
          <option value="oldest">Oldest</option>
        </select>
        <!-- Date Filter Modal -->
        <div id="dateFilterModal" class="modal">
          <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Filter by Date</h2>
            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate" name="startDate">

            <label for="endDate">End Date:</label>
            <input type="date" id="endDate" name="endDate">

            <button type="button" onclick="applyDateFilter()">Apply</button>
            <button type="button" onclick="resetDateFilters()">Reset</button>
            <button type="button" onclick="closeModal()">Cancel</button>
          </div>
        </div>
        <button type="button" id="dateFilterButton" onclick="openModal()">Filter by Date</button>

        <button type="button" id="resetButton" onclick="resetFilters()">Reset Filters</button>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th onclick="sortTable('customer_name')">Customer Name<span class="sort-indicator"
                    id="customer_name_indicator"></span></th>
                <th>Contact #<span class="sort-indicator" id="contact_indicator"></span></th>
                <th onclick="sortTable('services')">Services<span class="sort-indicator" id="services_indicator"></span>
                </th>
                <th>Note<span class="sort-indicator" id="note_indicator"></span></th>
                <th>Date<span class="sort-indicator" id="date_indicator"></span></th>
                <th>Time Queued<span class="sort-indicator" id="time_queued_indicator"></span></th>
                <th>Time Processed<span class="sort-indicator" id="time_processed_indicator"></span></th>
                <th>Time Finished<span class="sort-indicator" id="time_finished_indicator"></span></th>
                <th onclick="sortTable('price')">Price<span class="sort-indicator" id="price_indicator"></span></th>
              </tr>
            </thead>
            <tbody id="recordsTableBody">
              <?php
              $sql = "SELECT * FROM records ORDER BY date DESC, time_processed DESC";
              $result = $conn->query($sql);
              $records = [];

              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  $records[] = $row; // Store each record in the array
                }
              }
              ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="8" style="text-align: right;">Total Transactions:</td>
                <td id="totalTransactions"></td>
              </tr>
              <tr>
                <td colspan="8" style="text-align: right;">Total Price:</td>
                <td id="totalPrice"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script src="script/sidebar.js"></script>
  <script>
    let records = <?php echo json_encode($records); ?>;
    let sortDirection = {}; // Object to keep track of sorting direction

    // Function to initially sort records by most recent date and time
    function initialSortRecords() {
      records.sort((a, b) => {
        // Combine date with both time_queued and time_processed for comparison
        const dateTimeA_queued = new Date(`${a.date} ${a.time_queued}`);
        const dateTimeA_processed = new Date(`${a.date} ${a.time_processed}`);
        const dateTimeB_queued = new Date(`${b.date} ${b.time_queued}`);
        const dateTimeB_processed = new Date(`${b.date} ${b.time_processed}`);

        // Find the earliest of the combined times for each record
        const earliestA = dateTimeA_queued < dateTimeA_processed ? dateTimeA_queued : dateTimeA_processed;
        const earliestB = dateTimeB_queued < dateTimeB_processed ? dateTimeB_queued : dateTimeB_processed;

        // Sort by the most recent earliest time
        return earliestB - earliestA;
      });
      sortDirection['date'] = true; // Set the initial sort direction for the date column to ascending
    }


    function renderTable(filteredRecords) {
      const tbody = document.getElementById('recordsTableBody');
      tbody.innerHTML = "";
      let totalTransactions = 0;
      let totalPrice = 0;

      filteredRecords.forEach(record => {
        const row = document.createElement('tr');
        row.innerHTML = `
      <td>${record.customer_name}</td>
      <td>${record.contact}</td>
      <td>${record.services}</td>
      <td>${record.note}</td>
      <td>${record.date}</td>
      <td>${record.time_queued}</td>
      <td>${record.time_processed}</td>
      <td>${record.time_finished}</td>
      <td>${record.price} PHP</td>
    `;
        tbody.appendChild(row);

        totalTransactions++;
        totalPrice += parseFloat(record.price);
      });

      document.getElementById('totalTransactions').textContent = totalTransactions;
      document.getElementById('totalPrice').textContent = `${totalPrice.toFixed(2)} PHP`;
    }

    // Function to open the modal
function openModal() {
    document.getElementById("dateFilterModal").style.display = "block";
}

// Function to close the modal
function closeModal() {
    document.getElementById("dateFilterModal").style.display = "none";
}

// Function to apply the date filter
function applyDateFilter() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    // Close the modal
    closeModal();

    // Filter records based on the selected date range
    let filteredRecords = [...records];

    if (startDate) {
        // Include the start date in the comparison
        filteredRecords = filteredRecords.filter(record => new Date(record.date) >= new Date(startDate + 'T00:00:00'));
    }
    if (endDate) {
        filteredRecords = filteredRecords.filter(record => new Date(record.date) <= new Date(endDate + 'T23:59:59'));
    }

    renderTable(filteredRecords);
}

// Function to reset date filters
function resetDateFilters() {
    document.getElementById('startDate').value = ""; // Clear start date input
    document.getElementById('endDate').value = ""; // Clear end date input
    // Optionally call renderTable to reset the table to show all records
    renderTable(records); // Show all records without filtering
}

    function resetFilters() {
      document.getElementById('showLast').value = ""; // Clear the showLast input
      document.getElementById('searchCustomer').value = ""; // Clear the searchCustomer input
      document.getElementById('sortOrder').selectedIndex = 0;
      resetDateFilters();
      filterRecords();
      renderTable(filteredRecords);
    }
    function sortTable(column) {
      // Toggle sorting direction for the column
      sortDirection[column] = !sortDirection[column];
      const direction = sortDirection[column] ? 1 : -1;

      records.sort((a, b) => {
        if (typeof a[column] === 'string') {
          return direction * a[column].localeCompare(b[column]); // For strings
        }
        return direction * (a[column] - b[column]); // For numbers
      });

      updateSortIndicators(column, sortDirection[column]);
      renderTable(records);
    }

    function toggleApplyButton() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const applyButton = document.getElementById('applyButton');

    // Show the Apply button if either date is set
    if (startDate || endDate) {
        applyButton.style.display = "block";
    } else {
        applyButton.style.display = "none";
    }
}
    function updateSortIndicators(column, isAscending) {
      // Clear previous indicators
      const indicators = document.querySelectorAll('.sort-indicator');
      indicators.forEach(indicator => {
        indicator.classList.remove('ascending', 'descending');
      });

      const indicatorElement = document.getElementById(`${column}_indicator`);
      indicatorElement.classList.add(isAscending ? 'ascending' : 'descending');
    }

    function filterRecords() {
      let filteredRecords = [...records];
      const showLast = document.getElementById('showLast').value;
      const searchCustomer = document.getElementById('searchCustomer').value.toLowerCase();
      const sortOrder = document.getElementById('sortOrder').value;

      // Filter by searchCustomer
      if (searchCustomer) {
        filteredRecords = filteredRecords.filter(record =>
          record.customer_name.toLowerCase().includes(searchCustomer)
        );
      }

      // Sort records based on the selected order
      filteredRecords.sort((a, b) => {
        const dateTimeA = new Date(`${a.date} ${a.time_processed}`); // Combine date and time
        const dateTimeB = new Date(`${b.date} ${b.time_processed}`);
        return sortOrder === "mostRecent" ? dateTimeB - dateTimeA : dateTimeA - dateTimeB;
      });

      if (showLast > 0) {
        filteredRecords = filteredRecords.slice(0, showLast); // Show the top N records
      }

      renderTable(filteredRecords);
    }

    // Initial sort and render
    initialSortRecords();
    renderTable(records);
  </script>

</body>

</html>