<?php 
// Inclure les fichiers nécessaires pour la base de données et la session
require_once 'includes/db.php';
require_once 'includes/utilities.php';
require_once 'includes/session.php';
start_secure_session(); // Démarre une session sécurisée

// Fonction pour récupérer l'adresse IP de l'utilisateur
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Traitement du formulaire de connexion
$error_message = ''; // Initialisation du message d'erreur

if (isset($_POST['login'])) {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    // Requête préparée pour récupérer les informations de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    // Vérification du mot de passe
    if ($user && password_verify($password, $user['password'])) {
        // Stocker les informations dans la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['telephone'] = $user['phone']; // Stocke le téléphone

        // Enregistrer l'historique de connexion
        $ip_address = getUserIP();
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $login_time = date("Y-m-d H:i:s");

        $stmt = $pdo->prepare("
            INSERT INTO login_history (user_id, login_time, ip_address, user_agent) 
            VALUES (:user_id, :login_time, :ip_address, :user_agent)
        ");
        $stmt->execute([
            ':user_id' => $user['id'],
            ':login_time' => $login_time,
            ':ip_address' => $ip_address,
            ':user_agent' => $user_agent,
        ]);

        // Redirection
        if ($user['role'] == 'admin') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: proprietaire/dashboard.php');
        }
        exit();
    } else {
        // Message d'erreur en cas de mauvais identifiants
        $error_message = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daltys Conciergerie - Connexion</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body>

<!-- Conteneur principal -->
<div class="login-container">
    <div class="logo">
        <img src="public/images/logo.jpg" alt="Logo de Votre Conciergerie" class="logo-img">
    </div>

    <!-- Formulaire de connexion -->
    <div class="login-form">
        <h2><i class="fas fa-user"></i> Connexion à Votre Compte</h2>
        <form action="login.php" method="post" id="loginForm">
            <div class="form-group">
                <label for="username"><i class="fas fa-user-circle"></i> Nom d'utilisateur</label>
                <input type="text" id="username" name="username" placeholder="Nom d'utilisateur" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Mot de passe" required>
            </div>
            <div class="form-group">
                <button type="submit" name="login" class="login-btn"><i class="fas fa-sign-in-alt"></i> Se connecter</button>
            </div>
        </form>
        <p class="signup-link">Pas encore de compte ? <a href="inscription.php"><i class="fas fa-user-plus"></i> Inscrivez-vous ici</a></p>
        <p class="signup-link">Mot de passe oublié ? <a href="mot-de-passe-oublie.php"><i class="fas fa-key"></i> Changer le mot de passe ici</a></p>
    </div>

    <!-- Message d'erreur -->
    <?php if ($error_message): ?>
        <div id="error-message" class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>
</div> 

<script src="public/js/main.js" defer></script>

<script>
    // Validation côté client avec JavaScript
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        if (password.length < 6) {
            document.getElementById('error-message').innerText = 'Le mot de passe doit contenir au moins 6 caractères.';
            e.preventDefault();
        }
    });

    // Animation sur le bouton de connexion
    const loginButton = document.querySelector('.login-btn');
    loginButton.addEventListener('mouseover', function() {
        this.style.backgroundColor = "#2A9D8F";
    });
    loginButton.addEventListener('mouseout', function() {
        this.style.backgroundColor = "";
    });
</script>

<style>
    body {
        font-family: 'Montserrat', sans-serif;
        background-color: #f5f5f5;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .login-container {
        background-color: white;
        border-radius: 10px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
        width: 400px;
    }

    .logo {
        margin-bottom: 20px;
    }

    .logo-img {
        width: 150px;
    }

    .login-form h2 {
        font-family: 'Playfair Display', serif;
        margin-bottom: 20px;
        color: #333;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
    }

    .login-btn {
        width: 100%;
        padding: 12px;
        background-color: #264653;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .signup-link {
        margin-top: 15px;
    }

    a {
        color: #2A9D8F;
    }

    a i {
        margin-right: 8px;
    }

    .error-message {
        margin-top: 10px;
        color: red;
    }
</style>

</body>
</html>
