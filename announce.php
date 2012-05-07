<?
/*
FBT2.2 - Flippy's BitTorrent Tracker v2.2 (GPL)
flippy `at` ameritech `dot` net, modified by code `at` maven `dot` de
*/
require_once("common.php");

// Simulator of a BT client (for debug only)
/*
$_GET['info_hash'] = pack("H*", sha1(rand(0, 1)));
$_GET['port'] = rand(20, 120);
$_GET['left'] = rand(0, 3);
$_GET['compact'] = 1;
if(false)
{
	$_GET['event'] = 'stopped';
}
$_GET['numwant'] = 50;
*/

if($_GET['compact'] != 1)
	er('This tracker requires the compact tracker protocol. Please check your client for updates. Latest Generic, BitTornado or Azureus client recommended.');

$info_hash = $_GET['info_hash'];
if(strlen($info_hash) != 20)
	$info_hash = stripcslashes($_GET['info_hash']);
if(strlen($info_hash) != 20)
	er('Invalid info_hash');
$info_hash = bin2hex($info_hash);
$peer_ip = explode('.', $_SERVER['REMOTE_ADDR']);
$peer_ip = pack("C*", $peer_ip[0], $peer_ip[1], $peer_ip[2], $peer_ip[3]);
$peer_port = pack("n*", (int)$_GET['port']);
$time = intval((time() % 7680) / 60);
if($_GET['left'] == 0)
	$time += 128;
$time = pack("C", $time);

if(!file_exists($info_hash))
{
	if(file_exists('allow'))
	{
		$handle = fopen('allow', "r");
		flock($handle, LOCK_SH);
		$allow = fread($handle, filesize('allow'));
		fclose($handle);
		if(substr_count($allow, $info_hash) == 0)
			er('This torrent is not authorized on this tracker.');
	}
	$handle = fopen($info_hash, "w");
	fclose($handle);

}
$handle = fopen($info_hash, "rb+");
flock($handle, LOCK_EX);
$no_peers = intval(filesize($info_hash) / 7);
$data = fread($handle, $no_peers * 7);
$peer = array();
$updated = false;
for($i=0;$i<$no_peers;$i++)
{
	if($peer_ip . $peer_port == substr($data, $i * 7 + 1, 6))
	{
		$updated = true;
		if($_GET['event'] == 'stopped')
			$no_peers--;
		else
			$peer[$i] = $time . $peer_ip . $peer_port;
	}
	else
	{
		$peer[$i] = substr($data, $i * 7, 7);
	}
}
if($updated == false)
{
	$peer[] = $time . $peer_ip . $peer_port;
	$no_peers++;
}

rewind($handle);
ftruncate($handle, 0);
fwrite($handle, join('', $peer), $no_peers * 7);
fclose($handle);

if($_GET['event'] == 'stopped' || $_GET['numwant'] === 0)
{
	$o .= '';
}
else
{
	if($no_peers > 50)
	{
		$key = array_rand($peer, 50);
		foreach($key as $val)
			$o .= substr($peer[$val], 1, 6);
	}
	else
	{
		for($i=0;$i<$no_peers;$i++)
			$o .= substr($peer[$i], 1, 6);
	}
}

die('d8:intervali1800e5:peers' . strlen($o) . ':' . $o . 'e');
?>
