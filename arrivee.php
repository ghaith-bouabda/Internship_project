
<?php 
session_start();
include 'db.php';

$csrf_token = $_SESSION['csrf_token'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo '<script type="text/javascript">
            window.onload = function () { alert("Invalid CSRF token"); }
        </script>';
        exit;
    }
}
    
function fetchOptions($table, $conn) {
    $options = [];
    $result = $conn->query("SELECT * FROM $table");
    while ($row = $result->fetch_assoc()) {
        $options[] = $row;
    }
    return $options;
}
$optionasuivreOptions = fetchOptions('optionasuivre', $conn);
$optiondestOptions = fetchOptions('optiondest', $conn);
$optionobjetcourrOptions = fetchOptions('optionobjetcourr', $conn);
$optionoriginOptions = fetchOptions('optionorigin', $conn);
$optiontypecourrierOptions = fetchOptions('optiontypecourrier', $conn);

$result1 = $conn->query("SELECT * FROM arrivee");

$result = $conn->query("SELECT MAX(Norder) AS max_id FROM arrivee");
$row = $result->fetch_assoc();
$next_id = $row['max_id'] + 1;
 $today = date('Y-m-d');
 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $id = $_POST['id'];
    $dest = $_POST['dest'];
    $dossier = $_POST['dossier'];
    $montant = $_POST['montant'];
    $ref = $_POST['ref'];
    $datec = $_POST['datec'];
    $contact = $_POST['contact'];
    $resume = $_POST['resume'];
    $classement = $_POST['classement'];
    $typec = $_POST['typec'];
    $origine = $_POST['origine'];
    $objc = $_POST['objc'];
    $follow = $_POST['follow'];
    $img = $_FILES['img']['name'];

  
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["img"]["name"]);

    if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
        echo "The file ". htmlspecialchars( basename( $_FILES["img"]["name"])). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }

   $stmt = $conn->prepare("INSERT INTO arrivee ( Date, Destination, dossier, montant, reference, date_courrier, contact, Resume, Classement, Type_courrier, Origine, Objet_courrier, A_suivre, Piece_jointe,user_id) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
    $stmt->bind_param("sssissssssssssi", $today, $dest, $dossier, $montant, $ref, $datec, $contact, $resume, $classement, $typec, $origine, $objc, $follow, $img,$user_id);

    if ($stmt->execute()) {
       
        header("Location: arrivee.php");
        exit(); 
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
   
     <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    </head>
<body>
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
    <div class="nav">
        <p><a href="dashboard.php"><img src="img/Logo-Secil-min-300x288.png" width=30px></a></p>
    
    <div class="right-links">

        <a href="logout.php"><button class="btn">Log out</button></a>
        
    </div>
</div>
<div class="container h">
    <div class="form-group">
        <form method="post" class="form-inline" id="registrationForm" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

            <div class="input-group mb-3">
                <label for="id" class="input-group-text">N° Ordre:</label>
                <input type="number" id="id" name="id" class="form-control" value="<?php echo $next_id; ?>" readonly>
            </div>
            <div class="input-group mb-3">
                <label for="date" class="input-group-text">Date:</label>
                <input type="date" id="date" name="nomfour" class="form-control" disabled value="<?php echo $today; ?>">
            </div>
            <div class="input-group mb-3">
                <label for="dest" class="input-group-text">Destination:</label>
                <select id="dest" name="dest" class="form-select" required>
                    <?php foreach ($optiondestOptions as $option): ?>
                        <option value="<?php echo $option['value']; ?>"><?php echo $option['value']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group mb-3">
                <label for="dossier" class="input-group-text">Dossier:</label>
                <input type="text" id="dossier" name="dossier" class="form-control" required>
            </div>
            <div class="input-group mb-3">
                <label for="montant" class="input-group-text">Montant:</label>
                <input type="number" id="montant" name="montant" class="form-control" required>
            </div>
            <div class="input-group mb-3">
                <label for="ref" class="input-group-text">Reference:</label>
                <input type="text" id="ref" name="ref" class="form-control" required>
            </div>
            <div class="input-group mb-3">
                <label for="datec" class="input-group-text">Date Courrier:</label>
                <input type="date" id="datec" name="datec" class="form-control" required>
            </div>
            <div class="input-group mb-3">
                <label for="contact" class="input-group-text">Contact:</label>
                <input type="text" id="contact" name="contact" class="form-control" required>
            </div>
            <div class="input-group">
                <label for="resume" class="input-group-text">Resume:</label>
                <textarea id="resume" name="resume" class="form-control" rows="4" cols="50"></textarea>
            </div>
            <br>
            <div class="input-group mb-3">
                <label for="classement" class="input-group-text">Classement:</label>
                <input type="text" id="classement" name="classement" class="form-control" required>
            </div>
            <div class="input-group mb-3">
                <label for="typec" class="input-group-text">Type Courrier:</label>
                <select id="typec" name="typec" class="form-select" required>
                    <?php foreach ($optiontypecourrierOptions as $option): ?>
                        <option value="<?php echo $option['value']; ?>"><?php echo $option['value']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group mb-3">
                <label for="origine" class="input-group-text">Origine:</label>
                <select id="origine" name="origine" class="form-select" required>
                    <?php foreach ($optionoriginOptions as $option): ?>
                        <option value="<?php echo $option['value']; ?>"><?php echo $option['value']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group mb-3">
                <label for="objc" class="input-group-text">Objet Courrier:</label>
                <select id="objc" name="objc" class="form-select" required>
                    <?php foreach ($optionobjetcourrOptions as $option): ?>
                        <option value="<?php echo $option['value']; ?>"><?php echo $option['value']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group mb-3">
                <label for="follow" class="input-group-text">A Suivre:</label>
                <select id="follow" name="follow" class="form-select" required>
                    <?php foreach ($optionasuivreOptions as $option): ?>
                        <option value="<?php echo $option['value']; ?>"><?php echo $option['value']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group mb-3">
                <label for="img" class="input-group-text">Piece Jointe:</label>
                <input type="file" id="img" name="img" class="form-control" required>
            </div>
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-secondary" value="Reset">
        </form>
    </div>
</div>

       
    </body>

</html>
