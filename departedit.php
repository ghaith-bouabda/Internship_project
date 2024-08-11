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
        exit();
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
$documents = null;
$document = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['filter'])) {
        $criteria = $_POST['criteria'];
        $value = $_POST['value'];
        $user_id = $_SESSION['user_id']; 

        $sql = "SELECT * FROM depart WHERE user_id = ?";
        $params = [$user_id]; 
        $types = 'i'; 

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
    } elseif (isset($_POST['update'])) {
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

        $stmt = $conn->prepare("UPDATE depart SET Destination=?, dossier=?, montant=?, reference=?, date_courrier=?, contact=?, Resume=?, Classement=?, Type_courrier=?, Origine=?, Objet_courrier=?, A_suivre=?, Piece_jointe=? WHERE Norder=? AND user_id=?");
        $stmt->bind_param("ssdissssssssssi", $dest, $dossier, $montant, $ref, $datec, $contact, $resume, $classement, $typec, $origine, $objc, $follow, $img, $id, $user_id); 

        if ($stmt->execute()) {
            header("Location: departedit.php?search=" . $id);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        $conn->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $user_id = $_SESSION['user_id']; 
    $stmt = $conn->prepare("SELECT * FROM depart WHERE Norder = ? AND user_id = ?"); 
    $stmt->bind_param("ii", $id, $user_id); 
    $stmt->execute();
    $result = $stmt->get_result();
    $document = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Document</title>
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
        <?php if (!isset($document)): ?>
            <h2>Search Document</h2>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <div class="mb-3">
                    <label for="criteria" class="form-label">Filter Criteria:</label>
                    <select id="criteria" name="criteria" class="form-select" required>
                        <option value="">Select criteria...</option>
                        <option value="Destination">Destination</option>
                        <option value="Type_courrier">Type Courrier</option>
                        <option value="Origine">Origine</option>
                        <option value="Objet_courrier">Objet Courrier</option>
                        <option value="A_suivre">A Suivre</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="value" class="form-label">Value:</label>
                    <input type="text" id="value" name="value" class="form-control" required>
                </div>
                <button type="submit" name="filter" class="btn btn-primary">Search</button>
            </form>

            <?php if ($documents): ?>
                <h2>Search Results</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Destination</th>
                            <th>Type Courrier</th>
                            <th>Resume</th>
                            <th>Origine</th>
                            <th>Objet Courrier</th>
                            <th>A Suivre</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $document): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($document['Norder']); ?></td>
                                <td><?php echo htmlspecialchars($document['Destination']); ?></td>
                                <td><?php echo htmlspecialchars($document['type_courrier']); ?></td>
                                <td><?php echo htmlspecialchars($document['Resume']); ?></td>
                                <td><?php echo htmlspecialchars($document['origine']); ?></td>
                                <td><?php echo htmlspecialchars($document['objet_courrier']); ?></td>
                                <td><?php echo htmlspecialchars($document['a_suivre']); ?></td>
                                <td>
                                    <a href="departedit.php?id=<?php echo htmlspecialchars($document['Norder']); ?>" class="btn btn-secondary">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php else: ?>
            <h2>Edit Document</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($document['Norder']); ?>">
                <input type="hidden" name="existing_img" value="<?php echo htmlspecialchars($document['Piece_jointe']); ?>">
             
                <div class="mb-3">
                    <label for="dest" class="form-label">Destination:</label>
                    <select id="dest" name="dest" class="form-select" required>
                        <?php foreach ($optiondestOptions as $option): ?>
                            <option value="<?php echo htmlspecialchars($option['value']); ?>" <?php echo $option['value'] == $document['Destination'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($option['value']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="dossier" class="form-label">Dossier:</label>
                    <input type="text" id="dossier" name="dossier" class="form-control" value="<?php echo htmlspecialchars($document['dossier']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="montant" class="form-label">Montant:</label>
                    <input type="number" id="montant" name="montant" class="form-control" value="<?php echo htmlspecialchars($document['montant']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="ref" class="form-label">Reference:</label>
                    <input type="text" id="ref" name="ref" class="form-control" value="<?php echo htmlspecialchars($document['reference']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="datec" class="form-label">Date Courrier:</label>
                    <input type="date" id="datec" name="datec" class="form-control" value="<?php echo htmlspecialchars($document['date_courrier']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="contact" class="form-label">Contact:</label>
                    <input type="text" id="contact" name="contact" class="form-control" value="<?php echo htmlspecialchars($document['contact']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="resume" class="form-label">Resume:</label>
                    <textarea id="resume" name="resume" class="form-control" rows="3" required><?php echo htmlspecialchars($document['Resume']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="classement" class="form-label">Classement:</label>
                    <input type="text" id="classement" name="classement" class="form-control" value="<?php echo htmlspecialchars($document['Classement']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="typec" class="form-label">Type Courrier:</label>
                    <select id="typec" name="typec" class="form-select" required>
                        <option value="">Select Type Courrier...</option>
                        <?php foreach ($optiontypecourrierOptions as $option): ?>
                            <option value="<?php echo htmlspecialchars($option['value']); ?>" <?php echo $option['value'] == $document['type_courrier'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($option['value']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="origine" class="form-label">Origine:</label>
                    <select id="origine" name="origine" class="form-select" required>
                        <option value="">Select Origine...</option>
                        <?php foreach ($optionoriginOptions as $option): ?>
                            <option value="<?php echo htmlspecialchars($option['value']); ?>" <?php echo $option['value'] == $document['origine'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($option['value']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="objc" class="form-label">Objet Courrier:</label>
                    <select id="objc" name="objc" class="form-select" required>
                        <option value="">Select Objet Courrier...</option>
                        <?php foreach ($optionobjetcourrOptions as $option): ?>
                            <option value="<?php echo htmlspecialchars($option['value']); ?>" <?php echo $option['value'] == $document['objet_courrier'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($option['value']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="follow" class="form-label">A Suivre:</label>
                    <select id="follow" name="follow" class="form-select" required>
                        <option value="">Select A Suivre...</option>
                        <?php foreach ($optionasuivreOptions as $option): ?>
                            <option value="<?php echo htmlspecialchars($option['value']); ?>" <?php echo $option['value'] == $document['a_suivre'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($option['value']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="img" class="form-label">Piece Jointe:</label>
                    <input type="file" id="img" name="img" class="form-control">
                    <?php if ($document['Piece_jointe']): ?>
                        <br><a href="uploads/<?php echo htmlspecialchars($document['Piece_jointe']); ?>" target="_blank" class="btn btn-secondary" >View the file</a>
                        <?php endif; ?>
                </div>
                <button type="submit" name="update" class="btn btn-primary">Update Document</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
