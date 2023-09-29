<?php 
session_start();
require 'dbh/initialise.php';
$conn = initialise("high");

$filter = "";

$month_total = format_month_total($conn);
$highest_month = get_high_total_month($month_total); //THERE IS ERROR WITH THIS
$months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
$total_invoices_year = get_row_count($conn, "SELECT id FROM invoices WHERE YEAR(created_at) = YEAR(CURDATE())");
$total_invoice_months = get_invoice_months($conn, false);
$item_name_totals = get_items_totals_months($conn, false);
$item_invoice_total = force_length_jagged(get_invoices_for_items($conn), 5);

$year_total = 0;

?>
<head>    
    <title>Statistics</title>
    <?php include 'templates/head.php'; ?>
    <link rel="stylesheet" href="css/statistics_styles.css">
</head>
<body>
    <div id="nav-placeholder"></div>
    <div class="main main-content">
        <h5>Pages / <p class="inline-shallow">Statistics</p>
        </h5>
        <div id="widget-placeholder" class="grid-container">
            <div class="card stat-item-1">
                <h3>Month Invoice Breakdown</h3>
                <div class="widget-container">
                    <div class="year-stats" id="bar-graph-1">
                        <?php foreach ($months as $key => $value): ?>
                            <div data-value="<?php echo $month_total[$key][2]; ?>" onclick="selectWidgetA(this)" class="month-group">
                                <div class="graph-container">
                                    <div class="tooltip" id="<?php echo $value; ?>-tooltip-bar-a"></div>
                                </div>
                                <div data-value="£<?php echo number_format((float)$month_total[$key][2],2, ".", ","); ?>" id="<?php echo $value; ?>-bar-a" class="bar bar-gray" style="height: <?php echo $month_total[$key][2] / $highest_month * 100 + 1; ?>"></div>
                                <p class="month"><?php echo $value; ?></p>
                                <?php $year_total += $month_total[$key][2]; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="stats-info justify-space-around">
                        <div class="graph-container">
                            <div id="pie-a-container" class="percent">    
                                <div class="tooltip" id="tooltip-pie-a"></div>
                                <p id="total-a">Total: </p>
                            </div>
                        </div>
                        <div id="keys-a" class="info">
                        </div>
                        <div class="info">
                            <p>Month Total: <span data-value="0" id="month-total"></span></p>
                            <p>Invoices: <span data-value="0" id="month-invoices"></span></p>
                            <p>Profit: <span data-value="0" id="month-profit"></span></p>
                            <p>Profit Margin: <span data-value="0" id="month-margin"></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card stat-item-8">
                <h3>Top Item Breakdown</h3>
                <div class="widget-container">
                    <div class="stats-info justify-space-between">
                        <div class="horizontal-year-stats" id="bar-graph-2">
                            <?php foreach ($item_invoice_total as $key => $item_total): ?>
                                <?php if ($item_total[2] != 0): ?>
                                    <div id="bar-b-<?php echo $key; ?>" data-value="<?php echo $key; ?>" class="horizontal-month-group" onclick="selectWidgetB(this)">
                                        <div class="graph-container">
                                            <div class="tooltip" id="tooltip-bar-b-<?php echo $key; ?>"></div>
                                        </div>
                                        <p id="c-bar-tag-<?php echo $key; ?>" class="horizontal-tag"><?php echo $item_total[2]; ?></p>
                                        <div id="c-bar-data-<?php echo $key; ?>" data-value="<?php echo $item_invoice_total[$key][0]; ?>" class="horizontal-bar" style="width: <?php echo $item_total[2] / $item_invoice_total[0][2] * 200; ?>"></div>
                                    </div>
                                    <br>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <div id="in-out-bars-a" class="year-stats">
                        </div>
                        <div class="info">
                            <p>Item: <span data-value="0" id="item-name"></span></p>
                            <p>Invoices: <span data-value="0" id="item-invoices"></span></p>
                            <p>Margin: <span data-value="0" id="item-margin"></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card stat-item-9">
                <h3>Current Month Breakdown</h3>
                <div class="widget-container">
                    <div class="stats-info justify-space-between">
                        <div id="in-out-bars-b" class="year-stats">
                        </div>
                        <div class="graph-container">
                            <div class="percent" id="pie-b-container">
                                <div class="tooltip" id="tooltip-pie-b"></div>
                                <p id="total-c">Total: </p>
                            </div>
                        </div>
                        <div id="keys-b" class="info">
                        </div>
                        <div class="info">
                            <h7>Invoices</h7>
                            <p>Invoices: <span id="total-invoices"></span></p>
                            <p>Invoice Income: <span id="invoice-income"></span></p>
                            <p>Invoice Profit: <span id="invoice-profit"></span></p>
                            <br>
                            <h7>Customers</h7>
                            <p>New Customers: <span id="new-customers"></span></p>
                            <p>Outstanding Customers: <span id="outstanding-customers"></span></p>
                            <p>Debted Customers: <span id="debted-customers"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadStatistics();
        loadElement("sidenav.php", "nav-placeholder", collectErrorData);
    });
</script>