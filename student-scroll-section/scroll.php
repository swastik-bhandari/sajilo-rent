<?php
include("./data-generation.php")
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="./styles/header.css" />

    <link rel="stylesheet" href="./styles/general.css" />
    <link rel="stylesheet" href="./styles/rooms.css" />

    <title>Sajilo rent</title>
</head>

<body>

    <header>
        <div class="header-left-section">
            <div>
                <a class="header-link" href="./scroll.html">
                    <img src="../resources/logo.svg" class="sajilo-rent-logo" />
                    <img src="../resources/logo.png" class="sajilo-rent-mobile-logo" />
                </a>
            </div>
        </div>
        <div class="header-middile-section">
            <input type="text" placeholder="Search" class="search-bar" />

            <div class="selectWrapper">
                <select name="price" class="price">

                    <option value="10000" selected>Price</option>
                    <option value="3000">3000</option>
                    <option value="3500">3500</option>
                    <option value="4000">4000</option>
                    <option value="4500">4500</option>
                    <option value="5000">5000</option>
                    <option value="5500">5500</option>
                    <option value="6000">6000</option>
                </select>

                <select name="houseType" class="houseType">
                    <option value="" selected>Type</option>
                    <option value="1">Single Room</option>
                    <option value="2">Double Room</option>
                    <option value="3">Triple Room</option>
                    <option value="4">Appartment</option>
                </select>
            </div>

            <button class="search-button">
                <img src="../resources/search-icon.png" class="search-icon" />
            </button>
        </div>
        <div class="header-right-section">
            <a class="order-link header-link" href=".././studentsection/index.html">
                <!-- <span class="return-text">Returns</span> -->
                <span class="order-text">Map</span>
            </a>

        </div>
    </header>


    <main class="main">
        <section class="room-container-grid">
            <?php echo $roomsHTML; ?>
        </section>
    </main>

    <script type="module" src="../studentsection/script/location.js"></script>
</body>

</html>