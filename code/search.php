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
    </a>
    <a href="index.html" class="oval-button to_main">На головну</a>
    <a href="categories.php" class="oval-button categories">Категорії</a>
    <a href="products.php" class="oval-button goods">Товари</a>
    <a href="suppliers.php" class="oval-button suppliers">Постачальники</a>
    <a href="search.php" class="oval-button search">Пошук</a>
    <a href="task.html" class="oval-button task">Завдання</a>
    <a href="about.html" class="oval-button about">Про сайт</a>
    <div class="text_search"><h1>Вікно пошуку</h1></div>
    <div class="add_category">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="search_type">Пошук по:</label>
            <select id="search_type" name="search_type">
                <option value="products">Товарам</option>
                <option value="suppliers">Постачальникам</option>
            </select>
            <input type="text" id="search_query" name="search_query" placeholder="Введіть ключове слово" required>
            <input type="submit" name="search" value="Пошук">
        </form>
        </div>
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

        // Функція для відображення результатів пошуку
        function displayResults($results, $search_type) {
            if ($results->num_rows > 0) {
                echo "<div class='text_allsearch'><h1>Результати пошуку</h1></div>";
                echo "<div class='all-search'>";
                while ($row = $results->fetch_assoc()) {
                    if ($search_type == 'products') {
                        echo "<div class='product-item'>";
                        echo "<h3>" . $row['name'] . ", " . $row['brand'] . "</h3>";
                        echo "<p>Категорія: " . $row['category_name'] . "</p>";
                        echo "<p>Опис: " . $row['description'] . "</p>";
                        echo "<p>Кількість: " . $row['quantity'] . "</p>";
                        echo "<p>Ціна: " . $row['price'] . "</p>";
                        echo "</div>";
                    } else {
                        echo "<div class='supplier-item'>";
                        echo "<h3>" . $row['name'] . " " . $row['surname'] . "</h3>";
                        echo "<p>Адреса: " . $row['address'] . "</p>";
                        echo "<p>Телефон: " . $row['phone'] . "</p>";
                        echo "</div>";
                    }
                }
                echo "</div>";
            } else {
                echo "<p>Нічого не знайдено.</p>";
            }
        }

        // Обробка пошуку
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
            $search_type = $_POST['search_type'];
            $search_query = $_POST['search_query'];

            if ($search_type == 'products') {
                // Пошук по товарам
                $sql = "SELECT p.*, c.name AS category_name
                        FROM products p
                        JOIN categories c ON p.category_id = c.id
                        WHERE p.name LIKE '%$search_query%'
                           OR p.brand LIKE '%$search_query%'
                           OR p.description LIKE '%$search_query%'";
            } else {
                // Пошук по постачальникам
                $sql = "SELECT * FROM suppliers WHERE name LIKE '%$search_query%' OR surname LIKE '%$search_query%' OR address LIKE '%$search_query%'";
            }

            $result = $conn->query($sql);
            displayResults($result, $search_type);
        }

        // Закриття з'єднання
        $conn->close();
        ?>
    
</body>
</html>
