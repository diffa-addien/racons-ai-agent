<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Realtime Chatbox</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .no-scrollbar {
      overflow: scroll;
      /* Mengaktifkan scrolling */
    }

    /* Untuk WebKit (Chrome, Safari) */
    .no-scrollbar::-webkit-scrollbar {
      width: 0px;
      /* Lebar scrollbar 0px */
      background: transparent;
      /* Warna transparan */
    }

    /* Untuk Firefox */
    .no-scrollbar {
      scrollbar-width: none;
      /* Menyembunyikan scrollbar */
    }

    .container-mini {
      max-width: 760px;
    }

    .chat-container {
      height: calc(100vh - 150px);
      padding-top: 20px;
    }

    .chatbox {
      flex: 1;
      height: 100%;
      width: 100%;
      overflow-y: auto;
      /* border: 1px solid #ddd; */
      border-radius: 5px;
      /* padding: 10px; */
      margin-bottom: 10px;
      box-shadow: inset 5px 5px 10px rgba(0, 0, 0, 0.3);
      /* background-color: #f9f9f9; */
    }

    .message {
      margin-bottom: 10px;
    }

    .message.user {
      text-align: right;
    }

    .message.bot {
      text-align: left;
    }

    .message p {
      display: inline-block;
      padding: 10px;
      border-radius: 10px;
      max-width: 70%;
    }

    .message.user p {
      border-radius: 10px 0 10px 10px;
      background-color: #007bff;
      color: #fff;
    }

    .message.bot p {
      border-radius: 0px 10px 10px 10px;
      background-color: #e9ecef;
      color: #000;
    }

    #typingBubble {
      background-color: #E6F8F1;
      padding: 16px 28px;
      -webkit-border-radius: 20px;
      -webkit-border-bottom-left-radius: 2px;
      -moz-border-radius: 20px;
      -moz-border-radius-bottomleft: 2px;
      border-radius: 20px;
      border-bottom-left-radius: 2px;
      display: inline-block;
    }

    .typing {
      align-items: center;
      display: flex;
      height: 17px;
    }

    .typing .dot {
      animation: mercuryTypingAnimation 1.8s infinite ease-in-out;
      background-color: #6CAD96;
      /* //rgba(20,105,69,.7); */
      border-radius: 50%;
      height: 7px;
      margin-right: 4px;
      vertical-align: middle;
      width: 7px;
      display: inline-block;
    }

    .typing .dot:nth-child(1) {
      animation-delay: 200ms;
    }

    .typing .dot:nth-child(2) {
      animation-delay: 300ms;
    }

    .typing .dot:nth-child(3) {
      animation-delay: 400ms;
    }

    .typing .dot:last-child {
      margin-right: 0;
    }

    @keyframes mercuryTypingAnimation {
      0% {
        transform: translateY(0px);
        background-color: #6CAD96;
        /* // rgba(20,105,69,.7); */
      }

      28% {
        transform: translateY(-7px);
        background-color: #9ECAB9;
        /* //rgba(20,105,69,.4); */
      }

      44% {
        transform: translateY(0px);
        background-color: #B5D9CB;
        /* //rgba(20,105,69,.2); */
      }
    }
  </style>
</head>

<body>

  <!-- NAV START -->
  <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
    <div class="container-sm">
      <a class="navbar-brand" href="#">Racons AI</a>
      <button class="btn btn-outline-light ms-auto mb-2 mb-lg-0" type="submit">Advance</button>
    </div>
  </nav>
  <!-- NAV END -->

  <div class="container-sm container-mini py-3">

    <div class="chat-container d-flex justify-content-center align-items-center">
      <!-- Chatbox -->
      <div id="chatbox" class="chatbox no-scrollbar"></div>
    </div>

    <!-- Input dan Tombol Kirim -->
    <div class="input-group mt-3">
      <input type="text" id="user-input" class="form-control" placeholder="Ketik pesan Anda...">
      <button id="send-btn" class="btn btn-primary">Kirim</button>
    </div>
  </div>

  <!-- Bootstrap JS dan Popper.js -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
  <!-- JavaScript Kustom -->
  <script>
    // Fungsi untuk menambahkan pesan ke chatbox
    function addMessageToChatbox(message, sender) {
      const chatbox = document.getElementById('chatbox');
      const messageElement = document.createElement('div');
      messageElement.classList.add('message', sender);

      const messageText = document.createElement('p');
      messageText.textContent = message;

      messageElement.appendChild(messageText);
      chatbox.appendChild(messageElement);

      // Scroll ke bawah
      chatbox.scrollTop = chatbox.scrollHeight;
    }

    function chatLoading() {
      const chatbox = document.getElementById('chatbox');
      const typingBubble = document.createElement('div');
      typingBubble.setAttribute("id", "typingBubble");
      typingBubble.innerHTML = '' +
        '<div class="typing">' +
        '<div class="dot"></div>' +
        '<div class="dot"></div>' +
        '<div class="dot"></div>' +
        '</div>';
      chatbox.appendChild(typingBubble);
    }

    // Fungsi untuk mengirim pesan ke server
    async function sendMessageToServer(message) {
      try {
        const response = await fetch('<?= base_url() ?>gemini-answer', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            message: message,
          }),
        });

        if (!response.ok) {
          throw new Error('Gagal mengirim pesan');
        }

        const data = await response.json();
        console.log(data);
        return data; // Respons dari server
      } catch (error) {
        console.error('Error:', error);
        return 'Maaf, terjadi kesalahan saat menghubungi server.';
      }
    }

    // Event listener untuk tombol kirim
    document.getElementById('send-btn').addEventListener('click', async () => {
      const userInput = document.getElementById('user-input');
      const userMessage = userInput.value.trim();

      if (userMessage) {
        // Tambahkan pesan pengguna ke chatbox
        addMessageToChatbox(userMessage, 'user');
        chatLoading();
        userInput.value = ''; // Kosongkan input

        // Kirim pesan ke server dan dapatkan respons
        const botResponse = await sendMessageToServer(userMessage);

        if (botResponse) {
          document.getElementById("typingBubble").remove();
        }
        // Tambahkan respons server ke chatbox
        addMessageToChatbox(botResponse, 'bot');
      }
    });

    // Event listener untuk tombol Enter
    document.getElementById('user-input').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        document.getElementById('send-btn').click();
      }
    });
  </script>
</body>

</html>