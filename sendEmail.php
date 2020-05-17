<?php

function died($error) {
    echo '{"status":"ERROR","message":"' . $error . '"}';
    die();
}

// validation expected data exists
if (!isset($_POST['email']) ||      //viem pridat tel.cislo a ine potrebne udaje
        !isset($_POST['message']) ||
        !isset($_POST['name'])) {
    died('We are sorry, but there appears to be a problem with the form you submitted.');
}

$email = $_POST['email']; // required
$sprava = $_POST['message']; // required
$meno = $_POST['name']; // not required

$email_content = "<p>Bola odoslaná správa prostredníctvom formulára na webe iwin.sk:<br/>" // co bude v emaili
        . "<strong>Meno:</strong>" . $meno . "<br/>"
        . "<strong>Email:</strong>" . $email . "<br/>"
        . "<strong>Správa:</strong><br/>"
        . "" . $sprava . "<br/>"
        . "</p>"
        . "<p>Toto je automaticky generovaný email.</p>";
$error_message = "";
$email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
if (!preg_match($email_exp, $email)) {
    $error_message .= 'The Email Address you entered does not appear to be valid.<br />' . $email;
}

if (strlen($error_message) > 0) {
    died($error_message);
}

function clean_string($string) {
    $bad = array("content-type", "bcc:", "to:", "cc:", "href");
    return str_replace($bad, "", $string);
}

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: Dizmain Web Formular <milalud0@dlazba-in.sk>' . "\r\n";  // nazov formulara a z akeho emailu sa to bude odosielat
//$headers .= 'Cc: myboss@example.com' . "\r\n";
// create email headers
$headers .= 'X-Mailer: PHP/' . phpversion();
//mail($recipient, $subject, $message, $headers);

require_once 'swiftmailer-master/lib/swift_required.php'; // aby som odoslala email, potrebujem mat zlozku swift-master

// Create the Transport
$transport = Swift_SmtpTransport::newInstance('smtp.dlazba-in.sk', 25) // v hostingu servery sme vytvorili email na odosielanie
        ->setUsername('milalud0@dlazba-in.sk') //aky email
        ->setPassword('Le2o8;eLYz') // pristupeve heslo
;

// Create the Mailer using your created Transport
$mailer = Swift_Mailer::newInstance($transport);

// Create a message
$message = Swift_Message::newInstance('Dizmas Contact Form')// nazov formulara
        ->setFrom(array('milalud0@dlazba-in.sk' => 'Dizmain Web Formular')) // email skade sa odosiela a nazov formulara
        ->setTo(array('milalud0@gmail.com')) //email kde bude prichadzat od ludi odpoved
        ->setSubject('Dizmain web formular')  // nazov formulara
        ->setBody($email_content, 'text/html; charset=UTF-8')
;

// Send the message
$result = $mailer->send($message);
echo '{"status":"OK","message":""}';
?>