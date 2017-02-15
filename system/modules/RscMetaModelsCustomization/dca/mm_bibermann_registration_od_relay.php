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

$GLOBALS['TL_DCA']['mm_bibermann_registration_od_relay']['list']['global_operations']['registrationExportOdRelay'] = array
(
    'label'               => &$GLOBALS['TL_LANG']['mm_bibermann_registration_od_relay']['registrationExportOdRelay'],
    'href'                => 'act=registrationExportOdRelay',
    'class'               => 'header_icon registrationExport',
    'attributes'          => 'onclick="Backend.getScrollOffset();"'
);
$GLOBALS['TL_DCA']['mm_bibermann_registration_od_relay']['list']['global_operations']['registrationToggleShowCommentsOdRelay'] = array
(
    'label'               => &$GLOBALS['TL_LANG']['mm_bibermann_registration_od_relay']['registrationToggleShowCommentsOdRelay'],
    'href'                => 'act=registrationToggleShowCommentsOdRelay',
    'class'               => 'header_icon registrationToggleShowComments',
    'attributes'          => 'onclick="Backend.getScrollOffset();"'
);

$this->import('BackendUser', 'User'); 
if ($this->User->isAdmin || $this->User->isMemberOf(6))
{
  $GLOBALS['TL_DCA']['mm_bibermann_registration_od_relay']['list']['operations']['registrationTransferOdSdRelay'] = array
  (
      'label'               => &$GLOBALS['TL_LANG']['mm_bibermann_registration_od_relay']['registrationTransferOdSdRelay'],
      'href'                => 'act=registrationTransferOdSdRelay',
      'icon'                => 'system/modules/mm_customization/assets/registration_transfer.png',
  );
}