<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    <title>Camagru</title>
    <link href="https://fonts.googleapis.com/css?family=Pacifico|Roboto:300" rel="stylesheet">
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="icon" type="image/png" sizes="32x32" href="imgs/favicon.png">
    <script src="js/functions.js"></script>
    <script>
        window.onload = function () {
            var container = document.getElementById("im-container");
            var xhttp = new XMLHttpRequest();
            xhttp.open("POST","backend/functions.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("getphoto=0");
            xhttp.onreadystatechange = function()
            {
                if (xhttp.readyState === 4)
                    if(xhttp.status === 200)
                        if (container)
                            container.innerHTML = xhttp.responseText;
            };
            var totalpage = 0;
            var xhttpp = new XMLHttpRequest();
            xhttpp.open("POST","backend/functions.php", true);
            xhttpp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttpp.send("sizearray=1");
            xhttpp.onreadystatechange = function()
            {
                if (xhttpp.readyState === 4)
                    if(xhttpp.status === 200) {
                        var start = 2;
                        var pcontainer = document.getElementById("page-selector-container");
                        var btn = document.createElement("button");
                        totalpage = parseInt(xhttpp.responseText);
                        totalpage -= 10;
                        btn.value = "1";
                        btn.innerHTML = "1";
                        btn.onclick = function () {
                            takePhotoFrom(0);
                        };
                        if (xhttpp.responseText !== "0")
                            if (pcontainer)
                                pcontainer.appendChild(btn);
                        while (totalpage > 0) {
                            btn = document.createElement("button");
                            btn.value = start.toString();
                            btn.innerHTML = start.toString();
                            btn.onclick = function () {
                                takePhotoFrom(this.value);
                            };
                            if (pcontainer)
                                pcontainer.appendChild(btn);
                            totalpage -= 10;
                            start++;
                        }
                    }
            };
        }
    </script>
</head>
<body class="background">
<header>
    <div class="header">
        <h1 class="title">Camagru</h1>
        <?php if (isset($_SESSION['user'])) : ?>
            <button id="first" class="btn-blue-index" onclick="redirectPA()"><span class="fix-skew">Espace personnel</span></button>
            <button id="second" class="btn-red-index" onclick="destroy_session()"><span class="fix-skew">Se deconnecter</span></button>
        <?php else: ?>
            <button id="first" class="btn-orange delay" onclick="redirectRegister()"><span class="fix-skew">S'inscrire</span></button>
            <button id="second" class="btn-orange" onclick="redirectLogin()"><span class="fix-skew">Se connecter</span></button>
        <?php endif;?>
		<button id="test" class="btn-test" onclick="redirectIndex()"><span class="fix-skew">Page d'accueil</span></button>
    </div>
</header>
<div id="im-container" class="img-container"></div>
<div id="page-selector-container" style="text-align: center; margin-bottom: 4vh;"></div>
</body>
<footer>
    <p>Sarah Villain (@svillain) 2018</p>
</footer>
<div id="comment-pre-container">
    <img src="imgs/close.svg" class="close-comments" onclick="closeWindow()">
    <div id="comment-container">
    </div>
    <div id="textarenbtn" class="textareanbtn-container"></div>
</div>
</html>
