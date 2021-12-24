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
  # Parsed request
  private $req;
  # Controllers
  private $dbController;
  private $usersController;

  function __construct($dbConfig) {
    # Setup headers and db
    $this->setHeaders();

    # Setup db
    $this->dbController = new DbController($dbConfig);
    $this->conn = $this->dbController->getConnection();

    # Initilize controllers
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

  private function router() {
    switch($this->req['method']) {
      case 'GET':
        header('Content-Type: text/html');

        if (strpos($this->req['resource'], '/api/users/') === 0) {
          $this->usersController->getUserById($this->req);
        } elseif ($this->req['resource'] === '/api/users') {
          $this->usersController->getUsers();
        } elseif ($this->req['resource'] === '/test') {
          include_once './tests/users-e2e.php';
        } else {
          httpException("Route not found", 404)['end']();
          logMessage("Route not found $req");
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
