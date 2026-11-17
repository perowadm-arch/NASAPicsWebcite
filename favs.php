<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

$mi = new mysqli("localhost", "root", "", "baseformywebcite");
if ($mi->connect_errno) {
    die($mi->connect_error);
}

$mail_status = "";

// Получаем ID_User текущего пользователя по его email из сессии
$userStmt = $mi->prepare("SELECT ID_User FROM email WHERE Email_User = ?");
$userStmt->bind_param("s", $_SESSION['email']);
$userStmt->execute();
$userRes = $userStmt->get_result()->fetch_assoc();
$id_user = $userRes['ID_User'] ?? 0;

// 1. INSERT — сохранение фото в pictures и связывание в favourites
if (isset($_POST['add_fav']) && $id_user > 0) {
    $link_pic = trim($_POST['link_pic'] ?? '');
    $date_pic = trim($_POST['date_pic'] ?? '');

    if ($link_pic !== '' && $date_pic !== '') {
        $checkPic = $mi->prepare("SELECT ID_pic FROM pictures WHERE Date_pic = ?");
        $checkPic->bind_param("s", $date_pic);
        $checkPic->execute();
        $picRow = $checkPic->get_result()->fetch_assoc();

        if ($picRow) {
            $id_pic = $picRow['ID_pic'];
        } else {
            $insPic = $mi->prepare("INSERT INTO pictures (Link_pic, Date_pic) VALUES (?, ?)");
            $insPic->bind_param("ss", $link_pic, $date_pic);
            $insPic->execute();
            $id_pic = $mi->insert_id;
        }

        $checkFav = $mi->prepare("SELECT ID_fav FROM favourites WHERE ID_user = ? AND ID_pic = ?");
        $checkFav->bind_param("ii", $id_user, $id_pic);
        $checkFav->execute();

        if ($checkFav->get_result()->num_rows === 0) {
            $insFav = $mi->prepare("INSERT INTO favourites (ID_user, ID_pic) VALUES (?, ?)");
            $insFav->bind_param("ii", $id_user, $id_pic);
            $insFav->execute();
        }
    }
}

// 2. DELETE — удаление записи из избранного
if (isset($_POST['delete_fav']) && $id_user > 0) {
    $id_fav = (int)$_POST['id_fav'];
    $delFav = $mi->prepare("DELETE FROM favourites WHERE ID_fav = ? AND ID_user = ?");
    $delFav->bind_param("ii", $id_fav, $id_user);
    $delFav->execute();
}

// 3. MAIL — отправка выбранного фото на e-mail пользователя
if (isset($_POST['send_email'])) {
    $to = $_SESSION['email'];
    $link_pic = trim($_POST['link_pic'] ?? '');
    $date_pic = trim($_POST['date_pic'] ?? '');

    $subject = "NASA Photo from SpaceTime ($date_pic)";
    $message = "Hello, $to!\r\n\r\nHere is your favourite NASA photo for $date_pic:\r\n$link_pic";
    $headers = "From: SpaceTime <spacetime.project.mail@gmail.com>\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    @mail($to, $subject, $message, $headers);
    $mail_status = "Photo ($date_pic) has been sent to " . htmlspecialchars($to) . "!";
    }

    // 4. SELECT с JOIN — выборка всех избранных фото пользователя
    $favStmt = $mi->prepare("
    SELECT favourites.ID_fav, pictures.Link_pic, pictures.Date_pic
    FROM favourites
    JOIN pictures ON favourites.ID_pic = pictures.ID_pic
    WHERE favourites.ID_user = ?
    ORDER BY favourites.ID_fav DESC
    ");
    $favStmt->bind_param("i", $id_user);
    $favStmt->execute();
    $favs = $favStmt->get_result();
    ?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>SpaceTime</title>
        <link rel="stylesheet" href="css/Main.css" />
    </head>
    <body>
        <div class="wrapper2">
            <header class="container">
                <span class="logo">Spacetime</span>
                <nav>
                    <div class="menu">
                        <input id="menu-toggle" type="checkbox" />
                        <label class="menu-btn" for="menu-toggle"><span></span></label>
                        <ul class="menubox">
                            <li><a class="menu-item" href="index.php">HOME</a></li>
                            <li><a class="menu-item" href="pictures.php">SEE PHOTO</a></li>
                            <li class="active"><a class="menu-item" href="favs.php">FAVOURITES</a></li>
                            <?php if (($_SESSION['email'] ?? '') === 'perowa.d.m@gmail.com'): ?>
                            <li><a class="menu-item" href="users.php">ADMIN PANEL</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </nav>
            </header>

            <main class="container page">
                <h1>YOUR FAVOURITES</h1>

                <?php if ($mail_status !== ""): ?>
                <p style="margin-bottom: 25px; padding: 15px; border: 1px solid #727A96; border-radius: 8px; background: rgba(114, 122, 150, 0.2);">
                    <?php echo $mail_status; ?>
                </p>
                <?php endif; ?>

                <div class="favs">
                    <?php if ($favs->num_rows > 0): ?>
                    <?php while ($row = $favs->fetch_assoc()): ?>
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($row['Link_pic']); ?>" alt="NASA Photo" />
                        <h2>Date: <?php echo htmlspecialchars($row['Date_pic']); ?></h2>

                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <form method="POST" action="favs.php">
                                <input type="hidden" name="link_pic" value="<?php echo htmlspecialchars($row['Link_pic']); ?>" />
                                <input type="hidden" name="date_pic" value="<?php echo htmlspecialchars($row['Date_pic']); ?>" />
                                <button type="submit" name="send_email">SEND TO E-MAIL</button>
                            </form>

                            <form method="POST" action="favs.php">
                                <input type="hidden" name="id_fav" value="<?php echo $row['ID_fav']; ?>" />
                                <button type="submit" name="delete_fav" style="background: #8b3a3a;">REMOVE</button>
                            </form>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <p>No favourite photos yet. Go to SEE PHOTO to add some!</p>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </body>
</html>
