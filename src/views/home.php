<?php
    require_once __DIR__ . '/../models/activity.php';
    require_once __DIR__ . '/../models/user.php';
    $user = User::getUserById($_SESSION['user_id']);
    if (!$user) {
        unset($_SESSION['user_id']);
        header('Location: /login');
        exit;
    }
    $activities = Activity::getUserAcitivities($_SESSION['user_id']);
    $activities = array_values($activities);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Outside Tracker</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bubblegum+Sans&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/navbar.css">
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
            <div class="leftContainer">
                <div class="activitiesList">
                    <div class="activitiesHeader">
                        <h2>Activities</h2>
                        <select id="sortActivities">
                            <option value="none">Sort: None</option>
                            <option value="a-z">Sort: A-Z</option>
                            <option value="z-a">Sort: Z-A</option>
                            <option value="newest">Sort: Newest</option>
                            <option value="oldest">Sort: Oldest</option>
                        </select>
                    </div>
                    <div class="scrollableContainer" id="activitiesList"></div>
                </div>
            </div>
            <div class="rightContainer">
                <div class="dailyPercent">
                    <h2>Today's Activity</h2>
                    <div class="dailyPercentContainer" id="dailyPercentContainer">
                        <div class="dailyPercentProgress" id="dailyPercentProgress"></div>
                    </div>
                    <p class="dailyPercentText">0%</p>
                    <p>Daily goal: 4 hours</p>
                </div>
            </div>
        </div>
    </body>
    <script>
        let sort = "none";
        const ogActivities = <?php echo json_encode($activities); ?>;
        let activities = [...ogActivities];
        displayActivities();

        function displayActivities() {
            const activityList = document.getElementById("activitiesList");
            activityList.innerHTML = "";
            activities.forEach(activity => {
                const activityElement = document.createElement("a");
                activityElement.className = "activity";
                activityElement.href = `/activity/${activity.id}`;
                activityElement.innerHTML = `
                    <p>${activity.activityName} (${activity.activityDate})</p>
                    <p class='rightContent'>${activity.activityLength} hours</p>
                `;
                activityList.appendChild(activityElement);
            });
            if (activities.length === 0) {
                activityList.innerHTML = "<p>No activities found.</p>";
            }
        }

        function sortActivities() {
            sort = document.getElementById("sortActivities").value;
            activities = [...ogActivities];
            if (sort === "a-z") {
                activities.sort((a, b) => a.activityName.localeCompare(b.activityName));
            } else if (sort === "z-a") {
                activities.sort((a, b) => b.activityName.localeCompare(a.activityName));
            } else if (sort === "newest") {
                activities.sort((a, b) => new Date(b.activityDate) - new Date(a.activityDate));
            } else if (sort === "oldest") {
                activities.sort((a, b) => new Date(a.activityDate) - new Date(b.activityDate));
            }
            displayActivities();
        }

        function setDailyPercent() {
            const today = new Date();
            const todayString = `${today.getFullYear()}-${today.getMonth() + 1}-${today.getDate()}`;
            const todaysActivities = activities.filter(activity => {
                return activity.activityDate === todayString;
            })
            const totalHours = todaysActivities.reduce((sum, activity) => sum + activity.activityLength, 0);
            const dailyPercent = Math.floor((totalHours / 4) * 100); // Google only said 20 minutes per day, but that's for the weak
            document.getElementById("dailyPercentProgress").style.width = `${dailyPercent}%`;
            document.querySelector(".dailyPercentText").textContent = `${dailyPercent}%`;
        }
        setDailyPercent();
        document.getElementById("sortActivities").addEventListener("change", sortActivities);

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