function isColumnAllIntegers(table, columnIndex) {
    var rows = table.rows;
    for (var i = 1; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName('td');
        var cellValue = cells[columnIndex].textContent.trim();
        if (!Number.isInteger(parseInt(cellValue)) || cellValue.includes("-")) {
            return false;
        }
    }
    return true;
}

function isValidDate(dateString) {
    var dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    return dateRegex.test(dateString);
}

function isInt(value) {
    return !isNaN(value) &&
        parseInt(Number(value)) == value &&
        !isNaN(parseInt(value, 10));
}

function isFloat(value) {
    const floatValue = parseFloat(value);
    return Number.isFinite(floatValue) && Math.floor(floatValue) !== floatValue;
}

function dayDifferenceTotal() {
    var element = document.getElementById("widget-text-value-1");
    var difference = element.innerText;
    if (difference >= 0) {
        document.getElementById("widget-text-1").lastChild.textContent = " more than yesterday";
        element.innerText = difference;
        element.classList.add('text-success');
    } else {
        difference = Math.abs(difference);
        document.getElementById("widget-text-1").lastChild.textContent = " less than yesterday";
        element.innerText = Math.abs(difference);
        element.classList.add('text-unsuccess');
    }
}

function formatString(str) {
    return str
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

function checkBooks(table) {
    var debitTotal = 0;
    var creditTotal = 0;
    var rows = table.rows;
    for (var i = 1; i < rows.length; i++) {
        var cells = rows[i].cells;
        debitTotal += parseFloat(cells[1].textContent) || 0;
        creditTotal += parseFloat(cells[2].textContent) || 0;
    }
    if (debitTotal === creditTotal) {
        document.getElementById('message-form-title').innerText("The books are balanced");
        document.getElementById('message-form').style.display = 'block';
    } else {
        console.log("The books are not in balance!");
    }
}

function applyDebt() {
    if (checkSelect()) {
        var table = getTables()[0];
        var rows = table.rows;
        var customerIDs = [];
        for (i = 0; i < selected.length; i++) {
            var row = rows[selected[i]].getElementsByTagName("TD");
            customerIDs[i] = [row[1].innerText, row[9].dataset.value, row[12].dataset.value];
        }
        $.post("dbh/manage_data.php", {action: 'apply_debt', customer_ids: customerIDs}, function(data) {
            location.reload();
        });
    }
}

async function collectData(query, filter) {
    if (filter == null) {
        const response = await fetch(`dbh/data_collector.php?query=${query}`);
        const data = await response.text();
        return data;
    } else {
        const response_1 = await fetch(`dbh/data_collector.php?query=${query}&filter=` + encodeURIComponent(filter));
        const data_1 = await response_1.text();
        return data_1;
    }
}

async function fetchData(path, query, filter) {
    if (filter == null) {
        const response = await fetch(path + `?query=${query}`);
        if (response.ok) {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                return response.json();
            } else {
                return response.text();
            }
        }
        return Promise.reject(response);
    } else {
        const filterResponse = await fetch(path + `?query=${query}&filter=` + encodeURIComponent(filter));
        if (filterResponse.ok) {
            return filterResponse.json();
        }
        return Promise.reject(filterResponse);
    }
}

async function fetchDataPost(path, query, filter) {
    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=${query}` + (filter ? `&filter=${encodeURIComponent(filter)}` : '')
    };

    const response = await fetch(path, options);
    if (response.ok) {
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            return response.json();
        } else {
            return response.text();
        }
    }
    else {
        console.log(response);
        console.error("Fetch error:", response.status);
    }
    return Promise.reject(response);
}

function formatGBP(number) {
    return number.toLocaleString('en-US', { style: 'currency', currency: 'GBP' });
}