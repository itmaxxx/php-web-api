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

  $users = [];
  $users[] = array("fullname" => "Max Dmitriev", "age" => 21);
  $users[] = array("fullname" => "Ilia Mihov", "age" => 21);

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
        $userId = intval(substr($reqRes, strlen('/api/users/')));

        if (!+$userId) {
          httpException("'userId' should be number")['end']();
        }

        if ($userId < 0 || $userId >= count($users)) {
          httpException("User not found", 404)['end']();
        }

        $response = array(
          "user" => $users[$userId]
        );

        echo json_encode($response);
        exit;
      } elseif ($reqRes === '/api/users') {
        $response = array(
          "users" => $users
        );

        echo json_encode($response);
        exit;
      } else {
        httpException("Route not found", 404)['end']();
      }

      break;

    case 'POST':
      $body = array();
      $data = array();

      if ($reqContentType == 'application/json') {
        $body = file_get_contents("php://input");
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
          httpException("Error parsing json", 400)['end']();
        }
      } else if ($reqContentType == 'application/x-www-form-urlencoded') {
        httpException("Form content type", 200)['end']();
      } else {
        httpException("Unsupported Content-Type", 400)['end']();
      }
      
      if ($reqRes === '/api/users') {
        $users[] = $data;

        echo json_encode(array(
          "users" => $users
        ));
        exit;
      } else {
        httpException("Route not found", 404)['end']();
      }

      var_dump($body);

      break;

    default:
      httpException("Method not supported", 404)['end']();
      
      break;
  }
