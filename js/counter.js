// Function to update and save the global count via PHP
async function updateCounter() {
    try {
        // We fetch the 'counter.php' script you just created
        const response = await fetch('counter.php');
        const globalCount = await response.text();
        
        displayCounter(globalCount);
    } catch (error) {
        console.error("Error updating global counter:", error);
    }
}

// Function to show the count on the screen
function displayCounter(count) {
    const display = document.getElementById('display-count');
    if (display) {
        display.innerText = count || 0;
    }
}

// Initialize when the page loads
updateCounter();
