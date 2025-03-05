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
        $filePath = $uploadDir . basename($_FILES['userfile']['name']);
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $filePath)) {
            $fileInfo = "File uploaded: " . $filePath;
        } else {
            $fileInfo = "Error uploading the file.";
        }
    }

    $boundary = md5(time());

    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    $body = "--$boundary\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n";
    $body .= "Nombre: $name\n";
    $body .= "Correo: $email\n";
    $body .= "Mensaje:\n$message\n";
    $body .= "\n$fileInfo\r\n";

    if (isset($filePath) && file_exists($filePath)) {
        $fileContent = file_get_contents($filePath);
        $fileContent = chunk_split(base64_encode($fileContent));

        $body .= "--$boundary\r\n";
        $body .= "Content-Type: " . mime_content_type($filePath) . "; name=\"" . basename($filePath) . "\"\r\n";
        $body .= "Content-Disposition: attachment; filename=\"" . basename($filePath) . "\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "\r\n";
        $body .= $fileContent;
        $body .= "\r\n";
    }

    $body .= "--$boundary--";

    $to = "simon.moraleslozano@live.com";
    $subject = "Nuevo mensaje de contacto";
    
    if (mail($to, $subject, $body, $headers)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>