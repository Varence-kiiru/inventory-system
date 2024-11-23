<?php
session_start();
require_once 'config/config.php';
require_once 'includes/inventory_manager.php';

$inventoryManager = new InventoryManager($conn);

if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $product = $inventoryManager->getProduct($productId);
    $transactions = $inventoryManager->getProductHistory($productId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product History - The Olivian Group Limited</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="main-content">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6>Product History - <?php echo $product['product_name']; ?></h6>
                                <a href="inventory.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Inventory
                                </a>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table id="historyTable" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Type</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Quantity</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transactions as $transaction): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?php echo date('d M Y', strtotime($transaction['transaction_date'])); ?></h6>
                                                            <p class="text-xs text-secondary mb-0"><?php echo date('h:i A', strtotime($transaction['transaction_date'])); ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-sm bg-gradient-<?php echo $transaction['transaction_type'] == 'add' ? 'success' : 'primary'; ?>">
                                                        <?php echo ucfirst($transaction['transaction_type']); ?>
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="text-dark fw-bold">
                                                        <?php echo $transaction['quantity']; ?> units
                                                    </span>
                                                </td>
                                                <td>
                                                    <p class="text-xs text-secondary mb-0"><?php echo $transaction['notes']; ?></p>
                                                </td>
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
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#historyTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25
            });
        });
    </script>
</body>
</html>
