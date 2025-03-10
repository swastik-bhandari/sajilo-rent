
<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1); // Enable error reporting
ini_set('log_errors', 1);     // Log errors instead of displaying them
ini_set('error_log', 'php_errors.log'); // Specify the error log file


$sender = $_REQUEST['sender'];
$reciever = $_REQUEST['reciever'];
$status = $_REQUEST['status'];  
$jsonarray = [];
$studentorowner = '';
$seenornot = '';

$conn = new mysqli('localhost' , 'root' , '' , 'user_database');
if($conn->connect_error)
{   
    die(''. $conn->connect_error);
}
$query = "SELECT chat.sender , chat.reciever ,chat.id, chat.message , verified_users.status ,chat.seenornot FROM chat 
INNER JOIN verified_users ON verified_users.email = chat.sender 
WHERE (chat.sender = ? AND chat.reciever = ?) 
   OR (chat.reciever = ? AND chat.sender = ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssss" , $sender , $reciever , $sender , $reciever);

if(!$stmt->execute()){
    die("lol error");
}
$result = $stmt->get_result();

if($result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {   
        array_push($jsonarray , ["sender" => $row['sender'] , "reciever"=> $row['reciever'] , "message"=>$row['message'] , "seenornot"=>$row['seenornot'] , 'id'=>$row['id']]);
        $studentorowner = $row['status'];
        $seenornot = $row['seenornot'];
    }
}
if ($seenornot === 'unseen') {
    // If unseen and student sees it
    if ($status === 'student') {
        $seenornot = 'studentseen';
    }
    // If unseen and owner sees it
    else if ($status === 'owner') {
        $seenornot = 'ownerseen';
    }
} 
else if ($seenornot === 'studentseen' && $status=== 'owner') {
    // If student had seen it and now owner sees it, mark as fully seen
    $seenornot = 'seen';
} 
else if ($seenornot === 'ownerseen' && $status === 'student') {
    // If owner had seen it and now student sees it, mark as fully seen
    $seenornot = 'seen';
}
$query =  "UPDATE chat SET seenornot = ?
WHERE (sender = ? AND reciever = ?) 
   OR (reciever = ? AND sender = ?);";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssss" , $seenornot,$sender , $reciever , $sender , $reciever);
if(!$stmt->execute()){
    echo 'error here ';
};
$stmt->close();
$conn->close();
echo json_encode($jsonarray);
?>