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
	return '<tr><td><a href="./?info_hash=' . $file . '">' . $file . '</a></td><td>' . number_format($complete) . '</td><td>' . number_format($incomplete) . '</td></tr>';
}

// Discourage search engines from indexing your tracker
header('X-Robots-Tag: noindex, follow');
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Trippy's Bittorrent Tracker</title>
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
		.torrentlink, .cachedtorrent, .magnetlink { text-align: center; }
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
$upper_hash = strtoupper($info_hash);

if($info_hash && strlen($info_hash) == 40 && file_exists($info_hash))
{
	$torrent_name = $info_hash . '.torrent';
	if(file_exists($torrent_name))
	{
		echo '<p class="torrentlink">This torrent is available for download:<br><a href="' . $torrent_name . '">' . $torrent_name . '</a></p>';
	} else {
		echo '<p class="cachedtorrent">This torrent may be available from <a href="http://torrage.com/torrent/' . $upper_hash . '.torrent">Torrage</a> or <a href="http://torcache.net/torrent/' . $upper_hash . '.torrent">Torcache</a></p>';		
	}
	echo '<p class="magnetlink"><img src="data:image/gif;base64,R0lGODlhDAAMALMPAOXl5ewvErW1tebm5oocDkVFRePj47a2ts0WAOTk5MwVAIkcDesuEs0VAEZGRv///yH5BAEAAA8ALAAAAAAMAAwAAARB8MnnqpuzroZYzQvSNMroUeFIjornbK1mVkRzUgQSyPfbFi/dBRdzCAyJoTFhcBQOiYHyAABUDsiCxAFNWj6UbwQAOw%3D%3D" class="icon-magnet" alt="Magnet icon"> <a href="magnet:?xt=urn:btih:' . $info_hash . '&tr=udp%3A%2F%2Ftracker.openbittorrent.com%3A80&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80&tr=udp%3A%2F%2Ftracker.istole.it%3A6969&tr=udp%3A%2F%2Ftracker.ccc.de%3A80">Magnet Link</a></p>';
	echo '<table><tr><th>Client</th><th>Status</th><th>Port</th><th>Last action</th></tr>';
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
			$what = '<span class="status-peer">Peer</span>';
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
	<footer>Powered by <a href="https://github.com/Trippnology/TrippyBT">Trippy's Bittorrent Tracker</a> (GPL)</footer>
</body>
</html>