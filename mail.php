<?php
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

//create variables for database connection
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "scheme";

//create connection
$conn = new mysqli($servername, $username, $password, $dbname );

//check connection
if ($conn->connect_error){
    die("Connection Failed" . $conn->connect_error);

}
else{
    echo "Connected Successfully" . $dbname;
}

// Capture user input (from form submission)
$userEmail = $_POST['email'] ?? 'asuntanana@gmail.com';
$userName  = $_POST['name'] ?? 'Asunta';




// Save user into database
$stmt = $conn->prepare("INSERT IGNORE INTO users (name, email) VALUES (?, ?)");
$stmt->bind_param("ss", $userName, $userEmail);
$stmt->execute();
$stmt->close();

$sql = "SELECT name, email FROM users ORDER BY name ASC";
$result = $conn->query($sql);

echo "<h2>Registered Users</h2>";
if ($result->num_rows > 0) {
    echo "<ol>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['name']) . " (" . htmlspecialchars($row['email']) . ")</li>";
    }
    echo "</ol>";
} else {
    echo "No users found.";
}

$conn->close();


//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'elijah.reech@strathmore.edu';                     //SMTP username
    $mail->Password   = '';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('elijah.reech@strathmore.edu', 'Reech');
    $mail->addAddress('asuntanana@gmail.com', 'Asunta');     //Add a recipient
   
    $mail->isHTML(true);
    $mail->Subject = 'Welcome to BBIT course!';
    $mail->Body    = "
        <h3>Hello Avaia,</h3>
        <p>You have successfully created an account with <b>BBIT  course</b>.</p>
        <p>In order to use this account, you need to <a href='#'>Click Here</a> to complete the registration process.</p>
        <br>
        Regards,<br>
        Systems Admin<br>
        BBIT course
    ";

    $mail->send();
    echo "Message has been sent to {asuntanana@gmail.com}";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}