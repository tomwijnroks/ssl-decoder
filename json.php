<?php
error_reporting(E_ALL & ~E_NOTICE);
foreach (glob("functions/*.php") as $filename) {
  include $filename;
}

if ( isset($_GET['host']) && !empty($_GET['host'])) {
  $data = [];
  $hostname = mb_strtolower(get($_GET['host']));
  $host = parse_hostname($hostname);
  if ($host['port']) {
    $port = $host['port'];
  } else {
    $port = get($_GET['port'], '443');
  }
  $host = $host['hostname'];
  if ( !is_numeric($port) ) {
    $port = 443;
  }
  $data["data"] = check_json($host,$port);

} elseif(isset($_GET['csr']) && !empty($_GET['csr'])) {
  $data["data"]["chain"]["1"] = csr_parse_json($_GET['csr']);

} else {
  $data["error"] = ["Host is required"];

}

$data = utf8encodeNestedArray($data);

if(isset($data["data"]["error"])) {
  $data["error"] = $data["data"]["error"];
  unset($data["data"]);
}

if ($_GET["type"] == "pretty") {
  header('Content-Type: text/html');
  echo "<pre>";
  echo htmlspecialchars(json_encode($data,JSON_PRETTY_PRINT));
  echo "</pre>";
  ?>

  <?
} else {
  header('Content-Type: application/json');
  echo json_encode($data);
}

?>

