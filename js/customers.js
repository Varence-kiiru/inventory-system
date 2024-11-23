$(document).ready(function() {
    $('#addCustomerForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'ajax/add_customer.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#addCustomerModal').modal('hide');
                    location.reload();
                }
            }
        });
    });
});

// Edit Customer Handler
$('.edit-customer').on('click', function(e) {
    e.preventDefault();
    const customerId = $(this).data('id');
    
    $.ajax({
        url: 'ajax/get_customer.php',
        type: 'GET',
        data: { customer_id: customerId },
        dataType: 'json',
        success: function(customer) {
            $('#editCustomerForm input[name="customer_id"]').val(customer.customer_id);
            $('#editCustomerForm input[name="name"]').val(customer.name);
            $('#editCustomerForm input[name="company"]').val(customer.company);
            $('#editCustomerForm input[name="email"]').val(customer.email);
            $('#editCustomerForm input[name="phone"]').val(customer.phone);
            $('#editCustomerForm textarea[name="address"]').val(customer.address);
            $('#editCustomerForm select[name="status"]').val(customer.status);
            
            $('#editCustomerModal').modal('show');
        }
    });
});

$('#editCustomerForm').submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'ajax/update_customer.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#editCustomerModal').modal('hide');
                location.reload();
            }
        }
    });
});

// View History Handler
$('.view-history').on('click', function(e) {
    e.preventDefault();
    const customerId = $(this).data('id');
    window.location.href = 'customer_history.php?id=' + customerId;
});
