<?php

@include_once "./utils/httpException.php";
@include_once "./app/app.controller.php";

# Disable errors, should be false in production
# error_reporting(false);

# Check if function loaded successfully, or throw error
if (!function_exists("httpException")) {
  echo json_encode(array(
    "message" => "Runtime error",
    "statusCode" => 500
  ));
	exit;
}

# Safely load config
unset($dbConfig);
@include "./db/db.config.php";
if (empty($dbConfig)) {
  httpException("Config load error", 500)['end']();
}

# Setup app
$app = new AppController($dbConfig);

