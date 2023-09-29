var selectedMonths = [];
var selectedItem;

function loadStatistics() {
    generateGraphs();
    loadWidgetButtons();
    loadStatWidgets();
    showTooltip();
    setBarsC();
    setWidget3();
    setWidgets("invoices-alt", 5);

    configureWidgets(1, "<br><br><br>", "receipt_long", "blank", "blank", "Invoices");
    configureWidgets(2, "<br><br><br>", "local_grocery_store", "blank", "blank", "Items");
    configureWidgets(3, "<br><br><br>", "groups", "blank", "blank", "Customers");
    configureWidgets(4, "<br><br><br>", "traffic", "blank", "blank", "Site Traffic");
}


    


function generateGraphs() {
    generatePieChart("pie-a-", 4, document.getElementById("pie-a-container"));
    generatePieChart("pie-b-", 2, document.getElementById("pie-b-container"));
    generateKeys("key-a-", true, 4, document.getElementById("keys-a"), null);
    generateKeys("key-b-", true, 2, document.getElementById("keys-b"), ["Outstanding", "Received"]);
    generateInOutBars("b-bar-", document.getElementById("in-out-bars-a"));
    generateInOutBars("c-bar-", document.getElementById("in-out-bars-b"));
}

function loadWidgetButtons() {
    let html = '';
    for (let i = 1; i <= 4; i++) {
        html += `<div class="card stat-item-${i+1}" onclick="switchWidget(this)" id=widget-button-${i}>
                    <div class="card-header">
                        <div id="widget-box-${i}" class="icon icon-lg icon-shape bg-gradient-blue shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                            <i id="widget-icon-${i}" class="material-icons opacity-10"></i>
                        </div>
                        <p id="widget-title-${i}" class="text-end"></p>
                        <h6 id="widget-value-${i}" class="text-end"></h6>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer">
                        <p id="widget-text-${i}">
                            <span id="widget-text-value-${i}" class="text-sm font-weight-bolder"></span>
                        </p>
                    </div>
                </div>`;
    }
    const element = document.getElementById("widget-placeholder");
    element.innerHTML += html;
}

function switchWidget(element) {
    console.log("Switching: " + element.id);
    switch (element.id) {
        case "widget-button-1":
            setWidgets("invoices-alt", 5)
            break;
        case "widget-button-2":
            setWidgets("items", 5)
            break;
        case "widget-button-3":
            setWidgets("customers", 5)
            break;
        case "widget-button-4":
            
            break;
    }
}

function loadStatWidgets() {
    let html = '';
    for (let i = 5; i < 9; i++) {
        html += `<div class="card stat-widget-${i-4}">
                    <div class="card-header">
                        <div id="widget-box-${i}" class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                            <i id="widget-icon-${i}" class="material-icons opacity-10"></i>
                        </div>
                        <p id="widget-title-${i}" class="text-end"><br><br><br></p>
                        <h6 id="widget-value-${i}" class="text-end"></h6>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer">
                        <p id="widget-text-${i}">
                            <span id="widget-text-value-${i}" class="text-sm font-weight-bolder"></span>
                        </p>
                    </div>
                </div>`;
    }
    const element = document.getElementById("widget-placeholder");
    element.innerHTML += html;
}

function setWidget3() {
    Promise.all([collectWidgetData("month_in"), collectWidgetData("month_out"), collectWidgetData("total_owed"), collectWidgetData("money_received")])
    .then(([inTotal, outTotal, totalOwed, moneyReceived]) => {
        var max = inTotal;
        if (inTotal < outTotal) {
            max = outTotal;
        }
        document.getElementById("c-bar-in").setAttribute("style", "height:" + (inTotal / max * 100) + "px");
        document.getElementById("c-bar-in").setAttribute("data-value", "£" + Number(inTotal).toFixed(2));
        document.getElementById("c-bar-out").setAttribute("style", "height:" + (outTotal / max * 100) + "px");
        document.getElementById("c-bar-out").setAttribute("data-value", "£" + Number(outTotal).toFixed(2));

        var total = Number(totalOwed) + Number(moneyReceived);
        document.getElementById("pie-b-1").setAttribute("stroke-dasharray", Number(totalOwed) / Number(total) * 100 + ", 100");
        document.getElementById("pie-b-1").setAttribute("data-value", "£" + Number(totalOwed).toFixed(2));

        document.getElementById("total-c").innerText = "Total: £" + Number(total).toFixed(2);
        document.getElementById("pie-b-2").setAttribute("stroke-dasharray", 100 + ", 100");
        document.getElementById("pie-b-2").setAttribute("data-value", "£" + Number(moneyReceived).toFixed(2));
        // document.getElementById("key-c-1").textContent = "Total Owed";
        // document.getElementById("key-c-1").setAttribute("data-value", totalOwed);
        // document.getElementById("key-c-2").textContent = "Money Received";
        // document.getElementById("key-c-2").setAttribute("data-value", moneyReceived);
    });
    Promise.all([collectWidgetData("invoices_month"), collectWidgetData("income_month"), collectWidgetData("invoice_profit_month"), collectWidgetData("total_new_customers"), collectWidgetData("total_outstanding_customers"), collectWidgetData("total_debted_customers")])
    .then(([invoicesMonth, invoiceIncomeMonth, invoiceProfitMonth, newCustomers, outstandingCustomers, debtedCustomers]) => {
        document.getElementById("total-invoices").innerText = invoicesMonth;
        document.getElementById("invoice-income").innerText = "£" + invoiceIncomeMonth;
        document.getElementById("invoice-profit").innerText = "£" + invoiceProfitMonth;
        document.getElementById("new-customers").innerText = newCustomers;
        document.getElementById("outstanding-customers").innerText = outstandingCustomers;
        document.getElementById("debted-customers").innerText = debtedCustomers;
    });
}

function setBarsC() {
    var maxLength = 0;
    for (var i = 0; i < 5; i++) {
        var item = document.getElementById("c-bar-tag-" + i);
        if (item != null && item.innerText.length > maxLength) {
            maxLength = item.innerText.length;
        }
    }
    var validData = false;
    for (var i = 0; i < 5; i++) {
        var item = document.getElementById("c-bar-tag-" + i);
        if (item != null && item.innerText.length < maxLength) {
            difference = maxLength - item.innerText.length;
            for (var k = 0; k < difference; k++) {
                item.innerText = " " + item.innerText;
                validData = true;
            }
        }
    }
    if (!validData) {
    }
}

async function getInvoiceMonths() {
    const data = await fetchDataPost("dbh/data_handler.php", "get_item_name_totals");
    return JSON.parse(data);
}

async function getTopItems() {
    const data = await fetchDataPost("dbh/data_handler.php", "get_top_items");
    return JSON.parse(data);
}

async function getProfits() {
    const data = await fetchDataPost("dbh/data_handler.php", "get_profits");
    return JSON.parse(data);
}

async function selectWidgetB(item) {
    let data = await getTopItems();
    let itemNameTotals = data[item.dataset.value];

    let outTotal = (itemNameTotals[2] * itemNameTotals[3]).toFixed(2);
    let inTotal = (itemNameTotals[2] * itemNameTotals[4]).toFixed(2);
    let max = inTotal;
    if (inTotal > outTotal) {
        max = outTotal;
    }
    let set = selectItem(item);
    if (set) {
        document.getElementById("item-name").innerText = itemNameTotals[0];
        document.getElementById("item-invoices").innerText = itemNameTotals[1];
        document.getElementById("item-margin").innerText = ((inTotal - outTotal) / outTotal * 100).toFixed(2) + "%";
        document.getElementById("b-bar-in").setAttribute("style", "height:" + (inTotal / max * 100) + "px");
        document.getElementById("b-bar-in").setAttribute("data-value", "£" + inTotal);
        document.getElementById("b-bar-out").setAttribute("style", "height:" + (outTotal / max * 100) + "px");
        document.getElementById("b-bar-out").setAttribute("data-value", "£" + outTotal);
    } else {
        clearText(["item-name", "item-invoices", "item-margin"]);
        resetBar(["b-bar-in", "b-bar-out"]);
    }
}

function clearText(ids) {
    ids.forEach(id => {
        let element = document.getElementById(id);
        if (element) {
            element.textContent = "";
        }
    });
}

function resetBar(ids) {
    ids.forEach(id => {
       let element = document.getElementById(id);
       element.style.height = "100px";
       element.dataset.value = "empty";
    });
}

function formatGBP(number) {
    return number.toLocaleString('en-US', { style: 'currency', currency: 'GBP' });
}

async function selectWidgetA(item) {
    const profitData = await getProfits();
    const quantityData = await getMonthInvoiceQuantity(item);

    const totalB = document.getElementById("total-a");
    const monthTotalElement = document.getElementById("month-total");
    const monthInvoices = document.getElementById("month-invoices");
    const monthProfit = document.getElementById("month-profit");
    const monthMargin = document.getElementById("month-margin");

    const selectItem = selectMonth(item);
    const total = parseFloat(item.dataset.value);
    const quantity = parseInt(quantityData);
    const profit = parseFloat(profitData[getMonthFromBar(item)]);
    const totalValue = await getTotalMonthQuantity();
    const monthTotalValue = parseFloat(monthTotalElement.dataset.value + selectItem ? total : -total);
    totalB.innerText = "Total: " + totalValue;

    monthTotalElement.dataset.value = monthTotalValue;
    monthTotalElement.textContent = formatGBP(monthTotalValue);

    monthInvoices.dataset.value = parseInt(monthInvoices.dataset.value) + (selectItem ? quantity : -quantity);
    monthInvoices.innerText = monthInvoices.dataset.value;

    monthProfit.dataset.value = parseFloat(monthProfit.dataset.value) + (selectItem ? profit : -profit);
    monthProfit.innerText = formatGBP(parseFloat(monthProfit.dataset.value));

    if (total != 0 && profit != 0 && selectedMonths.length > 0) {
        monthMargin.innerText = (monthProfit.dataset.value / monthTotalElement.dataset.value * 100).toFixed(2) + "%";
    } else {
        monthMargin.innerText = "0%";
    }

    setPieChartB(item, selectItem);
}
function getMonthFromBar(item) {
    var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var month = months.indexOf(item.lastElementChild.innerText);
    return month;
}

async function getTotalMonthQuantity() {
    var total = 0;
    const data = await fetchDataPost("dbh/data_handler.php", "get_item_name_totals");
    console.log(data);
    for (var i = 0; i < selectedMonths.length; i++) {
        let itemNameTotals = JSON.parse(data)[selectedMonths[i]];
        for (var k = 0; k < itemNameTotals.length; k++) {
            total += parseInt(itemNameTotals[k][1])
        }
    }
    return total;
}

async function getMonthInvoiceQuantity(item) {
    var month = getMonthFromBar(item);
    const data = await fetchDataPost("dbh/data_handler.php", "get_invoice_months");
    var totalInvoiceMonths = JSON.parse(data)[month];
    return totalInvoiceMonths;
}

async function setPieChartB(item, set) {
    if (selectedMonths.length != 0) {
        var month = getMonthFromBar(item);
        var itemNameTotals = [];
        var itemNames = [];
        var total = 0;
        const data = await fetchDataPost("dbh/data_handler.php", "get_item_name_totals");
        for (var i = 0; i < selectedMonths.length; i++) {
            itemNameTotals[i] = JSON.parse(data)[selectedMonths[i]];
            for (var k = 0; k < itemNameTotals[i].length; k++) {
                total += parseInt(itemNameTotals[i][k][1]);
                if (!itemNames.includes(itemNameTotals[i][k][0])) {
                    itemNames.push(itemNameTotals[i][k][0]); 
                }
            }
        }
        var cleanedData = [];
        for (var i = 0; i < itemNames.length; i++) {
            cleanedData[i] = new Array(itemNames[i], 0);
            for (var k = 0; k < itemNameTotals.length; k++) {
                for (var m = 0; m < itemNameTotals[k].length; m++) {
                    if (itemNameTotals[k][m][0] == itemNames[i]) {
                        cleanedData[i][1] += parseInt(itemNameTotals[k][m][1]);
                    }
                }
            }
        }
        var sortedData = sortItemNameQuantity(cleanedData).slice(0, 4);
        currentTotal = 0;
        for (var i = 1; i < sortedData.length + 1; i++) {
            currentTotal += parseInt(sortedData[i-1][1]) / total * 100;
            if (i == 4) {
                currentTotal = 100;
            }
            document.getElementById("pie-a-"+i).setAttribute("stroke-dasharray", currentTotal + ", 100");
            document.getElementById("pie-a-"+i).setAttribute("data-value", "Quantity: " + sortedData[i-1][1]);
            var itemName = sortedData[i-1][0];
            document.getElementById("key-a-"+i).textContent = itemName;
            document.getElementById("key-a-"+i).setAttribute("data-value", sortedData[i-1][0]);
        }
    } else {
        for (var i = 1; i < 5; i++) {
            document.getElementById("pie-a-"+i).setAttribute("stroke-dasharray", i*25 + ", 100");
            document.getElementById("pie-a-"+i).setAttribute("data-value", "");
            document.getElementById("key-a-"+i).textContent = "";
            document.getElementById("key-a-"+i).setAttribute("data-value", "");
        }
    }
}

function sortItemNameQuantity(cleanedData) {
    var sorted = false;
    var tempValue;
    var tempName;
    while (!sorted) {
        sorted = true;
        for (var i = 0; i < cleanedData.length - 1; i++) {
            if (cleanedData[i][1] < cleanedData[i+1][1]) {
                tempName = cleanedData[i][0];
                tempValue = cleanedData[i][1];
                cleanedData[i][0] = cleanedData[i+1][0];
                cleanedData[i][1] = cleanedData[i+1][1];
                cleanedData[i+1][0] = tempName;
                cleanedData[i+1][1] = tempValue;
                sorted = false;
            }
        }
    }
    return cleanedData;
}

function selectMonth(item) {
    var monthIndex = getMonthFromBar(item);
    if (item.classList.contains("selected")) {
        item.classList.remove("selected");
        selectedMonths.splice(selectedMonths.indexOf(monthIndex), 1);
        return false;
    } 
    item.classList.add('selected');
    selectedMonths.push(monthIndex);
    return true;
}
function selectItem(item) {
    if (item.classList.contains("selected")) {
        item.classList.remove("selected");
        return false;
    }
    if (selectedItem != null) {
        selectedItem.classList.remove("selected");
    }
    item.classList.add('selected');
    selectedItem = item;
    return true;
}

function showTooltip() {
    for (var i = 1; i < 5; i++) {
        manageTooltipEvents(document.getElementById("pie-a-" + i), document.getElementById("tooltip-pie-a"));
        manageTooltipEvents(document.getElementById("key-a-" + i), document.getElementById("tooltip-key-a-" + i));
    }
    manageTooltipEvents(document.getElementById("pie-b-1"), document.getElementById("tooltip-pie-b"))
    manageTooltipEvents(document.getElementById("pie-b-2"), document.getElementById("tooltip-pie-b"))
    for (var i = 0; i < 5; i++) {
        manageTooltipEvents(document.getElementById("c-bar-data-" + i), document.getElementById("tooltip-bar-b-" + i));
    }
    var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    for (var i = 0; i < 12; i++) {
        manageTooltipEvents(document.getElementById(months[i] + "-bar-a"), document.getElementById(months[i] + "-tooltip-bar-a"));
    }
    manageTooltipEvents(document.getElementById("b-bar-out"), document.getElementById("tooltip-b-bar-out"));
    manageTooltipEvents(document.getElementById("b-bar-in"), document.getElementById("tooltip-b-bar-in"));
    manageTooltipEvents(document.getElementById("c-bar-out"), document.getElementById("tooltip-c-bar-out"));
    manageTooltipEvents(document.getElementById("c-bar-in"), document.getElementById("tooltip-c-bar-in"));
}
function manageTooltipEvents(svgElement, tooltipElement) {
    if (svgElement != null) {
        svgElement.addEventListener("mousemove", function(event) {
            var rect = svgElement.getBoundingClientRect();
            var x = event.clientX - rect.left + 30;
            var y = event.clientY - rect.top - 10;
            if (svgElement.id.includes("key") || svgElement.id.includes("data")) {
                x += 10;
            }
            
            tooltipElement.style.top = y + "px";
            tooltipElement.style.left = x + "px";
        });

        svgElement.addEventListener("mouseenter", function() {
            tooltipElement.innerText = svgElement.dataset.value;
            if (tooltipElement.innerText != "" && tooltipElement.innerText != "undefined") {
                tooltipElement.style.display = "block";
            }
        });

        svgElement.addEventListener("mouseleave", function() {
        tooltipElement.style.display = "none";
        });
    }
}