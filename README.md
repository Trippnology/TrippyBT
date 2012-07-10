# Trippy's Bittorrent Tracker v1.2.1 (GPL)

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

### README from http://uaequals42.tripod.com/Tracker/readme.html
FBT2 - As far as I know FBT (Flippy's BitTorrent Tracker) is the first BitTorrent PHP tracker that does NOT use MySQL. It users binary files to store its data. Also it requires the new 'compact' tracker protocol to save on bandwidth and gain valuable performance. Don't worry most of the recent clients fully support it.

**To Download:**
http://www.torrentz.com/FBT2.rar

**To Setup:** 
*   Get a PHP enabled web server. PHP 4.0.0 and up recommended
*   Upload announce.php, scrape.php and index.php to a **writable** directory
*   You are ready to go

**To restrict torrents used on your tracker:**
*   In main directory make a file called 'allow'
*   Add your torrent's hash (HEX format) to it, each on a new line if you wish

**FAQ:** 
*   Why do I get errors? 
    Chmod 0777 the directory that tracker's files are in.
*   Why binary? 
    Binary files are smaller and it takes less time to read them to the memory. Remember in this case hard drive's I/O speed is the main bottleneck.
*   How much can it take? 
    I've seen 3Mbit/s but web server was almost useless, however it should work smooth with 3000-5000 peers. For more peers additional tuning might be required (such as RAM Disk).
*   How do I contact the author? 
    Come to #torrentz at EFnet and ask for Flippy