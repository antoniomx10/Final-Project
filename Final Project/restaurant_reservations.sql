-- Database Setup
CREATE DATABASE restaurant_reservations;
USE restaurant_reservations;

-- Create Customers Table
CREATE TABLE Customers (
	customerId INT NOT NULL UNIQUE AUTO_INCREMENT,
    customerName VARCHAR(45) NOT NULL,
    contactInfo VARCHAR(200),
    PRIMARY KEY (customerId)
);

-- Create Reservations Table
CREATE TABLE Reservations (
	reservationId INT NOT NULL UNIQUE AUTO_INCREMENT,
    customerId INT NOT NULL,
    reservationTime DATETIME NOT NULL,
    numberOfGuests INT NOT NULL,
    specialRequests VARCHAR(200),
    PRIMARY KEY (reservationId),
    FOREIGN KEY (customerId) REFERENCES Customers(customerId)
);

-- Create DiningPreferences Table
CREATE TABLE DiningPreferences (
	preferenceId INT NOT NULL UNIQUE AUTO_INCREMENT,
    customerId INT NOT NULL,
    favoriteTable VARCHAR(45),
    dietaryRestrictions VARCHAR(200),
    PRIMARY KEY (preferenceId),
    FOREIGN KEY (customerId) REFERENCES Customers(customerId)
);

-- Stored Procedure: findReservations
DELIMITER //
CREATE PROCEDURE findReservations(IN inputcustomerId INT)
BEGIN
	SELECT * FROM Reservations WHERE customerId = inputcustomerId;
END //
DELIMITER ;

-- Stored Procedures: addSpecialRequest
DELIMITER // 
CREATE PROCEDURE addSpecialRequest(IN inputReservationId INT, IN requests VARCHAR(200))
BEGIN
	UPDATE Reservations SET specialRequests = requests WHERE reservationId = inputReservationId;
END //
DELIMITER ;

-- Stored Procedure: addReservation
DELIMITER // 
CREATE PROCEDURE addReservation(
	IN inputCustomerName VARCHAR(45),
    IN inputContactInfo VARCHAR(200),
    IN inputReservationTime DATETIME,
    IN inputNumberOfGuests INT,
    IN inputSpecialRequests VARCHAR(200)
)
BEGIN
    -- Declare variable for customerId
    DECLARE existingCustomerId INT;

    -- Check if customer exists
    SELECT customerId INTO existingCustomerId
    FROM Customers
    WHERE customerName = inputCustomerName AND contactInfo = inputContactInfo;

    IF existingCustomerId IS NULL THEN
        -- Insert new customer if not found
        INSERT INTO Customers (customerName, contactInfo)
        VALUES (inputCustomerName, inputContactInfo);

        -- Get the customerId of the newly created customer
        SELECT LAST_INSERT_ID() INTO existingCustomerId;
    END IF;

    -- Create a reservation
    INSERT INTO Reservations (customerId, reservationTime, numberOfGuests, specialRequests)
    VALUES (existingCustomerId, inputReservationTime, inputNumberOfGuests, inputSpecialRequests);
END //
DELIMITER ;

-- Data Population

-- Insert Customers
INSERT INTO Customers (customerName, contactInfo)
VALUES
('John Doe', 'john.doe@example.com'),
('Jane Smith', 'jane.smith@example.com'),
('Alice Johnson', 'alice.johnson@example.com');

-- Insert Reservations
INSERT INTO Reservations (customerId, reservationTime, numberOfGuests, specialRequests)
VALUES
(1, '2024-12-12 19:00:00', 4, 'Window seat'),
(2, '2023-12-16 18:20:00', 2, 'Vegetarian options'),
(3, '2024-12-17 20:00:00', 6, 'Anniversary celebration');

-- Insert DiningPreferences
INSERT INTO DiningPreferences (customerId, favoriteTable, dietaryRestrictions)
VALUES
(1, 'Table 5', 'Gluten-Free'),
(2, 'Table 3', 'None'),
(3, 'Table 7', 'Vegan');