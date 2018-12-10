<?php
session_start();
include("$_SERVER[DOCUMENT_ROOT]/config/database.php");

function responseCode($http_code, $exit_code) {
    header("HTTP/1.0 $http_code");
    exit($exit_code);
}
function activateUser($token) {
    GLOBAL $DB_DNS;
    GLOBAL $DB_USER;
    GLOBAL $DB_PASSWORD;
    $hash = preg_replace("/[^a-fA-F0-9]/", "", trim($token));
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    $query = "SELECT iduser, activated FROM `users` WHERE `hash` = ?";
    $db = $pdobj->prepare($query);
    $db->bindParam(1,$hash);
    $db->execute();
    $result = $db->fetch(PDO::FETCH_ASSOC);
    if ($result && $result['activated'] === 0)
    {
        $query = "UPDATE `users` SET `activated` = 1 WHERE iduser = ?";
        $db = $pdobj->prepare($query);
        $db->bindParam(1,$result['iduser'], PDO::PARAM_INT);
        $db->execute();
        $query = "UPDATE users SET hash = ? where iduser = ?";
        $db = $pdobj->prepare($query);
        $db->bindParam(1, $result['iduser'], PDO::PARAM_STR);
        $db->bindParam(2, $result['iduser'], PDO::PARAM_INT);
        $db->execute();
        return (0);
    }
    else
        return (-1);
}
function printImagesUser()
{
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    try {
        $query = "SELECT idphoto FROM photos WHERE iduser = ?";
        $db = $pdobj->prepare($query);
        $db->bindParam(1, $_SESSION['user']['iduser'], PDO::PARAM_INT);
        if ($db->execute())
        {
            $result = $db->fetchAll();
            foreach ($result as $img)
                echo "<img src='/userphoto/{$img['idphoto']}.png' style='width: 80%' onclick='removePhoto(this)'>";
        }
        else
            die("none");
    }
    catch (PDOException $exception)
    {
        die($exception->getMessage());
    }
}
function getLikes($idphoto) {
    $idphoto = preg_replace("/[^0-9_]/", "", trim($idphoto));
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    $query = "SELECT COUNT(*) as result FROM likes WHERE id_photo = ?";
    $db = $pdobj->prepare($query);
    $db->bindParam(1, $idphoto, PDO::PARAM_STR);
    $db->execute();
    $result = $db->fetch(PDO::FETCH_ASSOC);
    return ($result['result']);
}
function getComments($idphoto) {
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    $query = "SELECT COUNT(*) as result FROM comments WHERE id_photo = ?";
    $db = $pdobj->prepare($query);
    $db->bindParam(1, $idphoto, PDO::PARAM_STR);
    $db->execute();
    $result = $db->fetch(PDO::FETCH_ASSOC);
    return ($result['result']);
}
function getUser($idphoto) {
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    $query = "SELECT iduser FROM photos WHERE idphoto = '$idphoto' ";
    $db = $pdobj->prepare($query);
    $db->bindParam(1, $iduser, PDO::PARAM_STR);
    $db->execute();
    $result = $db->fetch(PDO::FETCH_ASSOC);
    $lol = $result["iduser"];
    $query = "SELECT username FROM users WHERE iduser = $lol ";
    $db = $pdobj->prepare($query);
    $db->bindParam(1, $username, PDO::PARAM_STR);
    $db->execute();
    $result = $db->fetch(PDO::FETCH_ASSOC);
    return ($result['username']);
}
function resize_image($data) {
    if (getimagesizefromstring(base64_decode($data)))
    {
        list($width, $height) = getimagesizefromstring(base64_decode($data));
        $src = imagecreatefromstring(base64_decode($data));
        $final = imagecreatetruecolor(640, 480);
        imagecopyresampled($final, $src, 0, 0, 0, 0, 640, 480, $width, $height);
        return $final;
    }
    return (null);
}

//DESTROY SESSION
if (count($_POST) === 1 && isset($_POST['destroy'])) {
    session_destroy();
    responseCode(200, 0);
}

//LOGIN
if (count($_POST) === 2 && isset($_POST['username'], $_POST['password'])) {
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    $username = preg_replace("/[^a-zA-Z0-9]/", "", trim($_POST['username']));
    $password = hash("whirlpool", preg_replace("/[^a-zA-Z0-9]/", "", trim($_POST['password'])));
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    if (empty($username) || empty($password))
        responseCode(400, -1);
    $query = "SELECT * FROM `users` WHERE `username` = ? AND `password` = ?";
    $db = $pdobj->prepare($query);
    $db->bindParam(1, $username, PDO::PARAM_STR);
    $db->bindParam(2, $password, PDO::PARAM_STR);
    $db->execute();
    $result = $db->fetch(PDO::FETCH_ASSOC);
    if ($result && $result['activated'] === 1)
    {
        $_SESSION['user'] = $result;
        responseCode(302, -1);
    }
    elseif ($result && $result['activated'] === 0)
        responseCode(401, -1);
    else
        responseCode(404, -1);
}

//REGISTER
if (count($_POST) === 5 && isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['name'], $_POST['surname'])) {
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    $username = preg_replace("/[^a-zA-Z0-9]/", "", trim($_POST['username']));
    $password = hash("whirlpool", preg_replace("/[^a-zA-Z0-9]/", "", trim($_POST['password'])));
    $name = preg_replace("/[^a-zA-Z0-9]/", "", trim($_POST['name']));
    $surname = preg_replace("/[^a-zA-Z0-9]/", "", trim($_POST['surname']));
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    if (empty($username) || empty($password) || empty($name) || empty($surname))
        responseCode(400, -1);
    $hash = hash("md5", $username.$password);
    $name[0] = strtoupper($name[0]);
    $surname[0] = strtoupper($surname[0]);
    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        responseCode(400, -1);
    $email = $_POST['email'];
    $query = "SELECT * FROM `users` WHERE `email` = ? AND `username` = ?";
    $db = $pdobj->prepare($query);
    $db->bindParam(1, $email, PDO::PARAM_STR);
    $db->bindParam(2, $username, PDO::PARAM_STR);
    $db->execute();
    if ($db->rowCount() > 0)
    {
        responseCode(302, -1);
    }
    else
    {
        try {
            $query = "INSERT INTO `users` (`username`, `email`, `password`, `name`, `surname`, `hash`) VALUES (?, ?, ?, ?, ?, ?)";
            $db = $pdobj->prepare($query);
            $db->bindParam(1, $username, PDO::PARAM_STR);
            $db->bindParam(2, $email, PDO::PARAM_STR);
            $db->bindParam(3, $password, PDO::PARAM_STR);
            $db->bindParam(4, $name, PDO::PARAM_STR);
            $db->bindParam(5, $surname, PDO::PARAM_STR);
            $db->bindParam(6, $hash, PDO::PARAM_STR);
            if ($db->execute())
            {
                $fullname = $name." ".$surname;
                $header = 'Content-type: text/html; charset=UTF-8' . "\r\n";
                $header .= 'From: <svillain@student.42.fr>' . "\r\n";
                $msg = 'Bonjour '.$fullname.'<br>Pour finaliser votre inscription, veuillez cliquer  <a href="http://'.$_SERVER['HTTP_HOST'].'/verify.php?token='.$hash.'">ICI</a>';
                mail($email,"[Votre compte Camagru]", $msg, $header);
                responseCode(201, 0);
            }
            else
                responseCode(400, -1);
        }
        catch (PDOException $exception)
        {
            if ($exception->getCode() == 23000)
                responseCode(409, -1);
            else
                responseCode(400, -1);
        }
    }
}

//RESTORE PASSWORD
if (count($_POST) === 1 && isset($_POST['email'])) {
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    $query = "SELECT `iduser`, `name`, `password`, `email` FROM users WHERE `email` = ?";
    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        responseCode(400, -1);
    $db = $pdobj->prepare($query);
    $db->bindParam(1, $_POST['email'], PDO::PARAM_STR);
    $db->execute();
    $result = $db->fetch(PDO::FETCH_ASSOC);
    if ($result)
    {
        $password = substr($result['password'], 0, 10);
        $hashedPW = hash("whirlpool", $password);
        $iduser = $result['iduser'];
        $name = $result['name'];
        $email = $result['email'];

        $query = "UPDATE `users` SET `password` = ? WHERE `iduser` = ?";
        $db = $pdobj->prepare($query);
        $db->bindParam(1, $hashedPW, PDO::PARAM_STR);
        $db->bindParam(2, $iduser, PDO::PARAM_INT);
        $db->execute();
        $header = 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $header .= 'From: <svillain@student.42.fr>' . "\r\n";
        $msg = 'Bonjour '.$name.' voici votre nouveau mot de passe '.$password;
        mail($email, "[Nouveau mot de passe]", $msg, $header);
        responseCode(201, 0);
    }
    else
        responseCode(404, -1);
}

//SAVE PHOTO
if (count($_POST) === 2 && isset($_POST['photo'], $_POST['superpos'], $_SESSION['user'])) {
	$superpos_id = (int)$_POST['superpos'];
	if (!isset($superpos_id))
		responseCode(500, -1);
	$superpos_id = preg_replace("/[^0-9]/", "", trim($superpos_id));
	if (empty($superpos_id))
		responseCode(500, -1);
    $data = $_POST['photo'];
    $data = str_replace('data:image/png;base64,', '', $data);
    $data = str_replace('data:image/jpeg;base64,', '', $data);
    $data = str_replace(' ', '+', $data);
    $data = @resize_image($data);
    if ($data === null)
    {
        printImagesUser();
        exit(1);
    }
	$marge_right = 0;
	$marge_top = 0;
	$sx = 0;
	$sy = 0;
	switch($superpos_id)
	{
		case 1:
			$superpos = imagecreatefrompng("$_SERVER[DOCUMENT_ROOT]/imgs/hat.png");
			$superpos = imagescale($superpos, 200);
			$sx = imagesx($superpos);
			$sy = imagesy($superpos);
			$marge_right = $sx / 2;
			break;
		case 2:
			$superpos = imagecreatefrompng("$_SERVER[DOCUMENT_ROOT]/imgs/sunglasses.png");
			$superpos = imagescale($superpos, 200);
			$sx = imagesx($superpos);
			$sy = imagesy($superpos);
			$marge_right = $sx / 2;
			$marge_top = 80; 
			break;
		case 3:
			$superpos = imagecreatefrompng("$_SERVER[DOCUMENT_ROOT]/imgs/pipe.png");
			$sx = imagesx($superpos);
			$sy = imagesy($superpos);
			$marge_right = -100;
			$marge_top = 250;
			break;
		default:
			responseCode(500, -1);
	}
	$name = str_replace(".", "", microtime(true))."_".$_SESSION['user']['iduser'];
	imagecopy($data, $superpos, (imagesx($data)/2) - $marge_right, $marge_top, 0, 0, $sx, $sy);
    imagepng($data, "$_SERVER[DOCUMENT_ROOT]/userphoto/$name.png");
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    try {
        $query = "INSERT INTO `photos` (idphoto, iduser) VALUES (?, ?)";
        $db = $pdobj->prepare($query);
        $db->bindParam(1, $name, PDO::PARAM_STR);
        $db->bindParam(2, $_SESSION['user']['iduser'], PDO::PARAM_INT);
        if ($db->execute())
            printImagesUser();
        else
            die("none");
    }
    catch (PDOException $exception)
    {
        die($exception->getMessage());
    }
}

//GET ALL PHOTO FROM THE STARTING POINT $_POST['getphoto']
if (count($_POST) === 1 && isset($_POST['getphoto']))
{
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    try {
        $query = "SELECT idphoto FROM photos";
        $db = $pdobj->prepare($query);
        $db->execute();
        $img_array = $db->fetchAll();
        $start = (int)preg_replace("/[^0-9]/", "", $_POST['getphoto']);
        if ($start < 0)
            exit(1);
        if ($start !== 0)
            $start = ($start * 10) - 10;
        $img_array = array_slice($img_array, $start, 10);
        if (isset($_SESSION['user']['iduser'])) {
            foreach ($img_array as $img) : ?>
                <div class="homepage-logged">
                    <img src="/userphoto/<?= $img['idphoto'] ?>.png">
                    <div id="<?= $img['idphoto'] ?>">
                        <img src="/imgs/e47a3abfc3c9178e3c0b98f888c6a802.svg" style="width: 30px" onclick="putLike(this)">
                        <p style="display: inline; margin-right: 10%"><?= getLikes($img['idphoto']) ?></p>
                        <img src="/imgs/commentaire.svg" style="width: 30px; margin-left: 10%" onclick="openComment(this)">
                        <p style="display: inline"><?= getComments($img['idphoto']) ?></p>
                        <p> <font color="blue">Crée par <?= getUser($img['idphoto']) ?></font></p>
                    </div>
                </div>
            <?php endforeach;
        } else {
            foreach ($img_array as $img) : ?>
                <div class="homepage-nolog" style="box-shadow: unset">
                    <img src="/userphoto/<?= $img['idphoto'] ?>.png" class="nolog">
                </div>
            <?php endforeach;
        }
    }
    catch (PDOException $e)
    {
        die($e->getMessage());
    }
}

//GET NUMBER OF PHOTOS
if (count($_POST) === 1 && isset($_POST['sizearray']))
{
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    try {
        $query = "SELECT COUNT(*) as total FROM photos";
        $db = $pdobj->prepare($query);
        $db->execute();
        $result = $db->fetch();
        echo $result['total'];
    }
    catch (PDOException $e)
    {
        die($e->getMessage());
    }
}

//LOADER IMAGES WHEN YOU OPEN THE PERSONAL PAGE
if (count($_POST) === 1 && isset($_POST['getphotos']))
{
    printImagesUser();
}

//PUT LIKE
if (count($_POST) === 1 && isset($_SESSION['user']['iduser'], $_POST['putlike']))
{
    $id_photo = preg_replace("/[^0-9_]/", "", trim($_POST['putlike']));
    if (empty($id_photo))
        responseCode(408, -1);
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    $query = "SELECT idphoto FROM photos WHERE idphoto = ?";
    $db = $pdobj->prepare($query);
    $db->bindParam(1, $id_photo, PDO::PARAM_STR);
    $db->execute();
    $result = $db->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $query = "SELECT * FROM `likes` WHERE id_user = ? AND id_photo = ?";
        $db = $pdobj->prepare($query);
        $db->bindParam(1, $_SESSION['user']['iduser'], PDO::PARAM_INT);
        $db->bindParam(2, $id_photo, PDO::PARAM_STR);
        $db->execute();
        $result = $db->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            $query = "INSERT INTO likes (id_user, id_photo) VALUES (?, ?)";
            $db = $pdobj->prepare($query);
            $db->bindParam(1, $_SESSION['user']['iduser'], PDO::PARAM_INT);
            $db->bindParam(2, $id_photo, PDO::PARAM_STR);
            $db->execute();
        } else {
            $query = "DELETE FROM likes where id_user = ? AND id_photo = ?";
            $db = $pdobj->prepare($query);
            $db->bindParam(1, $_SESSION['user']['iduser'], PDO::PARAM_INT);
            $db->bindParam(2, $id_photo, PDO::PARAM_STR);
            $db->execute();
        }
        echo $id_photo . " " . getLikes($id_photo);
    }
}

//OPEN/GET COMMENTS
if (count($_POST) === 1 && isset($_SESSION['user']['iduser'], $_POST['getcomments']))
{
    $getcomments = preg_replace("/[^0-9_]/", "", trim($_POST['getcomments']));
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    $query = "SELECT idphoto FROM photos WHERE idphoto = ?";
    $db = $pdobj->prepare($query);
    $db->bindParam(1, $getcomments, PDO::PARAM_STR);
    $db->execute();
    $result = $db->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        try {
            $query = "SELECT username, comment FROM users, comments, photos WHERE id_photo = ? AND comments.id_user = users.iduser AND comments.id_photo = photos.idphoto order by `date` ASC";
            $db = $pdobj->prepare($query);
            $db->bindParam(1, $getcomments, PDO::PARAM_STR);
            if ($db->execute()) {
                $result = $db->fetchAll();
                foreach ($result as $comment) {
                    echo "<p style=\"margin: 0 0 10px 10px; padding-top: 10px\"><span style=\"font-weight: bold\">{$comment['username']}:</span><span style=\"font-family: 'Roboto', sans-serif\">&nbsp;" . htmlspecialchars($comment['comment']) . "</span></p>";
                }
            } else
                die("none");
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
}

//PUSH COMMENT
if (count($_POST) === 2 && isset($_SESSION['user']['iduser'], $_POST['idphoto'], $_POST['text']))
{
    $idphoto = preg_replace("/[^0-9_]/", "", trim($_POST['idphoto']));
    $text = preg_replace("/[^a-zA-Z0-9]/", "", trim($_POST['text']));
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    $query = "SELECT idphoto FROM photos WHERE idphoto = ?";
    $db = $pdobj->prepare($query);
    $db->bindParam(1, $idphoto, PDO::PARAM_STR);
    $db->execute();
    $result = $db->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        try {
            $query = "INSERT INTO comments (id_user, id_photo, comment, date) VALUES (?, ?, ?, ?)";
            $db = $pdobj->prepare($query);
            $date = date("Y-m-d h:i:s");
            $db->bindParam(1, $_SESSION['user']['iduser'], PDO::PARAM_INT);
            $db->bindParam(2, $idphoto, PDO::PARAM_STR);
            $db->bindParam(3, $text, PDO::PARAM_STR);
            $db->bindParam(4, $date, PDO::PARAM_STR);
            $db->execute();
            $query = "SELECT name, surname, email, getmailpref FROM users, photos WHERE users.iduser = photos.iduser AND photos.idphoto = ?";
            $db = $pdobj->prepare($query);
            $db->bindParam(1, $idphoto, PDO::PARAM_STR);
            if ($db->execute()) {
                $result = $db->fetch();
                if ($result['getmailpref'] == 1) {
                    $fullname = $result['name'] . " " . $result['surname'];
                    $header = 'Content-type: text/html; charset=UTF-8' . "\r\n";
                    $header .= 'From: <svillain@student.42.fr>' . "\r\n";
                    $msg = 'Coucou ' . $fullname . ' On vient de commenter ta photo !';
                    mail($result['email'], "[Nouveau Commentaire]", $msg, $header);
                }
            }
            $query = "SELECT username, comment FROM users, comments, photos WHERE id_photo = ? AND comments.id_user = users.iduser AND comments.id_photo = photos.idphoto order by `date` ASC";
            $db = $pdobj->prepare($query);
            $db->bindParam(1, $idphoto, PDO::PARAM_STR);
            if ($db->execute()) {
                $result = $db->fetchAll();
                foreach ($result as $comment) {
                    echo "<p style=\"margin: 0 0 10px 10px; padding-top: 10px\"><span style=\"font-weight: bold\">{$comment['username']}:</span><span style=\"font-family: 'Roboto', sans-serif\">&nbsp;" . htmlspecialchars($comment['comment']) . "</span></p>";
                }
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
}

//REMOVE PHOTO
if (count($_POST) === 1 && isset($_SESSION['user']['iduser'], $_POST['rmphoto']))
{
    $idphoto = preg_replace("/[^0-9_]/", "", trim($_POST['rmphoto']));
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    try {
        $query = "SELECT idphoto FROM photos WHERE idphoto = ? AND iduser = ?";
        $db = $pdobj->prepare($query);
        $db->bindParam(1, $idphoto, PDO::PARAM_STR);
        $db->bindParam(2, $_SESSION['user']['iduser'], PDO::PARAM_INT);
        $db->execute();
        $result = $db->fetch();
        if ($result) {
            $query = "DELETE FROM photos WHERE idphoto = ? AND iduser = ?";
            $db = $pdobj->prepare($query);
            $db->bindParam(1, $idphoto, PDO::PARAM_STR);
            $db->bindParam(2, $_SESSION['user']['iduser'], PDO::PARAM_INT);
            if ($db->execute())
                if (@file_exists("$_SERVER[DOCUMENT_ROOT]/userphoto/" . $idphoto . ".png"))
                    @unlink("$_SERVER[DOCUMENT_ROOT]/userphoto/" . $idphoto . ".png");
        }
    }
    catch (PDOException $e)
    {
        die($e->getMessage());
    }
}

//UPDATE PERSONAL DATA
if (count($_POST) === 5 && isset($_SESSION['user']['iduser'], $_POST['changeinfo'], $_POST['username'], $_POST['email'], $_POST['password'], $_POST['emailpref']))
{
    global $DB_DNS;
    global $DB_USER;
    global $DB_PASSWORD;
    try {
        $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
        $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        responseCode(408, -1);
        die('Connection failed: ' . $e->getMessage());
    }
    try {
        $username = preg_replace("/[^a-zA-Z0-9]/", "", trim($_POST['username']));
        $email = $_POST['email'];
        $password = $_POST['password'];
        $pref = preg_replace("/[^0-1]/", "", trim($_POST['emailpref']));
        if (strlen($username) > 0) {
            $query = "UPDATE users SET username = ? WHERE iduser = ?";
            $db = $pdobj->prepare($query);
            $db->bindParam(1, $username, PDO::PARAM_STR);
            $db->bindParam(2, $_SESSION['user']['iduser'], PDO::PARAM_INT);
            $db->execute();
        }
        if (strlen($email) > 0)
        {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
                responseCode(400, -1);
            $query = "UPDATE users SET email = ? WHERE iduser = ?";
            $db = $pdobj->prepare($query);
            $db->bindParam(1, $email, PDO::PARAM_STR);
            $db->bindParam(2, $_SESSION['user']['iduser'], PDO::PARAM_INT);
            $db->execute();
        }
        if (strlen($password) > 0)
        {
            $password = hash("whirlpool", preg_replace("/[^a-zA-Z0-9]/", "", trim($password)));
            $query = "UPDATE users SET password = ? WHERE iduser = ?";
            $db = $pdobj->prepare($query);
            $db->bindParam(1, $password, PDO::PARAM_STR);
            $db->bindParam(2, $_SESSION['user']['iduser'], PDO::PARAM_INT);
            $db->execute();
        }
        if (strlen($pref) > 0) {
            $query = "UPDATE users SET getmailpref = ? WHERE iduser = ?";
            $db = $pdobj->prepare($query);
            $db->bindParam(1, $pref, PDO::PARAM_STR);
            $db->bindParam(2, $_SESSION['user']['iduser'], PDO::PARAM_INT);
            $db->execute();
            responseCode(200, 0);
        }
    }
    catch (PDOException $e)
    {
        responseCode(400, -1);
        die($e->getMessage());
    }
}
