<?php
session_start();
if (isset($_SESSION['user']))
{
	header("Location: index.php");
	exit(0);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
<title>Camagru - Se connecter</title>
<link rel="stylesheet" href="css/body.css">
<link rel="stylesheet" href="css/reset.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
<link rel="icon" type="image/png" sizes="32x32" href="imgs/favicon.png">
<script src="js/functions.js"></script>
</head>
<header>
<div class="header">
<h1 class="title">Camagru</h1>
<button id="first" class="btn-orange delay" onclick="redirectRegister()"><span class="fix-skew">S'inscrire</span></button>
<button id="test" class="btn-accueil" onclick="redirectIndex()"><span class="fix-skew">Page d'accueil</span></button>
</div>
</header>
<body>
<div class="register-centered-div">
<div class="register-container">
<h1 id="form-title">Connexion</h1>
<input id="username" type="text" placeholder="Nom d'utilisateur" maxlength="12" required autofocus autocomplete="off">
<input id="password" type="password" placeholder="Mot de passe" required autocomplete="off">
<button style="display: block" id="login-btn" class="btn-orange register" type="button" onclick="login()"><span class="fix-skew">Se connecter</span></button>
<button style="display: block" id="restore-btn" class="btn-orange register delay" type="button" onclick="recoverPassword(this)"><span id="recover-text" class="fix-skew">Mot de passe oublie</span></button>
</div>
</div>
<div id="redir_screen" style="display: none">
<div class="notification-container-login">
<div style="display: table-cell; vertical-align: middle; text-align: center">
<p class="center-message">Compte cree avec succes, redirection dans quelques secondes</p>
<img src="imgs/refresh-page-option.svg" class="rotation-refreshimage">
</div>
</div>
</div>
<div id="toast_error"></div>
<div id="toast_ok"></div>
</body>
</html>
