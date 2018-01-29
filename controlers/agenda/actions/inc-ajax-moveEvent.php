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
 * Agenda : déplacer un rdv de l'agenda
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */

$event = new msAgenda();
$event->set_fromID($p['user']['id']);
$event->set_userID($match['params']['userID']);
$event->set_eventID($_POST['eventid']);
$event->setStartDate($_POST['start']);
$event->setEndDate($_POST['end']);
$event->moveEvent();

//hook pour service externe
if (iset($p['config']['agendaService'])) {
    $hook=$p['config']['homeDirectory'].'controlers/services/'.$p['config']['agendaService'].'/inc-ajax-moveEvent.php';
    if (is_file($hook)) {
        include($hook);
    }
}

header('Content-Type: application/json');
echo json_encode(array("status"=>"ok"));
