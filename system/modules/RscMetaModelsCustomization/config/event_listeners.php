<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2016 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Cliff Parnitzky 2016-2017
 * @author     Cliff Parnitzky
 * @package    RscMetaModelsCustomization
 * @license    LGPL
 */ 

use ContaoCommunityAlliance\DcGeneral\DcGeneralEvents;
use ContaoCommunityAlliance\DcGeneral\Event\ActionEvent;

return array(
  ContaoCommunityAlliance\DcGeneral\DcGeneralEvents::ACTION => array(
    function (ActionEvent $event) {
      if ($event->getAction()->getName() == 'registrationTransferSdOdSingle' ||
          $event->getAction()->getName() == 'registrationTransferOdSdSingle' ||
          $event->getAction()->getName() == 'registrationTransferSdOdRelay' ||
          $event->getAction()->getName() == 'registrationTransferOdSdRelay') {
        
        $tableSdSingle = "mm_bibermann_registration_sd_single";
        $tableOdSingle = "mm_bibermann_registration_od_single";
        $tableSdRelay = "mm_bibermann_registration_sd_relay";
        $tableOdRelay = "mm_bibermann_registration_od_relay";
        
        $colsSingle = "tstamp, firstname, lastname, yearOfBirth, gender, street, postal, city, federalState, phone, email, club, licenseExists, licenseNumber, registrationDate, organizerConditionsAccepted, paid, comment";
        $colsRelay  = "tstamp, relayName, swimmerFirstname, swimmerLastname, swimmerYearOfBirth, swimmerGender, bikerFirstname, bikerLastname, bikerYearOfBirth, bikerGender, runnerFirstname, runnerLastname, runnerYearOfBirth, runnerGender, firstname, lastname, street, postal, city, federalState, phone, email, registrationDate, organizerConditionsAccepted, paid, comment";;
        
        $sourceTable = "";
        $targetTable = "";
        
        if ($event->getAction()->getName() == 'registrationTransferSdOdSingle') {
          $sourceTable = $tableSdSingle;
          $targetTable = $tableOdSingle;
          $cols = $colsSingle;
        } else if ($event->getAction()->getName() == 'registrationTransferOdSdSingle') {
          $sourceTable = $tableOdSingle;
          $targetTable = $tableSdSingle;
          $cols = $colsSingle;
        } else if ($event->getAction()->getName() == 'registrationTransferSdOdRelay') {
          $sourceTable = $tableSdRelay;
          $targetTable = $tableOdRelay;
          $cols = $colsRelay;
        } else if ($event->getAction()->getName() == 'registrationTransferOdSdRelay') {
          $sourceTable = $tableOdRelay;
          $targetTable = $tableSdRelay;
          $cols = $colsRelay;
        }
        
        $sql = "INSERT INTO $targetTable ($cols) "
             . "SELECT $cols "
             . "FROM $sourceTable t "
             . "WHERE t.id = ?";
        
        $id = explode('::', \Input::get('id'))[1];
        
        // transfer to other db table
        \Database::getInstance()->prepare($sql)->execute($id);
        
        // delete from current table
        \Database::getInstance()->prepare("DELETE FROM $sourceTable WHERE id = ?")->execute($id);
        
        \Message::addConfirmation($GLOBALS['TL_LANG']['MSC']['registrationTransferSuccessful']); 
        
        \Controller::redirect(str_replace('&act=' . $event->getAction()->getName(), '', \Environment::getInstance()->request));
      } else if ($event->getAction()->getName() == 'registrationToggleShowCommentsSdSingle' ||
                 $event->getAction()->getName() == 'registrationToggleShowCommentsSdRelay'  ||
                 $event->getAction()->getName() == 'registrationToggleShowCommentsOdSingle' ||
                 $event->getAction()->getName() == 'registrationToggleShowCommentsOdRelay'  ||
                 $event->getAction()->getName() == 'registrationToggleShowCommentsDuathlon') {
        
        $arrActionRenderSettingMapping = array(
          'registrationToggleShowCommentsSdSingle' => 50,
          'registrationToggleShowCommentsSdRelay'  => 59,
          'registrationToggleShowCommentsOdSingle' => 48,
          'registrationToggleShowCommentsOdRelay'  => 70,
          'registrationToggleShowCommentsDuathlon' => 51,
        );
        
        // delete from current table
        \Database::getInstance()->prepare("UPDATE tl_metamodel_rendersetting SET enabled = NOT enabled WHERE id = ?")->execute($arrActionRenderSettingMapping[$event->getAction()->getName()]);
        
        \Controller::redirect(str_replace('&act=' . $event->getAction()->getName(), '', \Environment::getInstance()->request));
      } else if ($event->getAction()->getName() == 'registrationExportSdSingle'     ||
                 $event->getAction()->getName() == 'registrationExportSdRelay'      ||
                 $event->getAction()->getName() == 'registrationExportOdSingle'     ||
                 $event->getAction()->getName() == 'registrationExportOdRelay'      ||
                 $event->getAction()->getName() == 'registrationExportDuathlonScSb' ||
                 $event->getAction()->getName() == 'registrationExportDuathlonSaJb') {
        
        $sourceTable = "";
        $header = "";
        $cols = array();
        
        $headerSingle = "#Startnummer;Nachname;Vorname;Jahrgang;Mannschaft;Ort;Bundesland;Geschlecht";
        $headerRelay = "#Startnummer;Nachname;Vorname;Mannschaft;Ort;Bundesland;S: Nachname;S: Vorname;S: Jahrgang;S: Geschlecht;R: Nachname;R: Vorname;R: Jahrgang;R: Geschlecht;L: Nachname;L: Vorname;L: Jahrgang;L: Geschlecht";
        
        $colsSingle = array(
          "0 as startno",
          "lastname",
          "firstname",
          "metaYearOfBirth.label AS yearOfBirth",
          "club", 
          "city",
          "metaFederalState.label AS federalState",
          "REPLACE(REPLACE(metaGender.label, 'männlich', 'm'), 'weiblich', 'w') AS gender");
        $colsRelay = array(
          "0 as startno",
          "lastname", 
          "firstname",
          "relayName", 
          "city",
          "metaFederalState.label AS federalState", 
          "swimmerLastname", 
          "swimmerFirstname", 
          "metaSwimmerYearOfBirth.label AS swimmerYearOfBirth", 
          "REPLACE(REPLACE(metaSwimmerGender.label, 'männlich', 'm'), 'weiblich', 'w') AS swimmerGender", 
          "bikerLastname", 
          "bikerFirstname", 
          "metaBikerYearOfBirth.label AS bikerYearOfBirth", 
          "REPLACE(REPLACE(metaBikerGender.label, 'männlich', 'm'), 'weiblich', 'w') AS bikerGender",
          "runnerLastname",
          "runnerFirstname", 
          "metaRunnerYearOfBirth.label AS runnerYearOfBirth",
          "REPLACE(REPLACE(metaRunnerGender.label, 'männlich', 'm'), 'weiblich', 'w') AS runnerGender");
        
        $joins = array();
        $where = "";
        
        if ($event->getAction()->getName() == 'registrationExportSdSingle') {
          $sourceTable = "mm_bibermann_registration_sd_single";
          $header = $headerSingle;
          $cols = $colsSingle;
          
          // add 'licenseExists'
          $header = $headerSingle . ";DTU-Startpass";
          $cols[] = "metaLicenseExists.label AS licenseExists";
          
          $joins[] = "JOIN mm_bibermann_registration_metadata metaGender ON metaGender.id = m.gender";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaYearOfBirth ON metaYearOfBirth.id = m.yearOfBirth";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaFederalState ON metaFederalState.id = m.federalState";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaLicenseExists ON metaLicenseExists.id = m.licenseExists ";
        } else if ($event->getAction()->getName() == 'registrationExportSdRelay') {
          $sourceTable = "mm_bibermann_registration_sd_relay";
          $header = $headerRelay;
          $cols = $colsRelay;
          
          $joins[] = "JOIN mm_bibermann_registration_metadata metaFederalState ON metaFederalState.id = m.federalState";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaSwimmerGender ON metaSwimmerGender.id = m.swimmerGender";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaSwimmerYearOfBirth ON metaSwimmerYearOfBirth.id = m.swimmerYearOfBirth";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaBikerGender ON metaBikerGender.id = m.bikerGender";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaBikerYearOfBirth ON metaBikerYearOfBirth.id = m.bikerYearOfBirth";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaRunnerGender ON metaRunnerGender.id = m.runnerGender";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaRunnerYearOfBirth ON metaRunnerYearOfBirth.id = m.runnerYearOfBirth";
        } else if ($event->getAction()->getName() == 'registrationExportOdSingle') {
          $sourceTable = "mm_bibermann_registration_od_single";
          $header = $headerSingle . ";DTU-Startpass";;
          $cols = $colsSingle;
          
          // add 'licenseExists'
          $header = $headerSingle . ";DTU-Startpass";
          $cols[] = "metaLicenseExists.label AS licenseExists";
          
          $joins[] = "JOIN mm_bibermann_registration_metadata metaGender ON metaGender.id = m.gender";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaYearOfBirth ON metaYearOfBirth.id = m.yearOfBirth";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaFederalState ON metaFederalState.id = m.federalState";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaLicenseExists ON metaLicenseExists.id = m.licenseExists ";
        } else if ($event->getAction()->getName() == 'registrationExportOdRelay') {
          $sourceTable = "mm_bibermann_registration_od_relay";
          $header = $headerRelay;
          $cols = $colsRelay;
          
          $joins[] = "JOIN mm_bibermann_registration_metadata metaFederalState ON metaFederalState.id = m.federalState";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaSwimmerGender ON metaSwimmerGender.id = m.swimmerGender";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaSwimmerYearOfBirth ON metaSwimmerYearOfBirth.id = m.swimmerYearOfBirth";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaBikerGender ON metaBikerGender.id = m.bikerGender";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaBikerYearOfBirth ON metaBikerYearOfBirth.id = m.bikerYearOfBirth";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaRunnerGender ON metaRunnerGender.id = m.runnerGender";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaRunnerYearOfBirth ON metaRunnerYearOfBirth.id = m.runnerYearOfBirth";
        } else if ($event->getAction()->getName() == 'registrationExportDuathlonScSb') {
          $sourceTable = "mm_bibermann_registration_duathlon";
          $header = $headerSingle;
          $cols = $colsSingle;
          
          $joins[] = "JOIN mm_bibermann_registration_metadata metaGender ON metaGender.id = m.gender";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaYearOfBirth ON metaYearOfBirth.id = m.yearOfBirth";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaFederalState ON metaFederalState.id = m.federalState";
          $where = "WHERE yearOfBirth IN (SELECT id FROM mm_bibermann_registration_metadata WHERE pid = 25 AND category IN (126))";
        } else if ($event->getAction()->getName() == 'registrationExportDuathlonSaJb') {
          $sourceTable = "mm_bibermann_registration_duathlon";
          $header = $headerSingle;
          $cols = $colsSingle;
          
          $joins[] = "JOIN mm_bibermann_registration_metadata metaGender ON metaGender.id = m.gender";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaYearOfBirth ON metaYearOfBirth.id = m.yearOfBirth";
          $joins[] = "JOIN mm_bibermann_registration_metadata metaFederalState ON metaFederalState.id = m.federalState";
          $where = "WHERE yearOfBirth IN (SELECT id FROM mm_bibermann_registration_metadata WHERE pid = 25 AND category IN (129))";
        }
        
        // Create the file
        $objFile = new \File('system/tmp/' . md5(uniqid(mt_rand(), true)), true);
        $objFile->write('');

        // Add the header
        $objFile->append($header);
        //$objFile->append("SELECT " . implode(", ", $cols) . " FROM $sourceTable m " . implode(" ", $joins) . " $where");
        
        $objResult = \Database::getInstance()->prepare("SELECT " . implode(", ", $cols) . " FROM $sourceTable m " . implode(" ", $joins) . " $where")->execute();
        
        while($row = $objResult->fetchRow()) {
          $arrData = array();
          foreach($row as $col) {
            $arrData[] = $col;
          }
          
          $objFile->append(implode(";", $arrData));
        }

        $objFile->close();
        $objFile->sendToBrowser($event->getAction()->getName() . '.csv');
      }
    },
    -1
  )
);