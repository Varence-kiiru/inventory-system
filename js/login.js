$(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'process_login.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    $('#loginAlert').html(response.message)
                        .removeClass('d-none alert-success')
                        .addClass('alert-danger')
                        .show();
                }
            }
        });
    });
});
