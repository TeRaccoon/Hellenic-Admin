function loadElement(file, elementID, callback, overwrite) {
    var container = document.getElementById(elementID);
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "templates/" + file, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (overwrite) {
                container.innerHTML = xhr.responseText;
            } else {
                container.insertAdjacentHTML('beforeend', xhr.responseText);
            }
            if (callback && typeof callback === 'function') {
                setTimeout(callback, 0)
            }
        }
    };
    xhr.send();
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

function createElementWithClass(elementType, className, parent) {
    const element = document.createElement(elementType);
    if (className) {
        element.setAttribute("class", className);
    }
    if (parent) {
        parent.appendChild(element);
    }
    return element;
}

function createElement(elementType, classString, ID, textContent, datasetValue, click, parent) {
    const element = document.createElement(elementType);
    if (classString) {
        element.setAttribute("class", classString);
    }
    if (ID) {
        element.id = ID;
    }
    if (textContent) {
        element.textContent = textContent;
    }
    if (datasetValue) {
        element.dataset.value = datasetValue;
    }
    if (click) {
        element.addEventListener("click", click);
    }
    if (parent) {
        parent.appendChild(element);
    }
    return element;
}

function createLinkElement(href, classString, ID, textContent, click, parent) {
    let link = createElement("a", classString, ID, textContent, null, click);
    if (href) {
        link.setAttribute("href", href);
    }
    if (parent) {
        parent.appendChild(link);
    }
    return link;
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

function editDataElement(elementID, textContent, datasetValue) {
    const element = document.getElementById(elementID);
    if (textContent) {
        element.textContent = textContent;
    }
    if (datasetValue) {
        element.dataset.value = datasetValue;
    }
}

function createImageElement(src, alt, id, click, parent) {
    const image = document.createElement("img");
    image.setAttribute("src", src);
    if (alt) {
        image.setAttribute("alt", alt);
    }
    if (id) {
        image.id = id;
    }
    if (click) {
        image.addEventListener("click", click);
    }
    if (parent) {
        parent.appendChild(image);
    }
    return image;
}

function createHeader(textContent) {
    const header = document.createElement("h3");
    header.textContent = textContent;
    return header;
}

function createTextElement(elementType, id, textContent, parent) {
    const textElement = document.createElement(elementType);
    if (id) {
        textElement.id = id;
    }
    if (textContent) {
        textElement.textContent = textContent;
    }
    if (parent) {
        parent.appendChild(textElement);
    }
    return textElement;
}

function createFormElement(action, method, classString, ID) {
    
}

function createInputElement(elementType, inputType, value, id, onInput, parent) {
    const input = document.createElement(elementType);
    input.setAttribute("type", inputType);
    if (value) {
        input.value = value;
    }
    if (id) {
        input.id = id;
    }
    console.log(onInput);
    if (onInput) {
        const fn = onInput[0];
        const params = onInput.slice(1); // Extract parameters
        input.addEventListener("input", function() {
            fn(input, ...params);
        });
    }
    parent.appendChild(input);
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

function createDoubleColumnFlex(leftElementType, rightElementType, leftElementText, rightElementText) {
    let container = createElementWithClass("div", "double-column-flex");
    createTextElement(leftElementType, null, leftElementText, container);
    createTextElement(rightElementType, null, rightElementText, container);
    return container;
}

function createBreakerLine() {
    const breakerLine = document.createElement("hr");
    return breakerLine;
}