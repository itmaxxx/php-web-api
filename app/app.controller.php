<?php

@include_once "./utils/httpException.php";
@include_once "./utils/request.php";
@include_once "./users/users.controller.php";
@include_once "./db/db.controller.php";

class AppController {
  # Connection
  private $conn;
  # Request object
  private $_req;
  # Parsed request array
  private $req;
  private $dbConfig;
  private $dbController;
  private $usersController;

  function __construct($dbConfig) {
    # Setup headers and db
    $this->setHeaders();
    $this->dbConfig = $dbConfig;
    $this->connectToDb();


    # Initilize controllers
    $this->dbController = new DbController($this->conn);
    $this->usersController = new UsersController($this->conn);

    # Parse request
    $this->_req = new Request($_SERVER);
    $this->req = $this->_req->getRequest();

    # Routing
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

  private function router() {
    switch($this->req['method']) {
      case 'GET':
        if (strpos($this->req['resource'], '/api/users/') === 0) {
          $this->usersController->getUserById($this->req);
        } elseif ($this->req['resource'] === '/api/users') {
          $this->usersController->getUsers();
        } else {
          httpException("Route not found", 404)['end']();
        }

        break;

      case 'POST':
        [$body, $data] = $this->_req->parseBody();

        if ($this->req['resource'] === '/api/users') {
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
