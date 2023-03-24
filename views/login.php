<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аутентификация</title>
    <link rel="stylesheet" href="../styles.css">
    <script src="../scripts.js" defer></script>
</head>
<body>
<div class="login-container">
    <div class="form-header">
        <h2>Вход</h2>
    </div>
    <form id="login-form" action="../auth.php" method="post">
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
</body>
</html>
