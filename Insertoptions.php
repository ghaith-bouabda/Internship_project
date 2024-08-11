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
    $optionasuivre = $_POST['optionasuivre'] ?? null;
    $optiondest = $_POST['optiondest'] ?? null;
    $optionobjetcourr = $_POST['optionobjetcourr'] ?? null;
    $optionorigin = $_POST['optionorigin'] ?? null;
    $optiontypecourrier = $_POST['optiontypecourrier'] ?? null;

   
    if (!empty($optionasuivre)) {
        $stmt1 = $conn->prepare("INSERT INTO optionasuivre (value) VALUES (?)");
        $stmt1->bind_param("s", $optionasuivre);
        if ($stmt1->execute()) {
            echo "Option added to option a suivre successfully.<br>";
        } else {
            echo "Error: " . $stmt1->error . "<br>";
        }
        $stmt1->close();
    }

   
    if (!empty($optiondest)) {
        $stmt2 = $conn->prepare("INSERT INTO optiondest (value) VALUES (?)");
        $stmt2->bind_param("s", $optiondest);
        if ($stmt2->execute()) {
            echo "Option added to optiondest successfully.<br>";
        } else {
            echo "Error: " . $stmt2->error . "<br>";
        }
        $stmt2->close();
    }

    
    if (!empty($optionobjetcourr)) {
        $stmt3 = $conn->prepare("INSERT INTO optionobjetcourr (value) VALUES (?)");
        $stmt3->bind_param("s", $optionobjetcourr);
        if ($stmt3->execute()) {
            echo "Option added to optionobjetcourr successfully.<br>";
        } else {
            echo "Error: " . $stmt3->error . "<br>";
        }
        $stmt3->close();
    }

    if (!empty($optionorigin)) {
        $stmt4 = $conn->prepare("INSERT INTO optionorigin (value) VALUES (?)");
        $stmt4->bind_param("s", $optionorigin);
        if ($stmt4->execute()) {
            echo "Option added to optionorigin successfully.<br>";
        } else {
            echo "Error: " . $stmt4->error . "<br>";
        }
        $stmt4->close();
    }

    if (!empty($optiontypecourrier)) {
        $stmt5 = $conn->prepare("INSERT INTO optiontypecourrier (value) VALUES (?)");
        $stmt5->bind_param("s", $optiontypecourrier);
        if ($stmt5->execute()) {
            echo "Option added to optiontypecourrier successfully.<br>";
        } else {
            echo "Error: " . $stmt5->error . "<br>";
        }
        $stmt5->close();
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
            <h2>Les options à ajouter</h2>
            <form method="post" action="insertoptions.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <label for="optionasuivre">Option for A Suivre:</label>
                <input type="text" id="optionasuivre" name="optionasuivre"><br><br>

                <label for="optiondest">Option for Destination:</label>
                <input type="text" id="optiondest" name="optiondest"><br><br>

                <label for="optionobjetcourr">Option for Objet Courrier:</label>
                <input type="text" id="optionobjetcourr" name="optionobjetcourr"><br><br>

                <label for="optionorigin">Option for Origine:</label>
                <input type="text" id="optionorigin" name="optionorigin"><br><br>

                <label for="optiontypecourrier">Option for Type Courrier:</label>
                <input type="text" id="optiontypecourrier" name="optiontypecourrier"><br><br>

                <input type="submit" value="Add Options">
            </form>
        </div>
    </div>
</body>
</html>
