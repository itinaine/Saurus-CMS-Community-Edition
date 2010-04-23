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
 * Button dialog window.
-->
<html>
	<head>
		<title>Button Properties</title>
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

	if ( oActiveEl && oActiveEl.tagName.toUpperCase() == "INPUT" && ( oActiveEl.type == "button" || oActiveEl.type == "submit" || oActiveEl.type == "reset" ) )
	{
		GetE('txtName').value	= oActiveEl.name ;
		GetE('txtValue').value	= oActiveEl.value ;
		GetE('txtType').value	= oActiveEl.type ;

		GetE('txtType').disabled = true ;
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
		oActiveEl.type = GetE('txtType').value ;
		oActiveEl = oEditor.FCK.InsertElement( oActiveEl ) ;
	}

	oActiveEl.name = GetE('txtName').value ;
	SetAttribute( oActiveEl, 'value', GetE('txtValue').value ) ;

	return true ;
}

	</script>
</head>
<body style="overflow: hidden">
	<table width="100%" style="height: 100%">
		<tr>
			<td align="center">
				<table border="0" cellpadding="0" cellspacing="0" width="80%">
					<tr>
						<td colspan="">
							<span fcklang="DlgCheckboxName">Name</span><br />
							<input type="text" size="20" id="txtName" style="width: 100%" />
						</td>
					</tr>
					<tr>
						<td>
							<span fcklang="DlgButtonText">Text (Value)</span><br />
							<input type="text" id="txtValue" style="width: 100%" />
						</td>
					</tr>
					<tr>
						<td>
							<span fcklang="DlgButtonType">Type</span><br />
							<select id="txtType">
								<option fcklang="DlgButtonTypeBtn" value="button" selected="selected">Button</option>
								<option fcklang="DlgButtonTypeSbm" value="submit">Submit</option>
								<option fcklang="DlgButtonTypeRst" value="reset">Reset</option>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>