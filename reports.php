<?php
session_start();
require_once 'config/config.php';
require_once 'includes/reports_manager.php';

$reportsManager = new ReportsManager($conn);

// Get date range from request or default to current month
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Get reports data
$salesReport = $reportsManager->getSalesReport($startDate, $endDate);
$productReport = $reportsManager->getProductReport($startDate, $endDate);
$customerReport = $reportsManager->getCustomerReport($startDate, $endDate);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - The Olivian Group Limited</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="main-content">
        <div class="container-fluid py-4">
            <!-- Date Range Filter -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="reportFilterForm" class="row g-3 align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" value="<?php echo $startDate; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" name="end_date" value="<?php echo $endDate; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label"> </label>
                                    <button type="submit" class="btn btn-primary d-block">Generate Reports</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Summary -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Sales Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="stats-box text-center">
                                        <h3>KSH <?php echo number_format($salesReport['total_sales'], 2); ?></h3>
                                        <p>Total Sales</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-box text-center">
                                        <h3><?php echo $salesReport['total_orders']; ?></h3>
                                        <p>Total Orders</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-box text-center">
                                        <h3>KSH <?php echo number_format($salesReport['average_order'], 2); ?></h3>
                                        <p>Average Order Value</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-box text-center">
                                        <h3>KSH <?php echo number_format($salesReport['total_vat'], 2); ?></h3>
                                        <p>Total VAT</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Performance -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Product Performance</h5>
                        </div>
                        <div class="card-body">
                            <table id="productReport" class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Units Sold</th>
                                        <th>Revenue</th>
                                        <th>Current Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productReport as $product): ?>
                                        <tr>
                                            <td><?php echo $product['product_name']; ?></td>
                                            <td><?php echo $product['units_sold']; ?></td>
                                            <td>KSH <?php echo number_format($product['revenue'], 2); ?></td>
                                            <td><?php echo $product['current_stock']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Analysis -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Customer Analysis</h5>
                        </div>
                        <div class="card-body">
                            <table id="customerReport" class="table">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Orders</th>
                                        <th>Total Spent</th>
                                        <th>Average Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customerReport as $customer): ?>
                                        <tr>
                                            <td><?php echo $customer['customer_name']; ?></td>
                                            <td><?php echo $customer['total_orders']; ?></td>
                                            <td>KSH <?php echo number_format($customer['total_spent'], 2); ?></td>
                                            <td>KSH <?php echo number_format($customer['average_order'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="js/reports.js"></script>
</body>
</html>
