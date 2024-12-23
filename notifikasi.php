<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['user_id'])) {
    header("Location:user/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil notifikasi dari database
$query_notifications = mysqli_query($konek, "SELECT * FROM notifications WHERE user_id = '$user_id' ORDER BY is_read ASC, id DESC") or die(mysqli_error($konek));
$notifications = [];
while ($row = mysqli_fetch_assoc($query_notifications)) {
    $notifications[] = $row;
}

// Tandai semua sebagai dibaca
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_as_read'])) {
    $update_query = "UPDATE notifications SET is_read = 1 WHERE user_id = '$user_id' AND is_read = 0";
    mysqli_query($konek, $update_query) or die(mysqli_error($konek));
    header("Location: notifikasi.php"); // Refresh halaman
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <title>Profile Pengguna</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Poppins", sans-serif;
            background-color: #F9F9F9;
        }
        .navbar{
            background-color: #1E5B86;
        }
        .nav-link{
            color:white;
        }
        .profile-nav{
            border-radius: 20%;
            width: 50px;
            height: 38px;
        }
        p{
            color: #ADA7A7;
        }
        .img-top img{
            width: 100%;
            margin-bottom: 10px;
        }
        main{
            width: 90%;
        }
        .btn{
            background-color: #1E5B86;
        }
        .profile-img{
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .name-email{
            margin-left: 20px;
        }
        .d-flex .profile-pict{
            width: 80px;
            height: 80px;
            border-radius: 100%;
        }
        .name-email{
            margin-top: 14px;
        }
        .form-section {
            display: flex;
            gap: 20px;
        }
        .form-section .left, .form-section .right {
            flex: 1;
        }
        .form-label {
            color: #555;
        }
        .carousel-control-prev, .carousel-control-next {
            opacity: 0;
            transition: opacity 0.3s;
        }
        #carouselExampleControls:hover .carousel-control-prev,
        #carouselExampleControls:hover .carousel-control-next {
            opacity: 1;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-primary px-3">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="index.php">Home</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="search.php">Buy</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="uploads/uploaded.php">Sell</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="monetisasi.php">History</a>
            </li>
        </ul>
        <form class="d-flex" role="search" action="search.php" method="get">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search">
            <?php if($_SESSION['profile_picture'] != "") { ?>
                <div class="dropdown" style="width : 38px;">
                    <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0;">
                        <img class="profile-nav" src="uploads/<?=$_SESSION['profile_picture']?>" alt="Profile Picture" style="width: 38px;">
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="user/logout.php">Logout</a></li>
                    </ul>
                </div>
                <?php } else { ?>
                <div class="dropdown" style="width : 38px;">
                    <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0;">
                        <img class="profile-nav" src="default.png" alt="Profile Picture" style="width: 38px;">
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="user/logout.php">Logout</a></li>
                    </ul>
                </div>
                <?php } ?>
            <a href="notifikasi.php" style="margin-left: 8px;"><img src="notif.png" alt="Notifikasi"></a>
            <a href="cart.php" style="margin-left: 8px; padding:1px; background-color:white; border-radius:8px;"><img src="cart (2).png" alt="Cart" style="height:36px"></a>
        </form>
        </div>
    </div>
    </nav>

    <main class="m-auto">
        <section class="container py-4">
            <h2>Notifikasi <span class="badge bg-white text-black">(<?= count(array_filter($notifications, fn($n) => $n['is_read'] == 0)); ?>)</span></h2>
            <form method="POST">
                <button class="btn btn-primary mb-3" style="float: right;" name="mark_as_read">Tandai dibaca</button>
            </form>
            <div class="clearfix"></div>
            <div class="list-group">
                <?php foreach ($notifications as $notif): ?>
                    <a href="#"
                    class="list-group-item list-group-item-action mb-2"
                    style="<?= $notif['is_read'] == 0 ? 'background-color: #4F98CA; color: white;' : 'border: 1px solid #4F98CA;' ?> border-radius: 8px;">
                        <h5 class="mb-1"><?= htmlspecialchars($notif['title']); ?></h5>
                        <p class="mb-1"><?= htmlspecialchars($notif['message']); ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        document.querySelector('form').addEventListener('submit', async function (e) {
        e.preventDefault(); // Prevent form submission
        const formData = new FormData(this);

        const response = await fetch('notifikasi.php', {
            method: 'POST',
            body: formData
        });

        if (response.ok) {
            // Refresh notifikasi tanpa reload
            location.reload();
        } else {
            alert('Gagal memperbarui notifikasi');
        }
    });
    </script>
</body>
</html>