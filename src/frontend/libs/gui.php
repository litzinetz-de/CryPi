<?php

class GUI
{
	function __construct($version)
	{
		$this->version=$version;
	}
	
	public function IndexPage()
	{
		echo 'Welcome to CryptoPi / CryPi V '.$this->version.'. Please choose an item to the left.';
	}
	
	public function GlobalHeader()
	{
		echo '<html>
		<head>
		<link rel="stylesheet" href="/style.css">
		<title>CrytoPi</title>
		</head>
		<body>
		<table width="100%" border="0" cellspacing="0">
		<tr class="tableheader">
		<td colspan="3">&nbsp;</td>
		</tr><tr>
		<td align="center">
		<div class="middle">&nbsp;<br>CryptoPi<br>&nbsp;</div>
		</td>
		</tr>
		<tr class="tableheader">
		<td colspan="3">&nbsp;</td>
		</tr>
		</table>
		<br><br><table width="100%" border="0"><tr>
		<td width="20%" valign="top">';
	}
	
	public function GlobalFooter()
	{
		echo '</td></tr></table><br><div id="footer">CryPi V '.$this->version.'</div>
		</body>
		</html>';
	}
	
	public function GlobalNavigation()
	{
		echo '<li><a href="?do=containers">Config container management</a></li>
		<br>
		<li><a href="?do=vpn">VPN management</a></li>
		<br>
		<li><a href="?do=system">System settings</a></li>
		<br>
		<li><a href="?do=logout">Logout</a>';
	}
	
	public function SysMSG($msg)
	{
		  echo '<center><b>'.$msg.'</b></center>';
	}
}

?>