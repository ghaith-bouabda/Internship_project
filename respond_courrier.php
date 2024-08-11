<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expiry']) || time() >= $_SESSION['csrf_token_expiry']) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_expiry'] = time() + 1800;
}

$csrf_token = $_SESSION['csrf_token'];
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo '<script type="text/javascript">
            window.onload = function () { alert("Invalid CSRF token"); } 
        </script>';
        exit;
    }

    $courrier_id = $_POST['courrier_id'];
    $response = $_POST['response'];

    $stmt = $conn->prepare("UPDATE arrivee SET response = ?, status = 'responded' WHERE Norder = ?");
    $stmt->bind_param("si", $response, $courrier_id);
    if ($stmt->execute()) {
        echo '<script type="text/javascript">
            window.onload = function () { alert("Response submitted successfully."); window.location = "user_dashboard.php"; }
        </script>';
    } else {
        echo '<script type="text/javascript">
            window.onload = function () { alert("Error submitting response."); }
        </script>';
    }

    $stmt->close();
    $conn->close();
} else {
    $courrier_id = $_GET['id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respond to Courrier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<header>
<nav>
                        <div class="">
                        <img src="img/logo-1-1.png"  alt="logo" class="logo">
                            <ul class="main-nav">
                            <li> <a href="user_dashboard.php">Home</a></li>

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
<body>
    <div class="container mt-4">
        <h2>Respond to Courrier</h2>
        <form method="POST" action="respond_courrier.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="courrier_id" value="<?php echo htmlspecialchars($courrier_id); ?>">
            <div class="mb-3">
                <label for="response" class="form-label">Response</label>
                <textarea class="form-control" id="response" name="response" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Response</button>
        </form>
    </div>
    <section><p>@Copyright 2024 -SCG. Tous droits réservés.</p></section>

</body>
</html>
