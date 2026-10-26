<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Outside Tracker</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bubblegum+Sans&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../css/activity.css">
        <link rel="stylesheet" href="../css/navbar.css">
    </head>
    <body>
        <nav>
            <a href="/" class="title">Outside Tracker</a>
            <div class="navRight">
                <a href="/about">About</a>
                <a href="/create">Add Activity</a>
                <button id="logoutButton">Logout</button>
            </div>
        </nav>
        <div class="container">
            <div class="activity">
                <?php
                    require_once __DIR__ . '/../models/activity.php';
                    require_once __DIR__ . '/../models/user.php';
                    $user = User::getUserById($_SESSION['user_id']);
                    if (!$user) {
                        unset($_SESSION['user_id']);
                        header('Location: /login');
                        exit;
                    }
                    $activityId = preg_replace('/\/activity\/(\d+)/', '$1', $_SERVER['REQUEST_URI']);
                    $activity = Activity::getActivityById($activityId);
                    if (!$activity || $activity['userId'] !== $_SESSION['user_id']) {
                        echo '<p class="notFound">Activity not found!</p>';
                        echo '<a href="/">Go to Home</a>';
                    } else {
                        echo "<h1>{$activity['activityName']}</h1>";
                        echo "<p>{$activity['activityDate']} | {$activity['activityLength']} hours</p>";
                        echo "<div class='buttonContainer'>";
                        echo '<a href="/">Go to Home</a>';
                        echo "<a href='/edit/{$activity['id']}'>Edit</a>";
                        echo "</div>";
                    }
                ?>
            </div>
        </div>
    </body>
    <script>
        document.getElementById("logoutButton").addEventListener("click", async function(event) {
            const response = await fetch("/api/auth/logout", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                }
            });
            if (response.ok) {
                window.location.href = "/login";
            } else {
                alert("Failed to logout!");
            }
        })
    </script>
</html>