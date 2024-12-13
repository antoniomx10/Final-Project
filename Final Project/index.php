<?php
// 1. Database connection
$host = 'localhost';  // Change this to your database host
$dbname = 'restaurant_reservations'; // Your database name
$username = 'root';  // Your database username
$password = '';  // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// 2. Check if form is submitted (addReservation action)
if (isset($_GET['action']) && $_GET['action'] === 'addReservation' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate the form data
    $customer_id = htmlspecialchars($_POST['customer_id']);
    $reservation_time = $_POST['reservation_time']; // Assuming it's valid date-time
    $number_of_guests = (int) $_POST['number_of_guests'];
    $special_requests = htmlspecialchars($_POST['special_requests']);

    // Validate data
    if (empty($customer_id) || empty($reservation_time) || empty($number_of_guests)) {
        $error_message = "Please fill in all required fields.";
    } else {
        try {
            // 3. Insert reservation into the database
            $stmt = $pdo->prepare("INSERT INTO reservations (customer_id, reservation_time, number_of_guests, special_requests) VALUES (?, ?, ?, ?)");
            $stmt->execute([$customer_id, $reservation_time, $number_of_guests, $special_requests]);

            // 4. Display success message
            $success_message = "Reservation added successfully!";
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Reservation</title>
</head>
<body>
    <h1>Add Reservation</h1>
    
    <!-- Display success or error message -->
    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <!-- Reservation Form -->
    <form method="POST" action="index.php?action=addReservation">
        Customer ID: <input type="text" name="customer_id" required><br>
        Reservation Time: <input type="datetime-local" name="reservation_time" required><br>
        Number of Guests: <input type="number" name="number_of_guests" required><br>
        Special Requests: <textarea name="special_requests"></textarea><br>
        <button type="submit">Submit</button>
    </form>

    <a href="index.php">Back to Home</a>
</body>
</html>
