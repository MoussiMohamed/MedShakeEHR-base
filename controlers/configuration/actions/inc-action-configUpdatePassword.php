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
 * Config : attribuer un mot de passe et un module à un utilisateur
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 * @edited fr33z00 <https://github.com/fr33z00>
 */
 $module = isset($_POST['p_5']) ? ($_POST['p_5'] != '' ? $_POST['p_5'] : 'public') : 'public';
 msSQL::sqlQuery("update people set pass=AES_ENCRYPT('".$_POST['p_2']."',@password), module='".$module."' where id='".$_POST['p_1']."' limit 1");
 msTools::redirection('/configuration/');
