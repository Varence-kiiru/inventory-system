
<?php
session_start();
require_once 'config/config.php';
require_once 'includes/inventory_manager.php';

$inventoryManager = new InventoryManager($conn);
$products = $inventoryManager->getAllProducts();
$settings = $inventoryManager->getSettings();

// Get statistics
$totalProducts = count($products);
$lowStockProducts = array_filter($products, fn($p) => $p['stock_quantity'] <= $p['min_stock_level']);
$vatExemptCount = array_reduce($products, function($count, $product) {
    return $count + ($product['is_vat_exempt'] == 1 ? 1 : 0);
}, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - The Olivian Group Limited</title>
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
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <!-- Total Products Card -->
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Products</p>
                                        <h5 class="font-weight-bolder mb-0"><?php echo $totalProducts; ?></h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                        <i class="fas fa-box text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Items Card -->
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Low Stock Items</p>
                                        <h5 class="font-weight-bolder mb-0"><?php echo count($lowStockProducts); ?></h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                        <i class="fas fa-exclamation-triangle text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VAT Exempt Products Card -->
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">VAT Exempt Products</p>
                                        <h5 class="font-weight-bolder mb-0"><?php echo $vatExemptCount; ?></h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <i class="fas fa-percent text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h6>Products Inventory</h6>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                <i class="fas fa-plus me-2"></i>Add Product
                            </button>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table id="inventoryTable" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Product</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Price</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stock</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">VAT</th>
                                            <th class="text-secondary opacity-7">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?php echo $product['product_name']; ?></h6>
                                                            <p class="text-xs text-secondary mb-0"><?php echo $product['product_code']; ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        KSH <?php echo number_format($product['unit_price'], 2); ?>
                                                    </p>
                                                    <?php if (!$product['is_vat_exempt']): ?>
                                                        <p class="text-xs text-secondary mb-0">Incl. <?php echo $settings['vat_rate']; ?>% VAT</p>
                                                    <?php endif; ?>
                                                </td>
                                                <!-- Stock Column -->
                                                <td class="align-middle text-center">
                                                    <span class="text-dark fw-bold">
                                                        <?php echo $product['stock_quantity']; ?> units
                                                    </span>
                                                </td>

                                                <!-- Status Column -->
                                                <td class="align-middle text-center">
                                                    <span class="text-dark fw-bold">
                                                        <?php if ($product['stock_quantity'] > $product['min_stock_level']): ?>
                                                            In Stock
                                                        <?php elseif ($product['stock_quantity'] > 0): ?>
                                                            Low Stock
                                                        <?php else: ?>
                                                            Out of Stock
                                                        <?php endif; ?>
                                                    </span>
                                                </td>

                                                <!-- VAT Column -->
                                                <td class="align-middle text-center">
                                                    <span class="text-dark fw-bold">
                                                        <?php echo $product['is_vat_exempt'] ? 'Exempt' : 'Applicable'; ?>
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="btn-group">
                                                        <button class="btn btn-link text-secondary mb-0" data-bs-toggle="dropdown">
                                                            <i class="fa fa-ellipsis-v text-xs"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item edit-product" href="#" data-id="<?php echo $product['product_id']; ?>">
                                                                    <i class="fas fa-edit me-2"></i>Edit
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item add-stock" href="#" data-id="<?php echo $product['product_id']; ?>">
                                                                    <i class="fas fa-plus me-2"></i>Add Stock
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item view-history" href="#" data-id="<?php echo $product['product_id']; ?>">
                                                                    <i class="fas fa-history me-2"></i>View History
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
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

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addProductForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Product Code</label>
                                    <input type="text" class="form-control" name="product_code" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Product Name</label>
                                    <input type="text" class="form-control" name="product_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Unit Price</label>
                                    <input type="number" step="0.01" class="form-control" name="unit_price" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Initial Stock</label>
                                    <input type="number" class="form-control" name="stock_quantity" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Minimum Stock Level</label>
                                    <input type="number" class="form-control" name="min_stock_level" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="d-block"> </label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="is_vat_exempt" id="vatExempt">
                                        <label class="form-check-label" for="vatExempt">VAT Exempt Product</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editProductForm">
                    <input type="hidden" name="product_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Product Code</label>
                                    <input type="text" class="form-control" name="product_code" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Product Name</label>
                                    <input type="text" class="form-control" name="product_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Unit Price</label>
                                    <input type="number" step="0.01" class="form-control" name="unit_price" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Minimum Stock Level</label>
                                    <input type="number" class="form-control" name="min_stock_level" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-check mt-3">
                            <input type="checkbox" class="form-check-input" name="is_vat_exempt" id="editVatExempt">
                            <label class="form-check-label" for="editVatExempt">VAT Exempt Product</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Stock Modal -->
    <div class="modal fade" id="addStockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addStockForm">
                    <input type="hidden" name="product_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Quantity to Add</label>
                            <input type="number" class="form-control" name="quantity" required min="1">
                        </div>
                        <div class="form-group mt-3">
                            <label>Notes</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Stock</button>
                    </div>
                </form>
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
    <script src="js/inventory.js"></script>
</body>
</html>

