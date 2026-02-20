// Wrap EVERYTHING in this listener to prevent "videojs is not defined"
window.addEventListener('DOMContentLoaded', () => {
const player = videojs('iptv-player');
const btn = document.getElementById('toggle-btn');
const list = document.getElementById('channel-list');
let isOn = false;

const M3U_URL = '!/1.m3u';

btn.addEventListener('click', () => {
isOn = !isOn;
if (isOn) {
btn.innerText = "TURN TV OFF";
btn.style.background = "#17A62F";
loadPlaylist();
} else {
location.reload(); 
}
});

async function loadPlaylist() {
  try {
    const response = await fetch(M3U_URL, {
      method: 'GET',
      mode: 'cors', // Explicitly request CORS mode
      headers: {
        'Accept': 'application/x-mpegURL, text/plain'
      }
    });
    if (!response.ok) throw new Error("File not found or CORS block");
    const data = await response.text();
    parseM3U(data);
  } catch (err) {
    console.error("CORS Error:", err);
    alert("CORS Error: The server hosting the playlist is blocking access.");
  }
}

function parseM3U(content) {
const lines = content.split('\n');
list.innerHTML = '';
lines.forEach((line, i) => {
if (line.startsWith('#EXTINF:')) {
const name = line.split(',')[1] || "Unknown Channel";
const url = lines[i + 1]?.trim();
if (url && !url.startsWith('#')) {
const div = document.createElement('div');
div.className = 'channel';
div.innerText = name;
div.onclick = () => {
player.src({ type: 'application/x-mpegURL', src: url });
crossOrigin: 'anonymous'
player.play();
};
list.appendChild(div);
}
}
});
}
});

