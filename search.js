function filterbooks() {
  // Get the search input value and convert to lowercase
  const input = document.getElementById('booksearch');
  const filter = input.value.toLowerCase();
  
  // Target the container of your files
  const container = document.querySelector('.format-group');
  const files = container.getElementsByClassName('summary-rite');

  // Loop through all file entries
  for (let i = 0; i < files.length; i++) {
    const link = files[i].getElementsByTagName('a')[0];
    const textValue = link.textContent || link.innerText;

    // Show/hide based on match
    if (textValue.toLowerCase().indexOf(filter) > -1) {
      files[i].style.display = "";
    } else {
      files[i].style.display = "none";
    }
  }
}
