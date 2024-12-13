<?php
class RestaurantDatabase {
    private $host = "localhost";
    private $database = "restaurant_reservations";
    private $user = "root";
    private $password = ""; // Consider moving this to a config file or environment variable
    private $connection;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->database, $this->port);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
        // Log successful connection instead of echoing
        error_log("Successfully connected to the database");
    }

    public function addReservation($customerId, $reservationTime, $numberOfGuests, $specialRequests) {
        $stmt = $this->connection->prepare(
            "INSERT INTO reservations (customerId, reservationTime, numberOfGuests, specialRequests) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("isss", $customerId, $reservationTime, $numberOfGuests, $specialRequests);
        
        // Check if execute was successful
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            error_log("Error executing query: " . $stmt->error);
            return false;
        }
    }

    public function getAllReservations() {
        $result = $this->connection->query("SELECT * FROM reservations");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addCustomer($customerName, $contactInfo) {
        $stmt = $this->connection->prepare(
            "INSERT INTO customers (customerName, contactInfo) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $customerName, $contactInfo);
        
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            error_log("Error executing query: " . $stmt->error);
            return false;
        }
    }

    public function getCustomerPreferences($customerId) {
        $stmt = $this->connection->prepare(
            "SELECT specialRequests FROM reservations WHERE customerId = ?"
        );
        $stmt->bind_param("i", $customerId); 
        $stmt->execute();
        $result = $stmt->get_result();
        $preferences = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $preferences;
    }

    // Optional: close the connection when you're done
    public function closeConnection() {
        $this->connection->close();
    }
}
?>
