$(document).ready(function() {
    // Initialize DataTable
    $('#inventoryTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 25,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search products..."
        }
    });

    // Edit Product Form Handler
    $('#editProductForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: 'ajax/update_product.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editProductModal').modal('hide');
                    location.reload();
                }
            }
        });
    });

    // Edit Product Handler
    $(document).ready(function() {
        $('.edit-product').click(function(e) {
            e.preventDefault();
            const productId = $(this).data('id');
            
            // Fetch product data
            $.ajax({
                url: 'ajax/get_product.php',
                type: 'GET',
                data: { product_id: productId },
                dataType: 'json',
                success: function(product) {
                    // Fill form with product data
                    $('#editProductForm input[name="product_id"]').val(product.product_id);
                    $('#editProductForm input[name="product_code"]').val(product.product_code);
                    $('#editProductForm input[name="product_name"]').val(product.product_name);
                    $('#editProductForm textarea[name="description"]').val(product.description);
                    $('#editProductForm input[name="unit_price"]').val(product.unit_price);
                    $('#editProductForm input[name="min_stock_level"]').val(product.min_stock_level);
                    $('#editProductForm input[name="is_vat_exempt"]').prop('checked', product.is_vat_exempt == 1);
                    
                    $('#editProductModal').modal('show');
                }
            });
        });

        // Handle form submission
        $('#editProductForm').submit(function(e) {
            e.preventDefault();
            
            $.ajax({
                url: 'ajax/update_product.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#editProductModal').modal('hide');
                        location.reload();
                    }
                }
            });
        });
    });

    // Add Product Form Handler
    $('#addProductForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax/add_product.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#addProductModal').modal('hide');
                    location.reload();
                }
            }
        });
    });

    // Edit Product Form Handler
    $('#editProductForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax/update_product.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editProductModal').modal('hide');
                    location.reload();
                }
            }
        });
    });

    // Add Stock Button and Form Handlers
    $(document).ready(function() {
        $('body').on('click', '.add-stock', function(e) {
            e.preventDefault();
            const productId = $(this).data('id');
            const productName = $(this).closest('tr').find('h6').text();
        
            $('#addStockForm')[0].reset();
            $('#addStockForm input[name="product_id"]').val(productId);
            $('#addStockModal .modal-title').text('Add Stock - ' + productName);
            $('#addStockModal').modal('show');
        });

        $('#addStockForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
        
            $.ajax({
                url: 'ajax/add_stock.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#addStockModal').modal('hide');
                        location.reload();
                    }
                }
            });
        });
    });
    // Handle view history button click
    $('.view-history').on('click', function(e) {
        e.preventDefault();
        const productId = $(this).data('id');
        
        window.location.href = 'product_history.php?id=' + productId;
    });
});
