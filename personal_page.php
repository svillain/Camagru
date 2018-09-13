<?php
session_start();
if (!isset($_SESSION['user']))
    header("Location: index.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    <title>Camagru - Espace personnel</title>
    <link href="https://fonts.googleapis.com/css?family=Pacifico|Roboto:300" rel="stylesheet">
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="icon" type="image/png" sizes="32x32" href="imgs/favicon.png">
    <script src="js/functions.js"></script>
    <script>
        window.onload = function () {
            getCamera();
            var c = document.getElementById("camera-container");
            var x = document.getElementById("prev-cont");
            if (window.innerWidth <= 600)
            {
                if (c) {
                    c.style.transform = "scale(" + window.innerWidth / 660 + ")";
                    if (x)
                        x.style.top = ((c.getBoundingClientRect().height + 20) - 480) + "px";
                }
            }
            var xhttp = new XMLHttpRequest();
            xhttp.open("POST","backend/functions.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("getphotos=1");
            xhttp.onreadystatechange = function()
            {
                if (xhttp.readyState === 4)
                    if(xhttp.status === 200)
                        if (x)
                            x.innerHTML = xhttp.responseText;
            };
        };
        window.onresize = function () {
            var c = document.getElementById("camera-container");
            var x = document.getElementById("prev-cont");
            if (window.innerWidth <= 600)
            {
                if (c)
                {
                    c.style.transform = "scale("+window.innerWidth/660+")";
                    if (x)
                        x.style.top = ((c.getBoundingClientRect().height + 20) - 480) + "px";
                }
            }
            else
                if (c)
                    c.style.transform = "scale(1)";
        }
    </script>
</head>
<header>
    <div class="header">
        <h1 class="title">Camagru</h1>
            <button id="test" class="btn-accueil-pp" onclick="redirectIndex()"><span class="fix-skew">Page d'accueil</span></button>
            <button id="second" class="btn-blue" onclick="editInfo()"><span class="fix-skew">Modifier mon profil</span></button>
            <button id="second" class="btn-red-pp" onclick="destroy_session()"><span class="fix-skew">Se deconnecter</span></button>
    </div>
</header>
<body>
    <header id="header-personal-zone">
        <h1 style="font-family: 'Roboto', sans-serif; color: blue; font-size: 300%; margin-top: 1.5vw; text-align: center ">Votre espace personnel</h1>
    </header>
<div class="flex-container">
    <div class="superpos-container">
        <div style="display: block">
            <img src="imgs/hat.png"><br>
            <label for="pic-hat">Chapeau</label>
            <input id="pic-hat" name="mask" type="radio" value="1" onchange="trigger(this)">
        </div>
        <div style="display: block">
            <img src="imgs/sunglasses.png"><br>
            <label for="pic-glasses">Lunettes de soleil</label>
            <input id="pic-glasses" name="mask" type="radio" value="2" onchange="trigger(this)">
        </div>
        <div style="display: block">
            <img src="imgs/pipe.png"><br>
            <label for="pic-pipe">Pipe</label>
            <input id="pic-pipe" name="mask" type="radio" value="3" onchange="trigger(this)">
        </div>
    </div>
    <div id="camera-container" class="camera-container">
        <img id="camera-img" width="640" height="480" style="display: none">
        <video id="camera" autoplay></video>
        <img id="camera-icon" src="imgs/photo-camera.svg" onclick="takePicture()">
        <img id="superpos" src="" class="selected">
        <input id="file-picker" type="file" accept="image/x-png, image/jpeg" style="display: none; position: absolute">
    </div>
    <div id="prev-cont" class="preview-container">
    </div>
</div>
<div id="info-editor">
    <img src="imgs/close.svg" class="close-comments" onclick="closeWindow2()">
    <div style="width: 80%; background-color: white; margin: 0 auto; border-bottom-right-radius: 20px; border-bottom-left-radius: 20px">
        <div class="register-centered-div">
            <div class="register-container">
                <h1>Mettre a jour mes informations</h1>
                <input id="username" type="text" placeholder="Nom d'utilisateur" maxlength="12" required autofocus autocomplete="off">
                <input id="email" type="email" placeholder="E-mail" maxlength="100" required autocomplete="off">
                <input id="password" type="password" placeholder="Mot de passe" required onkeyup="passwordComplexity()" autocomplete="off">
                <hr id="pwd_indicator" class="password_strength">
                <label style="float: left;"><input id="getmail" type="checkbox" style="width: unset; display: inline-block;" checked><span style="position: relative; top: -8px">Je souhaite recevoir un mail pour chaque commentaire poster sur mes photos.</span></label>
                <button class="btn-orange register" style="margin-bottom: 30px" type="button" onclick="updateInfo()"><span class="fix-skew">Mettre a jour</span></button>
            </div>
        </div>
    </div>
</div>
<footer>
    <p style="background-color:#8dd0f4;">Sarah Villain (@svillain) 2018 </p>
</footer>
</body>
</html>
