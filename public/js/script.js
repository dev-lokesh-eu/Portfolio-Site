const form = document.querySelector(".form[data-form]");
const submitButton = document.querySelector(".form-btn[data-form-btn]");
const messageContainer = document.createElement('div'); // Message display container
form.appendChild(messageContainer); // Add to form

// Enable submit button only when all inputs are valid
form.addEventListener("input", () => {
    const allInputsValid = [...form.querySelectorAll("[data-form-input]")].every(
        (input) => input.value.trim() !== ""
    );
    submitButton.disabled = !allInputsValid;
});

// Handle form submission
form.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(form);

    // Disable the submit button and update its text
    submitButton.disabled = true;
    submitButton.innerHTML = "Sending...";

    // Send the form data to the server
    fetch("/../../server/src/process_form.php", {
            method: "POST",
            body: formData,
        })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((responseData) => {
            // Display server response in the message container
            if (responseData.ok) {
                messageContainer.innerHTML = `<p style='color: green;'>${responseData.message}</p>`;
                form.reset(); // Reset the form fields
            } else {
                messageContainer.innerHTML = `<p style='color: red;'>${responseData.message}</p>`;
            }
        })
        .catch((error) => {
            console.error("Error:", error.message);
            messageContainer.innerHTML =
                "<p style='color: red;'>An unexpected error occurred. Please try again later.</p>";
        })
        .finally(() => {
            // Re-enable the submit button and reset its text
            submitButton.disabled = false;
            submitButton.innerHTML = "Send Message";
        });
});