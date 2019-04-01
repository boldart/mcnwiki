var site = 'http://localban.bombplates.com';
var filepath = 'C:\\Users\\Paul\\Documents\\iMacros\\Macros\\Bombplates\\Import\\files\\';
var macropath = 'Bombplates/import';

var imports = {};
<?php

$csvs = $argv;
array_shift($csvs);

foreach ($csvs AS $csv) {
  $nodes = array();
  $filename = "$csv.csv";
  
  $file = fopen($filename, 'r') or die ("Could not open file $filename\n");
  
  $labels = fgetcsv($file) or die ("Could not read header of $filename\n");
  
  while ($row = fgetcsv($file)) {
    $node = array();
    foreach ($row AS $index => $data) {
      $label = $labels[$index];
      $node[$label] = sanitize($data, $label);
    } // foreach index=>cell in row
    $nodes[] = $node;
  } // while line=fgets(file)
  fclose($file);
  
  $imports = array('tour' => $nodes);
  //$json = json_encode($imports);
  $json = json_encode($nodes);
  print "imports['$csv'] = $json;\n";
} // foreach csv in csvs

function sanitize($data, $label) {
  switch ($label) {
    case 'tour_date' :
    case 'post_date' :
      $time = (int)$data > 9999 ? $data : strtotime($data);
      $result = date('m/d/Y', $time);
      break;
    case 'meet_greet_start' :
    case 'meet_greet_stop' :
    case 'release_date' :
      $time = (int)$data > 9999 ? $data : strtotime($data);
      $result = date('Y-m-d', $time);
      break;
    case 'shour' :
    case 'ohour' :
      $data = (int)$data;
      $result = ($data >= 1 && $data <= 12) ? $data : '';
      break;
    case 'smin' :
    case 'omin' :
      $result = (int)$data ? sprintf('%02d', $data) : '';
      break;
    case 'sampm' :
    case 'oampm' :
      $result = (strtolower($data) == 'am') ? 'AM' : 'PM';
      break;
    case 'fan_club_only' :
      $result = (strtolower($data) == 'no' || !$data) ? 'NO' : 'YES';
      break;
    case 'purchase_1_type' :
    case 'purchase_2_type' :
    case 'purchase_3_type' :
    case 'purchase_4_type' :
    case 'purchase_5_type' :
    case 'purchase_6_type' :
    case 'purchase_7_type' :
    case 'purchase_8_type' :
    case 'purchase_9_type' :
    case 'purchase_10_type' :
      $data = strtolower($data);
      $result = (int)$data ? $data : (strpos($data, 'free') !== FALSE ? 0 : 2);
      break;
    default :
      $result = str_replace(' ', '<SP>', $data);
      $result = str_replace("\n", ' ', $result);
      break;
  } // switch label
  return $result;
} //sanitize
?>
for (var type in imports) {
  data = imports[type];
  for (var i = 0; i < data.length; i++) {
    datum = data[i];
    iimSet('site', site);
    iimSet('filepath', filepath);
    for (var key in datum) {
      value = datum[key];
      if (key.substring(0, 5) == 'file_') {
        if (value != '') {
          value = filepath + value;
        }
      }
      iimSet(key, value);
    }
    iimPlay(macropath + '/' + type + '.iim');
  }
}