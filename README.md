# Trippy's Bittorrent Tracker v1.2 (GPL)

A version of FBT v2.2 that has been updated to use HTML5.  
The announce URL is now displayed at the top of all pages.  
Torrent info page now includes magnet links.  
The original source is included in fbt22.zip  

## Original README
FBT2.2 - Flippy's BitTorrent Tracker v2.2 (GPL)
flippy `at` ameritech `dot` net, modified by code `at` maven `dot` de

Changelog (from v2.0):
- fixed scrape.php "er" function (via common.php include)
- shared locks for reading, exclusive locks for writing
- fixed negative time in display
- linked source & torrent file (if it exists)
- added .htaccess file as starting point

Readme:
- announce URL is "./announce.php"
- if the text-file "allow" exists, only the torrents whose (hexadecimal)
  info_hash is included in there will be tracked, otherwise the tracker
  will track EVERYTHING (i.e. a public tracker)!
- the web-server executing the scripts needs write permission in the
  current directory
- if "$info_hash.torrent" exists, it will be linked on the detailed info-page
- index.php expects info_hash as 40 ASCII bytes, announce.php and scrape.php
  are for torrent-clients and thus are 20 byte binary hashes.

Have fun!
