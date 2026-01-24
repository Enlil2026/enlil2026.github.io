<?php
session_start();
$storage_file = 'chat_storage.json';

if (!file_exists($storage_file)) {
    file_put_contents($storage_file, json_encode([]));
}

if (isset($_POST['login'])) {
    $_SESSION['user'] = htmlspecialchars($_POST['username']);
}
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (isset($_POST['send']) && isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $msg = htmlspecialchars($_POST['msg']);
    $media_files = []; // Array to store multiple file paths

    // Handle Multiple Files (Images & Videos)
    if (!empty($_FILES['media']['name'][0])) {
        if (!is_dir('uploads')) mkdir('uploads');

        foreach ($_FILES['media']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['media']['name'][$key];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'mov'];

            if (in_array($ext, $allowed_exts)) {
                $target_path = "uploads/" . time() . "_" . $key . "_" . basename($file_name);
                if (move_uploaded_file($tmp_name, $target_path)) {
                    $media_files[] = [
                        'path' => $target_path,
                        'type' => in_array($ext, ['mp4', 'webm', 'mov']) ? 'video' : 'image'
                    ];
                }
            }
        }
    }

    $current_data = json_decode(file_get_contents($storage_file), true);
    $new_message = [
        'id' => time() . rand(100, 999),
        'sender' => $user,
        'msg' => $msg,
        'media' => $media_files, // Changed from img_path to media array
        'timestamp' => time()
    ];
    $current_data[] = $new_message;
    file_put_contents($storage_file, json_encode($current_data));

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!-- Keep your existing HTML and CSS exactly as they are -->


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gold Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="180x180" href="favicon/favicon-180x180.png">


    <style>
    body { 
        background: #000; 
        color: #D4AF37; 
        font-family: sans-serif; 
        display: flex; 
        justify-content: center; 
        margin: 0; 
        padding: 10px; /* Prevents box from touching screen edges */
    }

    .chat-box { 
        width: 100%; 
        max-width: 500px; /* Desktop width */
        border: 2px solid #D4AF37; 
        padding: 15px; 
        border-radius: 10px; 
        background: #1a1a1a; 
        box-sizing: border-box; /* Ensures padding doesn't break width */
    }

    #messages { 
        height: 60vh; /* Responsive height: 60% of vertical screen */
        overflow-y: auto; 
        border-bottom: 1px solid #D4AF37; 
        margin-bottom: 10px; 
        padding: 5px; 
    }

    /* Make inputs expand to full width of the container */
    input[type="text"], input[type="file"], .gold-btn {
        width: 100%;
        margin-bottom: 10px;
        box-sizing: border-box;
    }

    .msg { margin-bottom: 10px; padding: 5px; border-left: 3px solid #D4AF37; word-wrap: break-word; }
    .gold-btn { background: #D4AF37; color: black; border: none; padding: 10px; cursor: pointer; font-weight: bold; }
    input[type="text"], input[type="password"] { background: #333; color: gold; border: 1px solid #D4AF37; padding: 10px; }
    img { max-width: 100%; height: auto; display: block; margin-top: 5px; border: 1px solid #D4AF37; }

    /* Tablet/Mobile Adjustments */
    @media (max-width: 480px) {
        .chat-box { padding: 10px; border-radius: 0; border: none; }
        #messages { height: 70vh; }
    }

    video { max-width: 100%; height: auto; display: block; margin-top: 5px; border: 1px solid #D4AF37; }

</style>
</head>
<body>

<div class="chat-box">
    <?php if (!isset($_SESSION['user'])): ?>
        <h2>Login</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <button type="submit" name="login" class="gold-btn">Enter Chat</button>
        </form>
    <?php else: ?>
        <div style="display:flex; justify-content: space-between;">
            <span>Welcome, <b><?php echo $_SESSION['user']; ?></b></span>
            <script>const currentUser = "<?php echo $_SESSION['user']; ?>";</script>
            <a href="?logout=1" style="color: #D4AF37;">Logout</a>
        </div>
         <!-- INSERT STEP 1 CODE HERE -->
    <button id="enable-notif" class="gold-btn" style="background:#444; font-size:12px; margin-top:10px;">
        Enable Desktop Notifications
    </button>

    <div id="messages"></div>


        <form method="POST" enctype="multipart/form-data">
    <input type="text" name="msg" id="msg-input" placeholder="Type message...">
    <!-- Notice 'media[]' and 'multiple' -->
    <input type="file" name="media[]" accept="image/*,video/*" multiple> 
    <button type="submit" name="send" class="gold-btn">Send</button>
</form>
        <audio id="notif-sound" src="received.wav"></audio>
    <?php endif; ?>
</div>

<script>
       let lastId = 0;
let isInitialLoad = true;

// 1. Request Notification Permission
const notifBtn = document.getElementById('enable-notif');
if (notifBtn) {
    notifBtn.addEventListener('click', () => {
        Notification.requestPermission().then(permission => {
            if (permission === "granted") {
                notifBtn.style.display = "none";
                new Notification("Notifications Enabled!");
            }
        });
    });
}

function loadMessages() {
    fetch('fetch.php?last_id=' + lastId)
        .then(res => res.json())
        .then(data => {
            if (data.length > 0) {
                const msgDiv = document.getElementById('messages');
                let playNotification = false;
                let lastSender = "";
                let lastText = "";

                data.forEach(m => {
                    const div = document.createElement('div');
                    div.className = 'msg';
                    div.innerHTML = `<b>${m.sender}:</b> ${m.msg}`;

                    if (m.media && m.media.length > 0) {
                        m.media.forEach(item => {
                            if (item.type === 'video') {
                                div.innerHTML += `<video controls src="${item.path}"></video>`;
                            } else {
                                div.innerHTML += `<img src="${item.path}">`;
                            }
                        });
                    }
                    
                    msgDiv.appendChild(div);
                    lastId = m.id;

                    if (!isInitialLoad && m.sender !== currentUser) {
                        playNotification = true;
                        lastSender = m.sender;
                        lastText = m.msg;
                    }
                });

                if (playNotification) {
                    // Play Sound
                    document.getElementById('notif-sound').play().catch(e => console.log("Audio blocked"));

                    // Trigger System Notification (Works when tab is in background)
                    if (Notification.permission === "granted") {
                        new Notification(`New message from ${lastSender}`, {
                            body: lastText,
                            icon: 'favicon/favicon-32x32.png' // Path to your icon
                        });
                    }
                }

                msgDiv.scrollTop = msgDiv.scrollHeight;
            }
            isInitialLoad = false; 
        });
}

if (document.getElementById('messages')) {
    loadMessages();
    setInterval(loadMessages, 1000); // Increased to 3s to be gentler on background tasks
}


    
</script>
</body>
</html>