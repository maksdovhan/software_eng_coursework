<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiySklad</title>
    <link rel="stylesheet" href="index.css">
    <link rel="icon" href="../images/box.png">
</head>
<body>
    <div class="title">MiySklad: електронний облік товарів</div>
    <div class="podtitle">Курсова робота студента групи ІО-24 Довганя Максима</div>
    <img src="../images/background.jpg">
    <a href="index.html"> <img src="../images/box.png" alt="Фото коробки" id="box"/></a>
    <a href="index.html" class="oval-button to_main">На головну</a>
    <a href="categories.php" class="oval-button categories">Категорії</a>
    <a href="products.php" class="oval-button goods">Товари</a>
    <a href="suppliers.php" class="oval-button suppliers">Постачальники</a>
    <a href="search.php" class="oval-button search">Пошук</a>
    <a href="task.html" class="oval-button task">Завдання</a>
    <a href="about.html" class="oval-button about">Про сайт</a>
    <div class="text_newcategory"><h1>Додати нову категорію</h1></div>
    <div class="text_allcategory"><h1>Список всіх категорій</h1></div>

    <?php
    // Налаштування підключення до бази даних
    $host = "localhost";
    $user = "user";
    $pass = "password";
    $database = "kursova_db";

    // Створення з'єднання
    $conn = new mysqli($host, $user, $pass, $database);

    // Перевірка підключення на помилки
    if ($conn->connect_error) {
        die("Помилка підключення до бази даних: " . $conn->connect_error);
    }
    
    echo "<div class='add_category'>";
    echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
        echo "<input type='hidden' name='action' value='add_category'>";
        echo "<label for='name'>Назва категорії:</label>";
        echo "<input type='text' id='name' name='name' required>";
        echo "<br>";
        echo "<label for='description'>Опис категорії:</label>";
        echo "<textarea id='description' name='description'></textarea>";
        echo "<br>";
        echo "<input type='submit' value='Додати'>";
    echo "</form>";
    echo "</div>";

    // Перевірка дії користувача
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $action = $_POST['edit_category'];

        // Обробка редагування категорії
        if ($action == "update") {
            $new_name = $_POST['edit_name'];
            $new_description = $_POST['edit_description'];
            $category_id = $_POST['category_id'];
            $exists = $conn->query("SELECT COUNT(*) FROM categories WHERE name = '$new_name'")->fetch_row()[0];

            // Перевірка введеної назви та опису
            if ($new_description != NULL) {
                $sql = "UPDATE categories SET description = '$new_description' WHERE id = $category_id";
                if ($conn->query($sql) === TRUE) {
                }
                echo "<div class='success-message-category'>Категорію успішно оновлено!</div>";
            }
            else {
                $sql = "SELECT * FROM categories WHERE name = 'non_existing_category'";
            }
            if (($exists == 0) AND ($new_name != NULL)) {
                $sql = "UPDATE categories SET name = '$new_name' WHERE id = $category_id";
                echo "<div class='success-message-category'>Категорію успішно оновлено!</div>";
            }
            else if ($new_description != NULL) {
                echo "<div class='success-message-category'>Опис категорії було оновлено!</div>";
            }
            else if ($exists !== 0) {
                echo "<div class='error-message-category'>Така категорія вже існує!</div>";
            }
            if ($conn->query($sql) === TRUE) {}
        }

        // Обробка видалення категорії
        else if ($action == "delete"){
            $category_id = $_POST['category_id'];

            // Видалення існуючої категорії
            $sql = "DELETE FROM categories WHERE id = $category_id";
            if ($conn->query($sql) === TRUE) {
                echo "<div class='success-message-category'>Категорію успішно видалено!</div>";
            }
        }

        // Обробка створення нової категорії
        else {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $exists = $conn->query("SELECT COUNT(*) FROM categories WHERE name = '$name'")->fetch_row()[0];
            if ($exists == 0) {
                $sql = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
            }
            else {
                $sql = "SELECT * FROM categories";
                echo "<div class='error-message-category'>Ця категорія вже існує!</div>";
            }
            if ($conn->query($sql) === TRUE) {
                echo "<div class='success-message-category'>Категорію успішно створено!</div>";
            }
        }
    }

    // Отримання всіх категорій із бази даних
    $sql = "SELECT * FROM categories";
    $result = $conn->query($sql);

    // Виведення всіх категорій
    if ($result->num_rows > 0) {
        echo "<div class='category-list'>";
        while($row = $result->fetch_assoc()) {
            echo "<div class='category-item'>";
            echo "<h3>" . $row["name"] . "</h3>";
            echo "<p>" . $row["description"] . "</p>";

            // Форма для редагування
            echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
            echo "<input type='hidden' name='category_id' value='" . $row["id"] . "'>";
            echo "<textarea name='edit_name' class='edit_category_name textarea-large' placeholder='Введіть нову назву...'></textarea>";
            echo "<input type='hidden' name='edit_category' value='update'>";
            echo "<textarea name='edit_description' class='edit_category_description textarea-large' placeholder='Введіть новий опис...'></textarea>";
            echo "<input type='hidden' name='edit_category' value='update'>";
            echo "<input type='image' src='../images/edit.png' alt='Редагувати' class='edit-button'>";
            echo "</form>";

            // Форма для видалення
            echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
            echo "<input type='hidden' name='category_id' value='" . $row["id"] . "'>";
            echo "<input type='hidden' name='edit_category' value='delete'>";
            echo "<input type='image' src='../images/trash.png' alt='Видалити' class='delete-category' name='delete_category'>";
            echo "</form>";
            echo "</div>";
        }
        echo "</div>";
    }
    else {
        echo "<div class='error-message-category'>Немає категорій!</div>";
    }

    // Закриття з'єднання
    $conn->close();
    ?>

</body>
</html>
