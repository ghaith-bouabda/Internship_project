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
$document = null; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['filter'])) {
        $criteria = $_POST['criteria'];
        $value = $_POST['value'];

        $sql = "SELECT * FROM arrivee WHERE 1=1";
        $params = [];
        $types = '';

        if ($criteria && $value) {
            $sql .= " AND $criteria = ?";
            $types .= 's';
            $params[] = $value;
        }

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $documents = $result->fetch_all(MYSQLI_ASSOC);

        if (empty($documents)) {
            echo "No document found with the provided criteria.";
        }
    } else {
        $user_id = $_SESSION['user_id'];
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

        if ($img) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["img"]["name"]);
            if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                echo "The file " . htmlspecialchars(basename($_FILES["img"]["name"])) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            $img = $_POST['existing_img'];
        }

        $stmt = $conn->prepare("UPDATE arrivee SET Destination=?, dossier=?, montant=?, reference=?, date_courrier=?, contact=?, Resume=?, Classement=?, Type_courrier=?, Origine=?, Objet_courrier=?, A_suivre=?, Piece_jointe=?  WHERE Norder=? AND user_id=?");
        $stmt->bind_param("ssdissssssssssi", $dest, $dossier, $montant, $ref, $datec, $contact, $resume, $classement, $typec, $origine, $objc, $follow, $img, $id, $user_id);

        if ($stmt->execute()) {
            header("Location: arriveeedit.php?search=" . $id);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        $conn->close();
    }
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
        }
    </style>
</head>
<body>
    <div class="nav">
        <p><a href="dashboard.php"><img src="img/Logo-Secil-min-300x288.png" width=30px></a></p>
        <div class="right-links">
            <a href="logout.php"><button class="btn">Log out</button></a>
        </div>
    </div>
    <div class="container">
        <h2>Search Document</h2>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div class="mb-3">
                <label for="criteria" class="form-label">Filter Criteria:</label>
                <select id="criteria" name="criteria" class="form-select" required>
                    <option value="">Select criteria...</option>
                    <option value="Destination">Destination</option>
                    <option value="type_courrier">Type Courrier</option>
                    <option value="origine">Origine</option>
                    <option value="objet_courrier">Objet Courrier</option>
                    <option value="a_suivre">A Suivre</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="value" class="form-label">Value:</label>
                <input type="text" id="value" name="value" class="form-control" required>
            </div>
            <button type="submit" name="filter" class="btn btn-primary">Search</button>
        </form>

        <?php if (!empty($documents)): ?>
            <div class="container mt-5">
                <h2>Documents Found</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>N° Order</th>
                            <th>Destination</th>
                            <th>Dossier</th>
                            <th>Montant</th>
                            <th>Reference</th>
                            <th>Date Courrier</th>
                            <th>Contact</th>
                            <th>Resume</th>
                            <th>Classement</th>
                            <th>Type Courrier</th>
                            <th>Origine</th>
                            <th>Objet Courrier</th>
                            <th>A Suivre</th>
                            <th>Status</th>
                            <th>Piece Jointe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($doc['Norder']); ?></td>
                                <td><?php echo htmlspecialchars($doc['Destination']); ?></td>
                                <td><?php echo htmlspecialchars($doc['dossier']); ?></td>
                                <td><?php echo htmlspecialchars($doc['montant']); ?></td>
                                <td><?php echo htmlspecialchars($doc['reference']); ?></td>
                                <td><?php echo htmlspecialchars($doc['date_courrier']); ?></td>
                                <td><?php echo htmlspecialchars($doc['contact']); ?></td>
                                <td><?php echo htmlspecialchars($doc['Resume']); ?></td>
                                <td><?php echo htmlspecialchars($doc['Classement']); ?></td>
                                <td><?php echo htmlspecialchars($doc['type_courrier']); ?></td>
                                <td><?php echo htmlspecialchars($doc['origine']); ?></td>
                                <td><?php echo htmlspecialchars($doc['objet_courrier']); ?></td>
                                <td><?php echo htmlspecialchars($doc['a_suivre']); ?></td>
                                <td><?php echo htmlspecialchars($doc['status']); ?></td>
                                <td><a href="uploads/<?php echo htmlspecialchars($doc['Piece_jointe']); ?>" target="_blank">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif (isset($_POST['filter'])): ?>
            <p>No document found with the provided criteria.</p>
        <?php endif; ?>
    </div>
</body>
</html>
