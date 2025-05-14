<?php
$header_title = "Dashboard";
$content = __DIR__ . '/dashboard.php';
include __DIR__ . '/../backend/layouts/app.php';
?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
.content {
    margin-left: 270px;
    padding: 30px;
    width: calc(100% - 270px);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    /* Push to bottom */
}

.card {
    background-color: #1e1e1e;
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.6);
    padding: 20px;
    transition: transform 0.2s ease;
    color: #ffffff;
}

.card:hover {
    transform: translateY(-4px);
}

.card-icon {
    font-size: 2rem;
    margin-bottom: 10px;
    color: #58a6ff;
}

.card-title {
    font-size: 1.0rem;
    font-weight: 600;
    color: #ccc;
}

.card-text {
    font-size: 1.2rem;
    font-weight: bold;
    color: #ffffff;
}

.greeting-box {
    background: linear-gradient(135deg, #0f2027, rgb(12, 28, 34), #2c5364);
    border-left: 5px solid #17a2b8;
}

.greeting-box h1 {
    font-size: 2.8rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.greeting-box p {
    font-size: 1.25rem;
    opacity: 0.9;
}

.time-info-container {
    display: flex;
    justify-content: center;
    width: 100%;
}

.time-info {
    text-align: center;
    font-size: 1.1rem;
    font-weight: bold;
    color: #cccccc;
}

.time-info i {
    margin-right: 10px;
}

.sun-animation,
.moon-animation {
    animation: spin 5s linear infinite;
}

@keyframes spin {
    100% {
        transform: rotate(360deg);
    }
}
</style>

  
<div class="content">
    <div class="time-info mb-2">
        <i id="timeIcon"></i>
        <span id="greeting"></span>, <span id="currentDate"></span>, <span id="currentTime"></span>
    </div>
    <div class="greeting-box p-4 mb-4 bg-dark text-white rounded shadow-lg">
        <div class="d-flex align-items-center">
            <?php if (!empty($profilePhoto)): ?>
            <img src="/<?= htmlspecialchars($profilePhoto) ?>" alt="Profile Picture" class="rounded-circle me-3"
                width="100" height="100">
            <?php else: ?>
                <img src="/image/default_profile.jpg" alt="Default Image"
                    style="max-width: 100px; max-height: 100px; border-radius: 4px; object-fit: cover; opacity: 0.7;">
            <?php endif; ?>
            <div>
                <h2 class="mb-1">Hello, <?= htmlspecialchars($userName ?? 'Guest'); ?> 👋</h2>
                <p class="mb-0">"Another day, another adventure—let’s get started!"</p>
                <?php if ($activeSince): ?>
                    <p class="mb-0"><small>Active since: <?= htmlspecialchars(date('j F, Y', strtotime($activeSince))) ?></small></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card text-white">
                <div class="card-body">
                    <div class="card-icon"><i class="bi bi-globe"></i></div>
                    <div class="card-title">Total Trips</div>
                    <div class="card-text"><?= count($trips) ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white mb-3">
                <div class="card-body">
                    <div class="card-icon"><i class="bi bi-play-circle-fill"></i></div>
                    <div class="card-title">Ongoing Trips:</div>
                    <?php if (!empty($ongoingTrips)): ?>
                    <?php foreach ($ongoingTrips as $ongoingTrip): ?>
                    <div class="mb-3">
                        <p><?= htmlspecialchars($ongoingTrip['trip']['name']) ?></p>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated ongoing-progress-bar-<?= $ongoingTrip['trip']['id'] ?>"
                                role="progressbar" style="width: <?= $ongoingTrip['progress'] ?>%;"
                                aria-valuenow="<?= $ongoingTrip['progress'] ?>" aria-valuemin="0" aria-valuemax="100">
                                <?= round($ongoingTrip['progress']) ?>%
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="card-text">0</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white">
                <div class="card-body">
                    <div class="card-icon"><i class="bi bi-check-circle-fill"></i></div>
                    <div class="card-title">Completed Trips</div>
                    <div class="card-text">
                        <?= count(array_filter($trips, fn($t) => strtotime($t['end_date']) < time())) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card text-white">
                <div class="card-body">
                    <div class="card-icon"><i class="bi bi-person-hearts"></i></div>
                    <div class="card-title">Total Accepted Participants (My Trips)</div>
                    <div class="card-text"><?= $totalAcceptedParticipants ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white">
                <div class="card-body">
                    <div class="card-icon"><i class="bi bi-hourglass-split"></i></div>
                    <div class="card-title">Upcoming Trips</div>
                    <div class="card-text">
                        <?= count($upcomingTrips) ?>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-md-4">
            <div class="card text-white">
                <div class="card-body">
                    <div class="card-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="card-title">Followers</div>
                    <div class="card-text"><?= $followerCount ?? 0 ?></div>
                </div>
            </div>
        </div>

        <?php if (!empty($pollsForMyTrips)): ?>
        <div class="mt-2 col-12">
            <hr class="bg-secondary mb-3">
            <h6 class="mb-3 text-white">Polls for Your Trips</h6>
            <div class="row g-4"> <?php foreach ($pollsForMyTrips as $poll): ?>
                <div class="col-md-4">
                    <div class="card bg-dark text-white shadow">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($poll['question']) ?></h5>
                            <p class="card-text">
                                <strong>Trip Name:</strong> <?= htmlspecialchars($poll['trip_name']) ?><br>
                                <strong>Day:</strong>
                                <?php if (isset($poll['day_title'])): ?>
                                <?= htmlspecialchars($poll['day_title']) ?>
                                <?php else: ?>
                                Not Specified
                                <?php endif; ?>
                                <br>
                                <strong>Created By:</strong>
                                <?= htmlspecialchars($poll['creator_name'] ?? 'Unknown') ?><br>
                                <strong>Created On:</strong> <?= htmlspecialchars($poll['created_at']) ?><br>
                                <strong>Likes:</strong> <?= htmlspecialchars($poll['likes']) ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="mt-5 col-12">
            <hr class="bg-secondary mb-3">
            <p class="text-muted">No polls have been created for your trips yet.</p>
        </div>
        <?php endif; ?>
    </div>
</div>




</div>



<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['success'])) {
    echo "<script>
        Swal.fire({
            title: 'Success!',
            text: '{$_SESSION['success']}',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => window.location.href = '/user/dashboard');
    </script>";
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo "<script>
        Swal.fire({
            title: 'Error!',
            text: '{$_SESSION['error']}',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then(() => window.location.href = '/user/dashboard');
    </script>";
    unset($_SESSION['error']);
}
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    function updateFooter() {
        const now = new Date();
        let hours = now.getHours();
        const minutes = now.getMinutes().toString().padStart(2, "0");
        const seconds = now.getSeconds().toString().padStart(2, "0");
        const ampm = hours >= 12 ? "PM" : "AM";
        hours = hours % 12 || 12;

        const greeting = document.getElementById("greeting");
        const timeIcon = document.getElementById("timeIcon");

        if (hours >= 5 && hours < 12 && ampm === "AM") {
            greeting.innerText = "Good Morning!";
            timeIcon.className = "fas fa-sun sun-animation";
        } else if (ampm === "PM" && hours < 5) {
            greeting.innerText = "Good Afternoon!";
            timeIcon.className = "fas fa-sun sun-animation";
        } else if (ampm === "PM" && hours >= 5) {
            greeting.innerText = "Good Evening!";
            timeIcon.className = "fas fa-moon moon-animation";
        } else {
            greeting.innerText = "Good Night!";
            timeIcon.className = "fas fa-moon moon-animation";
        }

        const date = now.toLocaleDateString("en-US", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric",
        });
        document.getElementById("currentDate").innerText = date;
        document.getElementById("currentTime").innerText = `${hours}:${minutes}:${seconds} ${ampm}`;
    }

    updateFooter();
    setInterval(updateFooter, 1000);
});
</script>