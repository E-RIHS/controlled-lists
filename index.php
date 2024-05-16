<?php

$defLabels = array();

//The script expects a relative directory called "local" to exist and 
// be writable by the script.
$localCache = checkDir();

$vocabJson = array();

if ($localCache)
  {$warning = "";}
else
  {$warning = "<div class=\"alert alert-warning\" role=\"alert\">".
    "Warning: The cache folder, <b>local</b>,  does not exist or is not writable. ". 
    "All data will be refreshed and formatted live.".
    "</div>";}
////////////////////////////////////////////////////////////////////////
  
$config_path = getFullURL("config.json");  
$config = getRemoteJsonDetails($config_path, true);

// Can be used to ignore handles and test local links for debugging
//if (true)
//  {$config = forceLocalurls($config);}

$options = $config["options"];

foreach (array_keys($options) as $k => $gv)
  {$gv = strtolower($gv);
   $nv = "GET_".$gv;
   if (isset($_GET[$gv]) and $gv == "group") {$$nv = $_GET[$gv];}
   else if (isset($_GET[$gv])) {$$nv = true;}
   else {$$nv =false;}}
   
if (isset($_GET["config"])) {$GET_config = true;}
else {$GET_config = false;}

if (isset($_GET["flush"])) {
  $message = flushCache();
  $warning .= "<div class=\"alert alert-warning\" role=\"alert\">".
    "$message".
    "</div>";}

$ths = $config["thesaurus-code"];
$api = $config["vocabulary-api"];   
$hds = $config["group-handles"];
$logo = $config["logo"];
$logoLink = $config["logo-link"];
$githubLogo = $config["github-logo"];
$githubLogoLink = $config["github-logo-link"];
$title =  $config["title"];
$vocabularyLink = $config["vocabulary-link"];
$vocabularyLabel = $config["vocabulary-label"];

// Special terms such as "other" will always be pushed to the top of 
// lists if they have been included in the relevant group.
list($specialID, $specialLabel, $specialUrl) = checkGroup ("g50");
$special = array();
$special = json_decode(getDefault ($specialID, $specialLabel, true, true));

if ($GET_config) {
  
  if ($GET_refresh)
    {$groupsUrl = $api."group/".$ths;  
     $groups = getRemoteJsonDetails($groupsUrl, true);}
  else
    {$groups = array();}
    
  checkConfig($config, $groups);
  }
else if (isset($_GET["vocab"]) and $GET_group)
  {
  list($groupID, $groupLabel, $url, $groupArr) = checkGroup ($GET_group);
  $vocabURL = $vocabularyLink."?idg=$groupID&idt=$ths";
  header( sprintf( "Location: $vocabURL" ) );
  exit;  
  }
else if (isset($_GET["view"]) and $GET_group)
  {
  buildtree($GET_group);
  exit;  
  }
else if ($GET_group) {
  list($groupID, $groupLabel, $url, $groupArr) = checkGroup ($GET_group);

  if ($GET_refresh)
    {$url = getFull($groupID, $groupLabel, $GET_refresh);
     $GET_refresh = false;}

  if ($GET_simple)
    {$out = getDefault($groupID, $groupLabel, False, True);}
  else if ($GET_full)
    {$out = getFull($groupID, $groupLabel);}
  else if ($GET_info)
    {$out = getInfo($groupID, $groupLabel, $groupArr, $GET_refresh);}
  else
    {$out = getDefault($groupID, $groupLabel);}
  
  // Directly display content
  
  header('Content-Type: application/json');
  header("Access-Control-Allow-Origin: *");  
  echo $out;
  
  //Alternatively we could redirect to the local file
  //header('Location: '.$url);   
  exit;  
  }
else 
  {
  $groupsUrl = $api."group/".$ths;  
  $out = getRemoteJsonDetails($groupsUrl, true);
  
  $loptions = array_keys($options);
  sort($loptions);
  
  foreach ($out as $k => $a)
    {$out[$k] = formatGroupDets ($a);}
  
  $out = json_encode($out);
  $ops = json_encode($options);
  
  // Using __FILE__ to reference the current file
  $filename = __FILE__;
  $lastModified = filemtime($filename);
  $dateString = date("Y-m-d", $lastModified);

echo <<<END
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$title</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="js/cls.js"></script>
</head>
<body>
  <div class="container">        
    <div class="d-flex justify-content-between align-items-center mt-5">
      <div  class="d-flex align-items-center">
        <h2 class="mt-0">$title</h2>
         &nbsp;-&nbsp;
        <!--- <a href="./?config" class="text-decoration-none mr-2"><i class="fas fa-cog" title="Tool Configuation" style="color: grey;"></i></a> --->
        <a href="./?config&refresh" class="text-decoration-none mr-2"><i class="fas fa-cog" title="Current Tool Configuation" style="color: #628d5d;"></i></a>
      </div>
      <div>
	<a href="$logoLink" class="mr-2"><img style="margin-bottom:8px;" src="$logo" height="38.391" alt="Logo"></a>
	<a href="$githubLogoLink"><img style="margin-bottom:8px;opacity:0.25;" src="$githubLogo" height="38.391" alt="Logo"></a>
	</div>
    </div>
    $warning
    <div id="groupsList" class="list-group mt-3">
      <!-- Group links will be injected here -->
    </div>
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0 pl-2 pt-1"  style="color: lightgray;">All terms referenced from the <a class="" style="color: lightgray;" href="$vocabularyLink">$vocabularyLabel</a></h5>
      <h5 class="mb-0 pr-2 pt-1"  style="color: lightgray;">$dateString</h5>
    </div>
  </div>

  <script>
  
  var optionsData = '$ops';      
  var groupsData = '$out';  
  
  // Parse the JSON data
  var groups = sortGroupsByTitle(JSON.parse(groupsData));
  var options = JSON.parse(optionsData);    
  
  // Loop through the groups and append them to the list
   $(document).ready(function() {
    groups.forEach(function(group) {
      $('#groupsList').append(generateGroupHTML(group));
      });
    });
    
  </script>
</body>
</html>

END;
    
}

function checkConfig ($config, $groups=false)
  {

  if($groups)
    {
    foreach ($groups as $gid => $group)
      {
      if (!isset($config["group-handles"][$group["idGroup"]]))
        {
        $config["group-handles"][$group["idGroup"]] = array(
          "id" => $group["idGroup"],
          "label" => getValue ($group, "labels", "en", "title"),
          "handle" => False,
          "url" => getFullURL("")."?group=". $group["idGroup"]
          );
        }    
      }
   
    $data = $config["group-handles"]; 
    uasort($data, function ($a, $b) {
      return strcmp($a['label'], $b['label']);
      });
    $config["group-handles"] = $data;
    }
  
  $out = json_encode($config);
  
  header('Content-Type: application/json');
  header("Access-Control-Allow-Origin: *");  
  echo $out;  
  exit;    
  }

function prg($exit=false, $alt=false, $noecho=false)
  {
  if ($alt === false) {$out = $GLOBALS;}
  else {$out = $alt;}
  
  ob_start();
  echo "<pre class=\"wrap\">";
  if (is_object($out))
    {var_dump($out);}
  else
    {print_r ($out);}
  echo "</pre>";
  $out = ob_get_contents();
  ob_end_clean(); // Don't send output to client
  
  if (!$noecho) {echo $out;}
    
  if ($exit) {exit;}
  else {return ($out);}
  }

function getRemoteJsonDetails($uri, $decode = false, $format = false, $maxRetries = 3, $retryDelay = 2) 
  {
  global $vocabJson;
  
  if ($format) {$uri .= "." . $format;}
  
  if (isset($vocabJson[$uri]))
    {
    //echo "<!--- Cached: $uri --->";
    $result = $vocabJson[$uri];  
    if ($decode) {return json_decode($result, true);}
    else {return ($results);}    
    }
  else
    {
    $attempt = 0;
    while ($attempt < $maxRetries) {
      //echo "<!--- Attempt [$attempt]: $uri --->";
      
      // Initialize cURL session
      $ch = curl_init();

      // Set cURL options
      curl_setopt($ch, CURLOPT_URL, $uri);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification for simplicity
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Ignore SSL certificate verification for simplicity
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; YourAppName/1.0)'); // Optional: Set a custom user agent

      // Execute cURL session
      $result = curl_exec($ch);
      $curlError = curl_errno($ch);
      curl_close($ch);

      // Check if there were any errors
      if (!$curlError) {
        // No errors, process the response
        $vocabJson[$uri] = $result;
        if ($decode) {return json_decode($result, true);}
        else {return $result;}
        } 
      else {
        // There was an error, increment attempt counter
        $attempt++;
        if ($attempt >= $maxRetries) {
          trigger_error('cURL Error after ' . $maxRetries . ' retries: ' . curl_error($ch));
          return false; // Optional: Return false or handle as needed
          }
        // Delay before retrying
        sleep($retryDelay);
        }
      }
    }
  }


function getValue ($arr, $string, $lang="en", $what="value")
  {
  $value = null;

  if (isset($arr[$string])) {
    foreach ($arr[$string] as $val) {
      if (!isset($val["lang"])) {$val["lang"] = False;}
      if ($val["lang"] == $lang) {
        $value = $val[$what];
        break; // Exit the loop when an English label is found
        }
      }

    if (!$value && isset($arr[$string][0])) {
      $value = $arr[$string][0][$what];}
    }      

  return($value);
  }
  
  
function getAllValues ($arr, $string, $lang=false)
  {
  $out = array();

  if (isset($arr[$string])) {
    foreach ($arr[$string] as $val) {
      if ($lang and $val["lang"] != $lang)
        {continue;}
      $out[] = $val["value"];      
      }
    }
    
  return($out);
  }
  
function getBandN ($arr, $string)
  {
  global $api, $defLabels;
  
  $out = array();
  
  if (isset($arr[$string])) {
    foreach ($arr[$string] as $k => $val) {    
      if (isset($defLabels[$val["value"]]))
        {$out[$val["value"]] = $defLabels[$val["value"]];}
      else
        {$parts = explode("/", $val["value"]);      
         $json_url = $api."concept/handle/".$parts[3]."/".$parts[4];                 
         $dets = getRemoteJsonDetails($json_url, true);      
         $out[$val["value"]] = getValue ($dets[$val["value"]], "http://www.w3.org/2004/02/skos/core#prefLabel");         
         $defLabels[$val["value"]] = $out[$val["value"]];
         }         
      }      
    }
    
  return($out);
  }
  
function getFull ($groupID, $groupLabel, $refresh=false)
  {
  global $api, $ths, $defLabels, $hds, $GET_info, $localCache, $special;
 
  // if the local cache directory is not writable always just refresh and out put the data.  
  if(!$localCache){$refresh = true;}
  
  // make the group label safe fora filename
  $groupLabel = simplifyFilePath($groupLabel);
  $filePath = "local/".$groupID."_".$groupLabel."_full.json";  
  $infoPath = "local/".$groupID."_".$groupLabel."_info.json";    
  
  $url = $api."group/".$ths."/branch?idGroups=" . $groupID;
    
  if (file_exists($filePath) && !$refresh) {
    $out = file_get_contents($filePath);
    return ($out);
    }
  else {
    $terms = getRemoteJsonDetails($url, true);
    
    $defaultJSON = getDefault ($groupID, $groupLabel, $refresh, false, $terms);    
    $defaultData = json_decode($defaultJSON, true);
    $defLabels = $defaultData["list"];
    
    $data = array();
    $orderByNotation = true;
    $orBy = array();

    foreach ($terms as $tid=> $term) {
      $row = array();
      $type = getValue ($term, "http://www.w3.org/1999/02/22-rdf-syntax-ns#type");
      $row["notation"] = getValue ($term, "http://www.w3.org/2004/02/skos/core#notation");
      if (!$row["notation"]) {$orderByNotation = false;}                 
      $row["prefLabel"] = getValue ($term, "http://www.w3.org/2004/02/skos/core#prefLabel");      
      
      if ((substr($row["prefLabel"], 0, 1) === '<' && substr($row["prefLabel"], -1) === '>') or
        ($type != "http://www.w3.org/2004/02/skos/core#Concept")) {
        //skip grouping terms in AAT
        //skip non concepts added to groups by OpenTheso
        }
      else
        {
        $row["definition"] = getValue ($term, "http://www.w3.org/2004/02/skos/core#definition");            
        $row["altLabel"] =  getAllValues ($term, "http://www.w3.org/2004/02/skos/core#altLabel");
        $row["narrower"] =  getBandN ($term, "http://www.w3.org/2004/02/skos/core#narrower");
        $row["broader"] =  getBandN ($term, "http://www.w3.org/2004/02/skos/core#broader");
            
        $tid_parts = explode("/", $tid);
        $row["term_json_url"] = $api."concept/handle/".$tid_parts[3]."/".$tid_parts[4];
        
        $data[$tid] = $row;
        }
      
      if (!isset($defLabels[$tid]))
        {$defLabels[$tid] = $row["prefLabel"];}
      } 

    // Custom comparison function for uasort
   // uasort($data, function ($a, $b) {
   //   return strcmp($a['prefLabel'], $b['prefLabel']);
    //  });
    if ($orderByNotation) {
      sortByKeyAndPromote($data, $special, 'notation');}
    else {
      sortByKeyAndPromote($data, $special, 'prefLabel');}

    if (!isset ($hds[$groupID]["handle"]))
      {$hds[$groupID]["handle"] = False;}
    
    if ($hds[$groupID]["handle"])
      {$use = $hds[$groupID]["handle"]."?urlappend=%26full";}
    else
      {$use = $hds[$groupID]["handle"];}
 
    $out = array(
      "id" => "$groupID",
      "label" => "$groupLabel",
      "created" => date('Y-m-d H:i:s', time()),
      "handle" => $use,
      "url" => getFullURL("")."?group=". $groupID,
      "data" => $data);

    $out = json_encode($out, JSON_PRETTY_PRINT);
    
    // Write the JSON to the file, creating or replacing as necessary
    if ($localCache)
      {file_put_contents($filePath, $out);}
      
    return($out);
    }
  } 
  
function getInfo ($groupID, $groupLabel, $groupArr, $refresh=false)
  {
  global $hds, $options, $localCache;
  
  $groupLabel = simplifyFilePath($groupLabel);
  $infoPath = "local/".$groupID."_".$groupLabel."_info.json";
      
  // if the local cache directory is not writable always just refresh and out put the data.  
  if(!$localCache){$refresh = true;}  
    
  if (file_exists($infoPath) && !$refresh) {
    $out = file_get_contents($infoPath);
    return ($out);
    }
  else 
    {
    $gd = formatGroupDets ($groupArr);       
      
    $out = array(
      "id" => $groupID,
      "label" => $groupLabel,      
      "created" => date('Y-m-d H:i:s', time()),
      "handle" =>  $gd["handle"],
      "url" => $gd["url"]."&info",
      "links" => array()
      );
    
    foreach ($gd["links"] as $opt => $la)
      {$out["links"][$opt] = $la[0];
       $out["links"]["refresh-".$opt] = $la[1];}
       
    if ($gd["handle"])
      {$out["links"]["refresh"] = $gd["handle"]."?urlappend=%26refresh";
       $out["handle"] .= "?urlappend=%26info";}
    else
      {$out["links"]["refresh"] = $gd["url"]."&refresh";}
    
    $out = json_encode($out, JSON_PRETTY_PRINT);
    
    // Write the JSON to the file, creating or replacing as necessary
    if ($localCache)
      {file_put_contents($infoPath, $out);}
    }
   
  //$out["gd"] = $gd;
  return ($out);
  }
  
function getDefault ($groupID, $groupLabel, $refresh=false, $simple=false, $terms=false)
  {  
  global $api, $ths, $hds, $localCache, $special;
    
  // if the local cache directory is not writable always just refresh and out put the data.  
  if(!$localCache){$refresh = true;}

  // make the group label safe fora filename
  $groupLabel = simplifyFilePath($groupLabel);

  $defaultFile = "local/".$groupID."_".$groupLabel."_default.json";
  $simpleFile = "local/".$groupID."_".$groupLabel."_simple.json";
  
  if ($simple)
    {$filePath = $simpleFile;}
  else
    {$filePath = $defaultFile;}     
    
  if (file_exists($filePath) && !$refresh) {
    $out = file_get_contents($filePath);
    return ($out);
    }
  else 
    {
    $url = $api."group/".$ths."/branch?idGroups=" . $groupID;
    if (!$terms)
      {$terms = getRemoteJsonDetails($url, true);}
      
    $data = array();
    $orderByNotation = true;
    $orBy = array();
    //if ($groupID != "g50") {prg(1, $terms);}
    
    foreach ($terms as $tid=> $term) {
      $type = getValue ($term, "http://www.w3.org/1999/02/22-rdf-syntax-ns#type");
      $val = getValue ($term, "http://www.w3.org/2004/02/skos/core#prefLabel");
      $notation = getValue ($term, "http://www.w3.org/2004/02/skos/core#notation");      
      if (!$notation) {$orderByNotation = false;}
      
      if ((substr($val, 0, 1) === '<' && substr($val, -1) === '>') or
        ($type != "http://www.w3.org/2004/02/skos/core#Concept")){
        //skip grouping terms in AAT
        //skip non concepts added to groups by OpenTheso
        }
      else
        {$orBy[$tid] = $notation;
         $data[$tid] = $val;}
      }     
    
    if (!$orderByNotation) {$orBy = array();}
    sortAndPromote($data, $special, $orBy, true);
    
    if (!isset ($hds[$groupID]["handle"]))
      {$hds[$groupID]["handle"] = False;}
    
    $out = array(
      "id" => "$groupID",
      "label" => "$groupLabel",
      "created" => date('Y-m-d H:i:s', time()),
      "handle" => $hds[$groupID]["handle"],
      "url" => getFullURL("")."?group=". $groupID,
      "list" => $data);
      
    $d_out = json_encode($out, JSON_PRETTY_PRINT);
    
    $data_values = array_values($data);
    $s_out = json_encode($data_values, JSON_PRETTY_PRINT);
    
    // If one can, write the JSON to the file, creating or replacing as necessary
    if ($localCache)
      {file_put_contents($defaultFile, $d_out);
       file_put_contents($simpleFile, $s_out);}
    
    if ($simple)
      {return($s_out);}
    else
      {return($d_out);}
    }  
  }
  
  
function checkGroup ($groupParam)
  {
  global $api, $ths;
  
  if (preg_match('/^g\d+$/', $groupParam)) {
    $groupID = $groupParam;
    $groupLabel = false;}
  else {
    $groupID = false;
    $groupLabel = $groupParam;}
      
  $groupsUrl = $api."group/".$ths;
  $groups = getRemoteJsonDetails($groupsUrl, true);
  $url = false;

  foreach ($groups as $group) {
    if ($groupLabel) {
      foreach ($group["labels"] as $label) {
        if (strtolower($label["title"]) == $groupParam) {
          $groupID = $group["idGroup"];
          $groupLabel = $label["title"];
          $groupArr = $group;
          break 2; // Exit both loops when a match is found
          }
        }
      }
    else if ($groupID == strtolower($group["idGroup"])) {
      foreach ($group["labels"] as $label) {
        if (strtolower($label["lang"]) == "en") {
          $groupLabel = $label["title"];
          $groupID = $group["idGroup"];
          $groupArr = $group;
          break 2; // Exit both loops when a match is found
          }
        }
      if (!$groupLabel) {
        $groupLabel = $label["title"];
        }
      }
    }
    
  if ($groupID and $groupLabel) {
    $url = $api."group/".$ths."/branch?idGroups=" . $groupID;}
    
  return (array($groupID, $groupLabel, $url, $groupArr));
  }
  
function simplifyFilePath($string) {    

    $string = preg_replace('/[ ]/', '_', $string);
    $string = strtolower($string);

    return $string;
}

function getFullURL($relativePath="") {

    // Check if HTTP or HTTPS
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';

    // Get the server name
    $server = $_SERVER['HTTP_HOST'];

    // If you need the base path, uncomment and adjust the next line
    $basePath =  trim($_SERVER['SCRIPT_URL'], "/");

    // Ensure the first character of the relative path is a '/'
    if (substr($relativePath, 0, 1) != '/') {
        $relativePath = '/' . $relativePath;
    }

    // Construct the full URL
    $fullURL = $protocol . '://' . $server . '/' . $basePath . $relativePath;

    return $fullURL;
}

function checkDir ($directory="local")
  {
  if (!is_dir($directory) or !is_writable($directory)) 
    {return (false);}
  else
    {return(true);}
  }

function reorderBasedOnValues($A, $B) {
    // Create an array of keys sorted by the values in B
    $keys = array_keys($B);
    usort($keys, function($a, $b) use ($B) {
        return $B[$a] <=> $B[$b];
    });

    // Reorder A based on the sorted keys
    $reorderedA = [];
    foreach ($keys as $key) {
        $reorderedA[$key] = $A[$key];
    }

    return $reorderedA;
}

function sortAndPromote(array &$array, array $testArray = array(), $sortBy=array(), $useAsort = true) 
  {  

    // Step 1: Sort the array using asort (preserving keys) or sort (reindexing)
    if ($sortBy)
      {$array = reorderBasedOnValues($array, $sortBy);}
    else if ($useAsort) {
        asort($array);
    } else {
        sort($array);
    }

    // Step 2: Create a temporary array to hold promoted elements
    $promoted = [];

    // Scan the array for elements that are in the test array
    foreach ($array as $key => $value) {
        if (in_array($value, $testArray)) {
            // If the value is in the test array, add it to the promoted array
            $promoted[$key] = $value;
            // Remove from original array to avoid duplication
            unset($array[$key]);
        }
    }

    // Step 3: Sort the promoted array based on the order of elements in the test array
    $sortedPromoted = [];
    foreach ($testArray as $testVal) {
        foreach ($promoted as $key => $val) {
            if ($val === $testVal) {
                $sortedPromoted[$key] = $val;
            }
        }
    }

    // Step 4: Merge the sorted promoted elements back to the beginning of the original array
    if ($useAsort) {
        $array = $sortedPromoted + $array; // '+' preserves keys
    } else {
        $array = array_merge($sortedPromoted, $array); // merge for reindexed arrays
    }
} 

function sortByKeyAndPromote(array &$data, array $testArray, $key = 'prefLabel') {
    // Create an associative array to quickly check if a value is in the test array
    $promoteValues = array_flip($testArray);

    // Define the custom sorting function
    $sortingFunction = function($a, $b) use ($promoteValues, $key) {
        // Check if both values are in the test array
        $aIsPromoted = isset($promoteValues[$a[$key]]);
        $bIsPromoted = isset($promoteValues[$b[$key]]);

        if ($aIsPromoted && !$bIsPromoted) {
            return -1; // $a should come before $b
        } elseif (!$aIsPromoted && $bIsPromoted) {
            return 1; // $b should come before $a
        }

        // If neither or both are promoted, sort normally
        return strcmp($a[$key], $b[$key]);
    };

    // Apply the sorting function
    uasort($data, $sortingFunction);
}

function formatGroupDets ($a)
  {    
  global $hds, $options;  
  //prg(0, $a);
  
  $loptions = array_keys($options);
  sort($loptions);
  
  $base = array(
    "id" => $a["idGroup"],
    "label" => getValue ($a, "labels", "en", "title"),
    "handle" => False,
    "url" => getFullURL("")."?group=". $a["idGroup"]
    );
    
  $out = array_merge($a, $base);
  
  if(isset($hds[$a["idGroup"]]))
    {$out = array_merge($out, $hds[$a["idGroup"]]);}
    
  foreach ($loptions as $j => $option)
    {      
    if (!in_array($option, array("group", "refresh")))
      {
      if($out["handle"])
        {
        if ($option == "default")
          {$out["links"][$option][0] = $out["handle"];
           $out["links"][$option][1] = $out["handle"]."?urlappend=%26refresh";}
        else
          {$out["links"][$option][0] = $out["handle"]."?urlappend=%26".$option;
           $out["links"][$option][1] = $out["handle"]."?urlappend=%26".$option."%26refresh";}
        }
      else
        {
        if ($option == "default")
          {$out["links"][$option][0] = $out["url"];
           $out["links"][$option][1] = $out["url"]."?refresh";}
        else
          {$out["links"][$option][0] = $out["url"]."&".$option;
           $out["links"][$option][1] = $out["url"]."&".$option."&refresh";}
        }
      }
    }
    
  return ($out);
  } 
  
function flushCache() 
  {
  $directory = "local";
  $message = "";
  
  if (is_dir($directory)) 
    {    
    // Open the directory
    $dirHandle = opendir($directory);
      
    // Check if the directory could not be opened
    if ($dirHandle === false) {
        $message .= "Failed to open the directory.<br>";
      }
    else
      {
      // Loop through all the files in the directory
      while (($file = readdir($dirHandle)) !== false) {
        // Construct the full path to the file
        $filePath = $directory . '/' . $file;

        // Check if the file is a JSON file
        if (is_file($filePath) && pathinfo($file, PATHINFO_EXTENSION) == 'json') {
            // Attempt to delete the file
            if (unlink($filePath)) {
                $message .= "&nbsp;&nbsp;Deleted $file<br>";
            } else {
                $message .= "&nbsp;&nbsp;Failed to delete $file<br>";
            }
          }
        }
	
      if (!$message)
	{$message.= "No local cache files where identified";}
      else
	{$message = "Deleting cached json files:<br>$message";}
        
      // Close the directory handle
      closedir($dirHandle);
      }    
    }
  else
    {$message .= "No local cache directory was found, so there are no cached files to delete.";} 
  
  return ($message);
  }

function buildTreeData($nodes, $links) {
   
    // Create an associative array to hold the node details
    $treeNodes = [];
    foreach ($nodes as $id => $label) {
        $treeNodes[$id] = [
            'name' => $label,
            'children' => []
        ];
    }

    // Identify the root node (a node that is never a target)
    $allTargets = array_column($links, 1);
    $rootNodeId = null;
    foreach ($nodes as $id => $label) {
        if (!in_array($id, $allTargets)) {
            $rootNodeId = $id;
            break;
        }
    }

    // If no root node is found, return an empty array or handle as needed
    if ($rootNodeId === null) {
        return []; // or handle this case appropriately
    }

    // Build the tree by linking nodes according to $links
    foreach ($links as $link) {
        $sourceId = $link[0];
        $targetId = $link[1];
        if (isset($treeNodes[$sourceId]) && isset($treeNodes[$targetId])) {
            $treeNodes[$sourceId]['children'][] = &$treeNodes[$targetId];
        }
    }

    // Return the tree from the root node
    return $treeNodes[$rootNodeId];
}

function forceLocalurls ($config)
  {
  $page = getFullURL();
  
  foreach ($config["group-handles"] as $gid => $a)
    {$a["handle"] = false;
     $pi = pathinfo($a["url"]);
     $a["url"] = $page.$pi["basename"];
     $config["group-handles"][$gid] = $a;}
    
  return ($config);  
  }
  
function buildtree($group)
  {
  list($groupID, $groupLabel, $url, $groupArr) = checkGroup ($group);
  
  $default = getFull($groupID, $groupLabel);
  $default = json_decode($default, true);
  
  $nodes = array();
  $links = array();
  
  $maxlabellength = 0;
  
  foreach ($default["data"] as $hid => $a)
    {
    if (strlen($a["prefLabel"]) > $maxlabellength) {$maxlabellength = strlen($a["prefLabel"]);}
    if (!isset($nodes[$hid])) {$nodes[$hid] = $a["prefLabel"];}
    }


  foreach ($default["data"] as $hid => $a)
    {
    if (!$a["narrower"] and !$a["broader"])
      {$links["$groupID - $hid"] = array($groupID, $hid);}
      
    foreach ($a["narrower"] as $nid => $nl)
      {
      if (!isset($nodes[$nid])) {$nodes[$nid] = $nl;}
      if (!isset($links["$hid - $nid"])) {
	$links["$hid - $nid"] = array($hid, $nid);}
      }
      
    foreach ($a["broader"] as $bid => $bl)
      {
      if (!isset($nodes[$bid])) 
	{$links["$groupID - $hid"] = array($groupID, $hid);}
      else if (!isset($links["$bid - $hid"])) {
	$links["$bid - $hid"] = array($bid, $hid);}
      }
    }

  $nodes[$groupID] = $groupLabel;

  $data = buildTreeData($nodes, $links);
  $jsData = json_encode($data);
  $page = getFullURL("");
  $hwidth = $maxlabellength * 7;

  echo <<<END

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tree Layout with Navigation Controls</title>
    <script src="https://d3js.org/d3.v6.min.js"></script>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        svg {
            width: 100%;
            height: 100vh;
            border: 1px solid #ccc;
        }
        .node circle {
            fill: steelblue;
            stroke: steelblue;
            stroke-width: 3px;
        }
        .node text {
            font: 12px sans-serif;
        }
        .link {
            fill: none;
            stroke: #ccc;
            stroke-width: 2px;
        }
        #navigation {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        .button {
            padding: 5px 10px;
            text-align: center;
            background: #ddd;
            border: 1px solid #ccc;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="navigation">
        <div class="button" onclick="resetZoom()">Home</div>
        <div class="button" onclick="goBack()">Back</div>
    </div>
    <div id="tree-container"></div>
    <script>
        const margin = { top: 20, right: 120, bottom: 20, left: 120 };
        const width = 960 - margin.left - margin.right;
        const height = 800 - margin.top - margin.bottom;
        const zoom = d3.zoom().scaleExtent([0.1, 10]).on("zoom", (event) => {
          svgGroup.attr("transform", event.transform);
          });
  
        const svg = d3.select("#tree-container").append("svg")
            .attr("viewBox", `0 0 \${width + margin.left + margin.right} \${height + margin.top + margin.bottom}`)
            .style("background-color", "white")
            .call(zoom)
            .append("g")
            .attr("transform", `translate(\${margin.left}, \${margin.top})`);

        const svgGroup = svg.append("g");

        const treeData = $jsData;

        const root = d3.hierarchy(treeData);
        const tree = d3.tree().nodeSize([25, $hwidth]); // Custom node size for compact layout
        update(root);

        function update(source) {
            tree(root);

            let minX = Infinity;
            let minY = Infinity;
            root.each(node => {
                if (node.y < minY) {
                    minX = node.x;
                    minY = node.y;
                }
            });
            const offsetY = height / 2 - minX;

            const nodes = root.descendants().reverse();
            const links = root.links();

            svgGroup.selectAll('g.node').remove();
            svgGroup.selectAll('path.link').remove();

            const node = svgGroup.selectAll('g.node')
                .data(nodes, d => d.id || (d.id = ++d.depth));

            const nodeEnter = node.enter().append('g')
                .attr('class', 'node')
                .attr("transform", d => `translate(\${d.y},\${d.x + offsetY})`);

            nodeEnter.append('circle')
                .attr('r', 5)
                .attr('class', 'node');

            nodeEnter.append('text')
                .attr("dy", "0.35em")
                .attr("x", d => d.children || d._children ? -10 : 10)
                .attr("text-anchor", d => d.children || d._children ? "end" : "start")
                .text(d => d.data.name);

            const link = svgGroup.selectAll('path.link')
                .data(links, d => d.target.id);

            link.enter().insert('path', "g")
                .attr("class", "link")
                .attr('d', d => d3.linkHorizontal()({
                    source: [d.source.y, d.source.x + offsetY],
                    target: [d.target.y, d.target.x + offsetY]
                }));
        }

        function resetZoom() {
          const zoomTransform = d3.zoomTransform(svg.node());
          const newZoomTransform = d3.zoomIdentity.translate(margin.left, margin.top);
          svg.transition().duration(750).call(
            zoom.transform,
            newZoomTransform,
          zoomTransform
          );
          }

        function goBack() {
            window.location.href = "$page"; // Replace with your URL
        }
    </script>
</body>
</html>
END;
  }
?>
