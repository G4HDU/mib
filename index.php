<?php

/*
* e107 website system
*
* Copyright (C) 2008-2013 e107 Inc (e107.org)
* Released under the terms and conditions of the
* GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
*
* e107 Blank Plugin
*
*/
if (!defined('e107_INIT'))
{
    require_once ("../../class2.php");
}
error_reporting(E_ALL);
require_once (e_PLUGIN . 'mib/includes/mib_class.php');

//print_a($_POST);

$mib = new mibClass;
if ($_GET['ajax'] == 'true'|| $_POST['mibCSV']=='Generate')
{

    $mib->runPage();
    exit;
} else
{

    require_once (HEADERF); // render the header (everything before the main content area)
    $mib->runPage();
    require_once (FOOTERF); // render the footer (everything after the main content area)
    exit;
}
// For a more elaborate plugin - please see e107_plugins/gallery


?>