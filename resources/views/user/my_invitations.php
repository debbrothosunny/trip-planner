<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>My Invitations</h2>

<table>
    <thead>
        <tr>
            <th>Trip ID</th>
            <th>Inviter</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($invitations as $invitation) : ?>
            <tr>
                <td><?php echo $invitation['trip_id']; ?></td>
                <td><?php echo $invitation['inviter_id']; ?></td>
                <td><?php echo ucfirst($invitation['status']); ?></td>
                <td>
                    <?php if ($invitation['status'] === 'pending') : ?>
                        <form method="POST" action="/user/trip-invitation/accept/<?php echo $invitation['id']; ?>">
                            <button type="submit">Accept</button>
                        </form>
                        <form method="POST" action="/user/trip-invitation/reject/<?php echo $invitation['id']; ?>">
                            <button type="submit">Reject</button>
                        </form>
                    <?php else : ?>
                        <?php echo ucfirst($invitation['status']); ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
