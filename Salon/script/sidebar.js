function toggleSidebar() {
  var sidebar = document.getElementById('sidebar');
  var mainContent = document.getElementById('main-content');
  var toggleButton = document.getElementById('toggle-btn');
  var header = document.getElementById('header');
  sidebar.classList.toggle('expanded');
  mainContent.classList.toggle('expanded');
  toggleButton.classList.toggle('expanded');
  header.classList.toggle('expanded');
}

