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
		echo '<form action="?do=system&write=y" method="post"><table border="0">
		<tr class="tableheader"><td colspan="2"><b>Network settings</b></td></tr>
		<tr><td>IP address:</td><td><input type="text" name="networking_addr" size="20" class="formstyle" value="'.$buffer['addr'].'"></td></tr>
		<tr><td>Netmask:</td><td><input type="text" name="networking_mask" size="20" class="formstyle" value="'.$buffer['mask'].'"></td></tr>
		<tr><td>Gateway:</td><td><input type="text" name="networking_gateway" size="20" class="formstyle" value="'.$buffer['gateway'].'"></td></tr>
		</table>
		<br><br>
		<input type="submit" value="Apply" class="formstyle"></form>';
	} else {
		$buffer=$c->GetNetworkSettings();
		if($_POST['networking_addr']!=$buffer['addr'] || $_POST['networking_mask']!=$buffer['mask'] || $_POST['networking_gateway']!=$buffer['gateway'])
		{
			if($c->SetNetworkSettings($_POST['networking_addr'],$_POST['networking_mask'],$_POST['networking_gateway']))
			{
				$g->SysMSG('Network settings applied.');
			} else {
				$g->SysMSG('Error! Please check the entered addresses.');
			}
			//$c->SetNetworkSettings($_POST['networking_addr'],$_POST['networking_mask'],$_POST['networking_gateway']);
		}
	}
}

if($_GET['do']=='containers')
{
	$containerlist=$c->ReadContainers();
	$container_mounted=$c->container_mounted();
	
	echo '<center><b>';
	if($container_mounted)
	{
		echo 'There is a container mounted. <a href="?do=dismount">Dismount now</a>';
	} else {
		echo 'There is no container mounted. Choose a container below or create a new one.';
	}
	
	echo '</b></center><br><br><form action="?" method="get"><input type="hidden" name="do" value="mount">
	<select name="container" size="5" class="formstyle">';
	foreach($containerlist as $cur_container)
	{
		echo '<option value="'.$cur_container.'">'.$cur_container.'</option>';
	}
	echo '<input type="submit" value="Mount" class="formstyle"></form>';
}

$g->GlobalFooter();

?>
