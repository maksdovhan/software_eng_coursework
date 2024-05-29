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

    <div class="text_newproduct"><h1>Список усіх товарів</h1></div>
    <div class="text_allgoods"><h1>Додати новий товар</h1></div>
    <div class="add_products">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="category">Категорія:</label>
            <select id="category" name="category" required class='choose-category'>
                <option value="">Виберіть категорію</option>
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

                // Функція для отримання списку категорій
                function getCategoriesList($conn) {
                    $sql = "SELECT * FROM categories";
                    $result = $conn->query($sql);
                    $categories = array();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $categories[] = $row;
                        }
                    }

                    return $categories;
                }

                // Функція для отримання списку всіх товарів
                function getAllProducts($conn, $sort_by_name, $sort_by_brand, $sort_by_price) {
                    $sql = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id";
                    if ($sort_by_name) {
                        $sql .= " ORDER BY p.name";
                    } elseif ($sort_by_brand) {
                        $sql .= " ORDER BY p.brand";
                    } elseif ($sort_by_price) {
                        $sql .= " ORDER BY p.price";
                    }
                    $result = $conn->query($sql);
                    $products = array();
                
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $products[] = $row;
                        }
                    }
                
                    return $products;
                }

                // Отримати список категорій
                $categories = getCategoriesList($conn);

                foreach ($categories as $category) {
                    echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
                }
                ?>
            </select>
            <br>
            <label for="name">Назва товару:</label>
            <input type="text" id="name" name="name" required>
            <br>
            <label for="description">Опис товару:</label>
            <textarea id="description" name="description" required></textarea>
            <br>
            <label for="quantity">Кількість на складі:</label>
            <input type="number" id="quantity" name="quantity" min="0" required>
            <br>
            <label for="brand">Бренд:</label>
            <input type="text" id="brand" name="brand" required>
            <br>
            <label for="price">Ціна:</label>
            <input type="number" id="price" name="price" min="0" required>
            <input type="submit" name="add_product" value="Додати">
        </form>
    </div>

    <?php
    // Обробка форми додавання товару
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
        $category_id = $_POST['category'];
        $name = $_POST['name'];
        $brand = $_POST['brand'];
        $description = $_POST['description'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];
    
        // Перевірка, чи введені всі необхідні дані
        if (!empty($category_id) && !empty($name) && !empty($brand) && !empty($description) && !empty($quantity) && !empty($price)) {
            $sql = "INSERT INTO products (category_id, name, brand, description, quantity, price) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssid", $category_id, $name, $brand, $description, $quantity, $price);
            $stmt->execute();
    
            echo "<div class='success-message-products'>Товар додано успішно!</div>";
            $stmt->close();
        } else {
            echo "<div class='error-message-products'>Будь ласка, заповніть всі поля.</div>";
        }
    }

    // Обробка видалення товару
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
            echo "<div class='success-message-products'>Товар видалено успішно!</div>";
        } else {
            echo "<div class='error-message-products'>Помилка: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }

    // Обробка редагування товару
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product'])) {
        $product_id = $_POST['product_id'];
        $name = $_POST['edit_name'];
        $brand = $_POST['edit_brand'];
        $description = $_POST['edit_description'];
        $quantity = $_POST['edit_quantity'];
        $price = $_POST['edit_price'];
    
        $sql = "UPDATE products SET name = ?, brand = ?, description = ?, quantity = ?, price = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiid", $name, $brand, $description, $quantity, $price, $product_id);
    
        if ($stmt->execute()) {
            echo "<div class='success-message-products'>Товар оновлено успішно!</div>";
        } else {
            echo "<div class='error-message-products'>Помилка: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }

    // Перевірка, чи натиснуто кнопку сортування
    $sort_by_name = isset($_POST['sort_product']);
    $sort_by_brand = isset($_POST['sort_brand']);
    $sort_by_price = isset($_POST['sort_money']);

    // Отримати список всіх товарів
    $all_products = getAllProducts($conn, $sort_by_name, $sort_by_brand, $sort_by_price);

    // Вивести список всіх товарів
    if (count($all_products) > 0) {
        echo "<div class='all-products'>";
        echo "<h2>Всі товари</h2>";
        echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
        echo "<ul>";
        foreach ($all_products as $product) {
            echo "<class='product-item'>";
            echo "<h3>" . $product['name'] . ", " . $product['brand'] . "</h3>";
            echo "<p>Категорія: " . $product['category_name'] . "</p>";
            echo "<p>Опис: " . $product['description'] . "</p>";
            echo "<p>Кількість: " . $product['quantity'] . "</p>";
            echo "<p>Ціна: " . $product['price'] . "</p>";
            echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
            echo "<input type='hidden' name='product_id' value='" . $product['id'] . "'>";
            echo "<input type='hidden' name='delete_product' value='true'>";
            echo "<input type='image' src='trash.png' alt='Видалити' class='delete-category'>";
            echo "</form>";
            echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
            echo "<input type='hidden' name='product_id' value='" . $product['id'] . "'>";
            echo "<input type='text' name='edit_name' required class='edit-input' value='" . $product['name'] . "' required>";
            echo "<input type='text' id='edit_brand' required class='edit-brand' name='edit_brand' value='" . $product['brand'] . "' required>";
            echo "<input type='text' id='edit_description' required class='edit-description' name='edit_description' value='" . $product['description'] . "' required>";
            echo "<input type='number' name='edit_quantity' required class='edit-quantity' value='" . $product['quantity'] . "' min='0' required>";
            echo "<input type='number' name='edit_price' required class='edit-price' value='" . $product['price'] . "' min='0' step='0.01' required>";
            echo "<input type='hidden' name='edit_product' value='true'>";
            echo "<input type='image' src='edit.png' alt='Редагувати' class='edit-button'>";
            echo "</form>";
            echo "<input type='hidden' name='sort_product' value='true'>";
            echo "<input type='image' src='sort.png' alt='Сортувати по назві' class='sort-products'>";
            echo "</form>";


            echo "<input type='hidden' name='sort_brand' value='true'>";
            echo "<input type='image' src='brand.png' alt='Сортувати по бренду' class='sort-brand'>";
            echo "</form>";


            echo "<input type='hidden' name='sort_money' value='true'>";
            echo "<input type='image' src='money.png' alt='Сортувати по ціні' class='sort-money'>";
            echo "</form>";
        }
        echo "</div>";
    } else {
        echo "<p>Немає доданих товарів.</p>";
    }

    $conn->close();?>

</body>
</html>
