<?php
session_start();
if (($_SESSION['email'] ?? '') !== 'perowa.d.m@gmail.com') {
    header("Location: index.php");
    exit;
}

$mi = new mysqli("localhost", "root", "", "baseformywebcite");
if ($mi->connect_errno) {
    die($mi->connect_error);
}

// 1. UPDATE
if (isset($_POST['update_user'])) {
    $id = (int)$_POST['id_user'];
    $new_email = trim($_POST['new_email']);
    if ($new_email !== '') {
        $upd = $mi->prepare("UPDATE email SET Email_User = ? WHERE ID_User = ?");
        $upd->bind_param("si", $new_email, $id);
        $upd->execute();
    }
}

// 2. DELETE
if (isset($_POST['delete_user'])) {
    $id = (int)$_POST['id_user'];
    
    // Сначала удаляем связанные записи из favourites
    $delFavs = $mi->prepare("DELETE FROM favourites WHERE ID_user = ?");
    $delFavs->bind_param("i", $id);
    $delFavs->execute();

    // Затем удаляем самого пользователя
    $del = $mi->prepare("DELETE FROM email WHERE ID_User = ?");
    $del->bind_param("i", $id);
    $del->execute();
}

// 3. SELECT 
$result = $mi->query("SELECT ID_User, Email_User FROM email");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>SpaceTime — Admin</title>
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
                        <li><a class="menu-item" href="favs.php">FAVOURITES</a></li>
                        <li class="active"><a class="menu-item" href="users.php">ADMIN PANEL</a></li>
                    </ul>
                </div>
            </nav>
        </header>
        <main class="container page">
            <h1>REGISTERED USERS</h1>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card" style="margin-bottom: 20px;">
                    <p><strong>ID: <?php echo $row['ID_User']; ?></strong></p>
                    
                    <!-- UPDATE -->
                    <form method="POST" action="users.php" style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="hidden" name="id_user" value="<?php echo $row['ID_User']; ?>" />
                        <input type="email" name="new_email" value="<?php echo htmlspecialchars($row['Email_User']); ?>" required />
                        <button type="submit" name="update_user">SAVE</button>
                    </form>

                    <!-- DELETE -->
                    <form method="POST" action="users.php">
                        <input type="hidden" name="id_user" value="<?php echo $row['ID_User']; ?>" />
                        <button type="submit" name="delete_user" style="background: #8b3a3a;">DELETE</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </main>
    </div>
</body>
</html>