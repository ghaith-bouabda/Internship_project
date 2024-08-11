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
$user_id = $_SESSION['user_id'];
include 'db.php';
$stmt1 =  $conn->prepare("SELECT * FROM arrivee WHERE user_id =? ");
$stmt1->bind_param("i", $user_id);
$stmt1->execute();
$result1 = $stmt1->get_result();

$stmt2 =  $conn->prepare("SELECT * FROM depart WHERE user_id =?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();


$stmt1->close();
$stmt2->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<header>
<nav>
                
<div class="row">
     <img src="img/logo-1-1.png"  alt="logo" class="logo">
                            <ul class="main-nav">

                                <li>   <a href="logout.php" >Log out</a></li>             
                            </ul> 
                        </div>    
                    </nav>
<nav>
</header>
<body>
  
</div>
    
    
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
.main{
  background-image:url('img/blue.jpg');
  background-position:center;
  background-size:cover;
  height:450px;
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
    margin-right: 40px;
    
    
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

 .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #121212;
            padding: 10px 20px;
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
        }


 .dropdown {
  position: relative;
  display: inline-block;
  margin-left:20%;
  margin-top:50px;
  color:white;
 
}

.dropdown-content{
  display: none;
  position: absolute;
  background-color: white;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  padding: 12px 16px;
  z-index: 1;
}
.dropdown-content a:link, a:visited {
  background-color: rgb(2, 3, 129);
 
  color: white;
  border: 2px solid #FBBF00;
  padding: 10px 40px;
  text-align: center;
  text-decoration: none;
  display: block;
}

.dropdown:hover .dropdown-content {
  display: block;


}
span{
  border: 1px solid #FBBF00;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  padding: 15px 30px;
  background-color: rgb(2, 3, 129);
  opacity:0.7;
 
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


<div class="main">

<div class="dropdown">
  <span>Arrivée</span>
  <div class="dropdown-content">
  <a href="arrivee.php"> Add</a>
  <a href="arriveeedit.php"> Edit</a>
  <a href="displayarrivee.php"> display </a>
  </div>
</div>
<div class="dropdown">
  <span>Depart</span>
  <div class="dropdown-content">
  <a href="depart.php"> Add</a>
  <a href="departedit.php"> Edit</a>
  <a href="displaydepart.php"> Display </a>
  </div>
</div>
<div class="dropdown">
  <span>Edit Options</span>
  <div class="dropdown-content">
  <a href="Insertoptions.php"> Add options</a>
  <a href="updateoptions.php"> Edit options</a>
  </div>
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