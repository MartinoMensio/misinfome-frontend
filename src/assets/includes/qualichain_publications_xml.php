<?php
# list publications for MK:Smart

include("common_inc.php");

$xml ="";
$xml.="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
$xml.="<rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:atom=\"http://www.w3.org/2005/Atom\" xmlns:rdfs=\"http://www.w3.org/2000/01/rdf-schema#\">\n";
$xml.="<channel>\n";
$xml.="<title>Qualichain Publications</title>\n";
$xml.="<link>https://qualichain-project.eu/quali-data/themes/qualichain/assets/includes/qualichain.xml</link>\n";
$xml.="<description>Qualichain Publications</description>\n";
$xml.="<language>en-gb</language>\n";
$xml.="<lastBuildDate>".str_replace("  "," ",date('r'))."</lastBuildDate>\n";
$xml.="<generator>KMi Impact</generator>\n";
$xml.="<docs>http://blogs.law.harvard.edu/tech/rss</docs>\n";
$xml.="<atom:link href=\"https://qualichain-project.eu/quali-data/themes/qualichain/assets/includes/qualichain.xml\" rel=\"self\" type=\"application/rss+xml\" />\n";

# retrieve all people and create associative array of fullnames with their OUCUs
# also get the logged-in user's OUCU as its not currently in a session variable
$fullnamesA=array();
$liveA=array();

$sql="SELECT * FROM people ORDER BY id";
$result=@mysql_query($sql,$connection) or die("SQL Error:<BR>$sql");
while ($row=mysql_fetch_array($result)) {
  $oucu=$row['oucu'];
  # $fullnamesA[$oucu]=$row['firstname']." ".$row['lastname'];
  $firstname=$row['firstname'];
  # some people have multiple first names
  $firstnameA=explode(' ',$firstname);
  $inits="";
  foreach($firstnameA as $f) {
    $inits.=strtoupper(substr($f,0,1)).".";
  }
  $fullnamesA[$oucu]=$row['lastname'].", ".$inits;
  $liveA[$oucu]=$row['live'];
  if ($row['id']==$_SESSION['ses_userid']) $thisuseroucu=$row['oucu'];
}

# showapubXML function in common.inc.php


# $sql="SELECT *,UNIX_TIMESTAMP(date) AS date, UNIX_TIMESTAMP(start_date) AS start_date, UNIX_TIMESTAMP(end_date) AS end_date FROM publications ORDER BY pub_id DESC";
#$sql="SELECT * FROM publications WHERE mksmart='Y' OR mksmart='O' ORDER BY pub_id DESC"; # include only MK:Smart "only" or MK:Smart and KMi
//$sql="SELECT * FROM publications WHERE mksmart='Y' OR mksmart='O' ORDER BY pub_id DESC";
$sql="SELECT DISTINCT p.* FROM publications p, projects pr WHERE FIND_IN_SET(pr.id, p.projects) AND pr.name = 'QualiChain' ORDER BY p.date DESC;";
$result=@mysql_query($sql,$connection) or die("ERROR:<BR>\n$sql");
$numAllPubs=mysql_num_rows($result);

# display all publications for everyone
if ($numAllPubs > 0) {

  # keep an array of the publication ids (this is a specific detail record) to avoid display of duplicates
  # (of which there shouldn't be any, of course, but there you go)
  while ($row=mysql_fetch_array($result)) {
    
    $xml.="<item>\n";
    $xml.=showapubXML($row, "", "", true);
    # $xml.="<link>http://news.kmi.open.ac.uk/$r/$theId</link>\n";
    # $xml.="<guid>http://news.kmi.open.ac.uk/rostra/news.php?r=$r&amp;t=2&amp;id=$theId</guid>\n";

    $xml.="</item>\n";
  
  }
} else {
  $xml.="No publications to display\n";
}

$xml.="</channel>\n";
$xml.="</rss>";

# convert entire content to UTF-8
$xml=iconv('CP1252','UTF-8//IGNORE',$xml);
header("Content-type: application/rss+xml; charset=UTF-8");
echo $xml;

?>
