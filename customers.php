<?php
session_start();
require_once 'config/config.php';
require_once 'includes/customer_manager.php';

$customerManager = new CustomerManager($conn);
$customers = $customerManager->getAllCustomers();

// Get statistics
$totalCustomers = count($customers);
$activeCustomers = array_filter($customers, fn($c) => $c['status'] === 'active');
$newCustomers = array_filter($customers, fn($c) => strtotime($c['created_at']) > strtotime('-30 days'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - The Olivian Group Limited</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="main-content">
        <div class="container-fluid py-4">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Customers</p>
                                        <h5 class="font-weight-bolder mb-0"><?php echo $totalCustomers; ?></h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                        <i class="fas fa-users text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Active Customers</p>
                                        <h5 class="font-weight-bolder mb-0"><?php echo count($activeCustomers); ?></h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                        <i class="fas fa-user-check text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">New Customers (30d)</p>
                                        <h5 class="font-weight-bolder mb-0"><?php echo count($newCustomers); ?></h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <i class="fas fa-user-plus text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customers Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h6>Customer Directory</h6>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                <i class="fas fa-plus me-2"></i>Add Customer
                            </button>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table id="customersTable" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Customer</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Contact</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Joined</th>
                                            <th class="text-secondary opacity-7">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($customers as $customer): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?php echo $customer['name']; ?></h6>
                                                            <p class="text-xs text-secondary mb-0"><?php echo $customer['company']; ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0"><?php echo $customer['email']; ?></p>
                                                    <p class="text-xs text-secondary mb-0"><?php echo $customer['phone']; ?></p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-dark fw-bold">
                                                        <?php echo ucfirst($customer['status']); ?>
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        <?php echo date('d M Y', strtotime($customer['created_at'])); ?>
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="btn-group">
                                                        <button class="btn btn-link text-secondary mb-0" data-bs-toggle="dropdown">
                                                            <i class="fa fa-ellipsis-v text-xs"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item edit-customer" href="#" data-id="<?php echo $customer['customer_id']; ?>">
                                                                    <i class="fas fa-edit me-2"></i>Edit
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item view-history" href="#" data-id="<?php echo $customer['customer_id']; ?>">
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

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addCustomerForm">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Company</label>
                            <input type="text" class="form-control" name="company">
                        </div>
                        <div class="form-group mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Phone</label>
                            <input type="tel" class="form-control" name="phone" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Address</label>
                            <textarea class="form-control" name="address" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCustomerForm">
                    <input type="hidden" name="customer_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Company</label>
                            <input type="text" class="form-control" name="company">
                        </div>
                        <div class="form-group mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Phone</label>
                            <input type="tel" class="form-control" name="phone" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Address</label>
                            <textarea class="form-control" name="address" rows="3"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
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

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="js/customers.js"></script>
</body>
</html>
