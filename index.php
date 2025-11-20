<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikacja PHP - Zarządzanie użytkownikami</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Aplikacja PHP - Zarządzanie użytkownikami</h1>
        
        <?php
        require_once 'config.php';
        
        $message = '';
        $messageType = '';
        
        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            try {
                $pdo = getConnection();
                
                if ($action === 'add') {
                    $name = $_POST['name'] ?? '';
                    $email = $_POST['email'] ?? '';
                    $age = $_POST['age'] ?? null;
                    
                    if (!empty($name) && !empty($email)) {
                        $stmt = $pdo->prepare("INSERT INTO users (name, email, age) VALUES (?, ?, ?)");
                        $stmt->execute([$name, $email, $age]);
                        $message = "Użytkownik został dodany pomyślnie!";
                        $messageType = 'success';
                    } else {
                        $message = "Proszę wypełnić wszystkie wymagane pola!";
                        $messageType = 'error';
                    }
                }
                
                if ($action === 'delete') {
                    $id = $_POST['id'] ?? 0;
                    if ($id > 0) {
                        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                        $stmt->execute([$id]);
                        $message = "Użytkownik został usunięty!";
                        $messageType = 'success';
                    }
                }
                
                if ($action === 'update') {
                    $id = $_POST['id'] ?? 0;
                    $name = $_POST['name'] ?? '';
                    $email = $_POST['email'] ?? '';
                    $age = $_POST['age'] ?? null;
                    
                    if ($id > 0 && !empty($name) && !empty($email)) {
                        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, age = ? WHERE id = ?");
                        $stmt->execute([$name, $email, $age, $id]);
                        $message = "Dane użytkownika zostały zaktualizowane!";
                        $messageType = 'success';
                    }
                }
                
            } catch (PDOException $e) {
                $message = "Błąd bazy danych: " . $e->getMessage();
                $messageType = 'error';
            }
        }
        
        // Display message
        if (!empty($message)) {
            echo "<div class='message {$messageType}'>{$message}</div>";
        }
        ?>
        
        <!-- Add User Form -->
        <h2>Dodaj nowego użytkownika</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label for="name">Imię i nazwisko *:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email *:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="age">Wiek:</label>
                <input type="number" id="age" name="age">
            </div>
            <button type="submit">Dodaj użytkownika</button>
        </form>
        
        <!-- Users List -->
        <h2>Lista użytkowników</h2>
        <?php
        try {
            $pdo = getConnection();
            $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
            $users = $stmt->fetchAll();
            
            if (count($users) > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Imię i nazwisko</th><th>Email</th><th>Wiek</th><th>Data dodania</th><th>Akcje</th></tr>";
                
                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>{$user['id']}</td>";
                    echo "<td>{$user['name']}</td>";
                    echo "<td>{$user['email']}</td>";
                    echo "<td>" . ($user['age'] ? $user['age'] : '-') . "</td>";
                    echo "<td>{$user['created_at']}</td>";
                    echo "<td>
                            <button onclick='editUser({$user['id']}, \"{$user['name']}\", \"{$user['email']}\", \"{$user['age']}\")'>Edytuj</button>
                            <form style='display:inline' method='POST' onsubmit='return confirm(\"Czy na pewno chcesz usunąć tego użytkownika?\")'>
                                <input type='hidden' name='action' value='delete'>
                                <input type='hidden' name='id' value='{$user['id']}'>
                                <button type='submit' class='btn-danger'>Usuń</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
                
                echo "</table>";
            } else {
                echo "<p>Brak użytkowników w bazie danych.</p>";
            }
        } catch (PDOException $e) {
            echo "<div class='message error'>Błąd podczas pobierania danych: " . $e->getMessage() . "</div>";
        }
        ?>
        
        <!-- Edit User Form (hidden by default) -->
        <div id="editForm" style="display:none; margin-top: 30px; padding: 20px; border: 2px solid #007bff; border-radius: 8px;">
            <h2>Edytuj użytkownika</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" id="editId" name="id">
                <div class="form-group">
                    <label for="editName">Imię i nazwisko *:</label>
                    <input type="text" id="editName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="editEmail">Email *:</label>
                    <input type="email" id="editEmail" name="email" required>
                </div>
                <div class="form-group">
                    <label for="editAge">Wiek:</label>
                    <input type="number" id="editAge" name="age">
                </div>
                <button type="submit">Zaktualizuj</button>
                <button type="button" onclick="cancelEdit()">Anuluj</button>
            </form>
        </div>
    </div>
    
    <script>
        function editUser(id, name, email, age) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editAge').value = age || '';
            document.getElementById('editForm').style.display = 'block';
        }
        
        function cancelEdit() {
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</body>
</html>