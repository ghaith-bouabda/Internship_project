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
                    <h2>Insert options</h2>
                    <form method="post" action="insertoptions.php">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                        <div class="mb-3">
                            <label for="optionasuivre" class="form-label">Option for A Suivre:</label>
                            <input type="text" id="optionasuivre" name="optionasuivre" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="optiondest" class="form-label">Option for Destination:</label>
                            <input type="text" id="optiondest" name="optiondest" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="optionobjetcourr" class="form-label">Option for Objet Courrier:</label>
                            <input type="text" id="optionobjetcourr" name="optionobjetcourr" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="optionorigin" class="form-label">Option for Origine:</label>
                            <input type="text" id="optionorigin" name="optionorigin" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="optiontypecourrier" class="form-label">Option for Type Courrier:</label>
                            <input type="text" id="optiontypecourrier" name="optiontypecourrier" class="form-control">
                        </div>

                        <input type="submit" class="btn btn-primary" value="Add Options">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <section><p>@Copyright 2024 -SCG. Tous droits réservés.</p></section>
</body>
</html>
