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

    <div class='text_allsuppliers'><h1>Список всіх постачальників</h1></div>
    <div class="text_newcategory"><h1>Додати нового постачальника</h1></div>
    <div class="add_category">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="name">Ім'я:</label>
            <input type="text" id="name" name="name" required>
            <br>
            <label for="surname">Прізвище:</label>
            <input type="text" id="surname" name="surname" required>
            <br>
            <label for="address">Адреса:</label>
            <input type="text" id="address" name="address" required>
            <br>
            <label for="phone">Номер телефону:</label>
            <input type="text" id="phone" name="phone" required>
            <br>
            <input type="submit" name="add_supplier" value="Додати">
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

    // Отримати список всіх постачальників
    $sql = "SELECT * FROM suppliers";
    $result = $conn->query($sql);

    $sort_by_name = isset($_POST['sort_name']);
    $sort_by_surname = isset($_POST['sort_surname']);

    function getAllSuppliers($conn, $sort_by_name, $sort_by_surname) {
        $sql = "SELECT * FROM suppliers";
        if ($sort_by_name) {
            $sql .= " ORDER BY name";
        } elseif ($sort_by_surname) {
            $sql .= " ORDER BY surname";
        }
        $result = $conn->query($sql);
        $suppliers = array();
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $suppliers[] = $row;
            }
        }
    
        return $suppliers;
    }

    $all_suppliers = getAllSuppliers($conn, $sort_by_name, $sort_by_surname);

    if (count($all_suppliers) > 0) {
        echo "<div class='all-suppliers'>";
        echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
        foreach ($all_suppliers as $supplier) {
            echo "<class='supplier-item'>";
            echo "<h3>" . $supplier['name'] . " " . $supplier['surname'] . "</h3>";
            echo "<p>Адреса: " . $supplier['address'] . "</p>";
            echo "<p>Телефон: " . $supplier['phone'] . "</p>";
    
            // Форма для видалення постачальника
            echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
            echo "<input type='hidden' name='supplier_id' value='" . $supplier['id'] . "'>";
            echo "<input type='hidden' name='delete_supplier' value='true'>";
            echo "<input type='image' src='trash.png' alt='Видалити' class='delete-supplier'>";
            echo "</form>";
    
            // Форма для редагування постачальника
            echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
            echo "<input type='hidden' name='supplier_id' value='" . $supplier['id'] . "'>";
            echo "<input type='text' name='edit_name' required class='edit-input' value='" . $supplier['name'] . "' required>";
            echo "<input type='text' name='edit_surname' required class='edit-input' value='" . $supplier['surname'] . "' required>";
            echo "<input type='text' name='edit_address' required class='edit-input' value='" . $supplier['address'] . "' required>";
            echo "<input type='text' name='edit_phone' required class='edit-input' value='" . $supplier['phone'] . "' required>";
            echo "<input type='hidden' name='edit_supplier' value='true'>";
            echo "<input type='image' src='edit.png' alt='Редагувати' class='change-button'>";
            echo "</form>";
    
            echo "<input type='hidden' name='sort_name' value='true'>";
            echo "<input type='image' src='name.png' alt='Сортувати по імені' class='sort-name'>";
            echo "</form>";
    
            echo "<input type='hidden' name='sort_surname' value='true'>";
            echo "<input type='image' src='sort.png' alt='Сортувати по прізвищу' class='sort-surname'>";
            echo "</form>";
        }
        echo "</div>";
    } else {
        echo "<p>Немає доданих постачальників.</p>";
    }

    // Обробка форми додавання постачальника
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_supplier'])) {
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
    
        // Перевірка формату номера телефону
        $phonePattern = "/^\+380\d{9}$/";
        if (!preg_match($phonePattern, $phone)) {
            echo "<div class='error-message-suppliers'>Введіть номер у форматі +380XXXXXXXXX!</div>";
        } else {
            // Перевірка, чи введені всі необхідні дані
            if (!empty($name) && !empty($surname) && !empty($address)) {
                $sql = "INSERT INTO suppliers (name, surname, address, phone) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $name, $surname, $address, $phone);
                $stmt->execute();
    
                echo "<div class='success-message-suppliers'>Постачальника додано успішно!</div>";
                $stmt->close();
            } else {
                echo "<div class='error-message-suppliers'>Будь ласка, заповніть всі поля.</div>";
            }
        }
    }

    // Обробка форми видалення постачальника
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_supplier'])) {
        $supplier_id = $_POST['supplier_id'];
    
        $sql = "DELETE FROM suppliers WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $supplier_id);
        $stmt->execute();
    
        echo "<div class='success-message-suppliers'>Постачальника видалено успішно!</div>";
        $stmt->close();
    }
    
    // Обробка форми редагування постачальника
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_supplier'])) {
        $supplier_id = $_POST['supplier_id'];
        $name = $_POST['edit_name'];
        $surname = $_POST['edit_surname'];
        $address = $_POST['edit_address'];
        $phone = $_POST['edit_phone'];
    
        $sql = "UPDATE suppliers SET name = ?, surname = ?, address = ?, phone = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $surname, $address, $phone, $supplier_id);
        $stmt->execute();
    
        echo "<div class='success-message-suppliers'>Дані постачальника оновлено успішно!</div>";
        $stmt->close();
    }
    
    $conn->close();?>

</body>
</html>
