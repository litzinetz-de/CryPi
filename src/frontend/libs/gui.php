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
		echo '<li><a href="?do=configs">Config container management</a></li>
		<br>
		<li><a href="?do=connection">VPN management</a></li>
		<br>
		<li><a href="?do=system">System settings</li>
		<br>
		<li><a href="?do=logout">Logout</a>';
	}
	
	public function SystemSettingsPage($addr,$mask,$gateway)
	{
		echo '<form action="?do=system&write=y" method="post"><table border="0">
		<tr class="tableheader"><td colspan="2"><b>Network settings</b></td></tr>
		<tr><td>IP address:</td><td><input type="text" name="networking_addr" size="20" class="formstyle" value="'.$addr.'"></td></tr>
		<tr><td>Netmask:</td><td><input type="text" name="networking_mask" size="20" class="formstyle" value="'.$mask.'"></td></tr>
		<tr><td>Gateway:</td><td><input type="text" name="networking_gateway" size="20" class="formstyle" value="'.$gateway.'"></td></tr>
		</table>
		<br><br>
		<input type="submit" value="Apply" class="formstyle"></form>';
	}
}

?>