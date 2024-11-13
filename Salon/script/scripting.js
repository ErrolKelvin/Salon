function filterServices() {
  var input, filter, serviceList, labels, label, i;
  input = document.getElementById('serviceSearch');
  filter = input.value.toLowerCase();
  serviceList = document.getElementById('serviceList');
  labels = serviceList.getElementsByTagName('label');

  var noResults = true;
  for (i = 0; i < labels.length; i++) {
      label = labels[i];
      if (label.textContent.toLowerCase().indexOf(filter) > -1) {
          label.style.display = "";
          noResults = false;
      } else {
          label.style.display = "none";
      }
  }
}

function validateForm() {
    let services = document.querySelectorAll('input[name="services[]"]:checked');
    if (services.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Select a service',
            text: 'Please select at least one service!'
        });
        return false;
    }
    return true;
}

document.querySelector('form[name="add_form"]').addEventListener('submit', function(event) {
    if (!validateForm()) {
        event.preventDefault(); 
    }
});

