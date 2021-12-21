<?php
	
function httpException($message, $code = 400) {
  header('Content-Type: application/json');
  http_response_code($code);

  echo json_encode(array(
    "message" => $message,
    "statusCode" => $code
  ));

  return array(
    "end" => function() { exit; }
  );
}
