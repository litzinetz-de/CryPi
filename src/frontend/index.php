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
		$g->SystemSettingsForm($buffer['addr'],$buffer['mask'],$buffer['gateway']);
	} else {
		$buffer=$c->GetNetworkSettings();
		if($_POST['addr']!=$buffer['addr'] || $POST['maks']!=$buffer['mask'] || $_POST['gateway']!=$buffer['gateway'])
		{
			if($c->SetNetworkSettings($_POST['addr'],$_POST['mask'],$_POST['gateway']))
			{
				$g->SysMSG('Network settings applied.');
			} else {
				$g->SysMSG('Error! Please check the entered addresses.');
			}
		}
	}
}

$g->GlobalFooter();

?>
