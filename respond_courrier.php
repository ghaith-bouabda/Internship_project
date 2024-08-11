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
</body>
</html>
