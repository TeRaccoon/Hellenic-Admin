<?php
    session_start();

    require 'dbh/dbh.php';
    require 'dbh/data_handler.php';


    //Run queries for widgets from initialise.php

    $invoice_info = get_invoice_info($conn, $_POST['print_row_id']);
    if (count($invoice_info) == 0) {
        $_SESSION["mysql_error"] = "Error: PHP-incorrect-invoice. Please select a valid invoice!";
        $_SESSION["error_type"] = "other";
        header("Location: {$_SERVER["HTTP_REFERER"]}");
        exit();
    }
    $invoice_title = $invoice_info[0][0];
    $due_date = $invoice_info[0][1];
    $net_value = $invoice_info[0][2];
    $total = $invoice_info[0][3];
    $vat = $invoice_info[0][4];
    $delivery_date = $invoice_info[0][5];
    $created_at = $invoice_info[0][6];
    $forename = $invoice_info[0][7];
    $surnmame = $invoice_info[0][8];
    $outstanding_balance = $invoice_info[0][9];

    $invoice_address = array(
        0 => $invoice_info[0][10],
        1 => $invoice_info[0][11],
        2 => $invoice_info[0][12],
        3 => $invoice_info[0][13]
    );
    $delivery_address = array(
        0 => $invoice_info[0][14],
        1 => $invoice_info[0][15],
        2 => $invoice_info[0][16],
        3 => $invoice_info[0][17]
    );

    $product_info = get_invoice_products($conn, $_POST['print_row_id']);

    $total_net = 0;
    $total_vat = 0;
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title><?php echo(ucfirst($invoice_title)); ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="css/invoice_styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <script src="js/form_handler.js"></script>
    <script src="js/table_handler.js"></script>
    <script src="js/element_loader.js"></script>
    <script src="js/data_handler.js"></script>
</head>

<body>
    <div id="invoice" class="invoice">
        <div class="details">
            <div class="detail-row">
                <span class="label">
                    <h2>Sales Invoice / Delivery</h2>
                </span>
                <span class="value"><img style="width:100px;height:100px;" src="images/logo.jpg" alt="Logo"></span>
            </div>
        </div>
        <div class="grid-container">
            <div class="itema">
                <span class="label"><b>Invoice Title:</b></span>
                <span id="invoice-title" class="value"><?php echo($invoice_title); ?></span><br>
                <span class="label"><b>Invoice Date:</b></span>
                <span class="value"><?php echo(date("d/m/Y")) ?></span><br>
            </div>
            <div class="itemb">
                <span class="label"><b>Estimated Delivery Date:</b></span>
                <span id="delivery-date" class="value"><?php echo($delivery_date); ?></span><br>
                <span class="label"><b>Created At:</b></span>
                <span id="created-at" class="value"><?php echo(date("d-m-Y")) ?></span><br>
            </div>
            <div class="card item1">
                <p> Unit 15<br>
                    Hilsea Industrial Estate<br>
                    Limberline Spur<br>
                    Portsmouth<br>
                    PO3 5JW</p>
            </div>
            <div class="card item2">
                <p>Tel.: 023 9250 2120<br>
                    Email: accounts@hellenicgrocery.co.uk<br>
                    Company No.: 8603006<br>
                    VAT No.: 171817403<br>
                    AWRS: XRAW00000106442<br>
                    EORI No.: GB171817403000</p>
            </div>
            <div class="card item3">
                <p class="title">Invoice Address:</p>
                <br>
                <?php foreach($invoice_address as $value): ?>
                <div><?php echo($value); ?></div>
                <?php endforeach; ?>
            </div>
            <div class="card item4">
                <p class="title">Delivery Address:</p>
                <br>
                <?php foreach($invoice_address as $value): ?>
                <div><?php echo($value); ?></div>
                <?php endforeach; ?>
            </div>
            <div class="card item5">
                <p class="title">Payment Details:</p>
                <p><u>Hellenic Grocery Ltd</u><br>
                    Account No: 63868745<br>
                    Sort Code: 20-69-40<br><br>
                    Please use this account for any payment</p>
            </div>
        </div>
        <div style="page-break-before: always;" class="grid-container">
            <div class="card item6">
                <div class="items">
                    <div class="item-row">
                        <span class="item-header-name">Item Name</span>
                        <span class="item-header-details">Quantity</span>
                        <span class="item-header-details">Unit Price</span>
                        <span class="item-header-details">Net Amount</span>
                        <span class="item-header-details">VAT</span>
                    </div>
                    <hr class="dark horizontal my-0">
                    </hr>
                    <br>
                    <?php foreach($product_info as $key => $row): ?>
                    <div class="item-row">
                        <span class="item-name"><?php echo($product_info[$key][0]); ?></span>
                        <span class="item-quantity"><?php echo($product_info[$key][2]); ?></span>
                        <span
                            class="item-unit-price">£<?php echo(number_format($product_info[$key][1], 2, ".", ",")); ?></span>
                        <span
                            class="item-total">£<?php echo(number_format($product_info[$key][1] * $product_info[$key][2], 2, ".", ",")); ?></span>
                        <?php $total_net += $product_info[$key][1] * $product_info[$key][2]; ?>
                        <?php if($product_info[$key][3] == 1): ?>
                        <span class="item-vat">
                            £<?php echo(number_format(($product_info[$key][1] * $product_info[$key][2]) * 0.2, 2, ".", ",")); ?>
                            <?php $total_vat += ($product_info[$key][1] * $product_info[$key][2]) * 0.2; ?>
                        </span>
                        <?php else: ?>
                        <span class="item-vat">
                            £--.--
                        </span>
                        <?php endif ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="card item7">
                <p class="title">Previous Outstanding Balance:</p>
                <div class="address-details">
                    £<?php echo(number_format($outstanding_balance, 2, ".", ",")); ?></div>
                <p class="title">Current Outstanding Balance:</p>
                <div class="address-details">
                    £<?php echo(number_format($outstanding_balance + $total_net + $total_vat, 2, ".", ",")); ?></div>
            </div>
            <div class="card item13">
                <div class="total">
                    <span class="label">Net Value:</span>
                    <span class="value">£<?php echo(number_format($total_net, 2, ".", ",")); ?></span><br>
                    <span class="label">VAT Value:</span>
                    <span class="value">£<?php echo(number_format($total_vat, 2, ".", ",")); ?></span><br>
                    <span class="label">Total:</span>
                    <span class="value">£<?php echo(number_format($total_net + $total_vat, 2, ".", ",")); ?></span>
                </div>
            </div>
            <div class="card item8">
                <p class="title">Agreement:</p>
                <div class="address-details">This serves as an acknowledgment that I have received my order in its
                    entirety, with no missing or damaged products. I have verified that all items listed on the invoice
                    are accounted for. Additionally, I confirm that all products have been delivered at the appropriate
                    temperature, meeting the necessary requirements.</div>
            </div>
            <div class="card item9">
                <p class="title">Customer Signature:</p>
                <br><br><br><br>
            </div>
            <div class="card item10">
                <p class="title">Driver Signature:</p>
                <br><br><br><br>
            </div>
        </div>
        <br><br>

    </div>
</body>

</html>
<script>
var invoiceContainer = document.getElementById('invoice');
var invoiceContainerHeight = invoiceContainer.offsetHeight;

// Get the available height of the A4 page (subtract any margins or headers/footers)
var pageHeight = 29.7 - (2 * 2.54); // Assuming A4 size with 2.54cm margins on all sides

// Check if the invoice content exceeds the page height
if (invoiceContainerHeight > pageHeight) {
    // Insert a page break element
    var pageBreakElement = document.createElement('div');
    pageBreakElement.classList.add('page-break');
    invoiceContainer.appendChild(pageBreakElement);
}
fixDates();

function fixDates() {
    convertDate("<?php echo($created_at); ?>", document.getElementById("created-at"));
    convertDate("<?php echo($delivery_date); ?>", document.getElementById("delivery-date"));
}

function convertDate(oldDate, element) {
    var date = new Date(oldDate);
    if (!isNaN(date.getTime())) {
        element.innerText = date.getMonth() + 1 + '/' + date.getDate() + '/' + date.getFullYear();
    }
}
</script>