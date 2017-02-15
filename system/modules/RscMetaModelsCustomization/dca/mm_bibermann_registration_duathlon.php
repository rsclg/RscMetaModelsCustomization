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

$GLOBALS['TL_DCA']['mm_bibermann_registration_duathlon']['list']['global_operations']['registrationExportDuathlonScSb'] = array
(
    'label'               => &$GLOBALS['TL_LANG']['mm_bibermann_registration_duathlon']['registrationExportDuathlonScSb'],
    'href'                => 'act=registrationExportDuathlonScSb',
    'class'               => 'header_icon registrationExport',
    'attributes'          => 'onclick="Backend.getScrollOffset();"'
);
$GLOBALS['TL_DCA']['mm_bibermann_registration_duathlon']['list']['global_operations']['registrationExportDuathlonSaJb'] = array
(
    'label'               => &$GLOBALS['TL_LANG']['mm_bibermann_registration_duathlon']['registrationExportDuathlonSaJb'],
    'href'                => 'act=registrationExportDuathlonSaJb',
    'class'               => 'header_icon registrationExport',
    'attributes'          => 'onclick="Backend.getScrollOffset();"'
);
$GLOBALS['TL_DCA']['mm_bibermann_registration_duathlon']['list']['global_operations']['registrationToggleShowCommentsDuathlon'] = array
(
    'label'               => &$GLOBALS['TL_LANG']['mm_bibermann_registration_duathlon']['registrationToggleShowCommentsDuathlon'],
    'href'                => 'act=registrationToggleShowCommentsDuathlon',
    'class'               => 'header_icon registrationToggleShowComments',
    'attributes'          => 'onclick="Backend.getScrollOffset();"'
);