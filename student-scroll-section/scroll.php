<?php
include("./data-generation.php");
include("../header.php")
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



    <main class="main">
        <section class="room-container-grid">
            <?php echo $roomsHTML; ?>
        </section>
    </main>

    <script type="module" src="../studentsection/script/location.js"></script>
</body>

</html>