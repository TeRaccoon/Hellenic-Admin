var allSelected = false;
var selected = [];

function checkSelect() {
    if (selected.length == 0) {
        displayErrorForm("No items selected!");
        return false;
    }
    return true;
}

function clearFilters() {
    var tables = getTables();
    document.getElementById("advanced-filter").value = "";
    document.getElementById("filter").value = "";
    for (k = 0; k < tables.length; k++) {
        var rows = tables[k].rows;
        var columnLength = rows[0].cells.length - 1;
        for (i = 1; i < rows.length; i++) {
            rows[i].style.display = "";
        }
        for (i = 0; i < columnLength - 1; i++) {
            rows[0].getElementsByTagName("TH")[i].style.display = "";
            for (k = 1; k < rows.length; k++) {
                rows[k].getElementsByTagName("TD")[i].style.display = "";
            }
        }
    }
}

function filterTable() {
    var tables = getTables();
    var column = document.getElementById("column-select").value;
    var filter = document.getElementById("advanced-filter").value.toUpperCase();
    for (k = 0; k < tables.length; k++) {
        var rows = tables[k].rows;
        for (i = 1; i < rows.length; i++) {
            var item = rows[i].getElementsByTagName("TD")[column].innerHTML.toUpperCase();
            if (item.indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
}

function searchTableFilter(table, column, filter, d) {
    filter = filter.toUpperCase();
    var rows = table.rows;
    for (i = 1; i < rows.length; i++) {
        var item = rows[i].getElementsByTagName("TD")[column].innerHTML.toUpperCase();
        if (item.indexOf(filter) != -1) {
            rows[i].style.display = "";
        } else {
            if (d) {
                table.deleteRow(i);
                i--;
            } else {
                rows[i].style.display = "none";
            }
        }
    }
}

function searchTableDateFilter(table, column, filter, d) {
    var currentDate = new Date();
    var currentYear = currentDate.getFullYear();
    var currentMonth = currentDate.getMonth() + filter;
    var currentDay = currentDate.getDate();
    var rows = table.rows;
    for (i = 1; i < rows.length; i++) {
        var expiryDate = rows[i].getElementsByTagName("TD")[column].innerHTML.toUpperCase();
        var inputYear = parseInt(expiryDate.substring(0, 4));
        var inputMonth = parseInt(expiryDate.substring(5, 7));
        var inputDay = parseInt(expiryDate.substring(8, 10));
        var monthDiff = (inputYear - currentYear) * 12 + (inputMonth - currentMonth);
        if (monthDiff <= 1 && (inputDay <= currentDay || monthDiff < 1)) {
            rows[i].style.display = "";
        } else {
            if (d) {
                table.deleteRow(i);
                i--;
            } else {
                rows[i].style.display = "none";
            }
        }
    }
}

function searchTable(d) {
    var tables = getTables();
    var filter = document.getElementById("filter").value.toUpperCase();
    for (k = 0; k < tables.length; k++) {
        var rows = tables[k].rows;
        var columnLength = rows[0].cells.length - 1;
        var located = false;
        for (i = 1; i < rows.length; i++) {
            for (h = 1; h < columnLength; h++) {
                var item = rows[i].getElementsByTagName("TD")[h].innerHTML.toUpperCase();
                if (item.indexOf(filter) > -1) {
                    located = true
                }
            }
            if (located == false) {
                if (d) {
                    tables[k].deleteRow(i);
                    i--;
                } else {
                    rows[i].style.display = "none";
                }
            } else {
                rows[i].style.display = "";
                located = false;
            }
        }
    }
}

function resetTableHeaders(table) {
    var rows = table.rows;
    var columnLength = rows[0].cells.length - 1;
    for (var i = 0; i < columnLength; i++) {
        if (rows[0].getElementsByTagName("TH")[i].lastChild.lastChild != null) {
            rows[0].getElementsByTagName("TH")[i].lastChild.remove();
        }
    }
}

function sortTable(tableHeader, table, n) {
    n++;
    var rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    resetTableHeaders(table);
    switching = true;
    dir = "asc";
    var integerSort = isColumnAllIntegers(table, n)
    const p = document.createElement('p');
    p.className = 'material-icons';
    p.textContent = 'arrow_upward';
    while (switching) {
        switching = false;
        rows = table.rows;
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            if (integerSort) {
                var intX = parseInt(x.innerHTML.toLowerCase().trim());
                var intY = parseInt(y.innerHTML.toLowerCase().trim());
                if (dir == "asc") {
                    if (intX > intY) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == "desc") {
                    if (intX < intY) {
                        shouldSwitch = true;
                        break;
                    }
                }
            } else {
                if (dir == "asc") {
                    console.log(x);
                    console.log(y);
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == "desc") {
                    if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount++;
        } else {
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                p.textContent = 'arrow_downward';
                switching = true;
            }
        }
    }
    tableHeader.appendChild(p);
}

function select(tr, checkbox) {
    if (event.target.tagName === 'INPUT') {
        checkbox = true;
    }
    var rowIndex = tr.rowIndex;
    if (!checkbox) {
        tr.children[0].lastChild.checked = !tr.children[0].lastChild.checked;
    }
    if (!selected.includes(rowIndex)) {
        selected.push(rowIndex);
        tr.classList.add('row-selected');
        if (checkbox) {
            tr.children[0].lastChild.checked = true;
        }
    } else {
        tr.classList.remove('row-selected');
        selected.pop(rowIndex);
        if (checkbox) {
            tr.children[0].lastChild.checked = false;
        }
    }
    console.log("Selected items: " + selected);
}


function selectAll() {
    var tables = getTables();
    allSelected = !allSelected;
    selected = [];
    for (k = 0; k < tables.length; k++) {
        var rows = tables[k].rows;
        if (allSelected) {
            for (i = 1; i < rows.length; i++) {
                document.getElementById("select-" + (i - 1)).checked = true;
                selected[i - 1] = i;
                rows[i].classList.add('row-selected');
            }
        } else {
            for (i = 1; i < rows.length; i++) {
                document.getElementById("select-" + (i - 1)).checked = false;
                rows[i].classList.remove('row-selected');
            }
            selected = [];
        }
    }
    console.log("Selected items: " + selected);
}

function deleteMode() {
    var tables = getTables();
    for (k = 0; k < tables.length; k++) {
        var rows = tables[k].rows;
        var columnLength = rows[0].cells.length;
        var button = document.getElementById("toolbar-icon-2");
        if (button == null) { // if the table is in edit mode
            for (i = 1; i < rows.length; i++) {
                var item = rows[i].getElementsByTagName("TD")[columnLength];
                item.lastChild.innerHTML = "&#xe3c9;";
                item.setAttribute("onclick", "displayEditForm(" + k + ", " + (i - 1) + ");");
            }
            button = document.getElementById("toolbar-icon-2");
            button.innerHTML = "&#xe872;"
        } else {
            for (i = 1; i < rows.length; i++) {
                var item = rows[i].getElementsByTagName("TD")[columnLength - 1];
                item.lastChild.innerHTML = "&#xe872;";
                item.setAttribute("onclick", "displayDeleteForm(" + k + ", " + (i - 1) + ");");
            }
            button.innerHTML = "&#xe3c9;"
        }
    }
}

function findColumnIndexByName(table, name) {
    name = name.toUpperCase();
    for (i = 0; i < table.rows[0].getElementsByTagName("TH").length; i++) {
        var column = table.rows[0].getElementsByTagName("TH")[i].innerHTML.toUpperCase();
        if (column == name) {
            return i;
        }
    }
    console.log("findColumnIndexByName ERROR: Could not find column!");
    return -1;
}

function clearEditColumn(table) {
    var rows = table.rows;
    var columnLength = rows[0].cells.length;
    for (i = 1; i < rows.length; i++) {
        var item = rows[i].getElementsByTagName("TD")[columnLength];
        item.style.display = "none";
    }
}

function clearEditColumns(tables) {
    for (k = 0; k < tables.length; k++) {
        var rows = tables[k].rows;
        var columnLength = rows[0].cells.length;
        for (i = 1; i < rows.length; i++) {
            var item = rows[i].getElementsByTagName("TD")[columnLength - 1];
            item.innerText = "";
            item.onclick = null;
        }
    }
}

function getTables() {
    return document.getElementsByTagName("TABLE");
}

function removeEmptyTable() {
    var tables = getTables();
    for (k = 0; k < tables.length; k++) {
        if (tables[k].rows.length < 2) {
            tables[k].remove();
            k = k - 1;
        }
    }
}

function viewAssoc() {
    if (selected.length == 0) {
        displayErrorForm("Nothing selected!");
    } else {
        var inputs = document.getElementById("view-assoc-form").elements;
        var rowData = getTables()[0].rows[selected[0]].getElementsByTagName("TD");
        for (i = 0; i < inputs.length - 1; i++) {
            inputs[i + 1].value = rowData[i].innerText;
        }
        document.getElementById("view-assoc-form").submit();
    }
}

function getUniqueRowFromColumn(table, columnToFind) {
    var data = [];
    var rows = table.rows;
    var columnLength = rows[0].cells.length - 1;
    for (var i = 0; i < columnLength; i++) {
        if (rows[0].getElementsByTagName("TH")[i].innerText == columnToFind) {
            for (var k = 1; k < rows.length; k++) {
                if (!data.includes(rows[k].getElementsByTagName("TD")[i].textContent)) {
                    data[data.length] = rows[k].getElementsByTagName("TD")[i].textContent;
                }
            }
            i = columnLength;
        }
    }
    return data;
}

function selectTab(tab, tableName, tableElementName, filter) {
    const tableContainer = document.querySelector('#' + tableElementName);
    const editFormContainer = document.getElementById("edit-form-container");
    const addFormContainer = document.getElementById("add-form-container");
    if (tableContainer != null) {
        fetch(`dbh/data_handler.php?tab=${tab}&table=${tableName}`)
            .then(response => response.text())
            .then(data => {
                tableContainer.innerHTML = data;
                colourCodeTable();
            });
        fetch(`dbh/data_handler.php?generateEditForm=true&table=${tableName}&filter=${filter}`)
            .then(response => response.text())
            .then(data => {
                editFormContainer.innerHTML = data;
                selectizeForms();
                formatDates();
            });
        fetch(`dbh/data_handler.php?generateAddForm=true&table=${tableName}&filter=${filter}`)
            .then(response => response.text())
            .then(data => {
                addFormContainer.innerHTML = data;
                selectizeForms();
                formatDates();
            });
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(tabElement => {
            if (tabElement.querySelector('h1').onclick.toString().includes(`'${tab}'`)) {
                tabElement.classList.add('tab-selected');
            } else {
                tabElement.classList.remove('tab-selected');
            }
        });
    }
    formatDates();
}

function toggleValue(element, n, table) {
    console.log(element.value);
    if (element.value == "Yes") {
        console.log("ding");
    }
    element.value = element.checked ? 'Yes' : 'No';
    element.parentElement.dataset.value = element.value;
    console.log(element.value);
    constructEditForm(n + 1, table);
    document.querySelector('#edit-form').submit();
}

function colourCodeTable() {
    var table = getTables()[0];
    var rows = table.rows;
    var columnLength = rows[0].cells.length - 1;
    for (var i = 0; i < columnLength; i++) {
        if (rows[0].getElementsByTagName("TH")[i].innerText == "Status") {
            for (var k = 1; k < rows.length; k++) {
                var element = rows[k].getElementsByTagName("TD")[i];
                if (element.innerText == "Overdue") {
                    element.style.backgroundImage = "linear-gradient(to right, rgba(255, 0, 0, 0.2), rgba(255, 0, 0, 0.2))";
                } else if (element.innerText == "Pending") {
                    element.style.backgroundImage = "linear-gradient(to right, rgba(255, 120, 1, 0.2), rgba(255, 120, 0, 0.2))";
                } else {
                    element.style.backgroundImage = "linear-gradient(to right, rgba(0, 255, 0, 0.2), rgba(0, 255, 0, 0.2))";
                }
            }
        }
    }
}

function selectizeForms() {
    $('select').selectize({});
}

function formatDates() {
    $(".form-datepicker").datepicker({
        dateFormat: "yy-mm-dd"
    });
}