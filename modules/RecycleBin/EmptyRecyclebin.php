<?php
/*********************************************************************************
 *** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 ** ("License"); You may not use this file except in compliance with the License
 ** The Original Code is:  vtiger CRM Open Source
 ** The Initial Developer of the Original Code is vtiger.
 ** Portions created by vtiger are Copyright (C) vtiger.
 ** All Rights Reserved.
 *********************************************************************************/

require_once('RecycleBinUtils.php');
global $adb,$log;
$allrec=vtlib_purify($_REQUEST['allrec']);
$idlist=vtlib_purify($_REQUEST['idlist']);
$excludedRecords=vtlib_purify($_REQUEST['excludedRecords']);
$selected_module = vtlib_purify($_REQUEST['selectmodule']);
$idlists = getSelectedRecordIds($_REQUEST,$selected_module,$idlist,$excludedRecords);
$idlists = array_filter($idlists); // this is to eliminate the empty value we always get from selection
if($allrec==1 and !empty($selected_module)){
	$delcrm=$adb->pquery("DELETE FROM vtiger_crmentity WHERE deleted = 1 and setype=?",array($selected_module));
	$delrel = $adb->pquery("DELETE FROM vtiger_relatedlists_rb WHERE entityid in (".generateQuestionMarks($idlists).")",array($idlists));
}elseif($allrec==1 and empty($selected_module)){  // empty all modules
	$adb->query('DELETE FROM vtiger_crmentity WHERE deleted = 1');
	// TODO Related records for the module records deleted from vtiger_crmentity have to be deleted
	// It needs lookup in the related tables and needs to be removed if doesn't have a reference record in vtiger_crmentity
	$adb->query('DELETE FROM vtiger_relatedlists_rb');
}else{
	if(count($idlists)>0) {
		$delselcrm=$adb->pquery("DELETE FROM vtiger_crmentity WHERE deleted = 1 and crmid in (".generateQuestionMarks($idlists).")",array($idlists));
		$delselrel = $adb->pquery("DELETE FROM vtiger_relatedlists_rb WHERE entityid in (".generateQuestionMarks($idlists).")",array($idlists));
	}
}
header("Location: index.php?module=RecycleBin&action=RecycleBinAjax&file=index&parenttab=$parenttab&mode=ajax&selected_module=$selected_module");
?>
