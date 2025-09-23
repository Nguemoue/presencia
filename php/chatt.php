<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ChatBot IAI - Présences</title>
  <link rel="stylesheet" href="style.css">
  <style>
body {
  margin: 0;
  background: #e5ddd5;
  font-family: Arial, sans-serif;
}
.bubble {
    margin: 8px;
    padding: 10px 15px;
    border-radius: 20px;
    max-width: 70%;
    animation: pop 0.3s ease-in-out;
}
.bubble.user {
    background: #dcf8c6;
    align-self: flex-end;
    text-align: right;
}
.bubble.bot {
    background: #fff;
    border: 1px solid #ddd;
    align-self: flex-start;
}

.chat-container {
  width: 100%;
  max-width: 480px;
  height: 100vh;
  margin: auto;
  display: flex;
  flex-direction: column;
  background: #fff;
  box-shadow: 0 0 8px rgba(0,0,0,0.3);
  border-radius: 10px;
  overflow: hidden;
}

.header {
  background: #075e54;
  color: #fff;
  padding: 15px;
  font-weight: bold;
  text-align: center;
}

.chat-box {
  flex: 1;
  padding: 15px;
  overflow-y: auto;
  background-image: url('https://i.imgur.com/9m1CWNV.png');
  background-size: cover;
  height: 400px;
    overflow-y: auto;
    padding: 10px;
    background: #f0f0f0;
}

.message {
  max-width: 75%;
  margin: 8px;
  padding: 10px 15px;
  border-radius: 18px;
  animation: slide 0.3s ease;
}

.user {
  background: #dcf8c6;
  align-self: flex-end;
  text-align: right;
}

.bot {
  background: #fff;
  border: 1px solid #ccc;
  align-self: flex-start;
  text-align: left;
}

.input-area {
  display: flex;
  border-top: 1px solid #ccc;
}

input[type="text"] {
  flex: 1;
  padding: 12px;
  border: none;
  font-size: 15px;
}

button {
  background: #25d366;
  border: none;
  color: white;
  padding: 0 20px;
  cursor: pointer;
  transition: 0.3s;
}

button:hover {
  background: #20b058;
}

@keyframes slide {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes pop {
    0% { transform: scale(0.8); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

  </style>
  <script>
   function sendMessage() {
  const input = document.getElementById("userInput");
  const message = input.value.trim();
  if (message === "") return;

  appendMessage("user", message);
  input.value = "";

  // Récupérer le rôle depuis le <body>
  const role = document.body.getAttribute("data-role") || "admin";

  // Déterminer le fichier chatbot selon le rôle
  let chatbotFile = "chatbot.php";
  if (role === "etudiant" || role === "personnel") {
    chatbotFile = "chatbot_etudiant.php";
  } else if (role === "admin") {
    chatbotFile = "chatbot_admin.php";
  }

  // Requête AJAX vers le bon fichier
  fetch(chatbotFile, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'message=' + encodeURIComponent(message)
  })
  .then(response => response.text())
  .then(reply => appendMessage("bot", reply));
}

function appendMessage(sender, text) {
  const chatBox = document.getElementById("chatBox");
  const msg = document.createElement("div");
  msg.className = "message " + sender;
  msg.innerHTML = text;
  chatBox.appendChild(msg);
  chatBox.scrollTop = chatBox.scrollHeight;
}
  </script>
</head>
<body>
  <div class="chat-container">
    <div class="header">🤖 ChatBot Présences</div>
    <div class="chat-box" id="chatBox"></div>
    <div class="input-area">
      <input type="text" id="userInput" placeholder="Pose ta question..." />
      <button onclick="sendMessage()">Envoyer</button>
    </div>
  </div>
  <script src="script.js"></script>
</body>
</html>