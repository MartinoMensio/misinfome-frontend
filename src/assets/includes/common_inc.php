<?php
ini_set('error_reporting', E_ALL);

# establish connection to mySQL server
# $server="kmi.open.ac.uk";
$server="mysql01.kmi.open.ac.uk";
$db_name="cc_kmi";
$dbUser="cc_kmi_ro";
$dbPass="rL81xdBNnsb27Eth";

# prefix relevant tables with:
$dataPrefix="performance_";

$connection=@mysql_connect($server,$dbUser,$dbPass) or die("ERROR: Could not connect to mySQL server");
$db=@mysql_select_db($db_name,$connection) or die("ERROR: Could not connect to $db_name database");

# if (version_compare(PHP_VERSION,'5','>=')) require_once('domxml-php4-to-php5.php'); //Load the PHP5 converter
/*
# read XML file
if (!isset($dom)) {
  $xmlpath=dirname(__FILE__)."/";
  # echo "Trying to open ".$xmlpath."menu.xml<BR>\n"; # DEBUG
  $dom=domxml_open_file($xmlpath."menu.xml"); # old method
}
*/

if (!isset($dom)) {
  $xmlpath=dirname(__FILE__)."/";
  # echo "Trying to open ".$xmlpath."menu.xml<BR>\n"; # DEBUG
  $dom = new DOMDocument();
  $dom->load($xmlpath."menu.xml");
}

# handle register_globals off
function registerglobals($which) {
  global $$which;
    if (isset($$which)) {
      if (is_array($$which)) {
      reset($$which);
      while (list($key,$val)=each($$which)) {
        global $$key;
        $$key=$val;
      }
    }
  }
}
registerglobals("_GET");
registerglobals("_POST");
# registerglobals("_COOKIE");
registerglobals("_SERVER");
# registerglobals("_ENV");
registerglobals("_SESSION");

# function to make a string safe for mySQL storage
function safe($text) {
  $text=trim($text);
  if (get_magic_quotes_gpc()) $text=stripslashes($text); # strip slashes that might have already been added by magic_quotes_gpc
  $text=mysql_real_escape_string($text);
  return $text;
}

# class and function to search for an element via its id
# used in place of apparently non-working function 'get_element_by_id'
class searchElementById {
        
  //Class which can find and return an XML node with a specified ID
  var $numberElements=0;
  var $numberAttributes=0;
  var $idToFind;
  var $tabElements;
        
  //Initialization function which accept 2 parameters :
  //$paramIdToFind : value of the ID to find
  //$paramTabElements : array of nodes which contains the element to find
  function searchElementById($paramIdToFind, $paramTabElements) {
    //Initialization of class variables
    $this->idToFind = $paramIdToFind;
    $this->tabElements = $paramTabElements;
  }
    
  //Function starting the research
  function beginSearching() {
    //Search and return the element found
    return $this->searchID($this->tabElements);
  }
        
  //Recursive function searching the desired node
  function searchID($tabElements) {
  
    //Variable of the number of elements
    $i = 0;
    //Variable of the number of attributes
    $j = 0;
    
    //Number of nodes in the elements' array
    $nbreNoeuds = count($tabElements);
                    
    //Loop on all elements
    for ($i=0;$i<$nbreNoeuds;$i++) {
                
      //Incrementation of the class variable counting the total number of elements
      $this->numberElements++;
                
      //Extraction of the attributes of the current element
      $tabAttributs = $tabElements[$i]->attributes();
      //Number of attributes in the current element
      $nbreAttributs = count($tabAttributs);
      //Loop on all attributes
      for ($j=0;$j<$nbreAttributs;$j++) {
                    
        //Incrementation of the class variable counting the total number of attributes
        $this->numberAttributes++;

        //Test if the current attribute is the attribute to find
        if ($tabAttributs[$j]->value==$this->idToFind) {
          //If yes, return the current element
          return $tabElements[$i];
        }
      }
                
      //Search children nodes of the current element
      $children = $tabElements[$i]->child_nodes();
      if (!is_bool($children)) {
        //If the current element has children, call recursively this function
        $result = $this->searchID($children);
        //If the result is not boolean, return the array
        if (!is_bool($result)) {
          return $result;
        }                
      }
    }
    return false;
  }
}

# function to retrieve the name, fielsd and example for a given subitem's ID
# and also to return its parent's node name
function getNodeAndParent($node) {
  global $dom;
  $nodeName="";
  $fields="";
  $example="";
  $parentName="";
  $toplevelitems=$dom->getElementsByTagName('toplevelitem');
  foreach ($toplevelitems as $tli) {
    $tn=$tli->getAttribute('name');
    if ($tn==$_SESSION['ses_category']) {
      # get all the 'item's under this 'toplevelitem'
      $items=$tli->getElementsByTagName('item');
      foreach ($items as $i) {
        $parentName=$i->getAttribute('name');
        $subitems=$i->getElementsByTagName('subitem');
        foreach ($subitems as $s) {
          $id=substr($s->getAttribute('id'),1); # strips off the 'x' I've had to add at the start of each ID
          if (intval($id)==intval($node)) {
            $nodeName=$s->getAttribute('name');
            $fields=$s->getAttribute('fields');
            $example=$s->getAttribute('example');
            
            $results=array($nodeName,$fields,$example,$parentName);
            return $results;
          }
        }
      }
    }
  }
  
}

# function to get around the stupid problem that explode creates a one-element array if the string is empty
function myexplode($delim,$string) {
  $string=trim($string);
  if (strlen($string)) {
    $array=explode($delim,$string);
  } else {
    $array=array();
  }
  return $array;
}

# reverses a human-entered date into YYYY-MM-DD for mySQL
function reverseDate($date) {
  if (preg_match("/([0-9]{1,2})[-\/.]([0-9]{1,2})[-\/.]([0-9]{2,4})/", $date, $dateA)) {
    if ($dateA[3]!="0000" && intval($dateA[3]) < 50) $dateA[3]=$dateA[3]+2000; # handle two-digit dates - assume they are 20xx
    $date=sprintf("%04d-%02d-%02d",$dateA[3],$dateA[2],$dateA[1]);
  } else {
    $date="00-00-0000";
  }
  return $date;
}

# reverses a human-entered date and returns as an array
function reverseDateA($date) {
  if (preg_match("/([0-9]{1,2})[-\/.]([0-9]{1,2})[-\/.]([0-9]{2,4})/", $date, $dateA)) {
    if ($dateA[3]!="0000" && intval($dateA[3]) < 50) $dateA[3]=$dateA[3]+2000; # handle two-digit dates - assume they are 20xx
    return $dateA;
  } else {
    $dateA=array();
    return $dateA;
  }
}

# reverses a mySQL date field
function forwardDate($date) {
  if (preg_match("/([0-9]{4})[-\/.]([0-9]{1,2})[-\/.]([0-9]{1,2})/", $date, $dateA)) {
    $date=sprintf("%02d-%02d-%04d",$dateA[3],$dateA[2],$dateA[1]);
  } else {
    $date="00-00-0000";
  }
  return $date;
}

# returns date as an array
function forwardDateA($date) {
  if (preg_match("/([0-9]{4})[-\/.]([0-9]{1,2})[-\/.]([0-9]{1,2})/", $date, $dateA)) {
    return $dateA;
  } else {
    # $dateA=array("00","00","0000");
    $dateA=array();
    return $dateA;
  }
}

# function to get all detail data for a give node and user
function getData($node,$showTitle=0) {
  global $connection, $dom;
  global $user, $extraClause, $entries;
  # print "DEBUG: getting data for node $node for user $user<BR>\n"; # DEBUG

  # find all raw data entries for this node for this user
  $sql="SELECT * FROM performance_rawdata WHERE node=$node AND user_id=$user ".$extraClause." ORDER BY datestamp DESC";
  $result=mysql_query($sql,$connection) or die("ERROR: Could not retrieve raw data with node $node for user $user<BR>$sql");
  $entries=mysql_num_rows($result);
  # print "&bull; $sql - produced $entries entries<BR>\n"; # DEBUG
  $data="";
  if ($entries > 0) {
    # if there is any raw data for this node, search for the node in the XML file and return
    # the node name and the field list - NB the name and fields list will be the same for all
    # of the records for this node, so there is no need to look them up more than once
    /*
    $children=$dom->child_nodes();
    $searchObject=new searchElementById($node, $children);
    $resultElement=$searchObject->beginSearching();
    */
    # $resultElement=$dom->getElementById($node); # doesn't work

    $nodeName="";
    $fields="";
    $toplevelitems=$dom->getElementsByTagName('toplevelitem');
    foreach ($toplevelitems as $tli) {
      $tn=$tli->getAttribute('name');
      if ($tn==$_SESSION['ses_category']) {
        # get all the 'item's under this 'toplevelitem'
        $items=$tli->getElementsByTagName('item');
        foreach ($items as $i) {
          $subitems=$i->getElementsByTagName('subitem');
          foreach ($subitems as $s) {
            $id=substr($s->getAttribute('id'),1); # strips off the 'x' I've had to add at the start of each ID
            if ($id==$node) {
              $nodeName=$s->getAttribute('name');
              $fields=$s->getAttribute('fields');
              break;
            }
          }
        }
      }
    }

    
    if (empty($nodeName)) exit("ERROR: Node id $node not found in XML file");
    # $resultElement=$dom->query('//*[@id="$node"]');
    
    # exit("XML error: search XML for node $node");
    # trap error of node not found in XML - not likely
    # if (!$resultElement) exit("ERROR: Node id $node not found in XML file");
    # $nodeName=$resultElement->getAttribute('name');
    if ($showTitle) $data="<B>".$nodeName.":</B><BR>\n";
    # $fields=$resultElement->getAttribute('fields');

    # print "DEBUG: node $node, name $nodeName, fields $fields<BR>\n";

    # loop through all the raw entries for this node number
    while ($row=mysql_fetch_array($result)) {
      $d=0;
      if (empty($fields)) {
        # no detail fields defined for this node, so for now just return the raw data
        $data.="<P>".stripslashes($row['rawdata'])."</P>\n";
      } else {
        # look for detail data matching this raw record
        $raw_id=$row['raw_id'];
        $fieldsArray=explode("|",$fields);
        $thisDetail="";
        foreach ($fieldsArray as $thisFieldName) {
          $thisFieldName=str_replace(" ","_",$thisFieldName);
          $sql="SELECT * FROM performance_detaildata WHERE raw=$raw_id AND user_id=$user AND detail_name='$thisFieldName' LIMIT 1";
          # print "$sql<BR>\n"; # DEBUG
          $result2=mysql_query($sql,$connection) or die("ERROR: Could not retrieve detail data<BR>$sql");
          $row2=mysql_fetch_array($result2);
          $field=stripslashes(trim($row2['detail_text']));
          $field=str_replace("£","&pound;",$field);
          if (!empty($field)) {
            $d++;
            $thisDetail.=trim($field).", "; # append only if detail field not empty
          }
        }
        if ($d > 0) {
          # remove the trailing comma and space, apply paragraph tags, and add to end of returned string
          $thisDetail=substr($thisDetail,0,-2);
          $data.="<P>".$thisDetail."</P>\n";
        } else {
          $data.="<P>".trim(stripslashes($row['rawdata']))."</P>\n"; # if no detail data entered, use raw data instead
        }
      }
    }
  }
  return $data;
}

# function to display a publication from a row of a pre-extracted query
# first field contains a row from a database query
# second field used for BOTH edit box and RAE status - * cannot be just true/false *
# supply start and end dates in positions 3 and 4
# flag in position 5 true if you want to display author names as surname, initial
# number in position 6 is the id of the 'next' record to edit - obviously not applicable for last record in query
function showapub($row,$edit="", $dateStart="", $dateEnd="", $rev=false, $next=false) {
  global $fullnamesA,$id,$t;
  $data="";
  # translate first author back to real name
  $first_author=stripslashes($row['first_author']);
  if (isset($fullnamesA[$first_author])) {
    $first_author=$fullnamesA[$first_author];
  } else if ($rev) {
    $lastname=strrchr($first_author," ");
    $initial=substr($first_author,0,1);
    $first_author=$lastname.", ".$initial.".";
  }
  
  # translate other authors back to real names
  $other_authors=substr(stripslashes($row['other_authors']),1,-1);
  if (!empty($other_authors)) {
    $otherAuthorsA=myexplode("|",$other_authors);
    $other_authors="";
    $i=0;
    foreach ($otherAuthorsA as $thisOtherAuthor) {
      # look for OUCUs first
      if (isset($fullnamesA[$thisOtherAuthor])) {
        if ($i==count($otherAuthorsA)-2) {
          $other_authors.=$fullnamesA[$thisOtherAuthor]." and ";
        } else {
          $other_authors.=$fullnamesA[$thisOtherAuthor].", ";
        }
      } else {
        # reverse name flag
        if ($rev) {
          $lastname=strrchr($thisOtherAuthor," ");
          $initial=substr($thisOtherAuthor,0,1);
          if ($i==count($otherAuthorsA)-2) {
            $other_authors.=$lastname.", ".$initial.". and ";
          } else {
            $other_authors.=$lastname.", ".$initial."., ";
          }
        } else {
          if ($i==count($otherAuthorsA)-2) {
            $other_authors.=$thisOtherAuthor." and ";
          } else {
            $other_authors.=$thisOtherAuthor.", ";
          }
        }
      }
      $i++;
    }
    $other_authors=substr($other_authors,0,-2); # remove last comma
  }
  
  # orignal string date fields returned as array
  $date=forwardDateA($row['date']);
  if ($date[2]==0) $date[2]=1;
  if ($date[3]==0) $date[3]=1;
  $compDate=mktime(0,0,0,$date[2],$date[3],$date[1]); # publication date for comparisons
  # $data.="<!-- dateStart $dateStart | pubDate ".$row['date']." | compDate m:".$date[2]." d:".$date[3]." y:".$date[1]." $compDate ".date('j M Y',$compDate)." | dateEnd $dateEnd  -->\n"; # DEBUG
  $start_date=forwardDateA($row['start_date']);
  $end_date=forwardDateA($row['end_date']);
  
  $flag=true; # printing flag
  if (!empty($dateStart) && !empty($dateEnd)) {
    if ($compDate < $dateStart || $compDate > $dateEnd) {
      $flag=false; # if outside the date range, unset printing flag
    }
  }
  if ($flag==true) {
    # get all remaining fields
    # $title=htmlentities(stripslashes($row['title']));
    $autheds=$row['autheds'];
    $title=stripslashes($row['title']);
    if (substr($title,-1,1)==".") $title=substr($title,0,-1); # ignore any . on the end of the title
    $conference_name=stripslashes($row['conference_name']);
    $work_type=$row['work_type'];
    $workshop_name=stripslashes($row['workshop_name']);
    $journal_name=stripslashes($row['journal_name']);
    $geo_location=stripslashes($row['geo_location']);
    $book_title=stripslashes($row['book_title']);
    $book_editors=stripslashes($row['book_editors']);
    $book_chapter=stripslashes($row['book_chapter']);
    $volume=stripslashes($row['volume']);
    $number=stripslashes($row['number']);
    $page_numbers=stripslashes($row['page_numbers']);
    $publisher=stripslashes($row['publisher']);
    $paper_url=stripslashes($row['paper_url']);
    $tech_report_number=stripslashes($row['tech_report_number']);
    $tech_report_type=stripslashes($row['tech_report_type']);
    $tech_rep_host_org=stripslashes($row['tech_rep_host_org']);
    $affiliation_for_pub=stripslashes($row['affiliation_for_pub']);
    $rating_justification=stripslashes($row['rating_justification']);
    # $rae=$row['rae']; # no longer used as RAE tagging done via people record
    $oro=$row['oro'];
    
    $t++; # increment total actually output
    $data.="<P>".htmlentities($first_author);
    if (!empty($other_authors)) {
      if (count($otherAuthorsA)==1) {
        $data.=" and ".htmlentities($other_authors);
      } else {
        $data.=", ".htmlentities($other_authors);
      }
    }
    if ($autheds=="editors") $data.=" (eds.)"; # editors, added June 2010
    if ($date[1] > 1970) {
      $data.=" (".$date[1].")";
    } elseif ($start_date[1] > 1970 && $end_date[1] > 1970) {
      if ($start_date[1]!=$end_date[1]) {
        $data.=" (".$start_date[1]." - ".$end_date[1].")";
      } else {
        $data.=" (".$start_date[1].")";
      }
    } elseif ($start_date[1] > 1970) {
      $data.=" (".$start_date[1].")";
    }
    if (!empty($title)) $data.=" <B>".$title."</B>"; # title no longer a required field
    # new logic for different presentations at a workshop or conference
    if ($work_type=="demo" && !empty($conference_name)) {
      $data.=", Demo at ".$conference_name;
    } elseif ($work_type=="poster" && !empty($conference_name)) {
      $data.=", Poster at ".$conference_name;
    } elseif (!empty($workshop_name) && !empty($conference_name)) {
      $data.=", Workshop: ".$workshop_name." at ".$conference_name;
    } else {
      if (!empty($workshop_name)) $data.=", Workshop: ".$workshop_name;
      if (!empty($conference_name)) $data.=", ".$conference_name;
    }
    if (!empty($journal_name)) $data.=", ".$journal_name;
    if (!empty($geo_location)) $data.=", ".$geo_location;
    if (!empty($book_chapter)) {
      $data.=" <B>$book_chapter</B>";
      if (!empty($book_editors)) $data.=", in eds. ".$book_editors;
      if (!empty($book_title)) $data.=", ".$book_title;
    } else {
      if (!empty($book_title)) $data.=" <B>$book_title</B>";
      if (!empty($book_editors)) $data.=", eds. ".$book_editors;
    }
    if (!empty($volume)) $data.=", ".$volume;
    if (!empty($number)) $data.=", ".$number;
    if (!empty($page_numbers)) $data.=", pp. ".$page_numbers;
    if (!empty($publisher)) $data.=", ".$publisher;
    
    if (!empty($tech_rep_host_org)) $data.=", ".$tech_rep_host_org;
    if (!empty($tech_report_type)) $data.=" ".$tech_report_type;
    if (!empty($tech_report_type) && !empty($tech_report_number)) $data.=" ".$tech_report_number;
    if (empty($tech_report_type) && !empty($tech_report_number)) $data.=" Technical report ".$tech_report_number;
    
    if (!empty($paper_url)) $data.=", url: ".rawurldecode($paper_url); # return entities to normal characters
    if (!empty($rating_justification)) ", ".$rating_justification;
    $data.="<BR>\n";
    # edit flag allows these lines to be suppressed
    if (!empty($edit)) {
      $data.="[ <A HREF='publication_form.php?id=$id&amp;pub_id=".$row['pub_id'];
      if ($next==true) $data.="&amp;next=1";
      $data.="'>edit</A>";
      /* # RAE removed until 2012
      $data.=" | RAE: ";
      # note that the edit flag is actually being used here to indicate if for this author
      # this publication should be in the RAE
      if ($edit=="Y") {
        $data.="<INPUT TYPE='checkbox' NAME='rae_".$row['pub_id']."' VALUE='Y' CHECKED ONCLICK=\"this.form.newrae.value='N'; return toggleRAE(this.form,'".$row['pub_id']."');\">";
      } else {
        $data.="<INPUT TYPE='checkbox' NAME='rae_".$row['pub_id']."' VALUE='N' ONCLICK=\"this.form.newrae.value='Y'; return toggleRAE(this.form,'".$row['pub_id']."');\">";
      }
      */
      $data.=" | Open Research Online: ";
      if ($oro=="Y") {
        $data.="<INPUT TYPE='checkbox' NAME='oro_".$row['pub_id']."' VALUE='Y' CHECKED ONCLICK=\"this.form.neworo.value='N'; return toggleORO(this.form,'".$row['pub_id']."');\">";
      } else {
        $data.="<INPUT TYPE='checkbox' NAME='oro_".$row['pub_id']."' VALUE='N' ONCLICK=\"this.form.neworo.value='Y'; return toggleORO(this.form,'".$row['pub_id']."');\">";
      }
      $data.=" ]<BR>\n";
    }
    $data.="</P>\n";
    # $data.="<HR SIZE='1'>\n";
  }
  return $data;
}

# function to return a publication from a row of a pre-extracted query as XML for an RSS feed
# first field contains a row from a database query
# second field used for BOTH edit box and RAE status - * cannot be just true/false *
# supply start and end dates in positions 3 and 4
# flag in position 5 true if you want to display author names as surname, initial
# number in position 6 is the id of the 'next' record to edit - obviously not applicable for last record in query
# fields returned: title, authors, dates, link, description (everything else)
function showapubXML($row, $dateStart="", $dateEnd="", $rev=false, $next=false) {
  global $fullnamesA,$id,$t;
  $xmltitle="";

  $xmlauthors="";
  $xmldate="";
  $xmllink="";
  $xmlguid="";
  $desc="";
  # translate first author back to real name
  $first_author=stripslashes($row['first_author']);
  if (isset($fullnamesA[$first_author])) {
    $first_author=$fullnamesA[$first_author];
  } else if ($rev) {
    $lastname=strrchr($first_author," ");
    $initial=substr($first_author,0,1);
    $first_author=$lastname.", ".$initial.".";
  }
  
  # translate other authors back to real names
  $other_authors=substr(stripslashes($row['other_authors']),1,-1);
  if (!empty($other_authors)) {
    $otherAuthorsA=myexplode("|",$other_authors);
    $other_authors="";
    $i=0;
    foreach ($otherAuthorsA as $thisOtherAuthor) {
      # look for OUCUs first
      if (isset($fullnamesA[$thisOtherAuthor])) {
        if ($i==count($otherAuthorsA)-2) {
          $other_authors.=$fullnamesA[$thisOtherAuthor]." and ";
        } else {
          $other_authors.=$fullnamesA[$thisOtherAuthor].", ";
        }
      } else {
        # reverse name flag
        if ($rev) {
          $lastname=strrchr($thisOtherAuthor," ");
          $initial=substr($thisOtherAuthor,0,1);
          if ($i==count($otherAuthorsA)-2) {
            $other_authors.=$lastname.", ".$initial.". and ";
          } else {
            $other_authors.=$lastname.", ".$initial."., ";
          }
        } else {
          if ($i==count($otherAuthorsA)-2) {
            $other_authors.=$thisOtherAuthor." and ";
          } else {
            $other_authors.=$thisOtherAuthor.", ";
          }
        }
      }
      $i++;
    }
    $other_authors=substr($other_authors,0,-2); # remove last comma
  }
  
  # orignal string date fields returned as array
  $date=forwardDateA($row['date']);
  if ($date[2]==0) $date[2]=1;
  if ($date[3]==0) $date[3]=1;
  $compDate=mktime(0,0,0,$date[2],$date[3],$date[1]); # publication date for comparisons
  # $desc.="<!-- dateStart $dateStart | pubDate ".$row['date']." | compDate m:".$date[2]." d:".$date[3]." y:".$date[1]." $compDate ".date('j M Y',$compDate)." | dateEnd $dateEnd  -->\n"; # DEBUG
  $start_date=forwardDateA($row['start_date']);
  $end_date=forwardDateA($row['end_date']);
  
  $flag=true; # printing flag
  if (!empty($dateStart) && !empty($dateEnd)) {
    if ($compDate < $dateStart || $compDate > $dateEnd) {
      $flag=false; # if outside the date range, unset printing flag
    }
  }
  if ($flag==true) {
    # get all remaining fields
    $autheds=$row['autheds'];
    $id = $row['id']; # just for the GUID
    $title = stripslashes($row['title']);
    //if (empty($title)) $title="(no title)"; # title no longer a required field
    $xmltitle="<title><![CDATA[".$title."]]></title>\n";
    if (substr($title,-1,1)==".") $title=substr($title,0,-1); # ignore any . on the end of the title
    $conference_name=stripslashes($row['conference_name']);
    $work_type=$row['work_type'];
    $workshop_name=stripslashes($row['workshop_name']);
    $journal_name=stripslashes($row['journal_name']);
    $geo_location=stripslashes($row['geo_location']);
    $book_title=stripslashes($row['book_title']);
    $book_editors=stripslashes($row['book_editors']);
    $book_chapter=stripslashes($row['book_chapter']);
    $volume=stripslashes($row['volume']);
    $number=stripslashes($row['number']);
    $page_numbers=stripslashes($row['page_numbers']);
    $publisher=stripslashes($row['publisher']);
    $paper_url=stripslashes($row['paper_url']);
    $tech_report_number=stripslashes($row['tech_report_number']);
    $tech_report_type=stripslashes($row['tech_report_type']);
    $tech_rep_host_org=stripslashes($row['tech_rep_host_org']);
    $affiliation_for_pub=stripslashes($row['affiliation_for_pub']);
    $rating_justification=stripslashes($row['rating_justification']);
    # $rae=$row['rae']; # no longer used as RAE tagging done via people record
    $oro_url=stripslashes($row['oro_url']);
    
    $t++; # increment total actually output
    
    # authors field
    $xmlauthors.="<dc:creator>".htmlentities($first_author);
    if (!empty($other_authors)) {
      if (count($otherAuthorsA)==1) {
        $xmlauthors.=" and ".htmlentities($other_authors);
      } else {
        $xmlauthors.=", ".htmlentities($other_authors);
      }
    }
    if ($autheds=="editors") $xmlauthors.=" (eds.)"; # editors, added June 2010
    $xmlauthors.="</dc:creator>\n";
    
    # date field
    $xmldate.="<dc:date>";
    if ($date[1] > 1970) {
      $xmldate.=$date[1];
    } elseif ($start_date[1] > 1970 && $end_date[1] > 1970) {
      if ($start_date[1]!=$end_date[1]) {
        $xmldate.=$start_date[1]." - ".$end_date[1];
      } else {
        $xmldate.=$start_date[1];
      }
    } elseif ($start_date[1] > 1970) {
      $xmldate.=$start_date[1];
    }
    $xmldate.="</dc:date>\n";
    
    # description field
    # new logic for different presentations at a workshop or conference
    if ($work_type=="demo" && !empty($conference_name)) {
      $desc.="Demo at ".$conference_name;
    } elseif ($work_type=="poster" && !empty($conference_name)) {
      $desc.="Poster at ".$conference_name;
    } elseif (!empty($workshop_name) && !empty($conference_name)) {
      $desc.="Workshop: ".$workshop_name." at ".$conference_name;
    } else {
      if (!empty($workshop_name)) $desc.="Workshop: ".$workshop_name;
      if (!empty($conference_name)) $desc.=$conference_name;
    }
    if (!empty($journal_name)) $desc.=", ".$journal_name;
    if (!empty($geo_location)) $desc.=", ".$geo_location;
    if (!empty($book_chapter)) {
      $desc.=" <strong>".$book_chapter."</strong>";
      if (!empty($book_editors)) $desc.=", in eds. ".$book_editors;
      if (!empty($book_title)) $desc.=", ".$book_title;
    } else {
      if (!empty($book_title)) $desc.=" <B>$book_title</B>";
      if (!empty($book_editors)) $desc.=", eds. ".$book_editors;
    }
    if (!empty($volume)) $desc.=", ".$volume;
    if (!empty($number)) $desc.=", ".$number;
    if (!empty($page_numbers)) $desc.=", pp. ".$page_numbers;
    if (!empty($publisher)) $desc.=", ".$publisher;
    
    if (!empty($tech_rep_host_org)) $desc.=", ".$tech_rep_host_org;
    if (!empty($tech_report_type)) $desc.=" ".$tech_report_type;
    if (!empty($tech_report_type) && !empty($tech_report_number)) $desc.=" ".$tech_report_number;
    if (empty($tech_report_type) && !empty($tech_report_number)) $desc.=" Technical report ".$tech_report_number;
    
    if (!empty($paper_url)) {
      $xmllink.="<link>".str_replace("&", "&amp;", rawurldecode($paper_url))."</link>\n"; # return entities to normal characters
    } elseif (!empty($oro_url)) {
      $xmllink.="<link>".str_replace("&", "&amp;", rawurldecode($oro_url))."</link>\n"; # return entities to normal characters
    }
    
    if (!empty($rating_justification)) ", ".$rating_justification;
  }
  $desc="<description><![CDATA[".$desc."]]></description>\n";
  $xmlguid="<guid>http://projects.kmi.open.ac.uk/mksmart/pubid".$id."</guid>\n";
  
  $return = $xmltitle.$xmlauthors.$xmldate.$xmllink.$desc.$xmlguid;
  
  return $return; # return everything in one field
}

# translate some phpBB text formatting codes back into HTML
# this was added for long 'example' fields in the XML
function unBB($e) {
  $e=str_replace("[br]","<br>",$e);
  $e=str_replace("[b]","<strong>",$e);
  $e=str_replace("[/b]","</strong>",$e);
  return $e;
}

# translates some odd characters that are often used by Word into safer equivalents
function transWordChars($theText) {
  $theText=str_replace(chr(145),"'",$theText); # Word single quote pair open
  $theText=str_replace(chr(146),"'",$theText); # Word single quote pair close
  $theText=str_replace(chr(180),"'",$theText); # odd single quote
  
  $theText=str_replace(chr(147),'"',$theText); # Word double quote pair open
  $theText=str_replace(chr(148),'"',$theText); # Word double quote pair close
  
  $theText=str_replace(chr(150),"-",$theText); # Word long dash
  $theText=str_replace(chr(151),"-",$theText); # Word longer dash
  $theText=str_replace(chr(226).chr(128).chr(147),"-",$theText); # long dash
  
  $theText=str_replace(chr(133),"...",$theText); # Ellipsis
  return $theText;
}

# translates some odd characters pasted from Word
function tw2($text) {
  $text=str_replace(chr(226).chr(128).chr(147),"-",$text); # long dash
  
  $text=str_replace(chr(226).chr(128).chr(152),"'",$text); # open single quote
  $text=str_replace(chr(226).chr(128).chr(153),"'",$text); # close single quote

  $text=str_replace(chr(226).chr(128).chr(156),'"',$text); # open double quote
  $text=str_replace(chr(226).chr(128).chr(157),'"',$text); # close double quote
  return $text;
}

# clean up submitted text - try to avoid problems later
function cleanText($text) {
  $text=strip_tags(trim($text));
  $text=transWordChars($text);
  $text=tw2($text);
  $text=str_replace(chr(13).chr(10),' ',$text); # new line to space
  return $text;
}

# function to toggle the RAE flag on a given publication ($pub_id) for a given user ($oucu)
function togglePub($pub_id, $oucu) {
  global $connection;
  # get current RAE flag list and turn into an array
  $sql="SELECT performance_raePubs FROM people WHERE oucu='$oucu' lIMIT 1";
  $result=@mysql_query($sql,$connection) or die("SQL Error:<BR>$sql");
  if (!mysql_num_rows($result)) {
    $message="ERROR: Could not find that person's record";
    return $message;
  }
  $row=mysql_fetch_array($result);
  $raePubsA=explode("|",$row['performance_raePubs']);
  # check to see if its already there
  $key=array_search($pub_id,$raePubsA);
  if ($key!==false) {
    # found, so remove
    array_splice($raePubsA,$key,1);
  } else {
    # no found, so append
    $raePubsA[]=$pub_id;
  }
  # sort the array, turn back into a delimited list then update the person's record
  sort($raePubsA);
  $raePubs=implode("|",$raePubsA);
  $sql="UPDATE people SET performance_raePubs='$raePubs' WHERE oucu='$oucu' LIMIT 1";
  $result=@mysql_query($sql,$connection) or die("SQL Error:<BR>$sql");
  $message="RAE inclusion toggled for publication #".$pub_id;
  return $message;
}

# function to check a string for presence of an OUCU - that being 2-6 lower case letters followed by 1-6 digits
function isOUCU($text) {
  if (preg_match('/[a-z]{2,6}[0-9]{1,6}/', $text)) return true;
  return false;
}
?>
