<?php
@include_once("./utils/httpException.php");
@include_once("users.service.php");

class UsersController {
  private $usersService;

  function __construct() {
    $this->usersService = new UsersService();
  }

  function getUsers() {
    $users = $this->usersService->getUsers();

    $response = array(
      "users" => $users
    );

    echo json_encode($response);
    exit;
  }

  function getUserById($req) {
    # Parse user id from url
    $userId = intval(substr($req['resource'], strlen('/api/users/')));

    if (!+$userId) {
      httpException("'userId' should be number")['end']();
    }

    $user = $this->usersService->getUserById($userId);

    if (is_null($user)) {
      httpException("User not found", 404)['end']();
    }

    $response = array(
      "user" => $user
    );

    echo json_encode($response);
    exit;
  }

  function createUser($userDto) {
    $result = $this->usersService->createUser($userDto);

    if (!$result) {
      httpException("Failed to create user", 400)['end']();
    }

    echo json_encode(array(
      "user" => $result,
      "users" => $users
    ));
    exit;
  }
}
