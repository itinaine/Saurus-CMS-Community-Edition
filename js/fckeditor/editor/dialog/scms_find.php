<?php

session_start();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<!--
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: fck_find.html
 * 	"Find" dialog window.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
-->
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?=htmlspecialchars($_SESSION['keel']['encoding']);?>">
		<meta content="noindex, nofollow" name="robots">
		<script type="text/javascript">

var oEditor = window.parent.InnerDialogLoaded() ;

function OnLoad()
{
	// Whole word is available on IE only.
	if ( oEditor.FCKBrowserInfo.IsIE )
		document.getElementById('divWord').style.display = '' ;

	// First of all, translate the dialog box texts.
	oEditor.FCKLanguageManager.TranslatePage( document ) ;

	window.parent.SetAutoSize( true ) ;
	
	document.getElementById('txtFind').focus();
}

function btnStat(frm)
{
	document.getElementById('btnFind').disabled =
		( document.getElementById('txtFind').value.length == 0 ) ;
}

function ReplaceTextNodes( parentNode, regex, replaceValue, replaceAll )
{
	for ( var i = 0 ; i < parentNode.childNodes.length ; i++ )
	{
		var oNode = parentNode.childNodes[i] ;
		if ( oNode.nodeType == 3 )
		{
			var sReplaced = oNode.nodeValue.replace( regex, replaceValue ) ;
			if ( oNode.nodeValue != sReplaced )
			{
				oNode.nodeValue = sReplaced ;
				if ( ! replaceAll )
					return true ;
			}
		}
		else
		{
			if ( ReplaceTextNodes( oNode, regex, replaceValue ) )
				return true ;
		}
	}
	return false ;
}

function GetRegexExpr()
{
	if ( document.getElementById('chkWord').checked )
		var sExpr = '\\b' + document.getElementById('txtFind').value + '\\b' ;
	else
		var sExpr = document.getElementById('txtFind').value ;

	return sExpr ;
}

function GetCase()
{
	return ( document.getElementById('chkCase').checked ? '' : 'i' ) ;
}

function Ok()
{
	if ( document.getElementById('txtFind').value.length == 0 )
		return ;

	if ( oEditor.FCKBrowserInfo.IsIE )
		FindIE() ;
	else
		FindGecko() ;
}

var oRange ;

if ( oEditor.FCKBrowserInfo.IsIE )
	oRange = oEditor.FCK.EditorDocument.body.createTextRange() ;

function FindIE()
{
	var iFlags = 0 ;

	if ( chkCase.checked )
		iFlags = iFlags | 4 ;

	if ( chkWord.checked )
		iFlags = iFlags | 2 ;

	var bFound = oRange.findText( document.getElementById('txtFind').value, 1, iFlags ) ;

	if ( bFound )
	{
		oRange.scrollIntoView() ;
		oRange.select() ;
		oRange.collapse(false) ;
		oLastRangeFound = oRange ;
	}
	else
	{
		oRange = oEditor.FCK.EditorDocument.body.createTextRange() ;
		alert( oEditor.FCKLang.DlgFindNotFoundMsg ) ;
	}
}

function FindGecko()
{
	var bCase = document.getElementById('chkCase').checked ;
	var bWord = document.getElementById('chkWord').checked ;

	// window.find( searchString, caseSensitive, backwards, wrapAround, wholeWord, searchInFrames, showDialog ) ;
	oEditor.FCK.EditorWindow.find( document.getElementById('txtFind').value, bCase, false, false, bWord, false, false ) ;

}
		</script>
	</head>
	<body onload="OnLoad()" scroll="no" style="OVERFLOW: hidden">
		<table cellSpacing="3" cellPadding="2" width="100%" border="0">
			<tr>
				<td nowrap>
					<label for="txtFind" fckLang="DlgReplaceFindLbl">Find what:</label>&nbsp;
				</td>
				<td width="100%">
					<input id="txtFind" style="WIDTH: 100%" tabIndex="1" type="text">
				</td>
				<td>
					<input id="btnFind" style="WIDTH: 100%; PADDING-RIGHT: 5px; PADDING-LEFT: 5px" onclick="Ok();"
						type="button" value="Find" fckLang="DlgFindFindBtn">
				</td>
			</tr>
			<tr>
				<td valign="bottom" colSpan="3">
					&nbsp;<input id="chkCase" tabIndex="3" type="checkbox"><label for="chkCase" fckLang="DlgReplaceCaseChk">Match 
						case</label>
					<br>
					<div id="divWord" style="DISPLAY: none">
						&nbsp;<input id="chkWord" tabIndex="4" type="checkbox"><label for="chkWord" fckLang="DlgReplaceWordChk">Match 
							whole word</label>
					</div>
				</td>
			</tr>
		</table>
	</body>
</html>
