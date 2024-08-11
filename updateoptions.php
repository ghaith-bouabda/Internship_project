<?php 
session_start();
include 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); 
}
$csrf_token = $_SESSION['csrf_token'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo '<script type="text/javascript">
            window.onload = function () { alert("Invalid CSRF token"); }
        </script>';
        exit;
    }
} 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table = $_POST['table'];
    $id = $_POST['id'];
    $new_value = $_POST['new_value'];
    $allowed_tables = ['optionasuivre', 'optiondest', 'optionobjetcourr', 'optionorigin', 'optiontypecourrier'];
    if (!in_array($table, $allowed_tables)) {
        echo "Invalid table selected.";
        exit();
    }

    $sql = "UPDATE $table SET value = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("si", $new_value, $id);
        if ($stmt->execute()) {
            echo "Option updated successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="nav">
        <p><a href="dashboard.php"><img src="img/Logo-Secil-min-300x288.png" width=30px></a></p>
        <div class="right-links">
        <a href="logout.php"><button class="btn">Log out</button></a>
        </div>
    </div>
    <style> 
 
 .nav {
         display: flex;
         justify-content: space-between;
         align-items: center;
         background-color: #121212;
         padding: 10px 10px;
         margin: 10px 0px;
         color: white;
     }

     .nav img {
         height: 30px; 
     }

     .nav .right-links {
         display: flex;
         align-items: center;
     }

     .nav .right-links a {
         text-decoration: none;
         color: white;
         margin-left: 15px;
     }
     .nav .right-links button {
         background-color: #555;
         border: none;
         color: white;
         padding: 10px 20px;
         cursor: pointer;
         border-radius: 5px;
     }

     .nav .right-links button:hover {
         background-color: #777;
     }</style>
    <div class="container">
        <div class="box form-box">
            <h2>Edit options</h2>
            <form method="post" action="updateoptions.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                <label for="table">Select Table:</label>
                <select id="table" name="table" required>
                    <option value="optionasuivre">A Suivre</option>
                    <option value="optiondest">Destination</option>
                    <option value="optionobjetcourr">Objet Courrier</option>
                    <option value="optionorigin">Origine</option>
                    <option value="optiontypecourrier">Type Courrier</option>
                </select><br><br>

                <label for="id">ID of Option to Edit:</label>
                <input type="number" id="id" name="id" required><br><br>

                <label for="new_value">New Value:</label>
                <input type="text" id="new_value" name="new_value" required><br><br>

                <input type="submit" value="Edit Option">
            </form>
        </div>
    </div>
</body>
</html>
