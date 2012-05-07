<?
/*
FBT2.2 - Flippy's BitTorrent Tracker v2.2 (GPL)
flippy `at` ameritech `dot` net, modified by code `at` maven `dot` de
*/
require_once("common.php");

function getstat($file)
{
	$handle = fopen($file, "rb");
	flock($handle, LOCK_SH); # shared lock for reader
	$no_peers = intval(filesize($file) / 7);
	$x = fread($handle, $no_peers * 7);
	fclose($handle); # don't need unlock as fclose unlocks
	for($j=0;$j<$no_peers;$j++)
	{
		$t_peer_seed = join('', unpack("C", substr($x, $j * 7, 1)));
		if($t_peer_seed >= 128)
			$complete++;
		else
			$incomplete++;
	}
	return '<tr><td><a href="./?info_hash=' . $file . '">' . $file . '</a></td><td class="seeds">' . number_format($complete) . '</td><td class="peers">' . number_format($incomplete) . '</td></tr>';
}


?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Trippy's BitTorrent Tracker</title>
	<style>
		body { 
			color:#867C68; 
			font-family: Verdana;
			font-size: 11px;
		}
		a:link, a:visited { 
			color: #800000;
			text-decoration: none;
		}
		a:hover {
			color: #FF0000;
			text-decoration: underline;
		}
		.announceinfo {
			width: 50%;
			margin: 1em auto;
			text-align: center;	
		}
		table {
			width: 50%;
			margin: 1em auto;
			text-align: center;
		}
		tr { padding: 0.5em; }
		th { background-color: #eae7e2; }
		.status-seed { color: #008000; }
		.status-peer { color: #eae7e2; }
		footer { text-align: center; }
	</style>
</head>

<body>
<header class="announceinfo">
	<p>Add the following URL to your torrents:</p>
	<code>http://<? echo $_SERVER['HTTP_HOST'] ?>/announce.php</code>
</header>

<?
$info_hash = $_GET['info_hash'];

if($info_hash && strlen($info_hash) == 40 && file_exists($info_hash))
{
	$torrent_name = $info_hash . '.torrent';
	if(file_exists($torrent_name))
		echo '<table><tr><td><a href="' . $torrent_name . '">' . $torrent_name . '</a></td></tr></table>';
	echo '<table><tr><th>ip</th><th>status</th><th>port</th><th>last action</th></tr>';
	$handle = fopen($info_hash, "rb");
	flock($handle, LOCK_SH);
	$no_peers = intval(filesize($info_hash) / 7);
	$x = fread($handle, $no_peers * 7);
	fclose($handle);
	$time = intval((time() % 7680) / 60);
	for($j=0;$j<$no_peers;$j++)
	{
		$ip = unpack("C*", substr($x, $j * 7 + 1, 4));
		$ip = $ip[1] . '.' . $ip[2] . '.' . $ip[3] . '.*';
		$port = join('', unpack("n*", substr($x, $j * 7 + 5, 2)));
		$t_peer_seed = join('', unpack("C", substr($x, $j * 7, 1)));
		if($t_peer_seed >= 128)
		{
			$what = '<span class="status-seed">Seed</span>';
			$t_time = $time - ($t_peer_seed - 128);
		}
		else
		{
			$what = '<span class="status-Peer">Peer</span>';
			$t_time = $time - $t_peer_seed;
			
		}
		if ($t_time < 0)
			$t_time += 128;
		echo '<tr><td>' . $ip . '</td><td>' . $what . '</td><td>' . $port . '</td><td>' . number_format($t_time) . ' mins ago</td></tr>';
	}
	echo '</table>';
}
else
{
?><table><tr><th>Infohash</th><th>Seeds</th><th>Peers</th></tr><?
	$handle = opendir('.');
	while (false !== ($file = readdir($handle)))
	{
		if(strlen($file) == 40)
			echo getstat($file);
	}
	closedir($handle);
?></table><?
}
?>
	<footer>Built on <a href="fbt22.zip">flippy's bittorrent tracker v2.2</a> (GPL)</footer>
</body>
</html>