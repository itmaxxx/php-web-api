<?php

function logMessage(string $message) {
  $file = fopen("./log.txt", "a");

  fwrite($file, date("r") . " " . $message . "\r\n");

  fclose($file);
}
