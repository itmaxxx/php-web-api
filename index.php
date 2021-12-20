<?php
  header('Content-Type: application/json');

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
    httpException("DB error", 500)['end']();
  }

  try {
    $DB = new PDO(
      "{$dbConfig['type']}:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']}",
      $dbConfig['user'],
      $dbConfig['pass']
    );
    $DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (Exception $ex) {
    httpException($ex->getMessage(), 500)['end']();
  }

  @include_once "users.controller.php";

  $usersController = new UsersController();

  $reqContentType;
  
  if (isset($_SERVER['CONTENT_TYPE'])) {
    $reqContentType = strtolower(trim($_SERVER['CONTENT_TYPE']));
  }

  $reqMethod = $_SERVER['REQUEST_METHOD'];
  # Request with query params
  $reqUrl = $_SERVER['REQUEST_URI'];
  # When we have query params in URL they won't be shown here
  # Required resource
  $reqRes = '/';

  if (isset($_SERVER['REDIRECT_URL'])) {
    $reqRes = $_SERVER['REDIRECT_URL'];
  }

  switch($reqMethod) {
    case 'GET':
      if (strpos($reqRes, '/api/users/') === 0) {
        $usersController->getUserById($reqRes);
      } elseif ($reqRes === '/api/users') {
        $usersController->getUsers();
      } else {
        httpException("Route not found", 404)['end']();
      }

      break;

    case 'POST':
      $body = array();
      $data = array();

      // Parse request body
      if ($reqContentType == 'application/json') {
        $body = file_get_contents("php://input");
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
          httpException("Error parsing json", 400)['end']();
        }
      } else if ($reqContentType == 'application/x-www-form-urlencoded') {
        httpException("Form content type not supported yet", 400)['end']();
      } else {
        httpException("Unsupported Content-Type", 400)['end']();
      }
      
      if ($reqRes === '/api/users') {
        $usersController->createUser($data);
      } else {
        httpException("Route not found", 404)['end']();
      }

      var_dump($body);

      break;

    default:
      httpException("Method not supported", 404)['end']();
      
      break;
  }
