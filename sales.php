<?php
session_start();
require_once 'config/config.php';
require_once 'includes/sales_manager.php';

$salesManager = new SalesManager($conn);
$sales = $salesManager->getAllSales();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Management - The Olivian Group Limited</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Sales Management</h2>
                <a href="new_sale.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Sale
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <table id="salesTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total Amount</th>
                                <th>VAT</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td>INV-<?php echo str_pad($sale['sale_id'], 6, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($sale['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                                <td><?php echo $sale['item_count']; ?></td>
                                <td>KSH <?php echo number_format($sale['total_amount'], 2); ?></td>
                                <td>KSH <?php echo number_format($sale['total_vat'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $sale['status'] === 'pending' ? 'warning' : 'success'; ?>">
                                        <?php echo ucfirst($sale['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="sale_invoice.php?id=<?php echo $sale['sale_id']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="print_invoice.php?id=<?php echo $sale['sale_id']; ?>" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="js/sales.js"></script>
</body>
</html>
