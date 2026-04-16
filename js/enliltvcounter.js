// Function to update and save the count
function updateCounter() {
    let count = localStorage.getItem('totalPageViews') || 0;
    count = parseInt(count) + 1;
    localStorage.setItem('totalPageViews', count);
}

// Function to show the count on the screen
function displayCounter() {
    const display = document.getElementById('enliltv-count');
    if (display) {
        display.innerText = localStorage.getItem('totalPageViews') || 0;
    }
}

// Check for the "storage" event to update across tabs
window.addEventListener('storage', (event) => {
    if (event.key === 'totalPageViews') {
        displayCounter();
    }
});

// Function to clear everything
function resetCounter() {
    if (confirm("Are you sure you want to clear the data?")) {
        localStorage.removeItem('totalPageViews');
        displayCounter(); // Update the screen to show 0
    }
}
