<?php 
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit(); 
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo '<script type="text/javascript">
            window.onload = function () { alert("Invalid CSRF token"); } 
        </script>';
        exit;
    }

    include 'db.php';

    $email = $_POST['email'];
    $password = $_POST['pwd'];

    $stmt = $conn->prepare("SELECT id, username, password, role,department FROM registration WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password, $role,$department);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['department'] = $department;

            if ($role == 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            echo '<script type="text/javascript">
                window.onload = function () { alert("Invalid email or password"); } 
            </script>';
        }
    } else {
        echo '<script type="text/javascript">
            window.onload = function () { alert("Invalid email or password"); } 
        </script>';
    }

    $stmt->close();
    $conn->close();
}
?>
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
header{
    position:sticky;
    top:0;
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
    margin-left: 40px;
    
    
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
    margin-left:44%;
    height: 35px;
    background:#2a1a55;
    border: 0;
    border-radius: 5px;
    font-size: 15px;
    cursor: pointer;
    transition: all .3s;
    margin-top: 10px;
    padding: 0 10px;
    color: #fdfdfd;
}
.btn:hover {
    opacity: 0.83;
}
  

   
.maindiv{
         width: 100%;
        height:100%;
        left:50%;
        background-image:linear-gradient(rgba(0, 0, 0, 0.41),rgba(0, 0, 0, 0.51)),url('img/blue.jpg');
        background-size: 100% 100%;
        min-height: 100vh;
        
    background-size: cover;
    background-position: center;
    background-attachment: fixed;


    }
form{
        width: 50vh;
        height:20vh;
        display:inline-block;
        border-radius: 5px;
        height: auto;
        padding: 15px 18px;
        margin: 10% 10%;
        margin-left: auto;
        margin-right: auto;
        background-color:hsla(230, 83%, 86%, 0.3);
     margin-left: 40%;
}
form input{
    margin-left:20%;
    padding: 15px 28px;
    display: block;

}
form h1{
    padding-left:40%;
    padding-bottom:5px;
    color:white;
}
.field_input label{
    padding-left:40%;
    color:white;
    font-family: 'Lato','Arial' ,sans-serif;
        font-weight: 300;
        font-size: 20px;
}
form a {
    
    color: #fdfdfd;
    text-decoration: none;

}
footer{
    background-color:#007bff;
    background-image:linear-gradient(135deg,rgb(2,3,129,0.7) 0%,rgb(2, 3, 129,0.7) 100%),url('img/blue.jpg');
    background-size:cover;
    height:500px;
    background-position: center;
    
}
footer p{
    display:inline-block;
    width: 270px;
    margin-left: 22%;
    padding-top:10%;
    
    color:#fff;
    word-spacing:5px;
}
footer img {
    position: absolute;
    padding-left: 44%;
}
section{
    height:100px;
    background-color:#FBBF00;
}
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

  
</head>
<header>
<nav>
                        <div class="row">
                        <img src="img/logo-1-1.png"  alt="logo" class="logo">
                            <ul class="main-nav">
                            <li> <a href="#">About us</a></li>
                                <li> <a href="#">Sign up</a></li>
                                
                                
                            </ul> 
                        </div>    
                    </nav>
<nav>
</header>
<body>
   
    <div class="maindiv">
    
        <div class="frm">
        
            <form method="post" action="login.php">
            <h1>Login</h1>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <div class="field_input">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="field_input">
                    <label for="pwd">Password:</label>
                    <input type="password" id="pwd" name="pwd" required>
                </div>
                <div class="field">
                    <input type="submit" class="btn" value="Log in">
                </div>
                <div class="">
                    Don't have an account? Click here to <a href="register.php">sign up!</a>
                </div>
            </form>
        </div>
    </div>
    <footer>
        <img src="img/white-1-1.png" alt="logo">
        <p>La Société des Ciments de Gabès est une société anonyme créée en 1973 spécialisée dans la fabrication des liants (Ciments et chaux artificielle). Son usine à Gabès est entrée en production en 1977.  Son marché privilégié s’étend sur toute la zone Sud de la Tunisie.</p>
         <p>
        
            Siège Social : <br>
            (+216) 71 950 952 // (+216) 71 950 957 <br>
            75, Av Kheireddine Pacha, Immeuble Pacha Center Bloc B, 5ème étage Bur B15, Montplaisir, 1073 Tunis.
            Usine :
            (+216) 75 350 063 // (+216) 75 350 822

            Rt el Hamma KM 10 BP 101, 6000 Gabès.
            Service commercial :
            sales@scg.com.tn </p></p></footer>
        
        <section><p>merci :)</p></section>
</body>
</html>
