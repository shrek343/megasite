<?php
// Подключаем файл с соединением к базе данных
include 'connect.php'; 

// Переменные для хранения сообщения об ошибке и успешной авторизации
$error_message = '';
$login_success = false;

// Проверяем, была ли отправлена форма
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы и экранируем их
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Подготовка и выполнение SQL-запроса для получения пользователя по email
    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Проверяем, существует ли пользователь с таким email
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Проверяем, совпадает ли введенный пароль с хешированным паролем
        if (password_verify($password, $hashed_password)) {
            // Закрываем подготовленный запрос
            $stmt->close();
            // Закрываем соединение с базой данных
            $conn->close();
            // Перенаправляем пользователя в личный кабинет
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Неверный пароль. Пожалуйста, попробуйте снова.";
        }
    } else {
        $error_message = "Пользователь с таким email не найден.";
    }

    // Закрываем подготовленный запрос
    $stmt->close();
}

// Закрываем соединение с базой данных
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }
        .navbar {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .login-form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 50px auto; /* Центрирование формы */
        }
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="email"],
        input[type="password"] {
            width: calc(100% - 16px); /* Учитываем отступы */
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Учитываем отступы в ширине */
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error-message {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            border: 1px solid #ebccd1;
            border-radius: 4px;
            margin-top: 20px;
            text-align: center;
        }
        .register-link {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Главная</a>
        <a href="register.php">Регистрация</a>
    </div>
    <div class="login-form">
        <h2>Авторизация</h2>
        <form method="POST" action="">
            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Пароль:</label>
            <input type="password" name="password" required>

            <input type="submit" value="Войти">
        </form>
        <div class="register-link">
            Если нет аккаунта - <a href="register.php">зарегистрируйтесь</a>
        </div>
        <?php if ($error_message): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>