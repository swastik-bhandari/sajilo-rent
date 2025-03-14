<?php 

    session_start();
    $email = $_SESSION["email"];
    $jsonarray = [];
    $conn = new mysqli('localhost', 'root', '', 'user_database');

    // Check connection
    if ($conn->connect_error) {
        die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
    }

    $query = "SELECT booked.email, booked.owner, profilepicture.image, signin.firstName, signin.lastName
              FROM booked 
              LEFT JOIN profilepicture ON booked.email = profilepicture.email
              INNER JOIN signin ON booked.email = signin.email 
              WHERE booked.owner = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die(json_encode(['error' => 'Prepare failed: ' . $conn->error]));
    }

    $stmt->bind_param("s", $email);
    
    if (!$stmt->execute()) {
        die(json_encode(['error' => 'Execution failed: ' . $stmt->error]));
    }

    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {   
            if ($row['owner'] === $email) {
                array_push($jsonarray, [
                    "email" => $row['email'], 
                    "username" => $row['firstName'] . ' ' . $row['lastName'], 
                    "image" => $row['image'] ? $row['image'] : 'images/default-profile.png'
                ]);
            } else {
                array_push($jsonarray, [
                    "email" => $row['owner'], 
                    "username" => $row['firstName'] . ' ' . $row['lastName'], 
                    "image" => $row['image'] ? $row['image'] : 'images/default-profile.png'
                ]);
            }
        }
    } else {
        echo json_encode(['error' => 'No records found']);
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->close();
    $conn->close();
    echo json_encode($jsonarray);

?>