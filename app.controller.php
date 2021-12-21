<?php
@include_once "funcs.php";
@include_once "users.controller.php";

class AppController {
  private $con;
  private $dbConfig;
  private $usersController;
  private $reqContentType, $reqMethod, $reqUrl, $reqRes;

  function __construct($dbConfig) {
    $this->dbConfig = $dbConfig;

    $this->setHeaders();
    $this->parseRequest();
    $this->connectToDb();
    $this->router();
  }

  private function setHeaders() {
    header('Content-Type: application/json');
  }

  private function connectToDb() {
    try {
      $this->conn = new PDO(
        "{$this->dbConfig['type']}:host={$this->dbConfig['host']};port={$this->dbConfig['port']};dbname={$this->dbConfig['name']}",
        $this->dbConfig['user'],
        $this->dbConfig['pass']
      );
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $ex) {
      httpException($ex->getMessage(), 500)['end']();
    }
  }

  private function parseRequest() {
    $this->reqMethod = $_SERVER['REQUEST_METHOD'];
    $this->usersController = new UsersController();
    # Request url with query params
    $this->reqUrl = $_SERVER['REQUEST_URI'];
    
    # Request url without query params
    # When we have query params in URL they won't be shown here
    $this->reqRes = '/';
    if (isset($_SERVER['REDIRECT_URL'])) {
      $this->reqRes = $_SERVER['REDIRECT_URL'];
    }

    if (isset($_SERVER['CONTENT_TYPE'])) {
      $this->reqContentType = strtolower(trim($_SERVER['CONTENT_TYPE']));
    }
  }

  private function parseRequestBody() {
    # Parsed body
    $body = array();
    # Parsed json from body
    $data = array();

    if ($this->reqContentType == 'application/json') {
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

    return array("body" => $body, "data" => $data);
  }

  private function router() {
    switch($this->reqMethod) {
      case 'GET':
        if (strpos($this->reqRes, '/api/users/') === 0) {
          $this->usersController->getUserById($this->reqRes);
        } elseif ($this->reqRes === '/api/users') {
          $this->usersController->getUsers();
        } else {
          httpException("Route not found", 404)['end']();
        }

        break;

      case 'POST':
        [$body, $data] = parseRequestBody();

        if ($this->reqRes === '/api/users') {
          $this->usersController->createUser($data);
        } else {
          httpException("Route not found", 404)['end']();
        }

        break;

      default:
        httpException("Method not supported", 404)['end']();
      
        break;
    }
  }
}
