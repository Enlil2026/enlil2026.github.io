function filterChannels() {
    // Get search input value
    let input = document.getElementById('channel-search').value.toLowerCase();
    // Get all channel elements (assumes wrap.js creates divs with class 'channel')
    let channels = document.getElementsByClassName('channel');

    for (let i = 0; i < channels.length; i++) {
        let channelName = channels[i].textContent || channels[i].innerText;
        // If the name matches the search, show it; otherwise, hide it
        if (channelName.toLowerCase().indexOf(input) > -1) {
            channels[i].style.display = "";
        } else {
            channels[i].style.display = "none";
        }
    }
}
