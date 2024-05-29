<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiySklad</title>
    <link rel="stylesheet" href="index.css">
    <link rel="icon" href="./box.png">
</head>
<body>
    <div class="title">MiySklad: електронний облік товарів</div>
    <div class="podtitle">Курсова робота студента групи ІО-24 Довганя Максима</div>
    <img src="./bg photo.jpg">
        <a href="index.html">
            <img src="./box.png" alt="Фото коробки" id="box">
    <a href="index.html" class="oval-button to_main">На головну</a>
    <a href="categories.php" class="oval-button categories">Категорії</a>
    <a href="products.php" class="oval-button goods">Товари</a>
    <a href="suppliers.php" class="oval-button suppliers">Постачальники</a>
    <a href="search.php" class="oval-button search">Пошук</a>
    <a href="task.html" class="oval-button task">Завдання</a>
    <a href="about.html" class="oval-button about">Про сайт</a>
    <a href="index.html">
        <img src="./box.png" alt="Фото коробки" id="box">
    </a>
    <div class="text_newcategory"><h1>Додати нову категорію</h1></div>
    <div class="text_allcategory"><h1>Список всіх категорій</h1></div>
    <div class="add_category">
    <?php
    // Налаштування підключення до бази даних
    $host = "localhost";
    $user = "user";
    $pass = "password";
    $database = "kursova_db";

    // Створення з'єднання
    $conn = new mysqli($host, $user, $pass, $database);

    // Перевірка на помилки підключення
    if ($conn->connect_error) {
        die("Помилка підключення до бази даних: " . $conn->connect_error);
    }

    // Обробка форми
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = isset($_POST["name"]) ? $_POST["name"] : '';
        $description = isset($_POST["description"]) ? $_POST["description"] : '';

        // Перевірка наявності категорії з таким самим ім'ям (лише для додавання)
        if (!isset($_POST['edit_category']) && !isset($_POST['delete_category'])) {
            $check_query = "SELECT COUNT(*) FROM categories WHERE name = '$name'";
            $result = $conn->query($check_query);
            $row = $result->fetch_row();
            $count = intval($row[0]);

            if ($count > 0) {
                echo "<div class='error-message-category'>Така категорія вже існує!</div>";
            } else {
                // Додавання нової категорії
                $sql = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
                if ($conn->query($sql) === TRUE) {
                    echo "<div class='success-message-category'>Категорію успішно додано!</div>";
                } else {
                    echo "Помилка: " . $sql . "<br>" . $conn->error;
                }
            }
        }
    }

    // Обробка видалення категорії
    if (isset($_POST['delete_category'])) {
        $category_id = $_POST['category_id'];

        // Видалення категорії з бази даних
        $sql = "DELETE FROM categories WHERE id = $category_id";

        if ($conn->query($sql) === TRUE) {
            echo "<div class='success-message-category'>Категорію успішно видалено!</div>";
        } else {
            echo "Помилка видалення категорії: " . $conn->error;
        }
    }

    // Отримати всі категорії з бази даних
    $sql = "SELECT * FROM categories";
    $result = $conn->query($sql);
    
    // Вивести всі категорії
    if ($result->num_rows > 0) {
        echo "<div class='category-list'>";
        while($row = $result->fetch_assoc()) {
            echo "<div class='category-item'>";
            echo "<h3>" . $row["name"] . "</h3>";
            echo "<p>" . $row["description"] . "</p>";
            echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
            echo "<input type='hidden' name='category_id' value='" . $row["id"] . "'>";
            echo "<input type='hidden' name='delete_category' value='true'>";
            echo "<input type='image' src='trash.png' alt='Видалити' class='delete-category' name='delete_category'>";
            echo "</form>";
            echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
            echo "<input type='hidden' name='category_id' value='" . $row["id"] . "'>";
            echo "<input type='text' name='edit_name' value='" . $row["name"] . "' required>";
            echo "<textarea name='edit_description'>" . $row["description"] . "</textarea>";
            echo "<input type='hidden' name='edit_category' value='true'>";
            echo "<input type='image' src='edit.png' alt='Редагувати' class='edit-button' name='edit_category'>";
            echo "</form>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "Немає доданих категорій";
    }

    // Обробка форми для редагування категорії
    if (isset($_POST['edit_category'])) {
        $category_id = $_POST['category_id'];
        $new_name = $_POST['edit_name'];
        $new_description = $_POST['edit_description'];

        // Оновлення інформації про категорію в базі даних
        $sql = "UPDATE categories SET name = '$new_name', description = '$new_description' WHERE id = $category_id";

        if ($conn->query($sql) === TRUE) {
            echo "<div class='success-message-category'>Категорію успішно оновлено!</div>";
        } else {
            echo "Помилка оновлення категорії: " . $conn->error;
        }
    }

    // Закриття з'єднання
    $conn->close();
    ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="name">Назва категорії:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="description">Опис категорії:</label>
        <textarea id="description" name="description"></textarea>
        <br>
        <input type="submit" value="Додати">
    </form>
    <div>
</body>
</html>
