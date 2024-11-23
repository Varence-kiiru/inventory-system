document.addEventListener('DOMContentLoaded', function() {
    const VAT_RATE = 0.16;

    // Sales calculations
    function updateTotals() {
        let subtotal = 0;
        let totalVat = 0;

        document.querySelectorAll('.product-row').forEach(row => {
            const quantity = parseInt(row.querySelector('.quantity').value) || 0;
            const select = row.querySelector('.product-select');
            const option = select.options[select.selectedIndex];
            
            if (option && option.value) {
                const price = parseFloat(option.dataset.price);
                const isVatExempt = option.dataset.vatExempt === '1';
                const lineTotal = quantity * price;
                const vatAmount = isVatExempt ? 0 : (lineTotal * VAT_RATE);
                
                row.querySelector('.unit-price').textContent = 'KSH ' + price.toFixed(2);
                row.querySelector('.vat-amount').textContent = 'KSH ' + vatAmount.toFixed(2);
                row.querySelector('.line-total').textContent = 'KSH ' + (lineTotal + vatAmount).toFixed(2);
                
                subtotal += lineTotal;
                totalVat += vatAmount;
            }
        });

        document.getElementById('subtotal').textContent = 'KSH ' + subtotal.toFixed(2);
        document.getElementById('total-vat').textContent = 'KSH ' + totalVat.toFixed(2);
        document.getElementById('grand-total').textContent = 'KSH ' + (subtotal + totalVat).toFixed(2);
    }

    // Event listeners for sales calculations
    document.querySelectorAll('.product-select, .quantity').forEach(element => {
        element.addEventListener('change', updateTotals);
    });

    // Initialize sales calculations
    updateTotals();

    $(document).ready(function() {
        $('#salesTable').DataTable();

        $('.toggle-status').change(function() {
            const saleId = $(this).data('sale-id');
            const isChecked = $(this).prop('checked');
            const row = $(this).closest('tr');
            console.log('Toggle clicked:', saleId, isChecked);
        
            $.ajax({
                url: 'ajax/update_sale_status.php',
                type: 'POST',
                data: {
                    sale_id: saleId,
                    status: isChecked ? 'paid' : 'pending'
                },
                success: function(response) {
                    console.log('Server response:', response);
                    if (response.success) {
                        row.find('.status-cell span').removeClass('bg-warning').addClass('bg-success').text('Paid');
                        row.find('.action-buttons').html(`
                            <a href="sale_invoice.php?id=${saleId}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="print_invoice.php?id=${saleId}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-print"></i>
                            </a>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Ajax error:', error);
                }
            });
        });
    });});
