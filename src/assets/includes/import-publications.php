<?PHP
  # define script parameters
  $BLOGURL    = "https://qualichain-project.eu/quali-data/themes/qualichain/assets/includes/qualichain.xml";
  $NUMITEMS   = 500;
  $TIMEFORMAT = "j F Y, g:ia";
  $CACHEFILE  = "quali-data/themes/qualichain/assets/cache/" . md5($BLOGURL);
  $CACHETIME  = 0; # hours

  # download the feed iff a cached version is missing or too old
  if(!file_exists($CACHEFILE) || ((time() - filemtime($CACHEFILE)) > 3600 * $CACHETIME)) {
    if($feed_contents = @file_get_contents($BLOGURL)) {
      # write feed contents to cache file
      $fp = fopen($CACHEFILE, 'w');
      fwrite($fp, $feed_contents);
      fclose($fp);
    }
  }

  include "class.rssparser.php";
  $rss_parser = new myRSSParser($CACHEFILE);

  # read feed data from cache file
  $feeddata = $rss_parser->getRawOutput();
  extract($feeddata['RSS']['CHANNEL'][0], EXTR_PREFIX_ALL, 'rss');
?>