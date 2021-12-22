<?php

@include_once("./utils/httpException.php");
@include_once "./utils/jsonResponse.php";
@include_once("users.service.php");

class UsersController {
  private $usersService;

  function __construct($conn) {
    $this->usersService = new UsersService($conn);
  }

  function getUsers() {
    $users = $this->usersService->getUsers();

    $response = array(
      "users" => $users
    );

    jsonResponse($response)['end']();
  }

  function getUserById($req) {
    # Parse user id from url
    $userId = intval(substr($req['resource'], strlen('/api/users/')));

    # if (!+$userId) {
    #   httpException("'userId' should be number")['end']();
    # }

    $user = $this->usersService->getUserById($userId);

    if (is_null($user)) {
      httpException("User not found", 404)['end']();
    }

    $response = array(
      "user" => $user
    );

    jsonResponse($response)['end']();
  }

  function createUser($userDto) {
    $result = $this->usersService->createUser($userDto);

    if (!$result) {
      httpException("Failed to create user", 400)['end']();
    }

    $response = array(
      "message" => "User created",
      "user" => $result
    );

    jsonResponse($response)['end']();
  }
}
