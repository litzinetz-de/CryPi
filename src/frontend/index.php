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
		if($_POST['networking_addr']!=$buffer['addr'] || $POST['networking_mask']!=$buffer['mask'] || $_POST['networking_gateway']!=$buffer['gateway'])
		{
			/*if($c->SetNetworkSettings($_POST['addr'],$_POST['mask'],$_POST['gateway']))
			{
				$g->SysMSG('Network settings applied.');
			} else {
				$g->SysMSG('Error! Please check the entered addresses.');
			}*/
			$c->SetNetworkSettings($_POST['networking_addr'],$_POST['networking_mask'],$_POST['networking_gateway']);
		}
	}
}

$g->GlobalFooter();

?>
