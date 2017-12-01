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
 * Patient : les actions avec reload de page
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */


//$debug='';
$m=$match['params']['m'];

$acceptedModes=array(
    'saveCsForm', // sauver le formulaire de consultation
    'sendMail', // envoyer un mail
    'saveOrdoForm', // sauver une ordonnance
    'saveReglementForm', // sauver une ordonnance
    'changeObjetCreationDate' // changer le creationDate d'un objet
);

if (!in_array($m, $acceptedModes)) {
    die;
} else {
    include('inc-action-'.$m.'.php');
}
