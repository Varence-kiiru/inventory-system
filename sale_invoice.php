<?php
session_start();
require_once 'config/config.php';
require_once 'includes/sales_manager.php';

function formatInvoiceNumber($saleId) {
    $prefix = 'TOG';
    $paddedId = str_pad($saleId, 4, '0', STR_PAD_LEFT);
    $datePart = date('mY');
    return "{$prefix}/{$paddedId}/{$datePart}";
}

$salesManager = new SalesManager($conn);
$sale_id = $_GET['id'];
$sale = $salesManager->getSaleById($sale_id);

$saleId = $sale_id;
$saleData = $salesManager->getSaleDetails($saleId);

if (!$saleData) {
    die('Sale not found');
}

// Get company settings
$stmt = $conn->prepare("SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ('company_name', 'vat_number', 'contact_email', 'company_logo')");
$stmt->execute();
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$invoiceNumber = formatInvoiceNumber($saleId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $invoiceNumber; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
        }
        .company-logo { max-height: 100px; max-width: 200px; }
        .footer-text {
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
            margin-top: 40px;
            font-size: 0.9em;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="text-end mb-4 no-print">
            <button onclick="window.print()" class="btn btn-primary me-2">Print Invoice</button>
            <a href="sales.php" class="btn btn-secondary">Back to Sales</a>
        </div>

        <div class="row">
            <div class="col-6">
                <?php if (!empty($settings['company_logo'])): ?>
                    <img src="<?php echo htmlspecialchars($settings['company_logo']); ?>" alt="Company Logo" class="company-logo mb-3">
                <?php endif; ?>
                <h2><?php echo htmlspecialchars($settings['company_name']); ?></h2>
                <p>VAT No: <?php echo htmlspecialchars($settings['vat_number']); ?></p>
                <p>Email: <?php echo htmlspecialchars($settings['contact_email']); ?></p>
            </div>
            <div class="col-6 text-end">
                <h1>INVOICE</h1>
                <p>Invoice #: <?php echo $invoiceNumber; ?></p>
                <p>Date: <?php echo date('d/m/Y', strtotime($saleData['created_at'])); ?></p>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-6">
                <h5>Bill To:</h5>
                <p><?php echo htmlspecialchars($saleData['customer_name']); ?><br>
                   Phone: <?php echo htmlspecialchars($saleData['phone']); ?></p>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">VAT</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($saleData['items'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td class="text-end"><?php echo $item['quantity']; ?></td>
                            <td class="text-end"><?php echo number_format($item['unit_price'], 2); ?></td>
                            <td class="text-end"><?php echo number_format($item['vat_amount'], 2); ?></td>
                            <td class="text-end"><?php echo number_format(($item['unit_price'] * $item['quantity']) + $item['vat_amount'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Subtotal:</th>
                            <td class="text-end"><?php echo number_format($saleData['total_amount'], 2); ?></td>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">VAT Total:</th>
                            <td class="text-end"><?php echo number_format(array_sum(array_column($saleData['items'], 'vat_amount')), 2); ?></td>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">Grand Total:</th>
                            <td class="text-end"><strong><?php echo number_format($saleData['total_amount'], 2); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
          <div class="row mt-4">
              <div class="col-12">
                  <div class="card">
                      <div class="card-header">
                          <h5>Payment Information</h5>
                      </div>
                      <div class="card-body">
                          <div class="row">
                              <div class="col-md-6">
                                  <p><strong>Method:</strong> 
                                      <?php 
                                      $paymentMethods = [
                                          'cash' => 'Cash',
                                          'mpesa' => 'M-PESA',
                                          'bank' => 'Bank Transfer'
                                      ];
                                      echo isset($paymentMethods[$saleData['payment_method']]) ? $paymentMethods[$saleData['payment_method']] : 'Cash';
                                      ?>
                                  </p>
                              </div>
                              <div class="col-md-6 d-flex justify-content-between align-items-center">
                                  <p class="mb-0">
                                      <strong>Status:</strong> 
                                      <span class="badge bg-<?php echo $saleData['status'] === 'pending' ? 'warning' : 'success'; ?>">
                                          <?php echo strtoupper($saleData['status']); ?>
                                      </span>
                                  </p>
                                  <?php if ($saleData['status'] === 'pending'): ?>
                                      <button class="btn btn-success mark-as-paid no-print" data-sale-id="<?php echo $saleId; ?>">
                                          Mark as Paid
                                      </button>
                                  <?php endif; ?>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        <div class="footer-text">
            <p><strong>Thank you for shopping with us!</strong></p>
            <p>Return Policy: No returns after 15 days; items must be undamaged, in original packaging, with receipt.</p>
            <p><small>This is a system generated receipt</small></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.mark-as-paid').click(function() {
        const saleId = $(this).data('sale-id');
        $.ajax({
            url: 'ajax/update_sale_status.php',
            type: 'POST',
            data: { sale_id: saleId, status: 'paid' },
            success: function(response) {
                if (response.success) {
                    window.location.reload();
                }
            }
        });
    });
});
</script>
</body>
</html>


