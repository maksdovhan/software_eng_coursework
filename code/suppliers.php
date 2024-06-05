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

    <div class='text_allsuppliers'><h1>Список всіх постачальників</h1></div>
    <div class="text_newcategory"><h1>Додати нового постачальника</h1></div>

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

    // Отримання списку всіх постачальників
    $sql = "SELECT * FROM suppliers";
    $result = $conn->query($sql);
    $sort_by_name = isset($_POST['sort_name']);
    $sort_by_surname = isset($_POST['sort_surname']);

    echo "<div class='add_category'>";
    echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
        echo "<label for='name'>Ім'я:</label>";
        echo "<input type='text' id='name' name='name' required>";
        echo "<br>";
        echo "<label for='surname'>Прізвище:</label>";
        echo "<input type='text' id='surname' name='surname' required>";
        echo "<br>";
        echo "<label for='address'>Адреса:</label>";
        echo "<input type='text' id='address' name='address' required>";
        echo "<br>";
        echo "<label for='phone'>Номер телефону:</label>";
        echo "<input type='text' id='phone' name='phone' required>";
        echo "<br>";
        echo "<input type='submit' name='add_supplier' value='Додати'>";
    echo "</form>";
    echo "</div>";

    // Функція для отримання усіх постачальників
    function getAllSuppliers($conn, $sort_by_name, $sort_by_surname) {
        $sql = "SELECT * FROM suppliers";
        if ($sort_by_name) {
            $sql .= " ORDER BY name";
        }
        elseif ($sort_by_surname) {
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

    // Виведення всіх постачальників, якщо вони існують
    if (count($all_suppliers) > 0) {
        echo "<div class='all-suppliers'>";
        echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
        if ($result->num_rows > 0) {
            echo "<div class='supplier-list'>";
            while ($supplier = $result->fetch_assoc()) {
                echo "<div class='supplier-item'>";
                echo "<h2>" . htmlspecialchars($supplier['name']) . " " . htmlspecialchars($supplier['surname']) . "</h2>";
                echo "<p><strong>Адреса:</strong> " . htmlspecialchars($supplier['address']) . "</p>";
                echo "<p><strong>Номер телефону:</strong> " . htmlspecialchars($supplier['phone']) . "</p>";

                // Сортування по імені постачальника
                echo "<input type='hidden' name='sort_name' value='true'>";
                echo "<input type='image' src='../images/name.png' alt='Сортувати по імені' class='sort-name'>";
                echo "</form>";
    
                // Сортування по прізвищу постачальника
                echo "<input type='hidden' name='sort_surname' value='true'>";
                echo "<input type='image' src='../images/sort.png' alt='Сортувати по прізвищу' class='sort-surname'>";
                echo "</form>";
        
                // Видалення постачальника
                echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' style='display:inline;'>";
                echo "<input type='hidden' name='supplier_id' value='" . $supplier['id'] . "'>";
                echo "<input type='hidden' name='delete_supplier' value='true'>";
                echo "<input type='image' src='../images/trash.png' alt='Видалити' class='delete-supplier'>";
                echo "</form>";
        
                // Редагування постачальника
                echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' style='display:inline;'>";
                echo "<input type='hidden' name='supplier_id' value='" . $supplier['id'] . "'>";
                echo "<input type='text' name='edit_name' required class='edit-input' value='" . htmlspecialchars($supplier['name']) . "'>";
                echo "<input type='text' name='edit_surname' required class='edit-input' value='" . htmlspecialchars($supplier['surname']) . "'>";
                echo "<input type='text' name='edit_address' required class='edit-input' value='" . htmlspecialchars($supplier['address']) . "'>";
                echo "<input type='text' name='edit_phone' required class='edit-input' value='" . htmlspecialchars($supplier['phone']) . "'>";
                echo "<input type='hidden' name='edit_supplier' value='true'>";
                echo "<input type='image' src='../images/edit.png' alt='Редагувати' class='change-button'>";
                echo "</form>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<p>Немає доданих постачальників.</p>";
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['add_supplier'])) {
            $name = $_POST['name'];
            $surname = $_POST['surname'];
            $address = $_POST['address'];
            $phone = $_POST['phone'];
            $phonePattern = "/^\+380\d{9}$/";

            // Перевірка правильності вводу номеру телефона
            if (!preg_match($phonePattern, $phone)) {
                echo "<div class='error-message-suppliers'>Введіть номер у форматі +380XXXXXXXXX!</div>";
            }
            else {
                // Перевірка незаповненої інформації про постачальника
                if (!empty($name) && !empty($surname) && !empty($address)) {
                    $sql = "INSERT INTO suppliers (name, surname, address, phone) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssss", $name, $surname, $address, $phone);
                    $stmt->execute();
                    echo "<div class='success-message-suppliers'>Постачальника додано успішно!</div>";
                    $stmt->close();
                }
                else {
                    echo "<div class='error-message-suppliers'>Будь ласка, заповніть всі поля.</div>";
                }
            }
        }
        elseif (isset($_POST['delete_supplier'])) {
            $supplier_id = $_POST['supplier_id'];
            $sql = "DELETE FROM suppliers WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $supplier_id);
            $stmt->execute();
            echo "<div class='success-message-suppliers'>Постачальника видалено успішно!</div>";
            $stmt->close();
        }
        elseif (isset($_POST['edit_supplier'])) {
            $supplier_id = $_POST['supplier_id'];
            $name = $_POST['edit_name'];
            $surname = $_POST['edit_surname'];
            $address = $_POST['edit_address'];
            $phone = $_POST['edit_phone'];
            $sql = "UPDATE suppliers SET name = ?, surname = ?, address = ?, phone = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $name, $surname, $address, $phone, $supplier_id);
            $stmt->execute();
            echo "<div class='success-message-suppliers'>Дані постачальника оновлено!</div>";
            $stmt->close();
        }
    }

    // Закриття з'єднання
    $conn->close();?>

</body>
</html>
