$(document).ready(function () {
// Функция для отображения ошибок-
    function showError(message) {
        const errorElement = document.getElementById("error");
        errorElement.textContent = message;
    }

// Функция для отображения приветствия
    function showWelcome() {
        const userName = document.getElementById("user-name");
        userName.textContent = sessionStorage.getItem("username");
        $("#login-container").fadeOut(300, function () {
            $("#welcome-container").fadeIn(300);
        });
    }

// Функция для отображения формы логина
    function showLoginForm() {
        $("#welcome-container").fadeOut(300, function () {
            $("#login-container").fadeIn(300);
        });
    }
// Проверка аутентификации при загрузке страницы
    if (sessionStorage.getItem("authenticated") !== "true") {
        showLoginForm();
    } else {
        showWelcome();
    }

// Обработка отправки формы
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
                    sessionStorage.setItem("authenticated", "true");
                    sessionStorage.setItem("username", username);
                    showWelcome();
                } else {
                    showError(data.error);
                }
            })
            .catch((error) => {
                showError("Произошла ошибка: " + error);
            });
    });

// Обработка выхода
    document.getElementById("logout-btn").addEventListener("click", function () {
        sessionStorage.removeItem("authenticated");
        sessionStorage.removeItem("username");
        showLoginForm();
    });
});