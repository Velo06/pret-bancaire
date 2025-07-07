<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connexion</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --primary-color: #88c417;
      --secondary-color: #182143;
      --bg-light: #f6f6f6;
      --white: #ffffff;
      --text-primary: #323232;
      --border-color: #e4e4e4;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Lato', sans-serif;
      background-color: var(--bg-light);
      color: var(--text-primary);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background: var(--white);
      padding: 2.5rem;
      border-radius: 8px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    .login-container h1 {
      margin-bottom: 2rem;
      color: var(--secondary-color);
    }

    .login-container input {
      width: 100%;
      padding: 12px;
      margin-bottom: 1rem;
      border: 1px solid var(--border-color);
      border-radius: 4px;
      font-size: 1rem;
    }

    .login-container input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(136, 196, 23, 0.1);
    }

    .login-container button {
      width: 100%;
      padding: 12px;
      background-color: var(--primary-color);
      color: var(--white);
      border: none;
      border-radius: 4px;
      font-weight: bold;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
    }

    .login-container button:hover {
      background-color: #70a516;
    }

    #message {
      margin-top: 1rem;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <h1>Connexion</h1>
    <input type="text" id="username" placeholder="Ex: antsa" required />
    <input type="password" id="password" placeholder="******" required />
    <button onclick="seConnecter()">Se connecter</button>
    <div id="message"></div>
  </div>

  <script>
    const apiBase = "http://localhost:8000/ws";

    function ajax(method, url, data, callback) {
      const xhr = new XMLHttpRequest();
      xhr.open(method, apiBase + url, true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = () => {
        if (xhr.readyState === 4 && xhr.status === 200) {
          callback(JSON.parse(xhr.responseText));
        }
      };
      xhr.send(data);
    }

    function seConnecter() {
      const username = document.getElementById("username").value;
      const password = document.getElementById("password").value;
      const messageDiv = document.getElementById("message");

      if (!username || !password) {
        messageDiv.style.color = "red";
        messageDiv.textContent = "Veuillez remplir tous les champs";
        return;
      }

      const data = `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`;

      ajax("POST", "/connexion", data, (response) => {
        if (response.success) {
          messageDiv.style.color = "green";
          messageDiv.textContent = "Connexion réussie !";
          setTimeout(() => {
            window.location.href = "Tableau.php";
          }, 1000);
        } else {
          messageDiv.style.color = "red";
          messageDiv.textContent = response.message || "Échec de la connexion";
        }
      });
    }
  </script>
</body>
</html>
