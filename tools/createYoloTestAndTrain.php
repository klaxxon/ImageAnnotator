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
  // Verify image exists
  $img = findImageFile($images_dir, substr($f, 0, strlen($f) - 4));
  if ($img != null) {
    $o = new stdClass();
    $o->image = $img;
    $o->fn = $f;
    $batch[] = $o;
  }
  else echo "Cannot find image associated with '$f'.\n";
}
closedir($d);

// Randomize
shuffle($batch);
$total = count($batch);
$cwd = getcwd();

$cnt = 0;
echo "Creating test.txt....";
$pfile = fopen("test.txt", "w");
for($a=0;$a<$total*$TEST_PERCENTAGE/100.0;$a++) {
  $fn = array_pop($batch);
  fwrite($pfile, "$cwd/$images_dir/".$fn->image."\n");
  $cnt++;
}
fclose($pfile);
echo "$cnt images\n";

$cnt = 0;
echo "Creating train.txt....";
$pfile = fopen("train.txt", "w");
while ($fn = array_pop($batch)) {
  fwrite($pfile, "$cwd/$images_dir/".$fn->image."\n");
  $cnt++;
}
fclose($pfile);
echo "$cnt images\n";


function findImageFile($dir, $pattern) {
  $imgexts = array('.jpg','.jpeg','.png','.gif');
  $len = strlen($pattern);
  $d = opendir($dir);
  while ($f = readdir($d)) {
    if (substr($f, 0, $len) == $pattern){
      $ext = strtolower(substr($f, $len));
      if (in_array($ext, $imgexts)) {
        closedir($d);
        return $f;
      }
    }
  }
  closedir($d);
  return null;
}
