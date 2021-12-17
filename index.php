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
          # var_dump(httpException("error"));

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
      $body = file_get_contents("php://input");

      if ($reqRes === '/api/users') {
        $users[] = $body;
      } else {
        httpException("Route not found", 404)['end']();
      }

      var_dump($body);

      break;

    default:
      httpException("Method not supported", 404)['end']();
      
      break;
  }
