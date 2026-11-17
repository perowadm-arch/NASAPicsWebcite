<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="" content="" />
    <title>SpaceTime</title>
    <link rel="stylesheet" href="css/Main.css" />
</head>
<body>
    <div class="wrapper">
        <header class="container">
            <span class="logo">Spacetime</span>
            <nav>
                <div class="menu">
                    <input id="menu-toggle" type="checkbox" />
                    <label class="menu-btn" for="menu-toggle"><span></span></label>
                    <ul class="menubox">
                        <li class="active"><a class="menu-item" href="#">HOME</a></li>
                        <li><a class="menu-item" href="pictures.php">SEE PHOTO</a></li>
                        <li><a class="menu-item" href="favs.php">FAVOURITES</a></li>
                        <?php if (($_SESSION['email'] ?? '') === 'perowa.d.m@gmail.com'): ?>
                        <li><a class="menu-item" href="users.php">ADMIN PANEL</a></li>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['email'])): ?>
                        <li><a class="menu-item" href="logout.php">LOGOUT</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
        </header>

        <div class="hero container">
            <div class="hero--info">
                <p id="clock"></p>
                <h1>Greetings, <?php echo htmlspecialchars($_SESSION['email'] ?? 'User'); ?></h1>
                <h2>There you can find out what photo NASA did on the date you'll enter.</h2>

            </div>
            <img src="css/image/moon.png" alt="" />

            <div class="hero--text">
                <p>This website will take your date and go straight to NASA's archives to show it specifically to you!</p>
                <p>You would be able to save the picture to favourites and, if want, send it to your e-mail from fav list.</p>
            </div>
        </div>
    </div>

    <div class="features">
        <div class="container">

            <p>
                The website utilizes NASA’s open API to access its archives,
                which contain a vast collection of images along with detailed information about them.
                While the European Space Agency (ESA) also maintains such archives,
                NASA’s archives are much better suited for this particular purpose.
                NASA even publishes images captured by European Space Agency satellites.
                You can enter any date, and the site will automatically query the archives to display an image taken on that exact day.
            </p>

            <div class="register">
                <div class="container">
                    <form id="regForm" action="save.php" method="post" onsubmit="return checkForm(this);">
                        <input type="email" name="email" placeholder="Enter your e-mail">
                        <button type="submit" id="signup">SIGN UP</button>
                        <p>To go to your favourites...</p>
                    </form>
                </div>
            </div>

        </div>
    </div>

<script>
function updateClock() {
    const now = new Date();
    document.getElementById("clock").textContent = now.toLocaleTimeString();
}
updateClock();
    setInterval(updateClock, 1000);

    function checkForm(form) {
        const email = form.email.value.trim();
        const regMail = new RegExp("^[0-9a-z_.-]+@[0-9a-z_.-]+\\.[a-z]{2,6}$", "i");

        if (!regMail.test(email)) {
            alert("Please enter a valid e-mail address!");
            return false;
        }
        return true;
    }
</script>    


</body>
</html>