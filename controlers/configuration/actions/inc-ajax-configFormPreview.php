<?php
/*
 * This file is part of MedShakeEHR.
 *
 * Copyright (c) 2019
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
 * Config > ajax : prévisualiser un formulaire
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */

if (!msUser::checkUserIsAdmin()) {die("Erreur: vous n'êtes pas administrateur");}
if(!is_numeric($_POST['formID'])) die();

//sortie du formulaire et préparation à son exploitation par le templates
if ($p['page']['formData']=msSQL::sqlUnique("select * from forms where id='".$_POST['formID']."' limit 1")) {

    //formulaire
    $forceAllTemplates="oui";
    $form = new msForm();
    $form->setFormID($_POST['formID']);
    $p['page']['form']=$form->getForm();

    if(!empty($p['page']['form'])) {
      $sqlGen = new msSqlGenerate;
      $sqlGen=$sqlGen->getSqlForForm($form->getFormIN());
      $basicTemplateCode=$form->getFlatBasicTemplateCode();

      $html = new msGetHtml;
      $html->set_template('configFormPreviewAjax.html.twig');
      $html = $html->genererHtmlVar($p);
    } else {
      $sqlGen="Formulaire d'affichage - données non disponibles";
      $basicTemplateCode="Formulaire d'affichage - données non disponibles";
      $html='<div class="alert alert-info" role="alert">
          Formulaire d\'affichage - aperçu non disponible !
          </div>';
    }

    exit(json_encode(array(
      'htmlFormPreview'=>$html,
      'basicTemplateCode'=>$basicTemplateCode,
      'sqlGen'=>$sqlGen
    )));

}