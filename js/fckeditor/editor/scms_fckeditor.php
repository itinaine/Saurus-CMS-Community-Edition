<?php

session_start();

?>
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
 * File Name: fckeditor.html
 * 	Main page that holds the editor.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title>FCKeditor</title>
		<meta name="robots" content="noindex, nofollow" />
		<meta http-equiv="Content-Type" content="text/html; charset=<?=htmlspecialchars($_SESSION['keel']['encoding']);?>">
		<script type="text/javascript" src="lang/fcklanguagemanager.js"></script>
		<meta http-equiv="Cache-Control" content="public">
		<script type="text/javascript" src="js/fck_startup.js"></script>
	</head>
	<body>
		<table height="100%" width="100%" cellpadding="0" cellspacing="0" border="0" style="TABLE-LAYOUT: fixed">
			<tr>
				<td style="OVERFLOW: hidden">
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr id="Collapsed" style="DISPLAY: none">
							<td id="ExpandHandle" class="TB_Expand" colspan="3" onclick="FCKToolbarSet.Expand();return false;"><img class="TB_ExpandImg" src="images/spacer.gif" width="8" height="4"></td>
						</tr>
						<tr id="Expanded" style="DISPLAY: none">
							<td id="CollapseHandle" style="DISPLAY: none" class="TB_Collapse" 
								valign="bottom" onclick="FCKToolbarSet.Collapse();return false;"><img class="TB_CollapseImg" src="images/spacer.gif" width="8" height="4"></td>
							<td id="eToolbar" class="TB_ToolbarSet"></td>
							<td width="1" class="TB_SideBorder"></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr id="eWysiwyg">
				<td id="eWysiwygCell" height="100%" valign="top">
					<iframe id="eEditorArea" name="eEditorArea" height="100%" width="100%" frameborder="no" src="fckblank.html"></iframe>
				</td>
			</tr>
			<tr id="eSource" style="DISPLAY: none">
				<td class="Source" height="100%" valign="top">
					<textarea id="eSourceField" dir="ltr" style="WIDTH: 100%; HEIGHT: 100%"></textarea>
				</td>
			</tr>
		</table>
	</body>
</html>
