<?
$sMsg = "";

if(isset($_POST["inpNewFolderName"])){	
	$sFolder = $_POST["inpCurrFolder"]."/".$_POST["inpNewFolderName"];

	if(is_dir($sFolder)==1){//folder already exist
		$sMsg = "<script>document.write(getText('Folder already exists.'))</script>";
	} else {
		if(mkdir($sFolder))
			$sMsg = "<script>document.write(getText('Folder created.'))</script>";
		else
			$sMsg = "<script>document.write(getText('Invalid input.'))</script>";
	}
} else {
	$sFolder = $_REQUEST["inpCurrFolder"];
}
?>
<base target="_self">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="style.css" rel="stylesheet" type="text/css">
<script>
	if(navigator.appName.indexOf('Microsoft')!=-1)
		var sLang=dialogArguments.sLang;
	else
		var sLang=window.opener.sLang;
	document.write("<scr"+"ipt src='language/"+sLang+"/foldernew.js'></scr"+"ipt>");
</script>
<script>writeTitle()</script>
<script>
function doPreSubmit(){
	var Form1 = document.forms.Form1;
	if(Form1.elements.inpNewFolderName.value==""){
		alert(getText("Invalid input."));
		return false;
	}
	return true;
}
function doSubmit(){
	if(doPreSubmit())document.forms.Form1.submit()
}
function refreshClose() {
	if(navigator.appName.indexOf('Microsoft')!=-1){
		dialogArguments.changeFolder('<?=$sFolder;?>')
	}else{
		window.opener.changeFolder('<?=$sFolder;?>')
	}
	self.close();
}	
</script>
</head>
<body onload="loadText()" style="overflow:hidden;margin:0;background: #f4f4f4">

<table width=100% height=100% align=center style="" cellpadding=0 cellspacing=0>
<tr>
<td valign=top style="padding-top:5px;padding-left:15px;padding-right:15px;padding-bottom:12px;height=100%">

<form method="post" action="" onsubmit="doPreSubmit()" name="Form1" id="Form1">
	<br>
	<input type="hidden" id="inpCurrFolder" name="inpCurrFolder" value="<?=$_REQUEST['inpCurrFolder']?>">
	<span id="txtLang">New Folder Name</span>: <br>
	<input type="text" id="inpNewFolderName" name="inpNewFolderName" class="inpTxt" size=38>
	<div><b><? echo $sMsg ?>&nbsp;</b></div>
</form>

</td>
</tr>
<tr>
<td class="dialogFooter" style="height:40px;padding-right:10px;" align=right valign=middle>
	<input style="width:135" type="button" name="btnCloseAndRefresh" id="btnCloseAndRefresh" value="close & refresh" onclick="refreshClose()" class="inpBtn" onmouseover="this.className='inpBtnOver';" onmouseout="this.className='inpBtnOut'">&nbsp;<input name="btnCreate" id="btnCreate" type="button" onclick="doSubmit()" value="create" class="inpBtn" onmouseover="this.className='inpBtnOver';" onmouseout="this.className='inpBtnOut'">
</td>
</tr>
</table>

</body>
</html>