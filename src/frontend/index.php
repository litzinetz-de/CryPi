<?php
require('libs/versionmanager.php');
require('libs/crypto.php');
require('libs/gui.php');

$v = new version();
$c = new crypto();
$g = new gui($v->GetCurrentVersion());

$g->GlobalHeader();

$g->GlobalNavigation();

echo '</td><td>';

if($_GET['do']=='')
{
	$g->IndexPage();
}
if($_GET['do']=='system')
{
	if($_GET['write']!='y')
	{
		$buffer=$c->GetNetworkSettings();
		$g->SystemSettingsPage($buffer['addr'],$buffer['mask'],$buffer['gateway']);
	}
}

$g->GlobalFooter();

?>
