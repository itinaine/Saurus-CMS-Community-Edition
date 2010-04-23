<?php

session_start();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
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
 * "Find" and "Replace" dialog box window.
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=htmlspecialchars($_SESSION['keel']['encoding']);?>">
	<meta content="noindex, nofollow" name="robots" />
	<script src="common/fck_dialog_common.js" type="text/javascript"></script>
	<script type="text/javascript">

var oEditor = window.parent.InnerDialogLoaded() ;
var FCKLang = oEditor.FCKLang ;

window.parent.AddTab( 'Find', FCKLang.DlgFindTitle ) ;
window.parent.AddTab( 'Replace', FCKLang.DlgReplaceTitle ) ;
var idMap = {} ;

function OnDialogTabChange( tabCode )
{
	ShowE( 'divFind', ( tabCode == 'Find' ) ) ;
	ShowE( 'divReplace', ( tabCode == 'Replace' ) ) ;
	idMap['FindText'] = 'txtFind' + tabCode ;
	idMap['CheckCase'] = 'chkCase' + tabCode ;
	idMap['CheckWord'] = 'chkWord' + tabCode ;

	if ( tabCode == 'Replace' )
		window.parent.SetAutoSize( true ) ;
}

function OnLoad()
{
	// First of all, translate the dialog box texts.
	oEditor.FCKLanguageManager.TranslatePage( document ) ;

	// Place the cursor at the start of document.
	// This will be the starting point of our search.
	var range = new oEditor.FCKDomRange( oEditor.FCK.EditorWindow ) ;
	range.SetStart( oEditor.FCK.EditorDocument.body, 1 ) ;
	range.SetEnd( oEditor.FCK.EditorDocument.body, 1 ) ;
	range.Collapse( true ) ;
	range.Select() ;

	// Show the appropriate tab at startup.
	if ( window.parent.name.search( 'Replace' ) == -1 )
	{
		window.parent.SetSelectedTab( 'Find' ) ;
		window.parent.SetAutoSize( true ) ;
	}
	else
		window.parent.SetSelectedTab( 'Replace' ) ;

}

function btnStat(frm)
{
	document.getElementById('btnReplace').disabled =
		document.getElementById('btnReplaceAll').disabled =
			document.getElementById('btnFind').disabled =
				( document.getElementById(idMap["FindText"]).value.length == 0 ) ;
}

function GetSelection()
{
	var range = new oEditor.FCKDomRange( oEditor.FCK.EditorWindow ) ;
	range.MoveToSelection() ;
	return range.CreateBookmark2() ;
}

function GetSearchString()
{
	return document.getElementById(idMap['FindText']).value ;
}

function GetReplaceString()
{
	return document.getElementById("txtReplace").value ;
}

function GetCheckCase()
{
	return !! ( document.getElementById(idMap['CheckCase']).checked ) ;
}

function GetMatchWord()
{
	return !! ( document.getElementById(idMap['CheckWord']).checked ) ;
}

// Get the data pointed to by a bookmark.
function GetData( bookmark )
{
	var cursor = oEditor.FCK.EditorDocument.documentElement ;
	for ( var i = 0 ; i < bookmark.length ; i++ )
	{
		var target = bookmark[i] ;
		var currentIndex = -1 ;
		if ( cursor.nodeType != 3 )
		{
			for (var j = 0 ; j < cursor.childNodes.length ; j++ )
			{
				var candidate = cursor.childNodes[j] ;
				if ( candidate.nodeType == 3 &&
						candidate.previousSibling &&
						candidate.previousSibling.nodeType == 3 )
					continue ;
				currentIndex++ ;
				if ( currentIndex == target )
				{
					cursor = candidate ;
					break ;
				}
			}
			if ( currentIndex < target )
				return null ;
		}
		else
		{
			if ( i != bookmark.length - 1 )
				return null ;
			while ( target >= cursor.length && cursor.nextSibling && cursor.nextSibling.nodeType == 3 )
			{
				target -= cursor.length ;
				cursor = cursor.nextSibling ;
			}
			cursor = cursor.nodeValue.charAt( target ) ;
			if ( cursor == "" )
				cursor = null ;
		}
	}
	return cursor ;
}

// With this function, we can treat the bookmark as an iterator for DFS.
function NextPosition( bookmark )
{
	// See if there's anything further down the tree.
	var next = bookmark.concat( [0] ) ;
	if ( GetData( next ) != null )
		return next ;

	// Nothing down there? See if there's anything next to me.
	var next = bookmark.slice( 0, bookmark.length - 1 ).concat( [ bookmark[ bookmark.length - 1 ] + 1 ] ) ;
	if ( GetData( next ) != null )
		return next ;

	// Nothing even next to me? See if there's anything next to my ancestors.
	for ( var i = bookmark.length - 1 ; i > 0 ; i-- )
	{
		var next = bookmark.slice( 0, i - 1 ).concat( [ bookmark[ i - 1 ] + 1 ] ) ;
		if ( GetData( next ) != null )
			return next ;
	}

	// There's absolutely nothing left to walk, return null.
	return null ;
}

// Is this character a unicode whitespace?
// Reference: http://unicode.org/Public/UNIDATA/PropList.txt
function CheckIsWhitespace( c )
{
	var code = c.charCodeAt( 0 );
	if ( code >= 9 && code <= 0xd )
		return true;
	if ( code >= 0x2000 && code <= 0x200a )
		return true;
	switch ( code )
	{
		case 0x20:
		case 0x85:
		case 0xa0:
		case 0x1680:
		case 0x180e:
		case 0x2028:
		case 0x2029:
		case 0x202f:
		case 0x205f:
		case 0x3000:
			return true;
		default:
			return false;
	}
}

// Knuth-Morris-Pratt Algorithm for stream input
KMP_NOMATCH = 0 ;
KMP_ADVANCED = 1 ;
KMP_MATCHED = 2 ;
function KmpMatch( pattern, ignoreCase )
{
	var overlap = [ -1 ] ;
	for ( var i = 0 ; i < pattern.length ; i++ )
	{
		overlap.push( overlap[i] + 1 ) ;
		while ( overlap[ i + 1 ] > 0 && pattern.charAt( i ) != pattern.charAt( overlap[ i + 1 ] - 1 ) )
			overlap[ i + 1 ] = overlap[ overlap[ i + 1 ] - 1 ] + 1 ;
	}
	this._Overlap = overlap ;
	this._State = 0 ;
	this._IgnoreCase = ( ignoreCase === true ) ;
	if ( ignoreCase )
		this.Pattern = pattern.toLowerCase();
	else
		this.Pattern = pattern ;
}
KmpMatch.prototype = {
	"FeedCharacter" : function( c )
	{
		if ( this._IgnoreCase )
			c = c.toLowerCase();

		while ( true )
		{
			if ( c == this.Pattern.charAt( this._State ) )
			{
				this._State++ ;
				if ( this._State == this.Pattern.length )
				{
					// found a match, start over, don't care about partial matches involving the current match
					this._State = 0;
					return KMP_MATCHED;
				}
				return KMP_ADVANCED;
			}
			else if ( this._State == 0 )
				return KMP_NOMATCH;
			else
				this._State = this._Overlap[ this._State ];
		}

		return null ;
	},
	"Reset" : function()
	{
		this._State = 0 ;
	}
};

function _Find()
{
	// Start from the end of the current selection.
	var matcher = new KmpMatch( GetSearchString(), ! GetCheckCase() ) ;
	var cursor = GetSelection().End ;
	var matchState = KMP_NOMATCH ;
	var matchBookmark = null ;

	// Match finding.
	while ( true )
	{
		// Perform KMP stream matching.
		//	- Reset KMP matcher if we encountered a block element.
		var data = GetData( cursor ) ;
		if ( data )
		{
			if ( data.tagName )
			{
				if ( oEditor.FCKListsLib.BlockElements[ data.tagName.toLowerCase() ] )
				{
					matcher.Reset();
					matchBookmark = null ;
				}
			}
			else if ( data.charAt != undefined )
			{
				matchState = matcher.FeedCharacter(data) ;

				if ( matchState == KMP_NOMATCH )
					matchBookmark = null ;
				else if ( matchState == KMP_ADVANCED && matchBookmark == null )
					matchBookmark = { Start : cursor.concat( [] ) } ;
				else if ( matchState == KMP_MATCHED )
				{
					if ( matchBookmark == null )
						matchBookmark = { Start : cursor.concat( [] ) } ;
					matchBookmark.End = cursor.concat( [] ) ;
					matchBookmark.End[ matchBookmark.End.length - 1 ]++;

					// Wait, do we have to match a whole word?
					if ( GetMatchWord() )
					{
						var startOk = false ;
						var endOk = false ;
						var start = matchBookmark.Start ;
						var end = matchBookmark.End ;
						if ( start[ start.length - 1 ] == 0 )
							startOk = true ;
						else
						{
							var cursorBeforeStart = start.slice( 0, start.length - 1 ) ;
							cursorBeforeStart.push( start[ start.length - 1 ] - 1 ) ;
							var dataBeforeStart = GetData( cursorBeforeStart ) ;
							if ( dataBeforeStart == null || dataBeforeStart.charAt == undefined )
								startOk = true ;
							else if ( CheckIsWhitespace( dataBeforeStart ) )
								startOk = true ;
						}

						// this is already one character beyond the last char, no need to move
						var cursorAfterEnd = end ;
						var dataAfterEnd = GetData( cursorAfterEnd );
						if ( dataAfterEnd == null || dataAfterEnd.charAt == undefined )
							endOk = true ;
						else if ( CheckIsWhitespace( dataAfterEnd ) )
							endOk = true ;

						if ( startOk && endOk )
							break ;
						else
							matcher.Reset() ;
					}
					else
						break ;
				}
			}
		}

		// Perform DFS across the document, until we've reached the end.
		cursor = NextPosition( cursor ) ;
		if ( cursor == null )
			break;
	}

	// If we've found a match, select the match.
	if ( matchState == KMP_MATCHED )
	{
		var range = new oEditor.FCKDomRange( oEditor.FCK.EditorWindow ) ;
		range.MoveToBookmark2( matchBookmark ) ;
		range.Select() ;
		var focus = range._Range.endContainer ;
		while ( focus && focus.nodeType != 1 )
			focus = focus.parentNode ;

		if ( focus )
		{
			if ( oEditor.FCKBrowserInfo.IsSafari )
				oEditor.FCKDomTools.ScrollIntoView( focus, false ) ;
			else
				focus.scrollIntoView( false ) ;
		}

		return true;
	}
	else
		return false;
}

function Find()
{
	var range = new oEditor.FCKDomRange( oEditor.FCK.EditorWindow ) ;
	range.MoveToSelection() ;
	range.Collapse( false ) ;
	range.Select() ;

	if ( ! _Find() )
		alert( FCKLang.DlgFindNotFoundMsg ) ;
}

function Replace()
{
	var selection = new oEditor.FCKDomRange( oEditor.FCK.EditorWindow ) ;
	selection.MoveToSelection() ;

	if ( selection.CheckIsCollapsed() )
	{
		if (! _Find() )
			alert( FCKLang.DlgFindNotFoundMsg ) ;
	}
	else
	{
		oEditor.FCKUndo.SaveUndoStep() ;
		selection.DeleteContents() ;
		selection.InsertNode( oEditor.FCK.EditorDocument.createTextNode( GetReplaceString() ) ) ;
		selection.Collapse( false ) ;
		selection.Select() ;
	}
}

function ReplaceAll()
{
	oEditor.FCKUndo.SaveUndoStep() ;
	var range = new oEditor.FCKDomRange( oEditor.FCK.EditorWindow ) ;

	var replaceCount = 0 ;

	while ( _Find() )
	{
		range.MoveToSelection() ;
		range.DeleteContents() ;
		range.InsertNode( oEditor.FCK.EditorDocument.createTextNode( GetReplaceString() ) ) ;
		range.Collapse( false ) ;
		range.Select() ;
		replaceCount++ ;
	}
	if ( replaceCount == 0 )
		alert( FCKLang.DlgFindNotFoundMsg ) ;
	window.parent.Cancel() ;
}
	</script>
</head>
<body onload="OnLoad()" style="overflow: hidden">
	<div id="divFind" style="display: none">
		<table cellspacing="3" cellpadding="2" width="100%" border="0">
			<tr>
				<td nowrap="nowrap">
					<label for="txtFindFind" fcklang="DlgReplaceFindLbl">
						Find what:</label>
				</td>
				<td width="100%">
					<input id="txtFindFind" onkeyup="btnStat(this.form)" style="width: 100%" tabindex="1"
						type="text" />
				</td>
				<td>
					<input id="btnFind" style="width: 80px" disabled="disabled" onclick="Find();"
						type="button" value="Find" fcklang="DlgFindFindBtn" />
				</td>
			</tr>
			<tr>
				<td valign="bottom" colspan="3">
					&nbsp;<input id="chkCaseFind" tabindex="3" type="checkbox" /><label for="chkCaseFind" fcklang="DlgReplaceCaseChk">Match
						case</label>
					<br />
					&nbsp;<input id="chkWordFind" tabindex="4" type="checkbox" /><label for="chkWordFind" fcklang="DlgReplaceWordChk">Match
						whole word</label>
				</td>
			</tr>
		</table>
	</div>
	<div id="divReplace" style="display:none">
		<table cellspacing="3" cellpadding="2" width="100%" border="0">
			<tr>
				<td nowrap="nowrap">
					<label for="txtFindReplace" fcklang="DlgReplaceFindLbl">
						Find what:</label>
				</td>
				<td width="100%">
					<input id="txtFindReplace" onkeyup="btnStat(this.form)" style="width: 100%" tabindex="1"
						type="text" />
				</td>
				<td>
					<input id="btnReplace" style="width: 80px" disabled="disabled" onclick="Replace();"
						type="button" value="Replace" fcklang="DlgReplaceReplaceBtn" />
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap="nowrap">
					<label for="txtReplace" fcklang="DlgReplaceReplaceLbl">
						Replace with:</label>
				</td>
				<td valign="top">
					<input id="txtReplace" style="width: 100%" tabindex="2" type="text" />
				</td>
				<td>
					<input id="btnReplaceAll" style="width: 80px" disabled="disabled" onclick="ReplaceAll()" type="button"
						value="Replace All" fcklang="DlgReplaceReplAllBtn" />
				</td>
			</tr>
			<tr>
				<td valign="bottom" colspan="3">
					&nbsp;<input id="chkCaseReplace" tabindex="3" type="checkbox" /><label for="chkCaseReplace" fcklang="DlgReplaceCaseChk">Match
						case</label>
					<br />
					&nbsp;<input id="chkWordReplace" tabindex="4" type="checkbox" /><label for="chkWordReplace" fcklang="DlgReplaceWordChk">Match
						whole word</label>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>
