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
 * Cron : rappels mails
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */

ini_set('display_errors', 1);
setlocale(LC_ALL, "fr_FR.UTF-8");
session_start();

/////////// Composer class auto-upload
require '../vendor/autoload.php';

/////////// Class medshakeEHR auto-upload
spl_autoload_register(function ($class) {
    include '../class/' . $class . '.php';
});


/////////// Config loader
$p['config']=Spyc::YAMLLoad('../config/config.yml');

/////////// SQL connexion
$mysqli=msSQL::sqlConnect();


/**
 * Envoi du mail de rappel
 * @param  array $pa tableau des var
 * @return array     tableau de log
 */
function sendmail($pa)
{
    global $p;

    $mail = new PHPMailer;
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
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

    $mail->setFrom($p['config']['smtpFrom'], $p['config']['smtpFromName']);
    $mail->addAddress($pa['email'], $pa['identite']);
    $mail->Subject = 'Rappel rdv le '.$pa['jourRdv'].' à '.$pa['heureRdv'];

    $msgRappel="Bonjour,\n\nNous vous rappelons votre RDV du ".$pa['jourRdv']." à ".$pa['heureRdv']." avec le Dr ... .\n\nNotez bien qu’aucun autre rendez-vous ne sera donné à une patiente n’ayant pas honoré le premier.\n\nMerci de votre confiance,\nÀ bientôt !\n\nPS : Ceci est un mail automatique, merci de ne pas répondre.";

    $mail->Body = nl2br($msgRappel);
    $mail->AltBody = $msgRappel;

    if (!$mail->send()) {
        $pa['status']=$mail->ErrorInfo;
    } else {
        $pa['status']="message envoyé";
    }
    return $pa;
}


$tsJourRDV=time()+($p['config']['mailRappelDaysBeforeRDV']*24*60*60);

$patientsList=file_get_contents('http://192.0.0.0/patientsDuJour.php?date='.date("Y-m-d", $tsJourRDV));
$patientsList=json_decode($patientsList, true);



if (is_array($patientsList)) {
    $listeID=array_column($patientsList, 'id');

    $listeEmail=msSQL::sql2tabKey("select toID, value from objets_data where toId in ('".implode("', '", $listeID)."') and typeID=4 and deleted='' and outdated='' ", 'toID', 'value');

    $date_sms=date("d/m/y", $tsJourRDV);

    $dejaInclus=[];
    foreach ($patientsList as $patient) {
        if (isset($listeEmail[$patient['id']])) {
            if (!in_array($listeEmail[$patient['id']], $dejaInclus)) {


                $detinataire=array(
                  'id'=>$patient['id'],
                  'typeCs'=>$patient['type'],
                  'jourRdv'=>$date_sms,
                  'heureRdv'=>$patient['heure'],
                  'identite'=>$patient['identite'],
                  'email'=>$listeEmail[$patient['id']]
                );
                $log[]=sendmail($detinataire);
            }
            $dejaInclus[]=$listeEmail[$patient['id']];
        }
    }

    //log json
    $logFileDirectory=$p['config']['mailRappelLogCampaignDirectory'].date('Y/m/d/');
    msTools::checkAndBuildTargetDir($logFileDirectory);
    file_put_contents($logFileDirectory.'RappelsRDV.json', json_encode($log));

}