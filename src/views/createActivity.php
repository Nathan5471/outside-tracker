<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Outside Tracker | Create Activity</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bubblegum+Sans&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/createActivity.css">
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
        <div class="createActivityContainer">
            <form id="createActivityForm">
                <h1>Create Activity</h1>
                <label for="activityName">Activity Name:</label>
                <input type="text" id="activityName" name="activityName" required>
                <label for="activityDate">Activity Date:</label>
                <input type="date" id="activityDate" name="activityDate" required>
                <label for="activityLength">Activity Length (hours):</label>
                <input type="number" id="activityLength" name="activityLength" required>
                <p id="errorMessage"></p>
                <button type="submit">Create Activity</button>
            </form>
        </div>
    </body>
    <script>
        document.getElementById('createActivityForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const activityName = document.getElementById('activityName').value;
            const activityDate = document.getElementById('activityDate').value;
            const activityLength = document.getElementById('activityLength').value;

            const response = await fetch('/api/activities/create', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ activityName, activityDate, activityLength })
            });
            const result = await response.json();
            if (response.ok) {
                window.location.href = `/activity/${result.activityId}`;
            } else {
                document.getElementById('errorMessage').textContent = result.error || 'An unknown error occurred';
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