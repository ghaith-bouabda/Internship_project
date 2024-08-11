<?php
include 'db.php';

$message = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['pwd'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $department = ($role == 'user') ? $_POST['department'] : null;

    $stmt = $conn->prepare("SELECT COUNT(*) FROM registration WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $user, $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $message = 'Username or email already exists.';
    } else {
        $stmt = $conn->prepare("INSERT INTO registration (username, email, password, role, department) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $user, $email, $pass, $role, $department);

        if ($stmt->execute()) {
            $message = 'New record created successfully. <a href="login.php">You can login now</a>';
        } else {
            $message = 'Error: ' . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Registration</title>
    
    <script>
        function toggleDepartmentField() {
            const roleSelect = document.getElementById('role');
            const departmentField = document.getElementById('departmentField');
            if (roleSelect.value === 'user') {
                departmentField.style.display = 'block';
            } else {
                departmentField.style.display = 'none';
            }
        }
    </script>
</head>

<header>
<nav>
                        <div class="row">
                        <img src="img/logo-1-1.png"  alt="logo" class="logo">
                            <ul class="main-nav">
                            <li> <a href="#">About us</a></li>
                              <li>  <a href="#">How it works</a></li>
                            <li>    <a href="#">Our cities</a></li>
                                <li> <a href="#">Sign up</a></li>
                                
                                
                            </ul> 
                        </div>    
                    </nav>
<nav>
</header>
<body>
    <div class="maindiv">
        <div class="frm">
         
            <form method="POST" action="">
                <div class="field_input">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="field_input">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" required>
                </div>
                <div class="field_input">
                    <label for="pwd">Password</label>
                    <input type="password" id="pwd" name="pwd" required>
                </div>
                <div class="field_input">
                    <label for="role">Role</label>
                    <select id="role" name="role" required onchange="toggleDepartmentField()">
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="field_input" id="departmentField" style="display: none;">
                    <label for="department">Department</label>
                    <select id="department" name="department"required onchange="toggleDepartmentField()">
                        <option value="RH">RH</option>
                        <option value="APPRO">APPRO</option>
                        <option value="Commercial">Com</option>
                        <option value="Finance">Fin</option>
                    </select>
                </div>
                <div class="field">
                    <input type="submit" class="btn" value="Submit">
                    <input type="reset" class="btn">
                </div>
            </form>
            <?php if (!empty($message)): ?>
                <div style='background-color:white; width:350px; height:150px; padding: 10px;'>
                    <p><?php echo $message; ?></p>
                </div>
            <?php endif; ?>
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
        
        <section><p>@Copyright 2024 -SCG. Tous droits réservés.</p></section>
</body>
</html>
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
    
    position:sticky;
    float: right;
    list-style: none;
    margin-top: 45px;
  
    
}

.main-nav li {
    display: inline-block;
    margin-left: 40px;
    margin-right:20px;
    
    
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
    height:50px;
    background-color:#FBBF00;
}</style>