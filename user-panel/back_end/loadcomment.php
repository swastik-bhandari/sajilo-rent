<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1); // Enable error reporting
ini_set('log_errors', 1);     // Log errors instead of displaying them
ini_set('error_log', 'php_errors.log'); // Specify the error log file

$email = $_REQUEST['email'];
$jsonarr = [];
$conn = new mysqli('localhost' , 'root' , '' , 'user_database');
if($conn->connect_error)
{
    die("lol conn error");
}
$query ="SELECT review.reviewer , review.reciever , review.rating  ,review.comment, review.date , signin.firstName , signin.lastName
FROM review 
INNER JOIN signin ON review.reviewer = signin.email
WHERE review.reciever = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);
if(!$stmt->execute())
{
    die("lol conn error while executing the query");

}
$result = $stmt->get_result();
if($result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        array_push($jsonarr, (["username" => $row['firstName']." ".$row['lastName'] , "comment"=>$row['comment'] , "rating"=>$row['rating'], "date"=>$row['date']  ]));
    }
   echo json_encode($jsonarr);
}
else{

    echo 'empty';
}
?>