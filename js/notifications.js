function checkNotifications() {
    $.get('ajax/check_notifications.php', function(data) {
        if (data.count > 0) {
            $('#notification-badge').text(data.count).show();
            // Optional: Show toast notification
            toastr.warning('You have new notifications');
        }
    });
}

$(document).ready(function() {
    // Check every 30 seconds
    setInterval(checkNotifications, 30000);
});
