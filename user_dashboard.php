<?php  
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role']=='admin') ) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expiry']) || time() >= $_SESSION['csrf_token_expiry']) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_expiry'] = time() + 1800; 
}

$csrf_token = $_SESSION['csrf_token'];
$user_id = $_SESSION['user_id'];
$department = $_SESSION['department'];
include 'db.php';


$stmt = $conn->prepare("SELECT * FROM arrivee WHERE destination = ?");
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();
$courriers = $result->fetch_all(MYSQLI_ASSOC);

$stmt = $conn->prepare("SELECT COUNT(*) AS unread_count FROM arrivee WHERE destination = ? AND status = 'unread'");
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();
$unread_count = $result->fetch_assoc()['unread_count'];


$stmt = $conn->prepare("SELECT COUNT(*) AS new_count FROM arrivee WHERE destination = ? AND status = 'unread' AND DATE(date_received) = CURDATE()");
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();
$new_count = $result->fetch_assoc()['new_count'];

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9em;
        }
        .status-unread {
            background-color: #ffdddd;
        }
        .status-read {
            background-color: #ddffdd;
        }
        .status-responded {
            background-color: #ddddff;
        }
    </style>
</head>
<header>
<nav>
                
<div class="r">
<img src="img/logo-1-1.png"  alt="logo" class="logo">
                            <ul class="main-nav">
                                <li> <a href="logout.php">Log out</a></li>             
                            </ul> 
                        </div>    
                    </nav>
<nav>
</header>
<body>

    <div class="maindiv">

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">New Courriers</h4>
                    <p>You have <strong><?php echo $new_count; ?></strong> new courrier(s) received today.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">Unread Courriers</h4>
                    <p>You have <strong><?php echo $unread_count; ?></strong> unread courrier(s).</p>
                </div>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Destination</th>
                    <th>Type Courrier</th>
                    <th>Resume</th>
                    <th>Origine</th>
                    <th>Objet Courrier</th>
                    <th>A Suivre</th>
                    <th>Status</th>
                    <th>Action</th> 
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courriers as $courrier): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($courrier['Norder']); ?></td>
                        <td><?php echo htmlspecialchars($courrier['Destination']); ?></td>
                        <td><?php echo htmlspecialchars($courrier['type_courrier']); ?></td>
                        <td><?php echo htmlspecialchars($courrier['Resume']); ?></td>
                        <td><?php echo htmlspecialchars($courrier['origine']); ?></td>
                        <td><?php echo htmlspecialchars($courrier['objet_courrier']); ?></td>
                        <td><?php echo htmlspecialchars($courrier['a_suivre']); ?></td>
                        <td>
                            <?php if ($courrier['status'] == 'unread'): ?>
                                <span class="status status-unread">Unread</span>
                            <?php elseif ($courrier['status'] == 'read'): ?>
                                <span class="status status-read">Read</span>
                            <?php elseif ($courrier['status'] == 'responded'): ?>
                                <span class="status status-responded">Responded</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($courrier['status'] != 'responded'): ?>
                                <a href="respond_courrier.php?id=<?php echo $courrier['Norder']; ?>" class="btn btn-primary btn-sm">Respond</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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


nav{
  height: 50px;;
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
  display: inline-block !important;
  margin-right: 20px;
  
  
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
}
</style>
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
