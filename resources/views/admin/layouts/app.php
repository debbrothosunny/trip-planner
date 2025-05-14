<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $_COOKIE['theme'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($header_title) ? $header_title : 'Dashboard'; ?> -</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <!-- Vue js -->
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const countriesTable = document.getElementById('countriesTable').getElementsByTagName('tbody')[0];
    const tableRows = countriesTable.getElementsByTagName('tr');

    searchInput.addEventListener('input', function() {
        const searchTerm = searchInput.value.toLowerCase();

        for (let i = 0; i < tableRows.length; i++) {
            const rowData = tableRows[i].textContent.toLowerCase();
            if (rowData.includes(searchTerm)) {
                tableRows[i].style.display = ''; // Show the row
            } else {
                tableRows[i].style.display = 'none'; // Hide the row
            }
        }
    });
});

// Function to toggle dark/light theme
function toggleTheme() {
            const body = document.body;
            const card = document.querySelector('.card-profile');
            const modalContent = document.querySelector('.modal-content');
            const modalHeader = document.querySelector('.modal-header');
            const modalFooter = document.querySelector('.modal-footer');
            const profileInfo = document.querySelectorAll('.profile-info');
            const themeIcon = document.getElementById('theme-toggle-icon'); // Assuming you have a theme toggle button

            body.classList.toggle('bg-dark');
            body.classList.toggle('text-light');
            card.classList.toggle('bg-dark');
            card.classList.toggle('text-light');
            modalContent.classList.toggle('bg-dark');
            modalContent.classList.toggle('text-light');
            modalHeader.classList.toggle('bg-dark');
            modalHeader.classList.toggle('text-light');
            modalFooter.classList.toggle('bg-dark');
            modalFooter.classList.toggle('text-light');

            profileInfo.forEach(span => {
                span.classList.toggle('text-light');
            });

            if (themeIcon) {
                themeIcon.classList.toggle('bi-sun-fill');
                themeIcon.classList.toggle('bi-moon-fill');
            }

            // Store the theme preference in local storage
            const currentTheme = body.classList.contains('bg-dark') ? 'dark' : 'light';
            localStorage.setItem('theme', currentTheme);
        }

        // Check for saved theme preference on page load
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                toggleTheme(); // Apply dark theme if saved
            }
        });
</script>