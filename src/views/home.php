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
            </div>
        </nav>
        <div class="container">
            <div class="leftContainer">
                <div class="activitiesList">
                    <h2>Activities</h2>
                    <div class="scrollableContainer">
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
                            foreach ($activities as $activity) {
                                echo "<a class=\"activity\" href=\"/activity/{$activity['id']}\">";
                                echo "<p>{$activity['activityName']} ({$activity['activityDate']})</p>";
                                echo "<p class='rightContent'>{$activity['activityLength']} hours</p>";
                                echo '</a>';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>