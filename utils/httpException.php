<?php

@include_once "./jsonResponse.php";
	
function httpException($data, $statusCode = 400) {
  return jsonResponse(array("error" => $data), $statusCode);
}
