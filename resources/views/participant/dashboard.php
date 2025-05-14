<?php
// Calculate time-based greetings
$currentTime = date('H');
if ($currentTime < 12) {
    $greeting = "Good Morning";
} elseif ($currentTime < 18) {
    $greeting = "Good Afternoon";
} elseif ($currentTime < 22) {
    $greeting = "Good Evening";
} else {
    $greeting = "Good Night";
}

$header_title = "Trip";
$content = __DIR__ . '/dashboard.php'; // Load actual content
include __DIR__ . '/../backend/layouts/app.php';
?>



<div class="container py-5">
    <div class="greeting-box p-4 mb-4 bg-dark text-white rounded shadow-lg">
    <div class="d-flex align-items-center">
        <?php if (!empty($participant['profile_photo'])): ?>
        <img src="/<?= htmlspecialchars($participant['profile_photo']) ?>" alt="Profile Picture"
            class="rounded-circle me-3" width="50" height="50">
        <?php else: ?>
        <i class="fas fa-user-circle fa-3x me-3 text-info"></i>
        <?php endif; ?>

        <div>
            <h2 class="mb-1">Hello, <?= htmlspecialchars($participant['name'] ?? 'Guest'); ?> 👋</h2>
            <p class="mb-0">"Another day, another adventure—let’s get started!"</p>
            <?php if ($activeSince): ?>
                <p class="mb-0"><small>Active since: <?= htmlspecialchars(date('j F, Y', strtotime($activeSince))) ?></small></p>
            <?php endif; ?>
        </div>
    </div>
</div>

 



<?php if (!empty($recommendations)): ?>
    <?php foreach ($recommendations as $recommendation): ?>
        <?php
            $mutedIds = $_SESSION['muted_recommendation_ids'] ?? [];
            if (in_array($recommendation['id'], $mutedIds)) {
                continue; // Skip muted trip
            }
        ?>

        <script>
            $(document).ready(function(){
                $("#recommendationModal_<?= $recommendation['id'] ?>").modal("show");
            });
        </script>

        <div class="modal fade" id="recommendationModal_<?= $recommendation['id'] ?>" tabindex="-1" aria-labelledby="recommendationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Recommended Trip!</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        A new trip matching your recent preferences...
                        <p>Life Style: <?= htmlspecialchars($recommendation['trip_style'] ?? '') ?></p>
                        <p>Destination: <?= htmlspecialchars($recommendation['destination'] ?? '') ?></p>
                        <p>Trip Name: <?= htmlspecialchars($recommendation['name'] ?? '') ?></p>
                        <a href="/participant/trips" class="btn btn-primary mb-2">View Trip</a><br>
                        <a href="?mute_recommendation=<?= $recommendation['id'] ?>" class="btn btn-sm btn-outline-danger">Mute Recommendation</a>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    <?php endforeach; ?>
<?php endif; ?>










    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card text-white h-100 shadow-lg">
                <div class="card-body">
                    <h5 class="card-titles text-primary text-center"><i class="fas fa-route me-2"></i> Ongoing Trips
                    </h5>
                    <p class="card-text display-5 text-center"><?= count($ongoingTrips) ?></p>
                    <?php if (count($ongoingTrips) > 0): ?>
                    <div class="list-group">
                        <?php foreach ($ongoingTrips as $trip): ?>
                        <div class="list-group-item bg-transparent text-white border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><?= htmlspecialchars($trip['trip_name']) ?></span>
                                <?php
                                    if (!empty($trip['start_date']) && !empty($trip['end_date'])) {
                                        $startDate = strtotime($trip['start_date']);
                                        $endDate = strtotime($trip['end_date']);
                                        $now = time();
                                        $totalDuration = $endDate - $startDate;
                                        $elapsed = $now - $startDate;
                                        $progress = ($totalDuration > 0) ? ($elapsed / $totalDuration) * 100 : 0;
                                        $progress = min(100, max(0, $progress));
                                    } else {
                                        $progress = 0;
                                    }
                                ?>
                                <div class="progress flex-grow-1 ms-3" style="height: 10px;">
                                    <div class="progress-bar progress-bar-animated progress-bar-striped"
                                        role="progressbar" style="width: <?= $progress ?>%;"
                                        aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="ms-2"><?= round($progress) ?>%</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <a href="/participant/trips" class="btn btn-outline-light btn-sm mt-3 w-100">
                        <i class="fas fa-eye me-1"></i> View Trips
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-white h-100 shadow-lg">
                <div class="card-body">
                    <h5 class="card-titles text-success text-center"><i class="fas fa-check-circle me-2"></i>
                        Accepted Trips</h5>
                    <p class="card-text display-5 text-center">
                        <?= count(array_filter($participantTrips, fn($t) => ($t['status'] ?? '') === 'accepted')) ?>
                    </p>
                    <a href="/participant/trips" class="btn btn-outline-light btn-sm mt-3 w-100">
                        <i class="fas fa-eye me-1"></i> View Accepted
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="time-info text-white mt-4 text-center">
        <i id="timeIcon"></i>
        <span id="greeting"></span>, <span id="currentDate"></span>, <span id="currentTime"></span>
    </div>
</div>
  

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

    const ongoingTrips = <?php echo json_encode($ongoingTrips); ?>;
    ongoingTrips.forEach(trip => {
        const tripDiv = document.querySelector(`[data-trip-id="${trip.id}"]`);
        if (tripDiv) {
            const progressBar = tripDiv.querySelector('.progress-bar');
            const startDate = new Date(trip.start_date).getTime();
            const endDate = new Date(trip.end_date).getTime();
            const now = Date.now();
            const totalDuration = endDate - startDate;
            const elapsed = now - startDate;
            const progress = (totalDuration > 0) ? (elapsed / totalDuration) * 100 : 0;
            const progressPercent = Math.min(100, Math.max(0, progress));

            progressBar.style.width = `${progressPercent}%`;
            progressBar.setAttribute('aria-valuenow', progressPercent);
            progressBar.textContent = `${Math.round(progressPercent)}%`;
        }
    });
});

$(document).ready(function(){
            <?php if (!empty($recommendations)): ?>
                <?php foreach ($recommendations as $recommendation): ?>
                    $("#recommendationModal_<?= $recommendation['id'] ?>").modal("show");
                <?php endforeach; ?>
            <?php endif; ?>
        });
</script>


<style>
body {
    background-color: #1e1e1e;
    /* Light black */
    font-family: 'Arial', sans-serif;
    color: #e0e0e0;
    transition: background-color 0.3s, color 0.3s;
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


.btn-light {
    background-color: #ffffff22;
    color: #fff;
    border: 1px solid #ffffff33;
    border-radius: 1.25rem;
    transition: all 0.3s;
}

.btn-light:hover {
    background-color: #ffffff40;
    color: white;
}

.btn-outline-primary {
    color: #0d6efd;
    border-color: #0d6efd;
    border-radius: 1.25rem;
    transition: background-color 0.3s, color 0.3s;
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    color: white;
}

.card-titles {
    font-size: 1rem;
    font-weight: bold;
}

.card-text {
    font-size: 2rem;
}

.time-info {
    text-align: center;
    margin-top: 3rem;
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

.btn-container {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}
</style>