$(document).ready(function() {
    $('.select2').select2();
    $('#addItemBtn').click(addNewItem);
    $(document).on('change', '.product-select, .quantity', updateCalculations);
    $(document).on('click', '.remove-item', removeItem);

    function addNewItem() {
        $('#noItemsRow').hide();
        const template = document.getElementById('productRowTemplate');
        const clone = template.content.cloneNode(true);
        $('#saleItems tbody').append(clone);
        $('.product-select').last().select2();
    }

    function removeItem() {
        $(this).closest('tr').remove();
        if ($('.item-row').length === 0) {
            $('#noItemsRow').show();
        }
        updateCalculations();
    }

    function updateCalculations() {
        let totalItems = 0;
        let subtotalExempt = 0;
        let subtotalVatable = 0;
        let vatAmount = 0;
        const VAT_RATE = 0.16;

        $('.item-row').each(function() {
            const row = $(this);
            const select = row.find('.product-select');
            const option = select.find(':selected');
            const quantity = parseInt(row.find('.quantity').val()) || 0;

            if (option.val()) {
                const priceInclVAT = parseFloat(option.data('price'));
                const isVatExempt = option.data('vat-exempt') === 1;

                if (isVatExempt) {
                    const lineTotal = priceInclVAT * quantity;
                    subtotalExempt += lineTotal;
                    row.find('.unit-price').text('KSH ' + priceInclVAT.toFixed(2));
                    row.find('.subtotal').text('KSH ' + lineTotal.toFixed(2));
                } else {
                    const priceExclVAT = priceInclVAT / (1 + VAT_RATE);
                    const lineTotal = priceExclVAT * quantity;
                    const lineVAT = lineTotal * VAT_RATE;

                    subtotalVatable += lineTotal;
                    vatAmount += lineVAT;

                    row.find('.unit-price').text('KSH ' + priceExclVAT.toFixed(2));
                    row.find('.subtotal').text('KSH ' + lineTotal.toFixed(2));
                }

                totalItems += quantity;
            }
        });

        $('#totalItems').text(totalItems);
        $('#subtotalExempt').text('KSH ' + subtotalExempt.toFixed(2));
        $('#subtotalVatable').text('KSH ' + subtotalVatable.toFixed(2));
        $('#vatAmount').text('KSH ' + vatAmount.toFixed(2));
        $('#totalAmount').text('KSH ' + (subtotalExempt + subtotalVatable + vatAmount).toFixed(2));
    }

    // Form submission handler
    $('#saleForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate customer selection
        const customerId = $('#customer_id').val();
        if (!customerId) {
            alert('Please select a customer before completing the sale');
            return false;
        }
        
        let formData = new FormData();
        formData.append('customer_id', customerId);
        formData.append('payment_method', $('#payment_method').val());
        formData.append('payment_status', $('#status').val());        
        // Collect products and quantities
        $('.item-row').each(function() {
            const productId = $(this).find('.product-select').val();
            const quantity = $(this).find('.quantity').val();
            
            if (productId && quantity) {
                formData.append('products[]', productId);
                formData.append('quantities[]', quantity);
            }
        });
    
        // Submit the sale
        $.ajax({
            url: 'process_sale.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    window.location.href = 'sale_invoice.php?id=' + response.sale_id;
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error processing sale. Please try again.');
                console.error(error);
            }
        });
    });
});

$('.product-select').change(function() {
    const selectedOption = $(this).find('option:selected');
    const stock = parseInt(selectedOption.data('stock'));
    
    if (stock === 0) {
        alert('This product is out of stock');
        $(this).val('');
    }
    
    const quantityInput = $(this).closest('tr').find('.quantity');
    quantityInput.attr('max', stock);
});
