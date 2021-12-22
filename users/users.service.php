<?php

class UsersService {
  private $conn;

  function __construct($conn)
  {
    $this->conn = $conn;
  }

  function getUsers()
  {
    $sql = "SELECT * FROM Users";
    $result = $this->conn->query($sql);

    $users = [];

    while ($user = $result->fetch()) {
      $users[] = $user;
    }

    return $users;
  }

  function getUserById($id)
  {
    if ($id < 0 || $id >= count($this->users)) {
      return null;
    }

    return $this->users[$id];
  }

  function createUser($user)
  {
    var_dump($user);

    # $sql = "INSERT INTO Users (name) VALUES ('Max')";
    # $conn->exec($sql);

    $this->users[] = $user;

    return true;
  }
}
