<?php

$TEST_PERCENTAGE = 10;

if ($argc <= 1) {
  echo "Please pass in the images directory.\n";
  return;
}
$images_dir = $argv[1];

$batch = array();
$d = opendir($images_dir);
while ($f = readdir($d)) {
  if (substr($f, -4) != '.txt') continue;
  $batch[] = $f;
}
closedir($d);

// Randomize
shuffle($batch);
$total = count($batch);
echo "Creating test.txt....\n";
$pfile = fopen("test.txt", "w");
for($a=0;$a<$total*$TEST_PERCENTAGE/100.0;$a++) {
  $fn = array_pop($batch);
  $fn = substr($fn, 0, strlen($fn) - 4);
  fwrite($pfile, "$images_dir/$fn\n");
}
fclose($pfile);
echo "Creating train.txt....\n";
$pfile = fopen("train.txt", "w");
while ($fn = array_pop($batch)) {
  $fn = substr($fn, 0, strlen($fn) - 4);
  fwrite($pfile, "$images_dir/$fn\n");
}
fclose($pfile);
