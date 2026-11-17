<?php session_start(); ?>
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
                        <li class="active"><a class="menu-item" href="pictures.php">SEE PHOTO</a></li>
                        <li><a class="menu-item" href="favs.php">FAVOURITES</a></li>
                        <?php if (($_SESSION['email'] ?? '') === 'perowa.d.m@gmail.com'): ?>
                        <li><a class="menu-item" href="users.php">ADMIN PANEL</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
        </header>
        <main class="container page">
            <h1>PICK A DATE</h1>
            <div class="search">
                <input type="date" id="date" />
                <button type="button" id="show">SHOW PHOTO</button>
            </div>
            <div class="card">
                <img id="photo" alt="" />
                <h2 id="title"></h2>
                <p id="text"></p>

                <form method="POST" action="favs.php" id="favForm" style="display: none;">
                    <input type="hidden" name="link_pic" id="link_pic" />
                    <input type="hidden" name="date_pic" id="date_pic" />
                    <button type="submit" name="add_fav" id="fav">ADD TO FAVOURITES</button>
                </form>
            </div>
        </main>
    </div>

    <!--<script>
        const API_KEY = 'mm7Q7P9MN7tQptR6jHnWQHDrDLCLmwhztqDpmPRd';

        document.getElementById('show').addEventListener('click', async () => {
            const dateVal = document.getElementById('date').value;
            const photoEl = document.getElementById('photo');
            const titleEl = document.getElementById('title');
            const textEl = document.getElementById('text');
            const favForm = document.getElementById('favForm');

            if (!dateVal) {
                alert('Pick the date');
                return;
            }

            titleEl.textContent = 'Loading...';
            textEl.textContent = '';
            photoEl.removeAttribute('src');
            favForm.style.display = 'none';

            try {
                const res = await fetch('https://api.nasa.gov/planetary/apod?api_key=' + API_KEY + '&date=' + dateVal);
                const data = await res.json();

                if (res.ok && data.media_type === 'image') {
                    const imgUrl = data.url || data.hdurl;
                    photoEl.src = imgUrl;
                    titleEl.textContent = data.title;
                    textEl.textContent = data.explanation;

                    document.getElementById('link_pic').value = imgUrl;
                    document.getElementById('date_pic').value = data.date;
                    favForm.style.display = 'block';
                    return;
                }

                // Запасной архив NASA без API-ключа (сработает, если лимит APOD исчерпан или выпало видео)
                const year = dateVal.split('-')[0];
                const backupRes = await fetch('https://images-api.nasa.gov/search?q=nebula%20galaxy&media_type=image&year_start=' + year + '&year_end=' + year);
                const backupData = await backupRes.json();
                const items = backupData.collection?.items || [];

                if (items.length > 0) {
                    const dayIndex = parseInt(dateVal.split('-')[2], 10) % items.length;
                    const item = items[dayIndex];
                    const imgUrl = item.links[0].href;
                    const info = item.data[0];

                    photoEl.src = imgUrl;
                    titleEl.textContent = info.title;
                    textEl.textContent = info.description || '';

                    document.getElementById('link_pic').value = imgUrl;
                    document.getElementById('date_pic').value = dateVal;
                    favForm.style.display = 'block';
                } else {
                    titleEl.textContent = 'For this date there is no photo';
                    textEl.textContent = 'Try picking ther date';
                }
            } catch (err) {
                titleEl.textContent = 'Connection error';
                textEl.textContent = err.message;
            }
        });
    </script>-->

    <script>
        document.getElementById('show').addEventListener('click', async () => {
            const dateVal = document.getElementById('date').value;
            const photoEl = document.getElementById('photo');
            const titleEl = document.getElementById('title');
            const textEl = document.getElementById('text');
            const favForm = document.getElementById('favForm');

            if (!dateVal) {
                alert('Pick the date');
                return;
            }

            titleEl.textContent = 'Loading...';
            textEl.textContent = '';
            photoEl.removeAttribute('src');
            favForm.style.display = 'none';

            try {
                const parts = dateVal.split('-');
                const year = parseInt(parts[0], 10);
                const month = parseInt(parts[1], 10);
                const day = parseInt(parts[2], 10);

                const topics = ['nebula', 'galaxy', 'saturn', 'jupiter', 'mars', 'supernova', 'hubble', 'earth', 'moon', 'cosmos', 'star', 'apollo'];
                const topic = topics[(month - 1) % topics.length];

                const res = await fetch('https://images-api.nasa.gov/search?q=' + topic + '&media_type=image');
                const data = await res.json();
                const items = (data.collection && data.collection.items) ? data.collection.items : [];

                const validItems = items.filter(item => item.links && item.links.length > 0 && item.data && item.data.length > 0);

                if (validItems.length > 0) {
                    const index = (year + month * 31 + day) % validItems.length;
                    const chosen = validItems[index];
                    const imgUrl = chosen.links[0].href;
                    const info = chosen.data[0];

                    photoEl.src = imgUrl;
                    titleEl.textContent = info.title;
                    textEl.textContent = info.description || 'NASA Archive Image';

                    document.getElementById('link_pic').value = imgUrl;
                    document.getElementById('date_pic').value = dateVal;
                    favForm.style.display = 'block';
                } else {
                    titleEl.textContent = 'Photo not found';
                    textEl.textContent = 'Try picking other date';
                }
            } catch (err) {
                titleEl.textContent = 'Connection error';
                textEl.textContent = err.message;
            }
        });
    </script>

</body>
</html>