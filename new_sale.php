<?php
session_start();
require_once 'config/config.php';
require_once 'includes/sales_manager.php';

$salesManager = new SalesManager($conn);

// Fetch customers and products for the form
try {
    $customers = $salesManager->getCustomers();
    $products = $salesManager->getProducts();
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching data: " . $e->getMessage();
    header("Location: error_page.php"); // Redirect to an error page
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
    $products = filter_input(INPUT_POST, 'products', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $quantities = filter_input(INPUT_POST, 'quantities', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

    // Validate inputs
    if (!$customer_id || empty($products) || empty($quantities) || !$payment_method || !$status) {
        $_SESSION['error'] = "Invalid input. Please fill in all required fields.";
        header("Location: new_sale.php");
        exit();
    }

    $sale_data = [
        'customer_id' => $customer_id,
        'products' => $products,
        'quantities' => $quantities,
        'payment_method' => $payment_method,
        'status' => $status
    ];

    try {
        $result = $salesManager->completeSale($sale_data);
        if ($result) {
            $_SESSION['success'] = "Sale completed successfully!";
        } else {
            $_SESSION['error'] = "Failed to complete the sale. Please try again.";
        }
        header("Location: sales.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error processing sale: " . $e->getMessage();
        header("Location: new_sale.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Sale - The Olivian Group Limited</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid py-4">
            <form id="saleForm" method="POST" action="">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4>Sale Items</h4>
                            </div>
                            <div class="card-body">
                                <table class="table" id="saleItems">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Unit Price</th>
                                            <th>Quantity</th>
                                            <th>VAT Status</th>
                                            <th>Subtotal</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="noItemsRow">
                                            <td colspan="6" class="text-center">No items added</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-primary" id="addItemBtn">
                                    <i class="fas fa-plus"></i> Add Item
                                </button>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4>Sale Details</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Customer</label>
                                        <select id="customer_id" name="customer_id" class="form-control select2" required>
                                            <option value="">Select Customer</option>
                                            <?php foreach ($customers as $customer): ?>
                                                <option value="<?php echo $customer['customer_id']; ?>">
                                                    <?php echo htmlspecialchars($customer['name']); ?> 
                                                    (<?php echo htmlspecialchars($customer['phone']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="payment_method">Payment Method</label>
                                        <select class="form-select" name="payment_method" id="payment_method" required>
                                            <option value="cash">Cash</option>
                                            <option value="mpesa">M-PESA</option>
                                            <option value="bank">Bank Transfer</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                    <label for="status">Payment Status</label>
                                        <select class="form-select" name="status" id="status" required>
                                            <option value="pending">Pending</option>
                                            <option value="paid">Paid</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Sale Summary</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label>Total Items:</label>
                                    <span id="totalItems">0</span>
                                </div>
                                <div class="mb-3">
                                    <label>Subtotal (VAT Exempt):</label>
                                    <span id="subtotalExempt">KSH 0.00</span>
                                </div>
                                <div class="mb-3">
                                    <label>Subtotal (VATable):</label>
                                    <span id="subtotalVatable">KSH 0.00</span>
                                </div>
                                <div class="mb-3">
                                    <label>VAT Amount (16%):</label>
                                    <span id="vatAmount">KSH 0.00</span>
                                </div>
                                <div class="mb-3">
                                    <label>Total Amount:</label>
                                    <span id="totalAmount">KSH 0.00</span>
                                </div>
                                <button type="submit" class="btn btn-success w-100">Complete Sale</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Product Selection Template -->
        <template id="productRowTemplate">
            <tr class="item-row">
                <td>
                    <select name="products[]" class="form-select product-select" required>
                        <option value="">Select Product</option>
                        <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['product_id']; ?>" 
                                data-price="<?php echo $product['unit_price']; ?>"
                                data-vat-exempt="<?php echo $product['is_vat_exempt']; ?>">
                            <?php echo htmlspecialchars($product['product_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td class="unit-price">KSH 0.00</td>
                <td>
                    <input type="number" name="quantities[]" class="form-control quantity" min="1" required>
                </td>
                <td class="vat-status">-</td>
                <td class="subtotal">KSH 0.00</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        </template>
    </div>   
    
    <!-- JavaScript Dependencies -->
    <!-- Update the JavaScript Dependencies section at the bottom of the file -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="js/new_sale.js"></script>
</body>
</html>
