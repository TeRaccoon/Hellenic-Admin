function loadElement(file, elementID, callback) {
    var container = document.getElementById(elementID);
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "templates/" + file, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            container.insertAdjacentHTML('beforeend', xhr.responseText);
            if (callback && typeof callback === 'function') {
                callback();
            }
        }
    };
    xhr.send();
}

function configureWidgets(widgetNumber, widgetTitle, widgetIcon, widgetValue, widgetTextValue, widgetText) {
    document.getElementById("widget-title-" + widgetNumber).innerHTML = widgetTitle;
    document.getElementById("widget-icon-" + widgetNumber).innerHTML = widgetIcon;
    collectWidgetData(widgetValue).then(data => {
        widgetValue = data;
        if (data.length > 15) {
            widgetValue = data.substring(0, 15) + "...";
        }
        document.getElementById("widget-value-" + widgetNumber).innerHTML = widgetValue;
    });
    collectWidgetData(widgetTextValue).then(data => {
        document.getElementById("widget-text-value-" + widgetNumber).innerHTML = data;
    });
    document.getElementById("widget-text-" + widgetNumber).lastChild.textContent = widgetText;
}

function collectWidgetData(dataName) {
    return fetch(`dbh/query_handler.php?query=${dataName}`)
        .then(response => response.text())
        .then(data => {
            return data;
        });
}

function collectWidgetDataDateRange(dataName, startDate, endDate) {
    return fetch(`dbh/query_handler.php?query=${dataName}&startDate=${startDate}&endDate=${endDate}`)
        .then(response => response.text())
        .then(data => {
            return data;
        });
}

function disableToolbarButton(buttonIndex) {
    var button = document.getElementById("toolbar-button-" + buttonIndex);
    var child = button.childNodes;
    child[1].innerHTML = "";
}

function setToolbar(buttonIndex, onclick, icon) {
    var button = document.getElementById("toolbar-icon-" + buttonIndex);
    console.log(button);
    button.innerHTML = icon;
    button.parentElement.setAttribute("onclick", onclick);
}

function generatePieChart(id, sections, appendeeElement) {
    var svg = document.createElement('svg');
    svg.setAttribute('viewBox', '0 0 36 36');
    svg.setAttribute('class', 'circular-chart');
    for (var i = sections; i > 0; i--) {
        var path = document.createElement('path');
        path.setAttribute('id', id + i);
        path.setAttribute('class', 'circle pie');
        path.setAttribute('stroke-dasharray', (100 / (sections - i + 1)) + ', 100');
        path.setAttribute('d', 'M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831');
        svg.appendChild(path);
    }
    appendeeElement.appendChild(svg);
}

function generateKeys(id, hasTooltip, keys, appendeeElement, keyText) {
    var colourClasses = ['green', 'blue', 'orange', 'purple'];
    for (var i = 1; i < keys + 1; i++) {
        var key = document.createElement('p');
        key.setAttribute('class', 'key');
        if (hasTooltip) {
            key.appendChild(generateTooltip(id, '-' + i));
        }
        var box = document.createElement('div');
        box.setAttribute('class', 'box ' + colourClasses[4 - keys + i - 1]);
        key.appendChild(box);
        var text = document.createElement('span');
        text.setAttribute('id', id + i);
        if (keyText != null) {
            text.innerText = keyText[i - 1]
        }
        key.appendChild(text);
        appendeeElement.appendChild(key);
    }
}

function generateInOutBars(id, appendeeElement) {
    for (var i = 0; i < 2; i++) {
        var group = document.createElement('div');
        group.setAttribute('class', 'month-group');
        var bar = document.createElement('div');
        bar.setAttribute('class', i == 0 ? 'bar red' : 'bar green');
        bar.setAttribute('id', i == 0 ? id + 'out' : id + 'in');
        bar.setAttribute('style', 'height:100px;')
        bar.appendChild(generateTooltip(i == 0 ? id + 'out' : id + 'in', ''));
        group.appendChild(bar);
        var p = document.createElement('p');
        p.setAttribute('class', 'month');
        p.innerText = i == 0 ? "Out" : "In";
        group.appendChild(p);
        appendeeElement.appendChild(group);
    }
}

function generateTooltip(id, i) {
    var graphContainer = document.createElement('div');
    graphContainer.setAttribute('class', 'graph-container');
    var tooltip = document.createElement('div');
    tooltip.setAttribute('class', 'tooltip');
    tooltip.setAttribute('id', 'tooltip-' + id + i);
    graphContainer.appendChild(tooltip);
    return graphContainer;
}

function clearElement(parentElement, elementToAvoid) {
    var child = parentElement.firstChild;
    while (child) {
        var nextChild = child.nextSibling;
        if (child !== elementToAvoid) {
            parentElement.removeChild(child);
        }
        child = nextChild;
    }
}

function setTrialBalanceTable() {
    clearElement(document.getElementById('trial-balance-header'), document.getElementById('trial-balance-widget'));
    var startDate = document.getElementById('trial-balance-start').value;
    var endDate = document.getElementById('trial-balance-end').value;
    collectWidgetDataDateRange("account_balances", startDate, endDate).then(data => {
        generateTrialBalanceTable(JSON.parse(data));
    });
}

function generateTrialBalanceTable(data) {
    var parentElement = document.getElementById('trial-balance-widget');
    var startDate = new Date(document.getElementById('trial-balance-start').value);
    let table = createElementWithClass("table", "data-table");


    var headerRow = table.insertRow();
    headerRow.setAttribute('class', 'table-heading');

    createTextElement("th", null, "Account", headerRow);
    createTextElement("th", null, "Debit", headerRow);
    createTextElement("th", null, "Credit", headerRow);


    var totalDebit = 0.00;
    var totalCredit = 0.00;
    if (data.length != 0) {
        for (var i = 0; i < data.length; i++) {
            var row = table.insertRow();
            row.insertCell().textContent = data[i]['account_code'];
            row.insertCell().textContent = Number(data[i]['total_debit']).toFixed(2);
            row.insertCell().textContent = Number(data[i]['total_credit']).toFixed(2);
            totalDebit += Number(Number(data[i]['total_debit']).toFixed(2));
            totalCredit += Number(Number(data[i]['total_credit']).toFixed(2));
        }
    } else {
        var row = table.insertRow();
        row.insertCell().textContent = "There was no data for the given months!";
        row.insertCell();
        row.insertCell();
    }

    var row = table.insertRow();
    row.style.textDecoration = "underline";
    row.style.fontWeight = "bold";
    row.insertCell().textContent = "Total";
    row.insertCell().textContent = totalDebit;
    row.insertCell().textContent = totalCredit;

    var row = table.insertRow();
    row.style.textDecoration = "underline";
    row.style.fontWeight = "bold";
    row.insertCell().textContent = "Difference";
    row.insertCell().textContent = Math.abs(totalDebit - totalCredit);
    row.insertCell();

    document.body.appendChild(table);
    parentElement.appendChild(table);
}

function createTable(parentElement, headers, data, id, assoc) {
    if (document.getElementById(id) == null) {
        var table = document.createElement("table");
        table.setAttribute('id', id)
        table.setAttribute('class', 'data-table');
        var headerRow = table.insertRow();
        headerRow.setAttribute('class', 'table-heading');
        if (!assoc) {
            for (var i = 0; i < headers.length; i++) {
                var cell = document.createElement("th");
                cell.textContent = headers[i];
                headerRow.appendChild(cell);
            }
            for (var i = 0; i < data.length; i++) {
                var dataRow = table.insertRow();
                console.log(data[i]);
                for (var k = 0; k < data[i].length; k++) {
                    dataRow.insertCell().textContent = data[i][k];
                }
            }
        } else {

        }
        var pageBreak = document.createElement('br');
        parentElement.appendChild(table);
        parentElement.appendChild(pageBreak);
    }
}

function createStartEndDatePickers(parentElement, ids, innerText, includeCalculate, calculateOnClick) {
    for (var i = 0; i < ids.length; i++) {
        var dateLabel = document.createElement('label');
        dateLabel.setAttribute('for', ids[i]);
        dateLabel.innerText = innerText[i];
        var datePicker = document.createElement('input');
        datePicker.setAttribute('type', 'text');
        datePicker.setAttribute('class', 'form-control form-datepicker');
        datePicker.setAttribute('id', ids[i]);
        datePicker.setAttribute('autocomplete', 'off');
        datePicker.setAttribute('style', 'font-family:Source Code Pro, FontAwesome');
        datePicker.setAttribute('placeholder', '\uf073');
        parentElement.appendChild(dateLabel);
        parentElement.appendChild(datePicker);
    }
    if (includeCalculate) {
        var calculateButton = document.createElement('button');
        calculateButton.setAttribute('type', 'button');
        calculateButton.setAttribute('class', 'button');
        calculateButton.addEventListener('click', calculateOnClick);
        calculateButton.innerText = "Calculate";

        parentElement.appendChild(calculateButton);
    }
}

function createDateButtons(parentElements, buttonLabels, buttonText, buttonOnClicks) {
    for (var i = 0; i < buttonLabels.length; i++) {
        var titleLabel = document.createElement('h4');
        titleLabel.innerText = buttonLabels[i];
        parentElements[i].appendChild(titleLabel);
        for (var k = 0; k < 4; k++) {
            var button = document.createElement('button');
            button.setAttribute('type', 'button');
            button.setAttribute('class', 'button');
            button.addEventListener('click', buttonOnClicks[i][k]);
            parentElements[i].appendChild(button);
            button.innerText = buttonText[k];
        }
    }
}

function generateProfitLossTable(data) {
    const parentElement = document.getElementById("table-placeholder");
    var headers = ["Sales Revenue", "Cost of Sales", "Gross Profit", "Expenses", "Net Profit"];
    var startDate = document.getElementById('profit-loss-start').value;
    var endDate = document.getElementById('profit-loss-end').value;
    if (document.getElementById("profit-loss-table" + startDate + "-" + endDate) == null) {
        setTabs(null, ["date-range"], ["From:" + (startDate.replace(/-/g, "/") + " To:" + endDate.replace(/-/g, "/"))], false, true);
    }
    createTable(parentElement, headers, data, "profit-loss-table" + startDate + "-" + endDate);
}

function calculateSalesRevenue() {
    var startDate = document.getElementById('profit-loss-start').value;
    var endDate = document.getElementById('profit-loss-end').value;
    Promise.all([collectWidgetDataDateRange("sales_revenue", startDate, endDate), collectWidgetDataDateRange("sales_cost", startDate, endDate), collectWidgetDataDateRange("expenses", startDate, endDate)])
        .then(([sales_revenue, sales_cost, expenses]) => {
            //Total Sales Revenue
            var salesRevenueData = JSON.parse(sales_revenue);
            var totalSalesRevenue = 0;
            for (var i = 0; i < salesRevenueData.length; i++) {
                totalSalesRevenue += Number(Number(salesRevenueData[i]['total_profit']).toFixed(2));
            }

            //Cost of Sales
            var salesCostData = Number(Number(JSON.parse(sales_cost)[0]['total_cost']).toFixed(2));

            //Gross Profit
            var grossProfit = Number(Number(totalSalesRevenue) - Number(salesCostData)).toFixed(2);

            //Expenses
            var totalExpenses = Number(expenses).toFixed(2);

            //Net Profit
            var netProfit = Number(grossProfit - totalExpenses).toFixed(2);

            generateProfitLossTable([
                [totalSalesRevenue, salesCostData, grossProfit, totalExpenses, netProfit]
            ]);
        });
}

function setDebtorCreditorTable(query, tabTitle) {
    clearElement(document.getElementById("table-placeholder"), document.getElementById('tabs'));
    clearElement(document.getElementById('tabs'), null);
    fetch(`dbh/query_handler.php?query=` + query)
        .then(response => response.text())
        .then(data => {
            queryData = JSON.parse(data);
            console.log(queryData);
            const parentElement = document.getElementById("table-placeholder");
            if (queryData.length == 0) {
                createTable(parentElement, ["No data!"], [], "debtor-creditor-table");
            } else {
                var rows = [];
                for (var i = 0; i < queryData[0].length; i++) {
                    var row = [];
                    for (var j = 0; j < queryData.length; j++) {
                        if (i == queryData[0].length - 1) {
                            queryData[j][i] = "£" + Number(queryData[j][i]).toFixed(2);
                        }
                        row.push(queryData[j][i]);
                    }
                    rows.push(row);
                }
                setTabs(null, ["debtor-creditor-data"], [tabTitle], false, true);
                createTable(parentElement, ["Customer ID", "First Name", "Surname", "Total Debt"], queryData, "debtor-creditor-table");
            }
        });
}

function extendSearchBar() {
    const searchBar = document.getElementById('search-bar');
    searchBar.classList.remove("item9");
    searchBar.classList.add("full-width-3");
}

function removeToolbarButtons() {
    for (var i = 1; i < 5; i++) {
        const element = document.getElementById("toolbar-button-" + i);
        element.replaceChildren();
        element.remove();
    }
}

function constructInfoWidget(dataLocation) {
    fetch(dataLocation)
        .then(response => response.json())
        .then(data => {
            const parentElement = document.getElementById("widget-placeholder");
            let container = createElementWithClass("div", "item13-alt");
            container.classList.add("card");
            createAndAppendValue(container, "h2", null, null, "Offer Codes", true);
            let list = createElementWithClass("ul", "info-list");
            data.forEach(item => {
                let code = item["code"];
                let resolved = item["resolved"];
                let line = "Code: " + code + "\r\nDescription: " + resolved;
                createAndAppendValue(list, "li", null, null, line, true);
            });
            container.appendChild(list);
            parentElement.appendChild(container);
        })
        .catch(error => console.error('Error fetching data:', error));
}

function createElementWithClass(elementType, className) {
    const element = document.createElement(elementType);
    if (className) {
        element.setAttribute("class", className);
    }
    return element;
}

function createAndAppendValue(parent, elementType, className, id, textContent, append) {
    const element = createElementWithClass(elementType, className);
    if (id) {
        element.id = id;
    }
    if (textContent) {
        element.textContent = textContent;
    }
    if (append) {
        parent.appendChild(element);
        return null;
    }
    return element;
}

function createHeader(textContent) {
    const header = document.createElement("h3");
    header.textContent = textContent;
    return header;
}

function createAndAppendValueContainer(parent, elementType, className, id, textContent, datasetValue) {
    const container = createElementWithClass(elementType, className);
    if (id) {
        container.id = id;
    }
    const value = createHeader(textContent);
    if (datasetValue) {
        container.dataset.value = datasetValue;
    }
    container.appendChild(value);
    parent.appendChild(container);
}