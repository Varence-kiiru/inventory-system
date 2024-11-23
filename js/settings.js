document.getElementById('startBackup').addEventListener('click', function() {
    fetch('backup_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=backup_db'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'backups/' + data.filename;
            showAlert('Backup created successfully!', 'success');
        }
    });
});

document.getElementById('exportSettings').addEventListener('click', function() {
    fetch('backup_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=export_settings'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'backups/' + data.filename;
            showAlert('Settings exported successfully!', 'success');
        }
    });
});

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.main-content').prepend(alertDiv);
}
