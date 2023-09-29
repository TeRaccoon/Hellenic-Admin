function collectErrorData() {
    fetch(`dbh/error_handler.php?checkError=true`)
        .then(response => response.text())
        .then(data => {
            generateErrorForm(data);
        });
}

function generateErrorForm(rawData) {
    const data = JSON.parse(rawData);
    const errorData = data[0];
    const submittedData = data[1];
    if (errorData[0] != "" && errorData[0] != null) {
        //Error type
        switch (errorData[2]) {
            case "add":
                var elements = document.getElementById("add-form").elements;
                var elementIndex = 2;
                for (var i = 0; i < elements.length - 3; i++, elementIndex++) {
                    elements[elementIndex].value = submittedData[i];
                    if (elements[elementIndex].nodeName == "SELECT") {
                        elementIndex++;
                    }
                }
                var errorMsg = document.getElementById("add_error");
                errorMsg.innerText = errorData[0];
                document.getElementById('add-form-container').style.display = 'block';
                break;
            case "delete":


            default:
                var errorMsg = document.getElementById("error-form-message");
                document.getElementById("error-form-header").innerText = "Error code: " + data[2];
                errorMsg.innerText = errorData[0] + "\nError description: " + data[3];
                document.getElementById('error-form').style.display = 'block';
                break;
                break;
        }
    }
}

function displayErrorForm(errorMsg) {
    document.getElementById('error-form-message').innerText = errorMsg;
    document.getElementById('error-form').style.display = "block";
}