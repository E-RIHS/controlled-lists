<?php

$defLabels = array();

//The script expects a relative directory called "local" to exist and 
// be writable by the script.
$localCache = checkDir();

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
$options = $config["options"];

foreach (array_keys($options) as $k => $gv)
  {$gv = strtolower($gv);
   $nv = "GET_".$gv;
   if (isset($_GET[$gv]) and $gv == "group") {$$nv = $_GET[$gv];}
   else if (isset($_GET[$gv])) {$$nv = true;}
   else {$$nv =false;}}

$ths = $config["thesaurus-code"];
$api = $config["vocabulary-api"];   
$hds = $config["group-handles"];
$logo = $config["logo"];
$logoLink = $config["logo-link"];
$title =  $config["title"];
$vocabularyLink = $config["vocabulary-link"];
$vocabularyLabel = $config["vocabulary-label"];

if ($GET_group) {
  list($groupID, $groupLabel, $url) = checkGroup ($GET_group);

  if ($GET_refresh)
    {$url = getFull($groupID, $groupLabel, $GET_refresh);
     $GET_refresh = false;}
    
  if ($GET_simple)
    {$out = getDefault($groupID, $groupLabel, False, True);}
  else if ($GET_full)
    {$out = getFull($groupID, $groupLabel);}
  else if ($GET_info)
    {$out = getInfo($groupID, $groupLabel, $GET_refresh);}
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
    {
    if(isset($hds[$a["idGroup"]]))
      {$out[$k] = array_merge($a, $hds[$a["idGroup"]]);}
    else
      {
      $out[$k]["id"] = $a["idGroup"];
      $out[$k]["label"] = getValue ($a, "labels", "en", "title");
      $out[$k]["handle"] = False;
      $out[$k]["url"] = getFullURL("")."?group=".$a["idGroup"];
      }
 
    foreach ($loptions as $j => $option)
      {      
      if (!in_array($option, array("group", "refresh")))
        {
        if($out[$k]["handle"])
          {
          if ($option == "default")
            {$out[$k]["links"][$option][0] = $out[$k]["handle"];
             $out[$k]["links"][$option][1] = $out[$k]["handle"]."?urlappend=%26refresh";}
          else
            {$out[$k]["links"][$option][0] = $out[$k]["handle"]."?urlappend=%26".$option;
             $out[$k]["links"][$option][1] = $out[$k]["handle"]."?urlappend=%26".$option."%26refresh";}
          }
        else
          {
          if ($option == "default")
            {$out[$k]["links"][$option][0] = $out[$k]["url"];
             $out[$k]["links"][$option][1] = $out[$k]["url"]."?refresh";}
          else
            {$out[$k]["links"][$option][0] = $out[$k]["url"]."&".$option;
             $out[$k]["links"][$option][1] = $out[$k]["url"]."&".$option."&refresh";}
          }
        }
      }
    }
    
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="js/cls.js"></script>
</head>
<body>
  <div class="container">        
    <div class="d-flex justify-content-between align-items-center mt-5">
      <h2 class="mt-5">$title</h2>	  
      <a href="$logoLink"><img style="margin-top:48px;margin-bottom:8px;" src="$logo" height="38.391" alt="Logo"></a>
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

function getRemoteJsonDetails($uri, $decode = false, $format = false) {
    if ($format) {
        $uri .= "." . $format;
    }

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification for simplicity
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Ignore SSL certificate verification for simplicity
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; YourAppName/1.0)'); // Optional: Set a custom user agent

    // Execute cURL session and close it
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        trigger_error('cURL Error: ' . curl_error($ch));
    }
    curl_close($ch);

    // Return the result
    if ($decode) {
        return json_decode($result, true);
    }
    return $result;
}


function getValue ($arr, $string, $lang="en", $what="value")
  {
  $value = null;

  if (isset($arr[$string])) {
    foreach ($arr[$string] as $val) {
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
  global $api, $ths, $defLabels, $hds, $GET_info, $localCache;
  
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
    
    $infoPath = getInfo ($groupID, $groupLabel, $refresh); //refresh this one too
    $defaultJSON = getDefault ($groupID, $groupLabel, $refresh, false, $terms);    
    $defaultData = json_decode($defaultJSON, true);
    $defLabels = $defaultData["list"];
    
    $data = array();

    foreach ($terms as $tid=> $term) {
      $row = array();                 
      $row["prefLabel"] = getValue ($term, "http://www.w3.org/2004/02/skos/core#prefLabel");      
      
      if (substr($row["prefLabel"], 0, 1) === '<' && substr($row["prefLabel"], -1) === '>') {
        //skip grouping terms in AAT
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
    uasort($data, function ($a, $b) {
      return strcmp($a['prefLabel'], $b['prefLabel']);
      });

    $out = array(
      "id" => "$groupID",
      "label" => "$groupLabel",
      "created" => date('Y-m-d H:i:s', time()),
      "handle" => $hds[$groupID]["handle"]."?urlappend=%26full",
      "data" => $data);

    $out = json_encode($out, JSON_PRETTY_PRINT);
    
    // Write the JSON to the file, creating or replacing as necessary
    if ($localCache)
      {file_put_contents($filePath, $out);}
      
    return($out);
    }
  } 
  
function getInfo ($groupID, $groupLabel, $refresh=false)
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
    $out = array(
      "id" => $groupID,
      "label" => $groupLabel,      
      "created" => date('Y-m-d H:i:s', time()),
      "handle" => $hds[$groupID]["handle"]."?urlappend=%26info",
      "links" => array()
      );
    
    $loptions = array_keys($options);
    sort($loptions);
    
    foreach ($loptions as $k => $option)
      {
      if ($option != "group")
        {
        $out["links"][$option] = $hds[$groupID]["handle"]."?urlappend=%26".$option;
        if ($option != "refresh")
          {$out["links"]["refresh-".$option] = $hds[$groupID]["handle"]."?urlappend=%26refresh%26".$option;}
        }        
      }
       
    $out = json_encode($out, JSON_PRETTY_PRINT);
    
    // Write the JSON to the file, creating or replacing as necessary
    if ($localCache)
      {file_put_contents($infoPath, $out);}
    }
    
  return ($out);
  }
  
function getDefault ($groupID, $groupLabel, $refresh=false, $simple=false, $terms=false)
  {
  global $api, $ths, $hds, $localCache;
  
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

    foreach ($terms as $tid=> $term) {
      $val = getValue ($term, "http://www.w3.org/2004/02/skos/core#prefLabel");
      if (substr($val, 0, 1) === '<' && substr($val, -1) === '>') {
        //skip grouping terms in AAT
        }
      else
        {$data[$tid] = $val;}
      }     
    
    asort($data);
    
    $out = array(
      "id" => "$groupID",
      "label" => "$groupLabel",
      "created" => date('Y-m-d H:i:s', time()),
      "handle" => $hds[$groupID]["handle"],
      "list" => $data);
      
    $d_out = json_encode($out, JSON_PRETTY_PRINT);
    
    sort($data);
    
    $s_out = json_encode($data, JSON_PRETTY_PRINT);
    
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
          break 2; // Exit both loops when a match is found
          }
        }
      }
    else if ($groupID == strtolower($group["idGroup"])) {
      foreach ($group["labels"] as $label) {
        if (strtolower($label["lang"]) == "en") {
          $groupLabel = $label["title"];
          $groupID = $group["idGroup"];
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
    
  return (array($groupID, $groupLabel, $url));
  }
  
function simplifyFilePath($string) {    

    $string = preg_replace('/[ ]/', '_', $string);
    $string = strtolower($string);

    return $string;
}

function getFullURL($relativePath) {

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
  
?>
