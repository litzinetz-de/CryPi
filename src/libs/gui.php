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
		<td width="20%">';
	}
	
	public function GlobalFooter()
	{
		echo '</td></tr></table><br><center><i>CryPi V '.$this->version.'</i></center>
		</body>
		</html>';
	}
	
	public function GlobalNavigation()
	{
		echo '<li><a href="?do=configs">Manage config containers</a></li>
		<br>
		<li><a href="?do=connection">Manage VPN connections</a></li>
		<br>
		<li><a href="?do=logout">Logout</a>';
	}
}

?>