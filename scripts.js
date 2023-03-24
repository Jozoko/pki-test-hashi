document.getElementById("login-form").addEventListener("submit", function (event) {
    event.preventDefault();
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    const formData = new FormData();
    formData.append("username", username);
    formData.append("password", password);

    fetch("auth.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                window.location.href = "success.php";
            } else {
                showError(data.error);
            }
        })
        .catch((error) => {
            showError("Произошла ошибка: " + error);
        });
});

function showError(message) {
    const errorContainer = document.getElementById("error-container");
    errorContainer.innerText = message;

    errorContainer.animate(
        [
            { opacity: 0, transform: "translateY(-10px)" },
            { opacity: 1, transform: "translateY(0px)" },
        ],
        {
            duration: 300,
            easing: "ease-out",
        }
    );
}
