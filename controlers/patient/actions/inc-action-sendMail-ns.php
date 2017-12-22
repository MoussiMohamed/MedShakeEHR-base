<?php
/*
 * This file is part of MedShakeEHR.
 *
 * Copyright (c) 2017
 * Bertrand Boutillier <b.boutillier@gmail.com>
 * http://www.medshake.net
 *
 * MedShakeEHR is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * MedShakeEHR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MedShakeEHR.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Patient > action : envoyer un mail (smtp standard)
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */

$mail = new PHPMailer\PHPMailer\PHPMailer;
$mail->CharSet = 'UTF-8';
//$mail->SMTPDebug = 4;
$mail->isSMTP();
$mail->Host = $p['config']['smtpHost'];
$mail->SMTPAuth = true;
$mail->Username = $p['config']['smtpUsername'];
$mail->Password = $p['config']['smtpPassword'];
if($p['config']['smtpOptions'] == 'on') {
  $mail->SMTPOptions = array(
    'ssl' => array(
      'verify_peer' => false,
      'verify_peer_name' => false,
      'allow_self_signed' => true
    )
  );
}
if(!empty($p['config']['smtpSecureType'])) $mail->SMTPSecure = $p['config']['smtpSecureType'];
$mail->Port = $p['config']['smtpPort'];


//obtenir le chemin complet de la pj
if (isset($_POST['objetID'])) {
    $doc = new msStockage;
    $doc->setObjetID($_POST['objetID']);
    $sourceFile=$doc->getPathToDoc();
    $ext=$doc->getFileExtOfDoc();
}


$mail->isHTML(false);
$mail->Subject = $_POST['mailSujet'];

$mail->setFrom($_POST['mailFrom'], $_POST['mailFromName']);
$mail->addAddress($_POST['mailTo']);

if (is_file($sourceFile)) {
    $mail->addAttachment($sourceFile, "document.".$ext);
}
$mail->Body    =  nl2br($_POST['mailBody']);
$mail->AltBody = $mail->Body;


if (!$mail->send()) {
    echo 'Le message n\'a pu être envoyé.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    //echo 'envoyé !';

    //logs
    $patient = new msObjet();
    $patient->setFromID($p['user']['id']);
    $patient->setToID($_POST['patientID']);

    //support (avec PJ ou sans)
    if (isset($_POST['objetID'])) {
        $supportID=$patient->createNewObjetByTypeName('mailPorteur', '', 1, $_POST['objetID']);
    } else {
        $supportID=$patient->createNewObjetByTypeName('mailPorteur', '', 1);
    }

    //from
    $patient->createNewObjetByTypeName('mailFrom', $_POST['mailFrom'], 1, $supportID);
    //to
    $patient->createNewObjetByTypeName('mailTo', $_POST['mailTo'], 1, $supportID);
    //sujet
    $patient->createNewObjetByTypeName('mailSujet', $_POST['mailSujet'], 1, $supportID);
    //message
    $patient->createNewObjetByTypeName('mailBody', $_POST['mailBody'], 1, $supportID);
    //pj ID
    if (isset($_POST['objetID'])) {
        $patient->createNewObjetByTypeName('mailPJ1', $_POST['objetID'], 1, $supportID);
    }

    msTools::redirection('/patient/'.$_POST['patientID'].'/');
}
