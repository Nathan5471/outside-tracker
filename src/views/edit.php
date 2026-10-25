<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Outside Tracker</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bubblegum+Sans&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../css/edit.css">
        <link rel="stylesheet" href="../css/navbar.css">
    </head>
    <body>
        <nav>
            <a href="/" class="title">Outside Tracker</a>
            <div class="navRight">
                <a href="/create">Add Activity</a>
                <button id="logoutButton">Logout</button>
            </div>
        </nav>
        <div class="container">
            <form class="activity">
                <?php
                    require_once __DIR__ . '/../models/activity.php';
                    require_once __DIR__ . '/../models/user.php';
                    $user = User::getUserById($_SESSION['user_id']);
                    if (!$user) {
                        unset($_SESSION['user_id']);
                        header('Location: /login');
                        exit;
                    }
                    $activityId = preg_replace('/\/edit\/(\d+)/', '$1', $_SERVER['REQUEST_URI']);
                    $activity = Activity::getActivityById($activityId);
                    if (!$activity || $activity['userId'] !== $_SESSION['user_id']) {
                        echo '<p class="notFound">Activity not found!</p>';
                        echo '<a href="/">Go to Home</a>';
                    } else {
                        echo "<h1>Edit {$activity['activityName']}</h1>";
                        echo "<label for='activityName'>Activity Name:</label>";
                        echo "<input type='text' id='activityName' name='activityName' value='{$activity['activityName']}' required>";
                        echo "<label for='activityDate'>Activity Date:</label>";
                        echo "<input type='date' id='activityDate' name='activityDate' value='{$activity['activityDate']}' required>";
                        echo "<label for='activityLength'>Activity Length (hours):</label>";
                        echo "<input type='number' id='activityLength' name='activityLength' value='{$activity['activityLength']}' required>";
                        echo "<p id='errorMessage'></p>";
                        echo "<div class='buttonContainer'>";
                        echo "<a href='/activity/{$activity['id']}'>Cancel</a>";
                        echo "<button type='submit'>Update</button>";
                        echo "<button type='button' class='deleteButton'>Delete</button>";
                        echo "</div>";
                    }
                ?>
            </form>
        </div>
    </body>
    <script>
        document.querySelector(".activity").onsubmit = async function(event) {
            event.preventDefault();
            const eventId = window.location.pathname.split('/').pop();
            const newEventName = document.getElementById("activityName").value;
            const newEventDate = document.getElementById("activityDate").value;
            const newEventLength = document.getElementById("activityLength").value;
            const response = await fetch("/api/activities/update", {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    activityId: eventId,
                    activityName: newEventName,
                    activityDate: newEventDate,
                    activityLength: newEventLength
                })
            });
            const data = await response.json();
            if (response.ok) {
                window.location.href = "/activity/" + eventId;
            } else {
                document.getElementById("errorMessage").textContent = data.error || "An unknown error occurred.";
            }
        }

        document.querySelector(".deleteButton").addEventListener("click", async function(event) {
            event.preventDefault();
            const eventId = window.location.pathname.split('/').pop();
            const response = await fetch("/api/activities/delete", {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    activityId: eventId
                })
            });
            const data = await response.json();
            if (response.ok) {
                window.location.href = "/";
            } else {
                document.getElementById("errorMessage").textContent = data.error || "An unknown error occurred.";
            }
        })

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