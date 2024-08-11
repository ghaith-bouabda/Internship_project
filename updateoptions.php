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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<header>
<nav>
                        <div class="">
                        <img src="img/logo-1-1.png"  alt="logo" class="logo">
                            <ul class="main-nav">
                            <li> <a href="dashboard.php">Home</a></li>

                            <li> <a href="#">About us</a></li>
                                
                                
                            </ul> 
                        </div>    
                    </nav>
<nav>
</header>
<body>
    <style> 
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
html {
    font-family: 'Lato','Arial' ,sans-serif;
        font-weight: 300;
        font-size: 15px;
}
body {
    padding-bottom: 60px; 
}
nav{
    height:50px;
    background-color:white;
 
}
.logo {
    height: 70px;
    width: auto;
    float: left;
    margin-top: 10px;
}

.main-nav {
    
    
    float: right;
    list-style: none;
    margin-top: 45px;
  
    
}

.main-nav li {
    display: inline-block;
    margin-right: 50px;
    
    
}

.main-nav li a:link,
.main-nav li a:visited{
    padding: 6px 0;
    color: #000;
    text-decoration: none;
    text-transform: uppercase;
    font-size: 90%;
    border-bottom: 2px solid transparent;
    transition: border-bottom 0.2s;
}
.main-nav li a:hover,
.main-nav li a:active{
    border-bottom: 2px solid #2a1a55;
} 




 .btn {
    
    height: 35px;
    background:#2a1a55;
    border: 0;
    border-radius: 5px;
    font-size: 15px;
    cursor: pointer;
    transition: all .3s;
    margin-top: 10px;
  
    color: #fdfdfd;
}
.btn:hover {
    opacity: 0.83;
}
section{
    height:50px;
    background-color:#FBBF00;
    position: fixed;
    width: 100%;
  left: 0;
  bottom: 0;
}

 </style>
   <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="form-box">
                    <h2>Edit Options</h2>
                    <form method="post" action="updateoptions.php">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                        <div class="mb-3">
                            <label for="table" class="form-label">Select Table:</label>
                            <select id="table" name="table" class="form-select" required>
                                <option value="optionasuivre">A Suivre</option>
                                <option value="optiondest">Destination</option>
                                <option value="optionobjetcourr">Objet Courrier</option>
                                <option value="optionorigin">Origine</option>
                                <option value="optiontypecourrier">Type Courrier</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id" class="form-label">ID of Option to Edit:</label>
                            <input type="number" id="id" name="id" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_value" class="form-label">New Value:</label>
                            <input type="text" id="new_value" name="new_value" class="form-control" required>
                        </div>

                        <input type="submit" class="btn btn-primary" value="Edit Option">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <section><p>@Copyright 2024 -SCG. Tous droits réservés.</p></section>
</body>
</html>
