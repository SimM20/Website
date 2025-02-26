<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['useremail']);
    $message = htmlspecialchars($_POST['usermessage']);
    $fileInfo = "None.";

    if (isset($_FILES['userfile']) && $_FILES['userfile']['error'] == 0) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $destination = $uploadDir . basename($_FILES['userfile']['name']);
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $destination)) {
            $fileInfo = "File uploaded: " . $destination;
        } else {
            $fileInfo = "Error uploading the file.";
        }
    }

    $to = "simon.moraleslozano@live.com";
    $subject = "Nuevo mensaje de contacto";
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";

    $body = "Nombre: $name\n";
    $body .= "Correo: $email\n";
    $body .= "Mensaje:\n$message\n";
    $body .= "\n$fileInfo";

    if (mail($to, $subject, $body, $headers)) {
        echo "Message sent successfully!";
    } else {
        echo "Message couldnt be sent, try again later.";
    }
} else {
    echo "Access declined";
}
?>
