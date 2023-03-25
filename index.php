<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDAP-аутентификация</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts.js" defer></script>
</head>
<body>
<div class="container">
    <!-- Вход -->
    <div class="login-container" id="login-container" style="display: none;">
        <div class="form-header">
            <h2>Вход</h2>
        </div>
        <form id="login-form">
            <div class="input-container">
                <label for="username">Логин</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-container">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="error-container" id="error-container"></div>
            <button type="submit" class="login-btn">Войти</button>
        </form>
    </div>

    <!-- Приветствие -->
    <div class="login-container" id="welcome-container" style="display: none;">
        <div class="form-header">
            <h2>Привет, <span id="user-name"></span>!</h2>
        </div>
        <button id="logout-btn" class="login-btn">Выход</button>
    </div>
</div>
</body>
</html>
