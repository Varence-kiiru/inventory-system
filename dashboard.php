<?php
session_start();
require_once 'config/config.php';
require_once 'includes/dashboard_manager.php';

$dashboardManager = new DashboardManager($conn);
$metrics = $dashboardManager->getDashboardMetrics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - The Olivian Group Limited</title>

    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <div class="main-content">
        <div class="container-fluid py-4">
            <!-- Welcome Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user']['full_name']); ?></h2>
                    <p class="text-muted">Here's your business overview for today</p>
                </div>
            </div>
              <!-- Quick Stats Cards -->
              <div class="row g-4 mb-4">
                  <div class="col-xl-3 col-md-6">
                      <div class="card bg-primary text-white">
                          <div class="card-body">
                              <div class="d-flex justify-content-between">
                                  <div>
                                      <h6>Today's Sales</h6>
                                      <h3>KSH <?php echo number_format($metrics['daily_sales'], 2); ?></h3>
                                  </div>
                                  <div class="icon-box">
                                      <i class="fas fa-dollar-sign fa-2x"></i>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="col-xl-3 col-md-6">
                      <div class="card bg-success text-white">
                          <div class="card-body">
                              <div class="d-flex justify-content-between">
                                  <div>
                                      <h6>Monthly VAT</h6>
                                      <h3>KSH <?php echo number_format($metrics['monthly_vat'], 2); ?></h3>
                                      <small>Resets: <?php echo date('d M Y', strtotime('last day of this month')); ?></small>
                                  </div>
                                  <div class="icon-box">
                                      <i class="fas fa-chart-line fa-2x"></i>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="col-xl-3 col-md-6">
                      <div class="card bg-info text-white">
                          <div class="card-body">
                              <div class="d-flex justify-content-between">
                                  <div>
                                      <h6>Total Products</h6>
                                      <h3><?php echo number_format($metrics['total_products']); ?></h3>
                                  </div>
                                  <div class="icon-box">
                                      <i class="fas fa-box fa-2x"></i>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="col-xl-3 col-md-6">
                      <div class="card bg-warning text-white">
                          <div class="card-body">
                              <div class="d-flex justify-content-between">
                                  <div>
                                      <h6>Pending Orders</h6>
                                      <h3><?php echo number_format($metrics['pending_orders']); ?></h3>
                                  </div>
                                  <div class="icon-box">
                                      <i class="fas fa-clock fa-2x"></i>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="col-xl-3 col-md-6">
                      <div class="card bg-danger text-white">
                          <div class="card-body">
                              <div class="d-flex justify-content-between">
                                  <div>
                                      <h6>Low Stock Items</h6>
                                      <h3><?php echo number_format($metrics['low_stock_items']); ?></h3>
                                  </div>
                                  <div class="icon-box">
                                      <i class="fas fa-exclamation-triangle fa-2x"></i>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
            <!-- Sales Trend Chart -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Sales Trend</h5>
                            <select class="form-select form-select-sm w-auto" id="salesPeriod">
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <canvas id="salesTrendChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products and Inventory Value -->
            <div class="row mb-4">
                <!-- Top Selling Products -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Top Selling Products</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Code</th>
                                            <th>Units Sold</th>
                                            <th>Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($metrics['top_products'] as $product): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                            <td><?php echo htmlspecialchars($product['product_code']); ?></td>
                                            <td><?php echo number_format($product['units_sold']); ?></td>
                                            <td>KSH <?php echo number_format($product['revenue'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                  <!-- Recent Transactions -->
                  <div class="col-xl-6">
                      <div class="card">
                          <div class="card-header">
                              <h5>Recent Transactions</h5>
                          </div>
                          <div class="card-body">
                              <div class="table-responsive">
                                  <table class="table">
                                      <thead>
                                          <tr>
                                              <th>ID</th>
                                              <th>Customer</th>
                                              <th>Product</th>
                                              <th>Amount</th>
                                              <th>Status</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          <?php foreach ($metrics['recent_transactions'] as $transaction): ?>
                                          <tr>
                                              <td><?php echo $transaction['id']; ?></td>
                                              <td><?php echo htmlspecialchars($transaction['customer_name']); ?></td>
                                              <td><?php echo htmlspecialchars($transaction['product_name']); ?></td>
                                              <td>KSH <?php echo number_format($transaction['amount'], 2); ?></td>
                                              <td>
                                                  <span class="badge bg-<?php echo $transaction['status'] === 'pending' ? 'warning' : 'success'; ?>">
                                                      <?php echo ucfirst($transaction['status']); ?>
                                                  </span>
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

              <!-- Customer Insights and VAT Summary -->
              <div class="row mb-4">
                  <!-- Recent Customers -->
                  <div class="col-xl-4">
                      <div class="card">
                          <div class="card-header">
                              <h5>Recent Customers</h5>
                          </div>
                          <div class="card-body">
                              <ul class="list-group">
                                  <?php foreach ($metrics['recent_customers'] as $customer): ?>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <div>
                                          <h6 class="mb-0"><?php echo htmlspecialchars($customer['name']); ?></h6>
                                          <small class="text-muted"><?php echo htmlspecialchars($customer['company']); ?></small>
                                      </div>
                                      <span class="badge bg-primary rounded-pill">
                                          <?php echo date('d M', strtotime($customer['created_at'])); ?>
                                      </span>
                                  </li>
                                  <?php endforeach; ?>
                              </ul>
                          </div>
                      </div>
                  </div>

                  <!-- VAT Summary -->
                  <div class="col-xl-4">
                      <div class="card">
                          <div class="card-header">
                              <h5>VAT Summary</h5>
                          </div>
                          <div class="card-body">
                              <div class="d-flex justify-content-between mb-3">
                                  <div>VAT Collected</div>
                                  <div>KSH <?php echo number_format($metrics['vat_summary']['collected'], 2); ?></div>
                              </div>
                              <div class="d-flex justify-content-between">
                                  <div>Exempt Sales</div>
                                  <div>KSH <?php echo number_format($metrics['vat_summary']['exempt'], 2); ?></div>
                              </div>
                          </div>
                      </div>
                  </div>
                    <!-- System Alerts -->
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>System Alerts</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($metrics['system_alerts'] as $alert): ?>
                                <div class="alert alert-<?php echo $alert['type']; ?> mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong><?php echo $alert['title']; ?></strong>
                                        <small class="text-muted"><?php echo $alert['timestamp']; ?></small>
                                    </div>
                                    <p class="mb-0 mt-1"><?php echo $alert['message']; ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
          </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>
