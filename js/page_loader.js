function loadPage(tableName, accessLevel, altTable) {
    fetch(`dbh/page_loader.php?table=${tableName}&accessLevel=${accessLevel}&altTable=${altTable}`)
        .then(response => {
            if (response.ok) {
                // Redirect after the fetch request has completed successfully
                window.location.href = "view.php";
            } else {
                console.log(response);
                console.error("Fetch error:", response.status);
                window.location.href = "login.php";
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
        });
}

function formatPage() {
    fetch(`dbh/page_loader.php?getData=true`)
        .then(response => response.text())
        .then(data => {
            setPage(data);
        });
}

function setPage(sessionData) {
    var tableName = JSON.parse(sessionData)[0];
    var altTable = JSON.parse(sessionData)[2];
    document.getElementById("pageTitle").innerText = tableName[0].toUpperCase() + tableName.substr(1);
    document.getElementById("pageName").innerText = tableName[0].toUpperCase() + tableName.substr(1);
    if (altTable != "dashboard" && tableName != "profit_loss_account" && tableName != "aged_debtors_creditors") {
        setTabs(tableName, ["all"], ["All"], true, false);
    }
    switch (tableName) {
        case "invoices":
            if (altTable == "dashboard") {
                setTabsDynamic(["invoices", "stocked_items", "stocked_items", "customers"], tableName, ["invoice_delivery_today", "expiring_month", "expired", "debted_customers"], ["Deliveries Today", "Expiring Products", "Expired Products", "Debted Customers"]);
                selectTab('invoice_delivery_today', 'invoices', 'invoices')
                loadElement("widgets.html", "widget-placeholder", function() {
                    setWidgets("dashboard", 1);
                });
                document.getElementById("pageTitle").innerText = "Dashboard";
                document.getElementById("pageName").innerText = "Dashboard";
            } else {
                setTabs(tableName, ["packing_invoices", "sales_invoices"], ["Packing Invoices", "Sales Invoices"], true, false);
                loadElement("widgets.html", "widget-placeholder", function() {
                    setWidgets(tableName, 1);
                });
                disableToolbarButton(4);
            }
            break;

        case "customer_address":
            document.getElementById("pageTitle").innerText = "Address Book";
            document.getElementById("pageName").innerText = "Address Book";
            break;

        case "stocked_items":
            document.getElementById("pageTitle").innerText = "Stocked Items";
            document.getElementById("pageName").innerText = "Stocked Items";
            break;

        case "invoiced_items":
            document.getElementById("pageTitle").innerText = "Items Invoiced";
            document.getElementById("pageName").innerText = "Items Invoiced";
            break;

        case "general_ledger":
            document.getElementById("pageTitle").innerText = "General Ledger";
            document.getElementById("pageName").innerText = "General Ledger";
            generateLedgerWidgets();
            break;

        case "customer_payments":
            document.getElementById("pageTitle").innerText = "Customer Payments";
            document.getElementById("pageName").innerText = "Customer Payments";
            break;

        case "profit_loss_account":
            document.getElementById("pageTitle").innerText = "Profit / Loss";
            document.getElementById("pageName").innerText = "Profit / Loss";
            setProfitLoss();
            break;

        case "aged_debtors_creditors":
            document.getElementById("pageTitle").innerText = "Aged Debtor and Creditors";
            document.getElementById("pageName").innerText = "Aged Debtor and Creditors";
            setAgedDebtorsCreditors();
            extendSearchBar();
            removeToolbarButtons();
            break;

        case "customers":
            setTabs(tableName, ["retail_customer", "wholesale_customer"], ["Retail Customers", "Wholesale Customers"], true);
            loadElement("widgets.html", "widget-placeholder", function() {
                setWidgets(tableName, 1);
            });
            setToolbar(3, "applyDebt()", "add_card");
            break;

        case "offers":
            constructInfoWidget("data/offer_codes.json");
            break;
    }
    if (altTable != "dashboard") {
        selectTab("all", tableName, tableName);
    }
    handlePage();
}

function setTabs(tableName, tabNames, tabTitles, onclick, unrestrictSize) {
    const tabElement = document.getElementById("tabs");
    for (var i = 0; i < tabNames.length; i++) {
        var tabDiv = document.createElement('div');
        tabDiv.className = 'tab';
        var h1 = document.createElement('h1');
        h1.innerHTML = tabTitles[i];
        if (onclick) {
            h1.setAttribute('onclick', "selectTab('" + tabNames[i].toLowerCase() + "', '" + tableName + "', '" + tableName + "')");
        }
        if (unrestrictSize) {
            tabDiv.setAttribute('style', 'width:auto');
        }
        tabDiv.appendChild(h1);
        tabElement.appendChild(tabDiv);
    }
}

function setTabsDynamic(accessedTables, startingTable, tabNames, tabTitles) {
    const tabElement = document.getElementById("tabs");
    for (var i = 0; i < tabNames.length; i++) {
        var tabDiv = document.createElement('div');
        tabDiv.className = 'tab';
        var h1 = document.createElement('h1');
        h1.innerHTML = tabTitles[i];
        h1.setAttribute('onclick', "selectTab('" + tabNames[i].toLowerCase() + "', '" + accessedTables[i] + "', '" + startingTable + "')");
        tabDiv.appendChild(h1);
        tabElement.appendChild(tabDiv);
    }
}

function setWidgets(tableName, startIndex) {
    switch (tableName) {
        case "invoices":
            configureWidgets(startIndex, "Today's Invoices", "receipt_long", "invoices_today", "invoices_today_yesterday_difference", " more than yesterday");
            configureWidgets(startIndex + 1, "Pending Invoices", "timer", "invoices_pending", "invoices_pending_week", " from this week");
            configureWidgets(startIndex + 2, "Outstanding Invoices", "markunread_mailbox", "invoices_overdue", "invoices_overdue_week", " from this week");
            configureWidgets(startIndex + 3, "Completed Today", "check", "invoices_completed_today", "invoices_completed_week", " from this week");
            dayDifferenceTotal();
            break;

        case "invoices-alt":
            configureWidgets(startIndex, "Total", "receipt", "invoices", "invoices_today", " from today.");
            configureWidgets(startIndex + 1, "Completed", "task", "invoices_completed", "invoices_completed_today", " completed today.");
            configureWidgets(startIndex + 2, "Pending", "pending_actions", "invoices_pending", "invoices_pending_week", " pending from this week.");
            configureWidgets(startIndex + 3, "Overdue", "assignment_late", "invoices_overdue", "invoices_due_today", " due today.");
            break;

        case "customers":
            configureWidgets(startIndex, "Total Customers", "person", "total_customers", "total_customers_week", " from this week");
            configureWidgets(startIndex + 1, "Total Retail", "shopping_bag", "total_retail_customers", "blank", "");
            configureWidgets(startIndex + 2, "Total Wholesale", "store", "total_wholesale_customers", "blank", "");
            configureWidgets(startIndex + 3, "Total with Outstanding", "money_off", "total_outstanding_customers", "total_debted_customers", " with no last payment");
            break;

        case "dashboard":
            configureWidgets(startIndex, "Invoices Due Today", "outgoing_mail", "invoices_due_today", "invoices_due_week", " due this week.");
            configureWidgets(startIndex + 1, "Products Expiring This Week", "hourglass_empty", "products_expiring_week", "products_expiring_month", " expiring this month.");
            configureWidgets(startIndex + 2, "Income Today", "payments", "income_today", "income_week", " this week.");
            configureWidgets(startIndex + 3, "Outstanding Invoices", "markunread_mailbox", "invoices_overdue", "invoices_overdue_week", " from this week");
            break;

        case "items":
            configureWidgets(startIndex, "Sold Today", "price_check", "items_sold_today", "items_sold_week", " sold this week.");
            configureWidgets(startIndex + 1, "Total Invoiced", "format_list_numbered", "invoiced_items", "invoiced_items_week", " invoiced this week.");
            configureWidgets(startIndex + 2, "Top Item", "shopping_cart_checkout", "top_item", "top_item_week", " for this week.");
            configureWidgets(startIndex + 3, "Least Popular Item", "production_quantity_limits", "bottom_item", "bottom_item_week", " for this week.");
            break;
    }
}

function generateLedgerWidgets() {
    const parentElement = document.getElementById("widget-placeholder");
    var accountCodeWidget = document.createElement("div");
    accountCodeWidget.setAttribute('class', 'card item13 account-code-widget');

    var trialBalanceWidget = document.createElement("div");
    trialBalanceWidget.setAttribute('class', 'card item14 account-code-widget');
    trialBalanceWidget.setAttribute('id', 'trial-balance-widget');
    trialBalanceWidget.style.position = "relative";
    var trialBalanceHeader = document.createElement('h4');
    trialBalanceHeader.innerText = "Trial Balance";
    trialBalanceHeader.setAttribute('id', 'trial-balance-header');
    trialBalanceWidget.appendChild(trialBalanceHeader);

    createStartEndDatePickers(accountCodeWidget, ['trial-balance-start', 'trial-balance-end'], ['Start date:', 'End date:'], true, setTrialBalanceTable);

    parentElement.appendChild(trialBalanceWidget);
    parentElement.appendChild(accountCodeWidget);
}

function setProfitLoss() {
    const parentElement = document.getElementById("widget-placeholder");
    var profitLossWidget = document.createElement("div");
    profitLossWidget.setAttribute('class', 'card item13 account-code-widget');
    createStartEndDatePickers(profitLossWidget, ['profit-loss-start', 'profit-loss-end'], ['Start date:', 'End date:'], true, calculateSalesRevenue);
    $(".form-datepicker").datepicker({
        dateFormat: "yy-mm-dd"
    });
    parentElement.appendChild(profitLossWidget);
}

function setAgedDebtorsCreditors() {
    const parentElement = document.getElementById("widget-placeholder");
    var datePickerWidgetA = document.createElement("div");
    datePickerWidgetA.setAttribute('class', 'card item15 account-code-widget');
    var datePickerWidgetB = document.createElement("div");
    datePickerWidgetB.setAttribute('class', 'card item16 account-code-widget');
    var buttonLabels = ["Debtors", "Creditors"];
    var buttonTexts = ["0-30 Days", "31-60 Days", "61-90 Days", "90+ Days"];
    var buttonOnClicks = [
        [
            function() { setDebtorCreditorTable("0-30-debtor", "Debtors 0-30 days"); },
            function() { setDebtorCreditorTable("31-60-debtor", "Debtors 31-60 days"); },
            function() { setDebtorCreditorTable("61-90-debtor", "Debtors 61-90 days"); },
            function() { setDebtorCreditorTable("90-x-debtor", "Debtors 90+ days"); }
        ],
        [
            function() { setDebtorCreditorTable("0-30-creditor", "Creditors 0-30 days"); },
            function() { setDebtorCreditorTable("31-60-creditor", "Creditors 31-60 days"); },
            function() { setDebtorCreditorTable("61-90-creditor", "Creditors 61-90 days"); },
            function() { setDebtorCreditorTable("90-x-creditor", "Creditors 90+ days"); }
        ]
    ];
    createDateButtons([datePickerWidgetA, datePickerWidgetB], buttonLabels, buttonTexts, buttonOnClicks);
    parentElement.appendChild(datePickerWidgetA);
    parentElement.appendChild(datePickerWidgetB);
}