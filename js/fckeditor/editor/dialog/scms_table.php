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
 * Table dialog window.
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Table Properties</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=htmlspecialchars($_SESSION['keel']['encoding']);?>">
	<meta name="robots" content="noindex, nofollow" />
	<script src="common/fck_dialog_common.js" type="text/javascript"></script>
	<script type="text/javascript">

var oEditor = window.parent.InnerDialogLoaded() ;

// Gets the document DOM
var oDOM = oEditor.FCK.EditorDocument ;

// Gets the table if there is one selected.
var table ;
var e = oEditor.FCKSelection.GetSelectedElement() ;

if ( ( !e && document.location.search.substr(1) == 'Parent' ) || ( e && e.tagName != 'TABLE' ) )
	e = oEditor.FCKSelection.MoveToAncestorNode( 'TABLE' ) ;

if ( e && e.tagName == "TABLE" )
	table = e ;

// Fired when the window loading process is finished. It sets the fields with the
// actual values if a table is selected in the editor.
window.onload = function()
{
	// First of all, translate the dialog box texts
	oEditor.FCKLanguageManager.TranslatePage(document) ;

	if (table)
	{
		document.getElementById('txtRows').value    = table.rows.length ;
		document.getElementById('txtColumns').value = table.rows[0].cells.length ;

		// Gets the value from the Width or the Style attribute
		var iWidth  = (table.style.width  ? table.style.width  : table.width ) ;
		var iHeight = (table.style.height ? table.style.height : table.height ) ;

		if (iWidth.indexOf('%') >= 0)			// Percentual = %
		{
			iWidth = parseInt( iWidth.substr(0,iWidth.length - 1), 10 ) ;
			document.getElementById('selWidthType').value = "percent" ;
		}
		else if (iWidth.indexOf('px') >= 0)		// Style Pixel = px
		{																										  //
			iWidth = iWidth.substr(0,iWidth.length - 2);
			document.getElementById('selWidthType').value = "pixels" ;
		}

		if (iHeight && iHeight.indexOf('px') >= 0)		// Style Pixel = px
			iHeight = iHeight.substr(0,iHeight.length - 2);

		document.getElementById('txtWidth').value		= iWidth || '' ;
		document.getElementById('txtHeight').value		= iHeight || '' ;
		document.getElementById('txtBorder').value		= GetAttribute( table, 'border', '' ) ;
		document.getElementById('selAlignment').value	= GetAttribute( table, 'align', '' ) ;
		document.getElementById('txtCellPadding').value	= GetAttribute( table, 'cellPadding', '' ) ;
		document.getElementById('txtCellSpacing').value	= GetAttribute( table, 'cellSpacing', '' ) ;
		document.getElementById('txtSummary').value     = GetAttribute( table, 'summary', '' ) ;
//		document.getElementById('cmbFontStyle').value	= table.className ;

		var eCaption = oEditor.FCKDomTools.GetFirstChild( table, 'CAPTION' ) ;
		if ( eCaption ) document.getElementById('txtCaption').value = eCaption.innerHTML ;

		document.getElementById('txtRows').disabled    = true ;
		document.getElementById('txtColumns').disabled = true ;
	}

	window.parent.SetOkButton( true ) ;
	window.parent.SetAutoSize( true ) ;
}

// Fired when the user press the OK button
function Ok()
{
	var bExists = ( table != null ) ;

	if ( ! bExists )
		table = oEditor.FCK.EditorDocument.createElement( "TABLE" ) ;

	// Removes the Width and Height styles
	if ( bExists && table.style.width )		table.style.width = null ; //.removeAttribute("width") ;
	if ( bExists && table.style.height )	table.style.height = null ; //.removeAttribute("height") ;

	var sWidth = GetE('txtWidth').value ;
	if ( sWidth.length > 0 && GetE('selWidthType').value == 'percent' )
		sWidth += '%' ;

	SetAttribute( table, 'width'		, sWidth ) ;
	SetAttribute( table, 'height'		, GetE('txtHeight').value ) ;
	SetAttribute( table, 'border'		, GetE('txtBorder').value ) ;
	SetAttribute( table, 'align'		, GetE('selAlignment').value ) ;
	SetAttribute( table, 'cellPadding'	, GetE('txtCellPadding').value ) ;
	SetAttribute( table, 'cellSpacing'	, GetE('txtCellSpacing').value ) ;
	SetAttribute( table, 'summary'		, GetE('txtSummary').value ) ;

	var eCaption = oEditor.FCKDomTools.GetFirstChild( table, 'CAPTION' ) ;

	if ( document.getElementById('txtCaption').value != '')
	{
		if ( !eCaption )
		{
			eCaption = oEditor.FCK.EditorDocument.createElement( 'CAPTION' ) ;
			table.insertBefore( eCaption, table.firstChild ) ;
		}

		eCaption.innerHTML = document.getElementById('txtCaption').value ;
	}
	else if ( bExists && eCaption )
	{
		// TODO: It causes an IE internal error if using removeChild or
		// table.deleteCaption() (see #505).
		if ( oEditor.FCKBrowserInfo.IsIE )
			eCaption.innerHTML = '' ;
		else
			eCaption.parentNode.removeChild( eCaption ) ;
	}

	if (! bExists)
	{
		var iRows = document.getElementById('txtRows').value ;
		var iCols = document.getElementById('txtColumns').value ;

		for ( var r = 0 ; r < iRows ; r++ )
		{
			var oRow = table.insertRow(-1) ;
			for ( var c = 0 ; c < iCols ; c++ )
			{
				var oCell = oRow.insertCell(-1) ;
				if ( oEditor.FCKBrowserInfo.IsGeckoLike )
					oEditor.FCKTools.AppendBogusBr( oCell ) ;
			}
		}

		oEditor.FCKUndo.SaveUndoStep() ;

		oEditor.FCK.InsertElement( table ) ;
	}

	return true ;
}

	</script>
</head>
<body style="overflow: hidden">
	<table id="otable" cellspacing="0" cellpadding="0" width="100%" border="0" style="height: 100%">
		<tr>
			<td>
				<table cellspacing="1" cellpadding="1" width="100%" border="0">
					<tr>
						<td valign="top">
							<table cellspacing="0" cellpadding="0" border="0">
								<tr>
									<td>
										<span fcklang="DlgTableRows">Rows</span>:</td>
									<td>
										&nbsp;<input id="txtRows" type="text" maxlength="3" size="2" value="3" name="txtRows"
											onkeypress="return IsDigit(event);" /></td>
								</tr>
								<tr>
									<td>
										<span fcklang="DlgTableColumns">Columns</span>:</td>
									<td>
										&nbsp;<input id="txtColumns" type="text" maxlength="2" size="2" value="2" name="txtColumns"
											onkeypress="return IsDigit(event);" /></td>
								</tr>
								<tr>
									<td>
										&nbsp;</td>
									<td>
										&nbsp;</td>
								</tr>
								<tr>
									<td>
										<span fcklang="DlgTableBorder">Border size</span>:</td>
									<td>
										&nbsp;<input id="txtBorder" type="text" maxlength="2" size="2" value="0" name="txtBorder"
											onkeypress="return IsDigit(event);" /></td>
								</tr>
								<tr>
									<td>
										<span fcklang="DlgTableAlign">Alignment</span>:</td>
									<td>
										&nbsp;<select id="selAlignment" name="selAlignment">
											<option fcklang="DlgTableAlignNotSet" value="" selected="selected">&lt;Not set&gt;</option>
											<option fcklang="DlgTableAlignLeft" value="left">Left</option>
											<option fcklang="DlgTableAlignCenter" value="center">Center</option>
											<option fcklang="DlgTableAlignRight" value="right">Right</option>
										</select></td>
								</tr>
							</table>
						</td>
						<td>
							&nbsp;&nbsp;&nbsp;</td>
						<td align="right" valign="top">
							<table cellspacing="0" cellpadding="0" border="0">
								<tr>
									<td>
										<span fcklang="DlgTableWidth">Width</span>:</td>
									<td>
										&nbsp;<input id="txtWidth" type="text" maxlength="4" size="3" value="200" name="txtWidth"
											onkeypress="return IsDigit(event);" /></td>
									<td>
										&nbsp;<select id="selWidthType" name="selWidthType">
											<option fcklang="DlgTableWidthPx" value="pixels" selected="selected">pixels</option>
											<option fcklang="DlgTableWidthPc" value="percent">percent</option>
										</select></td>
								</tr>
								<tr>
									<td>
										<span fcklang="DlgTableHeight">Height</span>:</td>
									<td>
										&nbsp;<input id="txtHeight" type="text" maxlength="4" size="3" name="txtHeight" onkeypress="return IsDigit(event);" /></td>
									<td>
										&nbsp;<span fcklang="DlgTableWidthPx">pixels</span></td>
								</tr>
								<tr>
									<td>
										&nbsp;</td>
									<td>
										&nbsp;</td>
									<td>
										&nbsp;</td>
								</tr>
								<tr>
									<td nowrap="nowrap">
										<span fcklang="DlgTableCellSpace">Cell spacing</span>:</td>
									<td>
										&nbsp;<input id="txtCellSpacing" type="text" maxlength="2" size="2" value="1" name="txtCellSpacing"
											onkeypress="return IsDigit(event);" /></td>
									<td>
										&nbsp;</td>
								</tr>
								<tr>
									<td nowrap="nowrap">
										<span fcklang="DlgTableCellPad">Cell padding</span>:</td>
									<td>
										&nbsp;<input id="txtCellPadding" type="text" maxlength="2" size="2" value="1" name="txtCellPadding"
											onkeypress="return IsDigit(event);" /></td>
									<td>
										&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<table cellspacing="0" cellpadding="0" width="100%" border="0">
					<tr>
						<td nowrap="nowrap">
							<span fcklang="DlgTableCaption">Caption</span>:&nbsp;</td>
						<td>
							&nbsp;</td>
						<td width="100%" nowrap="nowrap">
							<input id="txtCaption" type="text" style="width: 100%" /></td>
					</tr>
					<tr>
						<td nowrap="nowrap">
							<span fcklang="DlgTableSummary">Summary</span>:&nbsp;</td>
						<td>
							&nbsp;</td>
						<td width="100%" nowrap="nowrap">
							<input id="txtSummary" type="text" style="width: 100%" /></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
