<?php

function getSearchConfig() {
  static $config;

  if (!$config) {
    var_dump('callded');
    $config = include('./image-gather.config.php');
  }

  return $config;
}

function searchFace($name) {
  extract($config);
  
  $name = urlencode($name);
  $url = "https://www.googleapis.com/customsearch/v1?key={$apiKey}&searchType=image&fileType=jpg&cx={$searchID}&q={$name}";

  $res = json_decode(file_get_contents($url), true);

  return $res['items'] ?: [];
}

function gatherFace($name, $affix, $type) {
  $n = 0;

  foreach (searchFace($name) as $item) {
    $data = file_get_contents($item['link']);

    if (!$data) {
      continue;
    }

    $n++;
    $filename = "./data/img_origin/{$type}_{$affix}_{$n}.jpg";

    file_put_contents($filename, $data);
  }
  sleep(1);
}

function gatherFaces() {
  $list = explode("\n", file_get_contents('./person.txt'));
  
  foreach ($list as $row) {
    if ($row === '') {
      continue;
    }
    list($type, $affix, $name) = explode(' ', $row);

    gatherFace($name, $affix, $type);
  }
}

function run() {
  gatherFaces();
}

run();

