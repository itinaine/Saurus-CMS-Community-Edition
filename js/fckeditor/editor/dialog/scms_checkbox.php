<?php

session_start();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<!--
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2007 Frederico Caldeira Knabben
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * Checkbox dialog window.
-->
<html>
	<head>
		<title>Checkbox Properties</title>
		<meta http-equiv="Content-Type" content="text/html; charset=<?=htmlspecialchars($_SESSION['keel']['encoding']);?>">
		<meta content="noindex, nofollow" name="robots">
		<script src="common/fck_dialog_common.js" type="text/javascript"></script>
		<script type="text/javascript">

var oEditor = window.parent.InnerDialogLoaded() ;

// Gets the document DOM
var oDOM = oEditor.FCK.EditorDocument ;

var oActiveEl = oEditor.FCKSelection.GetSelectedElement() ;

window.onload = function()
{
	// First of all, translate the dialog box texts
	oEditor.FCKLanguageManager.TranslatePage(document) ;

	if ( oActiveEl && oActiveEl.tagName == 'INPUT' && oActiveEl.type == 'checkbox' )
	{
		GetE('txtName').value		= oActiveEl.name ;
		GetE('txtValue').value		= oEditor.FCKBrowserInfo.IsIE ? oActiveEl.value : GetAttribute( oActiveEl, 'value' ) ;
		GetE('txtSelected').checked	= oActiveEl.checked ;
	}
	else
		oActiveEl = null ;

	window.parent.SetOkButton( true ) ;
	window.parent.SetAutoSize( true ) ;
}

function Ok()
{
	oEditor.FCKUndo.SaveUndoStep() ;
	
	if ( !oActiveEl )
	{
		oActiveEl = oEditor.FCK.EditorDocument.createElement( 'INPUT' ) ;
		oActiveEl.type = 'checkbox' ;
		oActiveEl = oEditor.FCK.InsertElement( oActiveEl ) ;
	}

	if ( GetE('txtName').value.length > 0 )
		oActiveEl.name = GetE('txtName').value ;

	if ( oEditor.FCKBrowserInfo.IsIE )
		oActiveEl.value = GetE('txtValue').value ;
	else
		SetAttribute( oActiveEl, 'value', GetE('txtValue').value ) ;

	var bIsChecked = GetE('txtSelected').checked ;
	SetAttribute( oActiveEl, 'checked', bIsChecked ? 'checked' : null ) ;	// For Firefox
	oActiveEl.checked = bIsChecked ;

	return true ;
}

		</script>
	</head>
	<body style="OVERFLOW: hidden" scroll="no">
		<table height="100%" width="100%">
			<tr>
				<td align="center">
					<table border="0" cellpadding="0" cellspacing="0" width="80%">
						<tr>
							<td>
								<span fckLang="DlgCheckboxName">Name</span><br>
								<input type="text" size="20" id="txtName" style="WIDTH: 100%">
							</td>
						</tr>
						<tr>
							<td>
								<span fckLang="DlgCheckboxValue">Value</span><br>
								<input type="text" size="20" id="txtValue" style="WIDTH: 100%">
							</td>
						</tr>
						<tr>
							<td><input type="checkbox" id="txtSelected"><label for="txtSelected" fckLang="DlgCheckboxSelected">Checked</label></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>
