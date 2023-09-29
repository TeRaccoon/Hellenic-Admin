<?php
session_start();
?>
<head>
    <link rel="stylesheet" href="css/sidenav_styles.css">
</head>
<button id="sidenav-toggle" class="hamburger">&#9776;</button>
<div class="sidenav border-radius-xl ms-3 my-3">
    <div>
        <h4>
            <p>Hellenic Grocery</p>
        </h4>
    </div>
    <hr class="dark horizontal my-0">
    <ul>
        <li>
            <h5>SALES</h5>
        </li>
        <?php if ($_SESSION['access_level'] != "low"): ?>
            <li>
                <a onclick="loadPage('invoices', 'high', 'dashboard')">
                    <i class="material-icons">dashboard</i> Dashboard
                </a>
            </li>
        <?php endif; ?>
        <li>
            <a onclick="loadPage('invoices', 'high', false)">
                <i class="material-icons">receipt_long</i> Invoices
            </a>
        </li>
    </ul>
    <hr class="dark horizontal my-0">
    <ul>
        <li>
            <h5>PEOPLE</h5>
        </li>
        <li>
            <a onclick="loadPage('customers', 'low')">
                <i class="material-icons fa">&#xf02b;</i> Customers
            </a>
        </li>
        <li>
            <a onclick="loadPage('customer_address', 'low')">
                <i class="material-icons">import_contacts</i> Addresses
            </a>
        </li>
        <li>
            <a onclick="loadPage('suppliers', 'low')">
                <i class="material-icons">person</i> Suppliers
            </a>
        </li>
    </ul>
    <hr class="dark horizontal my-0">
    <ul>
        <li>
            <h5>STOCK</h5>
        </li>
        <li>
            <a onclick="loadPage('items', 'low')">
                <i class="material-icons">local_grocery_store</i> Items
            </a>
        </li>
        <li>
            <a onclick="loadPage('retail_items', 'low')">
                <i class="material-icons">local_grocery_store</i> Retail Items
            </a>
        </li>
        <li>
            <a onclick="loadPage('allergen_information', 'low', false)">
                <i class="material-icons">production_quantity_limits</i> Allergen Information
            </a>
        </li>
        <li>
            <a onclick="loadPage('nutrition_information', 'low', false)">
                <i class="material-icons">medical_information</i> Nutrition Information
            </a>
        </li>
        <li>
            <a onclick="loadPage('stocked_items', 'low', false)">
                <i class="material-icons">archive</i> Stocked Items</a>
        </li>
        <li>
            <a onclick="loadPage('warehouse', 'low', false)">
                <i class="material-icons">warehouse</i> Warehouses
            </a>
        </li>
        <?php if ($_SESSION['access_level'] != "low"): ?>
            <li>
                <a onclick="loadPage('invoiced_items', 'high', false)">
                    <i class="material-icons">fact_check</i> Invoiced Items
                </a>
            </li>
        <?php endif; ?>
    </ul>
    <hr class="dark horizontal my-0">
    <?php if ($_SESSION['access_level'] == "full"): ?>
        <ul>
            <li>
                <h5>ACCOUNTING</h5>
            </li>
            <li>
                <a onclick="loadPage('supplier_invoices', 'high', false)">
                    <i class="material-icons">description</i> Supplier Invoices
                </a>
            </li>
            <li id="sidenav-user_management">
                <a onclick="loadPage('customer_payments', 'high', false)">
                    <i class="material-icons">payments</i> Cust. Payments
                </a>
            </li>
            <li id="sidenav-user_management">
                <a onclick="loadPage('apr_charges', 'high', false)">
                    <i class="material-icons">savings</i> APR Charges
                </a>
            </li>
            <li id="sidenav-user_management">
                <a onclick="loadPage('payments', 'high', false)">
                    <i class="material-icons">account_balance</i> Payments
                </a>
            </li>
            <li id="sidenav-user_management">
                <a onclick="loadPage('general_ledger', 'high', false)">
                    <i class="material-icons">import_contacts</i> General Ledger
                </a>
            </li>
            <li id="sidenav-user_management">
                <a onclick="loadPage('profit_loss_account', 'high', true)">
                    <i class="material-icons">query_stats</i> Profit / Loss
                </a>
            </li>
            <li id="sidenav-user_management">
                <a onclick="loadPage('aged_debtors_creditors', 'high', true)">
                    <i class="material-icons">stacked_line_chart</i> Aged Debtors / Creditors
                </a>
            </li>
        </ul>
    <?php endif; ?>
    <hr class="dark horizontal my-0">
    <?php if ($_SESSION['access_level'] != "low"): ?>
        <ul>
            <li>
                <h5>RETAIL MANAGEMENT</h5>
            </li>
            <li id="sidenav-offers">
                <a onclick="loadPage('offers', 'high', false)">
                    <i class="material-icons">discount</i> Retail Offers
                </a>
            </li>
            <li id="discount-codes-nav">
                <a onclick="loadPage('discount_codes', 'high', false)">
                    <i class="material-icons">vpn_key</i> Discount Codes
                </a>
            </li>
        </ul>
    <?php endif; ?>
    <hr class="dark horizontal my-0">
    <?php if ($_SESSION['access_level'] == "full"): ?>
        <ul>
            <li>
                <h5>ADMIN</h5>
            </li>
            <li id="sidenav-user_management">
                <a onclick="loadPage('users', 'high')">
                    <i class="material-icons">manage_accounts</i> User Management
                </a>
            </li>
            <li id="sidenav-statistics">
                <a href="./statistics.php">
                    <i class="material-icons">insert_chart</i> Statistics
                </a>
            </li>
            <li>
                <a onclick="loadPage('image_locations', 'high')">
                    <i class="material-icons">image</i> Image Locations
                </a>
            </li>
        </ul>
    <hr class="dark horizontal my-0">
    <?php endif; ?>
    <ul>
        <li>
            <a href="./login.php">
                <i class="material-icons">logout</i> Logout
            </a>
        </li>
    </ul>
</div>