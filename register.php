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
    <title>Camagru - S'inscrire</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/reset.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="imgs/favicon.png">
    <script src="js/functions.js"></script>
</head>
<header>
<div class="header">
<h1 class="title">Camagru</h1>
<button id="second" class="btn-orange" onclick="redirectLogin()"><span class="fix-skew">Se connecter</span></button>
<button id="test" class="btn-accueil" onclick="redirectIndex()"><span class="fix-skew">Page d'accueil</span></button>
</div>
</header>
<body>
<div class="register-centered-div">
    <div class="register-container">
        <h1>Inscription</h1>
        <input id="username" type="text" placeholder="Nom d'utilisateur" maxlength="12" required autofocus autocomplete="off">
        <input id="email" type="email" placeholder="E-mail" maxlength="100" required autocomplete="off">
        <input id="password" type="password" placeholder="Mot de passe" required onkeyup="passwordComplexity()" autocomplete="off">
        <hr id="pwd_indicator" class="password_strength">
        <input id="name" type="text" placeholder="Prenom" maxlength="45" required autocomplete="off">
        <input id="surname" type="text" placeholder="Nom" maxlength="45" required autocomplete="off">
        <button class="btn-orange register" type="button" onclick="register()"><span class="fix-skew">S'inscrire</span></button>
    </div>
</div>
<div id="toast_error"></div>
<div id="toast_ok"></div>
</body>
</html>
