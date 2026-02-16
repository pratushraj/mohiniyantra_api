function checkAndMove(currentInput) {
    const value = parseInt(currentInput.value, 10);
    // Check if the value is valid and within the desired range
    if (!isNaN(value) && value >= 10 && value <= 99) {
        // Get the ID of the next input from `data-next`
        const nextInputId = currentInput.getAttribute("data-next");
        if (nextInputId) {
            // Find the next input by its ID
            const nextInput = document.getElementById(nextInputId);
            
            if (nextInput) {
                // Move focus to the next input
                nextInput.focus();
            }
        }
    }

}
function checkAndMove_RM(currentInput) {
    const value = parseInt(currentInput.value, 10);
    console.log("LLLLLLLLLLLLLL");
    // Check if the value is valid and within the desired range
    if (!isNaN(value) && value >= 100 && value <= 999) {
        // Get the ID of the next input from `data-next`
        const nextInputId = currentInput.getAttribute("data-next-rm");
        console.log("LLLLLLLL",nextInputId);
        if (nextInputId) {
            // Find the next input by its ID
            const nextInput = document.getElementById(nextInputId);
            
            if (nextInput) {
                // Move focus to the next input
                nextInput.focus();
            }
        }
    }

}

function checkAndMove_RY(currentInput) {
    const value = parseInt(currentInput.value, 10);
    // Check if the value is valid and within the desired range
    if (!isNaN(value) && value >= 100 && value <= 999) {
        // Get the ID of the next input from `data-next`
        const nextInputId = currentInput.getAttribute("data-next-ry");
        if (nextInputId) {
            // Find the next input by its ID
            const nextInput = document.getElementById(nextInputId);
            
            if (nextInput) {
                // Move focus to the next input
                nextInput.focus();
            }
        }
    }

}
function checkAndMove_RG(currentInput) {
    const value = parseInt(currentInput.value, 10);
    // Check if the value is valid and within the desired range
    if (!isNaN(value) && value >= 100 && value <= 999) {
        // Get the ID of the next input from `data-next`
        const nextInputId = currentInput.getAttribute("data-next-rg");
        if (nextInputId) {
            // Find the next input by its ID
            const nextInput = document.getElementById(nextInputId);
            
            if (nextInput) {
                // Move focus to the next input
                nextInput.focus();
            }
        }
    }

}
function checkAndMove_RS(currentInput) {
    const value = parseInt(currentInput.value, 10);
    // Check if the value is valid and within the desired range
    if (!isNaN(value) && value >= 100 && value <= 999) {
        // Get the ID of the next input from `data-next`
        const nextInputId = currentInput.getAttribute("data-next-rs");
        if (nextInputId) {
            // Find the next input by its ID
            const nextInput = document.getElementById(nextInputId);
            
            if (nextInput) {
                // Move focus to the next input
                nextInput.focus();
            }
        }
    }

}

function checkAndMove_SGB(currentInput) {
    const value = parseInt(currentInput.value, 10);
    // Check if the value is valid and within the desired range
    if (!isNaN(value) && value >= 10 && value <= 99) {
        // Get the ID of the next input from `data-next`
        const nextInputId = currentInput.getAttribute("data-next-b");
        if (nextInputId) {
            // Find the next input by its ID
            const nextInput = document.getElementById(nextInputId);
            
            if (nextInput) {
                // Move focus to the next input
                nextInput.focus();
            }
        }
    }

}