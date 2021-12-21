<?php
@include_once "funcs.php";
	
if (!function_exists("httpException")) {
  echo json_encode(array(
    "message" => "Runtime error",
    "statusCode" => 500
  ));
	exit;
}

unset($dbConfig);
@include "db.config.php";
if (empty($dbConfig)) {
  httpException("Config load error", 500)['end']();
}

@include_once "app.controller.php";

# Setup app
$app = new AppController($dbConfig);

exit;

try {
  $sql = "DROP TABLE IF EXISTS Users";
  $conn->exec($sql);

  $sql = <<<SQL
    CREATE TABLE IF NOT EXISTS Users (
      id BIGINT PRIMARY KEY, 
      name VARCHAR(30),
      age INTEGER
    );
  SQL;
  $conn->exec($sql);

  # $sql = "INSERT INTO Users (name) VALUES ('Max')";
  # $conn->exec($sql);

  $sql = "SELECT * FROM Users";
  $result = $conn->query($sql);

  while ($row = $result->fetch()) {
    echo $row['id'] . ') '. $row['name'] . ' ' . $row['age'];
  }
} catch (Exception $ex) {
  httpException($ex->getMessage(), 500)['end']();
}


