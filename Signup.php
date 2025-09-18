<?php
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "1234";
$dbname     = "scheme";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userEmail = $_POST['email'] ?? '';
    $userName  = $_POST['name'] ?? '';

    if (!empty($userEmail) && !empty($userName)) {
        $stmt = $conn->prepare("INSERT IGNORE INTO users (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $userName, $userEmail);

        if ($stmt->execute()) {
            $message .= "<p class='success'>User registered successfully!</p>";

            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->SMTPDebug  = SMTP::DEBUG_OFF;
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'elijah.reech@strathmore.edu';
                $mail->Password   = 'whal blbb hrrr aebq';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                // Recipients
                $mail->setFrom('elijah.reech@strathmore.edu', 'BBIT Enterprise');
                $mail->addAddress($userEmail, $userName);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Welcome to BBIT Enterprise!';
                $mail->Body    = "
                    <h3>Hello " . htmlspecialchars($userName) . ",</h3>
                    <p>You have successfully created an account with <b>BBIT Enterprise</b>.</p>
                    <p>Please <a href='#'>Click Here</a> to complete your registration.</p>
                    <br>
                    Regards,<br>
                    Systems Admin<br>
                    <b>BBIT Enterprise</b>
                ";

                $mail->send();
                $message .= "<p class='success'>A welcome email has been sent to <b>{$userEmail}</b></p>";
            } catch (Exception $e) {
                $message .= "<p class='error'>Email could not be sent. Error: {$mail->ErrorInfo}</p>";
            }
        } else {
            $message .= "<p class='error'>Failed to register user.</p>";
        }
        $stmt->close();
    } else {
        $message .= "<p class='error'>Please enter both name and email.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Registration</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to right, #74ebd5, #9face6);
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
    }
    .container {
      background: #fff;
      margin-top: 50px;
      padding: 30px 40px;
      border-radius: 16px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
      max-width: 500px;
      width: 100%;
      animation: fadeIn 0.6s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    label {
      font-weight: 600;
      display: block;
      margin: 10px 0 5px;
      color: #555;
    }
    input[type="text"], input[type="email"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 14px;
      transition: border 0.3s;
    }
    input[type="text"]:focus, input[type="email"]:focus {
      border-color: #28a745;
      outline: none;
    }
    button {
      width: 100%;
      padding: 14px;
      border: none;
      border-radius: 8px;
      background: #28a745;
      color: #fff;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
    }
    button:hover {
      background: #218838;
      transform: scale(1.02);
    }
    .success {
      color: #28a745;
      text-align: center;
      font-weight: bold;
      margin-bottom: 15px;
      transition: color 0.3s;
    }
    .success:hover {
      color: #218838;
    }
    .error {
      color: red;
      text-align: center;
      font-weight: bold;
      margin-bottom: 15px;
    }
    hr {
      margin: 25px 0;
      border: none;
      border-top: 1px solid #eee;
    }
    ol {
      padding-left: 20px;
    }
    li {
      margin-bottom: 8px;
      color: #444;
    }
  </style>
  <script>
    function validateForm() {
      let name = document.getElementById("name").value.trim();
      let email = document.getElementById("email").value.trim();

      if (name === "") {
        alert("Full Name is required!");
        return false;
      }
      if (email === "") {
        alert("Email Address is required!");
        return false;
      }
      let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
      if (!email.match(emailPattern)) {
        alert("Please enter a valid email address!");
        return false;
      }
      return true;
    }
  </script>
</head>
<body>
  <div class="container">
    <h2>User Sign Up</h2>
    <?php if (!empty($message)) echo $message; ?>

    <form action="Signup.php" method="POST" onsubmit="return validateForm();">
      <label for="name">Full Name:</label>
      <input type="text" id="name" name="name" placeholder="Enter your full name" required>

      <label for="email">Email Address:</label>
      <input type="email" id="email" name="email" placeholder="Enter your email" required>

      <button type="submit">Sign Up</button>
    </form>

    <hr>
    <h2>Registered Users</h2>
    <?php
    $sql = "SELECT name, email FROM users ORDER BY name ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<ol>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($row['name']) . " (<i>" . htmlspecialchars($row['email']) . "</i>)</li>";
        }
        echo "</ol>";
    } else {
        echo "<p>No users found.</p>";
    }
    $conn->close();
    ?>
  </div>
</body>
</html>
