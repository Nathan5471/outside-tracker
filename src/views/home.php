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
                <a href="/about">About</a>
                <a href="/create">Add Activity</a>
                <button id="logoutButton">Logout</button>
            </div>
        </nav>
        <div class="container">
            <div class="leftContainer">
                <div class="activitiesList">
                    <div class="activitiesHeader">
                        <h2>Activities</h2>
                        <select id="sortActivities" class="leftAuto">
                            <option value="none">Sort: None</option>
                            <option value="a-z">Sort: A-Z</option>
                            <option value="z-a">Sort: Z-A</option>
                            <option value="newest">Sort: Newest</option>
                            <option value="oldest">Sort: Oldest</option>
                        </select>
                        <a href="/api/activities/export" class="exportButton">Export</a>
                    </div>
                    <input type="text" id="searchActivities" placeholder="Search activities...">
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
                <div class="heatmap">
                    <h2>Past 91 Days Heatmap</h2>
                    <div class="heatmapGrid"></div>
                </div>
            </div>
        </div>
    </body>
    <script>
        let sort = "none";
        const ogActivities = <?php echo json_encode($activities); ?>;
        let searchedActivities = [...ogActivities];
        displayActivities();

        function displayActivities() {
            const activityList = document.getElementById("activitiesList");
            activityList.innerHTML = "";
            searchedActivities.forEach(activity => {
                const activityElement = document.createElement("a");
                activityElement.className = "activity";
                activityElement.href = `/activity/${activity.id}`;
                activityElement.innerHTML = `
                    <p>${activity.activityName} (${activity.activityDate})</p>
                    <p class='rightContent'>${activity.activityLength} hours</p>
                `;
                activityList.appendChild(activityElement);
            });
            if (searchedActivities.length === 0) {
                activityList.innerHTML = "<p>No activities found.</p>";
            }
        }

        function sortActivities() {
            sort = document.getElementById("sortActivities").value;
            if (sort === "a-z") {
                searchedActivities.sort((a, b) => a.activityName.localeCompare(b.activityName));
            } else if (sort === "z-a") {
                searchedActivities.sort((a, b) => b.activityName.localeCompare(a.activityName));
            } else if (sort === "newest") {
                searchedActivities.sort((a, b) => new Date(b.activityDate) - new Date(a.activityDate));
            } else if (sort === "oldest") {
                searchedActivities.sort((a, b) => new Date(a.activityDate) - new Date(b.activityDate));
            }
            displayActivities();
        }

        function searchActivities() {
            const searchTerm = document.getElementById("searchActivities").value.toLowerCase();
            searchedActivities = ogActivities.filter(activity => activity.activityName.toLowerCase().includes(searchTerm));
            sortActivities();
        }

        function setDailyPercent() {
            const today = new Date();
            const todayString = `${today.getFullYear()}-${today.getMonth() + 1}-${today.getDate()}`;
            const todaysActivities = ogActivities.filter(activity => {
                return activity.activityDate === todayString;
            })
            const totalHours = todaysActivities.reduce((sum, activity) => sum + activity.activityLength, 0);
            const dailyPercent = Math.floor((totalHours / 4) * 100); // Google only said 20 minutes per day, but that's for the weak
            document.getElementById("dailyPercentProgress").style.width = `${dailyPercent}%`;
            document.querySelector(".dailyPercentText").textContent = `${dailyPercent}%`;
        }
        setDailyPercent();

        function generateHeatmap() {
            const heatmapGrid = document.querySelector(".heatmapGrid");
            heatmapGrid.innerHTML = "";
            const today = new Date();
            for (let i = 90; i >= 0; i--) {
                const date = new Date(today);
                date.setDate(today.getDate() - i);
                const dateString = `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`;
                const cell = document.createElement("div");
                cell.classList.add("heatmapCell");
                heatmapGrid.appendChild(cell);
                const datesActivities = ogActivities.filter(activity => activity.activityDate === dateString);
                const totalHours = datesActivities.reduce((sum, activity) => sum + activity.activityLength, 0);
                if (totalHours === 0) {
                    cell.classList.add("level0");
                } else if (totalHours < 1) {
                    cell.classList.add("level1");
                } else if (totalHours < 2) {
                    cell.classList.add("level2");
                } else if (totalHours < 3) {
                    cell.classList.add("level3");
                } else {
                    cell.classList.add("level4");
                }
            }
        }
        generateHeatmap();

        document.getElementById("sortActivities").addEventListener("change", sortActivities);
        document.getElementById("searchActivities").addEventListener("input", searchActivities);

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