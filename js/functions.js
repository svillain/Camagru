function badValue(username, email, password, name, surname, toast_ok, toast_error) {
    if (!username || !username.value.length)
    {
        if (toast_error)
        {
            toast_error.innerHTML = "Nom d'utilisateur invalide";
            toast_error.className = "show";
            setTimeout(function() {
                toast_error.className = toast_error.className.replace("show", "");
            }, 3000);
        }
        return (1);
    }
    else if (!email || !email.value.length || email.value.indexOf('@') === -1)
    {
        if (toast_error)
        {
            toast_error.innerHTML = "Email invalide";
            toast_error.className = "show";
            setTimeout(function() {
                toast_error.className = toast_error.className.replace("show", "");
            }, 3000);
        }
        return (1);
    }
    else if (!password || !password.value.length)
    {
        if (toast_error)
        {
            toast_error.innerHTML = "Mot de passe invalide";
            toast_error.className = "show";
            setTimeout(function() {
                toast_error.className = toast_error.className.replace("show", "");
            }, 3000);
        }
        return (1);
    }
    else if (!name || !name.value.length)
    {
        if (toast_error)
        {
            toast_error.innerHTML = "Prenom invalide";
            toast_error.className = "show";
            setTimeout(function() {
                toast_error.className = toast_error.className.replace("show", "");
            }, 3000);
        }
        return (1);
    }
    else if (!surname || !surname.value.length)
    {
        if (toast_error)
        {
            toast_error.innerHTML = "Nom invalide";
            toast_error.className = "show";
            setTimeout(function() {
                toast_error.className = toast_error.className.replace("show", "");
            }, 3000);
        }
        return (1);
    }
    return (0);
}
function badValueUP(username, password, toast_error) {
    if (!username || !username.value.length)
    {
        if (toast_error)
        {
            toast_error.innerHTML = "Nom d'utilisateur invalide";
            toast_error.className = "show";
            setTimeout(function() {
                toast_error.className = toast_error.className.replace("show", "");
            }, 3000);
        }
        return (1);
    }
    else if (!password || !password.value.length)
    {
        if (toast_error)
        {
            toast_error.innerHTML = "Mot de passe invalide";
            toast_error.className = "show";
            setTimeout(function() {
                toast_error.className = toast_error.className.replace("show", "");
            }, 3000);
        }
        return (1);
    }
    return (0);
}
function closeWindow() {
    var preco = document.getElementById("comment-pre-container");
    if (preco) {
        preco.style.display = "none";
    }
}
function closeWindow2() {
    var infoedit = document.getElementById("info-editor");
    if (infoedit) {
        infoedit.style.display = "none";
    }
}
function destroy_session() {
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST","backend/functions.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("destroy=true");
    xhttp.onreadystatechange = function()
    {
        if (xhttp.readyState === 4)
            if(xhttp.status === 200)
                window.location.replace("index.php");
    };
}
function getCamera() {
    const video = document.getElementById("camera");
    const constraints = {
        video: true
    };
    function success(stream) {
        if (video)
            video.srcObject = stream;
    }
    function error() {
        var camicon = document.getElementById("camera-icon");
        if (camicon)
            camicon.onclick = takePicturefromPic;
        var container = document.getElementById("camera-container");
        var filepicker = document.getElementById("file-picker");
        var cameraImg = document.getElementById("camera-img");
        if (container && filepicker && cameraImg)
        {
            document.getElementById("camera").remove();
            filepicker.setAttribute("style", "display: initial; position: absolute");
            cameraImg.setAttribute("style", "display: initial; border: 1px solid");
            filepicker.onchange = function () {
                if (this.files[0].type === "image/png" || this.files[0].type === "image/jpeg")
                {
                    var reader = new FileReader();
                    reader.readAsDataURL(this.files[0]);
                    reader.onload = done;
                }
            }
        }
        function done(img) {
            var camimg = document.getElementById("camera-img");
            if (camimg && img)
                camimg.setAttribute('src', img.target.result);
        }
    }
    navigator.mediaDevices.getUserMedia(constraints).then(success).catch(error);
}
function login() {
    var username = document.getElementById("username");
    var password = document.getElementById("password");
    var toast_error = document.getElementById("toast_error");
    var redir_screen = document.getElementById("redir_screen");

    if (!badValueUP(username, password, toast_error))
    {
        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", "backend/functions.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("username="+username.value+"&password="+password.value);
        xhttp.onreadystatechange = function()
        {
            if (this.readyState === 4)
            {
                if (this.status === 400)
                {
                    if (toast_error)
                    {
                        toast_error.innerHTML = "Veuillez verifier vos informations";
                        toast_error.className = "show";
                        setTimeout(function() {
                            toast_error.className = toast_error.className.replace("show", "");
                        }, 3000);
                    }
                }
                if (this.status === 404)
                {
                    if (toast_error)
                    {
                        toast_error.innerHTML = "Nom d'utilisateur introuvable / Mauvais mot de passe";
                        toast_error.className = "show";
                        setTimeout(function() {
                            toast_error.className = toast_error.className.replace("show", "");
                        }, 3000);
                    }
                }
                if (this.status === 302)
                {
                    if (redir_screen)
                    {
                        redir_screen.style.display = "";
                        redir_screen.classList.add('login-completed');
                        setTimeout(function() {
                            window.location.replace("index.php");
                        }, 3000);
                    }
                }
                if (this.status === 401)
                {
                    if (toast_error)
                    {
                        toast_error.innerHTML = "Veuillez verifier votre mail";
                        toast_error.className = "show";
                        setTimeout(function() {
                            toast_error.className = toast_error.className.replace("show", "");
                        }, 3000);
                    }
                }
            }
        };
    }
}
function openComment(commentbtn) {
    var commentsPreContainer = document.getElementById("comment-pre-container");
    var commentsContainer = document.getElementById("comment-container");
    var textnbtn = document.getElementById("textarenbtn");
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST","backend/functions.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    if (commentbtn) {
        xhttp.send("getcomments=" + commentbtn.parentNode.getAttribute('id'));
        xhttp.onreadystatechange = function () {
            if (xhttp.readyState === 4)
                if (xhttp.status === 200) {
                    if (commentsPreContainer)
                        commentsPreContainer.style.display = "initial";
                    if (commentsContainer)
                        commentsContainer.innerHTML = xhttp.responseText;
                    var textarea = document.createElement("textarea");
                    var submitbtn = document.createElement("button");
                    textarea.setAttribute("id", "textarea");
                    textarea.setAttribute("maxlength", "500");
                    textarea.setAttribute("autofocus", "true");
                    textarea.setAttribute("rows", "5");
                    textarea.setAttribute("style", "resize: none; width: 100%; border: unset; border-top: 1px solid; box-sizing: border-box; outline: unset;");
                    submitbtn.innerHTML = "Comment";
                    submitbtn.setAttribute("id", commentbtn.parentNode.getAttribute('id'));
                    submitbtn.setAttribute("style", "background-color: white; border: 2px solid black;");
                    submitbtn.onclick = function () {
                        pushComment(this)
                    };
                    if (textnbtn) {
                        textnbtn.innerHTML = "";
                        textnbtn.appendChild(textarea);
                        textnbtn.appendChild(submitbtn);
                    }
                }
        };
    }
}
function passwordComplexity() {
    var indicator = document.getElementById("pwd_indicator");
    var password = document.getElementById("password");
    if (password && password.value.length < 5)
        if (indicator)
            indicator.style.background = "red";
    if (password && password.value.length >= 5)
        if (indicator)
            indicator.style.background = "yellow";
    if (password && password.value.length >= 10)
        if (indicator)
            indicator.style.background = "green";
}
function pushComment(btn) {
    var xhttp = new XMLHttpRequest();
    var comment = document.getElementById("textarea");
    var commentsContainer = document.getElementById("comment-container");
    if (comment && btn) {
        comment.value = comment.value.trim();
        if (comment.value.length !== 0) {
            xhttp.open("POST", "backend/functions.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("idphoto=" + btn.getAttribute('id') + "&text=" + comment.value);
            comment.value = "";
            xhttp.onreadystatechange = function () {
                if (xhttp.readyState === 4)
                    if (xhttp.status === 200) {
                        if (commentsContainer)
                            commentsContainer.innerHTML = xhttp.responseText;
                    }
            }
        }
    }
}
function putLike(likebtn) {
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST","backend/functions.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    if (likebtn) {
        xhttp.send("putlike=" + likebtn.parentNode.getAttribute('id'));
        xhttp.onreadystatechange = function () {
            if (xhttp.readyState === 4)
                if (xhttp.status === 200) {
                    var x = document.getElementById(xhttp.responseText.substr(0, xhttp.responseText.indexOf(" ")));
                    if (x)
                        x.childNodes[3].innerHTML = xhttp.responseText.substr(xhttp.responseText.indexOf(" ") + 1, xhttp.responseText.length);
                }
        };
    }
}
function recoverPassword() {
    var email = document.getElementById("username");
    var password = document.getElementById("password");
    var btnLogin = document.getElementById("login-btn");
    var btnLabel = document.getElementById("recover-text");
    var formTitle = document.getElementById("form-title");
    var btnRestore = document.getElementById("restore-btn");
    var toast_error = document.getElementById("toast_error");
    var toast_ok = document.getElementById("toast_ok");

    if (email && password && btnLogin && btnLabel && formTitle && btnRestore && toast_error && toast_ok) {
        btnLogin.style.display = "none";
        password.style.display = "none";
        btnLabel.innerHTML = "Envoyer le lien";
        formTitle.innerHTML = "Nouveau mot de passe";
        email.placeholder = "Email";
        email.maxLength = 100;
        btnRestore.onclick = function () {
            if (!email || !email.value.length || email.value.indexOf("@") === -1) {
                if (toast_error) {
                    toast_error.innerHTML = "Erreur, veuillez verifier l'adresse mail";
                    toast_error.className = "show";
                    setTimeout(function () {
                        toast_error.className = toast_error.className.replace("show", "");
                    }, 3000);
                }
            }
            else {
                var xhttp = new XMLHttpRequest();
                xhttp.open("POST", "backend/functions.php", true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhttp.send("email=" + email.value);
                xhttp.onreadystatechange = function () {
                    if (this.readyState === 4) {
                        if (this.status === 201) {
                            if (toast_ok) {
                                toast_ok.innerHTML = "Nouveau mot de passe envoye!";
                                toast_ok.className = "show";
                                setTimeout(function () {
                                    toast_ok.className = toast_ok.className.replace("show", "");
                                }, 3000);
                            }
                        }
                        if (this.status === 400) {
                            if (toast_error) {
                                toast_error.innerHTML = "Erreur, veuillez verifier l'adresse mail";
                                toast_error.className = "show";
                                setTimeout(function () {
                                    toast_error.className = toast_error.className.replace("show", "");
                                }, 3000);
                            }
                        }
                        if (this.status === 404) {
                            if (toast_error) {
                                toast_error.innerHTML = "Erreur, utilisateur introuvable";
                                toast_error.className = "show";
                                setTimeout(function () {
                                    toast_error.className = toast_error.className.replace("show", "");
                                }, 3000);
                            }
                        }
                    }
                }
            }
        }
    }
}
function redirectLogin() {
    window.location.href = "login.php";
}
function redirectPA() {
    window.location.href = "personal_page.php";
}
function redirectRegister() {
    window.location.href = "register.php";
}
function redirectIndex() {
    window.location.href = "index.php";
}
function register() {
    var username = document.getElementById("username");
    var email = document.getElementById("email");
    var password = document.getElementById("password");
    var name = document.getElementById("name");
    var surname = document.getElementById("surname");

    var toast_error = document.getElementById("toast_error");
    var toast_ok = document.getElementById("toast_ok");

    if (!badValue(username, email, password, name, surname, toast_ok, toast_error))
    {
        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", "backend/functions.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("username="+username.value+"&email="+email.value+"&password="+password.value+"&name="+name.value+
            "&surname="+surname.value);
        xhttp.onreadystatechange = function()
        {
            if (this.readyState === 4)
            {
                if (this.status === 302)
                {
                    if (toast_error)
                    {
                        toast_error.innerHTML = "Nom d'utilisateur deja utilise";
                        toast_error.className = "show";
                        setTimeout(function() {
                            toast_error.className = toast_error.className.replace("show", "");
                        }, 3000);
                    }
                }
                if (this.status === 400)
                {
                    if (toast_error)
                    {
                        toast_error.innerHTML = "Veuillez verifier les informations";
                        toast_error.className = "show";
                        setTimeout(function() {
                            toast_error.className = toast_error.className.replace("show", "");
                        }, 3000);
                    }
                }
                if (this.status === 201)
                {
                    if (toast_ok)
                    {
                        toast_ok.innerHTML = "Compte cree avec succes";
                        toast_ok.className = "show";
                        setTimeout(function() {
                            toast_ok.className = toast_ok.className.replace("show", "");
                        }, 3000);
                    }
                }
                if (this.status === 409)
                {
                    if (toast_error)
                    {
                        toast_error.innerHTML = "Un utilisateur avec ce nom/email existe deja";
                        toast_error.className = "show";
                        setTimeout(function() {
                            toast_error.className = toast_error.className.replace("show", "");
                        }, 3000);
                    }
                }
            }
        };
    }
}
function takePhotoFrom(start) {
    var container = document.getElementById("im-container");
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST","backend/functions.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("getphoto=" + start);
    xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4)
            if (xhttp.status === 200)
                if (container)
                    container.innerHTML = xhttp.responseText;
    };
}
function takePicture() {
    var canvas = document.createElement("canvas");
    var container = document.getElementById("prev-cont");
    var camera = document.getElementById("camera");
    var hat = document.getElementById("pic-hat");
    var glasses = document.getElementById("pic-glasses");
    var pipe = document.getElementById("pic-pipe");

    if (canvas && container && camera && hat && glasses && pipe) {
        canvas.className = "photo-canvas";
        canvas.width = camera.videoWidth;
        canvas.height = camera.videoHeight;
        canvas.getContext('2d').drawImage(camera, 0, 0);
        if (hat.checked || glasses.checked || pipe.checked) {
            var xhttp = new XMLHttpRequest();
            xhttp.open("POST", "backend/functions.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            if (document.getElementById("pic-hat").checked)
                xhttp.send("photo=" + canvas.toDataURL("image/png", 1) + "&superpos=1");
            else if (document.getElementById("pic-glasses").checked)
                xhttp.send("photo=" + canvas.toDataURL("image/png", 1) + "&superpos=2");
            else if (document.getElementById("pic-pipe").checked)
                xhttp.send("photo=" + canvas.toDataURL("image/png", 1) + "&superpos=3");
            xhttp.onreadystatechange = function () {
                if (xhttp.readyState === 4)
                    if (xhttp.status === 200)
                        container.innerHTML = xhttp.responseText;
            };
        }
    }
}
function takePicturefromPic() {
    var filepicker = document.getElementById("file-picker");
    var hat = document.getElementById("pic-hat");
    var glasses = document.getElementById("pic-glasses");
    var pipe = document.getElementById("pic-pipe");
    var camera = document.getElementById("camera-img");
    var container = document.getElementById("prev-cont");
    if (filepicker && hat && glasses && pipe && camera && container) {
        if (filepicker.files[0]) {
            var xhttp = new XMLHttpRequest();
            xhttp.open("POST", "backend/functions.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            if (hat.checked)
                xhttp.send("photo=" + camera.getAttribute("src") + "&superpos=1");
            else if (glasses.checked)
                xhttp.send("photo=" + camera.getAttribute("src") + "&superpos=2");
            else if (pipe.checked)
                xhttp.send("photo=" + camera.getAttribute("src") + "&superpos=3");
            xhttp.onreadystatechange = function () {
                if (xhttp.readyState === 4)
                    if (xhttp.status === 200)
                        container.innerHTML = xhttp.responseText;
            };
        }
    }
}
function trigger(radio) {
    var cameraButton = document.getElementById("camera-icon");
    var cameraContainer = document.getElementById("camera-container");
    var image = document.getElementById("superpos");
    if (radio && cameraButton && cameraContainer && image) {
        cameraButton.style.display = "initial";
        image.style = "";
        if (radio.value === "1")
        {
            image.src = "imgs/hat.png";
            image.style.width = "200px";
            image.style.left = "calc(50% - (200px/2))";
        }
        else if (radio.value === "2")
        {
            image.src = "imgs/sunglasses.png";
            image.style.top = "80px";
            image.style.width = "200px";
            image.style.left = "calc(50% - (200px/2))";
        }
        else if (radio.value === "3")
        {
            image.src = "imgs/pipe.png";
            image.style.top = "250px";
            image.style.left = "calc(50% + 100px)";
        }
    }
    else
        if (cameraButton)
            cameraButton.style.display = "none";
}
function removePhoto(idphoto) {
    if (confirm("Vous etes sur le point de supprimer definitevement cette image\nEtes vous sur ?")) {
        if (idphoto) {
            var id = idphoto.getAttribute("src").replace("/userphoto/", "").replace(".png", "");
            var xhttp = new XMLHttpRequest();
            xhttp.open("POST", "backend/functions.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("rmphoto=" + id);
            xhttp.onreadystatechange = function () {
                if (xhttp.readyState === 4)
                    if (xhttp.status === 200)
                        idphoto.remove();
            }
        }
    }
}
function editInfo() {
    var infoeditor = document.getElementById("info-editor");
    if (infoeditor)
        infoeditor.style.display = "initial";
}
function updateInfo() {
    var username = document.getElementById("username");
    var email = document.getElementById("email");
    var password = document.getElementById("password");
    var pref = document.getElementById("getmail").checked ? 1 : 0;
    if (username && email && password && pref) {
        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", "backend/functions.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("changeinfo=1&username=" + username.value + "&email=" + email.value + "&password=" + password.value + "&emailpref=" + pref);
        xhttp.onreadystatechange = function () {
            if (xhttp.readyState === 4) {
                if (xhttp.status === 200)
                    alert("Data updated!");
                if (xhttp.status === 400)
                    alert("Error");
            }
        };
    }
}
