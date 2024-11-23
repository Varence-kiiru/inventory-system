$(document).ready(function() {
    // Add User
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serialize() + '&action=create';
        
        $.ajax({
            url: 'process_user.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    $('#addUserModal').modal('hide');
                    location.reload();
                }
            }
        });
    });

    // Edit User
    $('.edit-user').on('click', function() {
        let userId = $(this).data('id');
        
        $.get('get_user.php', {id: userId}, function(user) {
            $('#edit_user_id').val(user.user_id);
            $('#edit_full_name').val(user.full_name);
            $('#edit_email').val(user.email);
            $('#edit_role').val(user.role);
            $('#editUserModal').modal('show');
        });
    });

    // Update User
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serialize() + '&action=update';
        
        $.ajax({
            url: 'process_user.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    $('#editUserModal').modal('hide');
                    location.reload();
                }
            }
        });
    });

    // Delete User
    $('.delete-user').on('click', function() {
        if(confirm('Are you sure you want to delete this user?')) {
            let userId = $(this).data('id');
            
            $.ajax({
                url: 'process_user.php',
                type: 'POST',
                data: {
                    action: 'delete',
                    user_id: userId
                },
                success: function(response) {
                    if(response.success) {
                        location.reload();
                    }
                }
            });
        }
    });

    // Status Toggle Handler
    $('.toggle-status').on('change', function() {
        const userId = $(this).data('id');
        const status = this.checked ? 'active' : 'inactive';
        
        $.ajax({
            url: 'process_user.php',
            type: 'POST',
            data: {
                action: 'update_status',
                user_id: userId,
                status: status
            },
            success: function(response) {
                if(response.success) {
                    location.reload();
                } else {
                    $(this).prop('checked', !this.checked);
                }
            }.bind(this)
        });
    });
});
