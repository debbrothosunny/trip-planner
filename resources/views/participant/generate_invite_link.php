<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite Link Generated</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Invite Link Generated!</h2>
        <p>Share this link with your friends and family:</p>
        <div class="alert alert-info">
            <a href="<?= htmlspecialchars($invitationLink) ?>" target="_blank"><?= htmlspecialchars($invitationLink) ?></a>
        </div>
        <p>Once they click the link, they can join the trip.</p>
        <p><a href="/participant/trip-details/<?= htmlspecialchars($tripId ?? '') ?>">Back to Trip Details</a></p>
    </div>
</body>
</html>