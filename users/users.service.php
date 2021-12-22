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

    while ($user = $result->fetch(PDO::FETCH_ASSOC)) {
      $users[] = $user;
    }

    return $users;
  }

  function getUserById($id)
  {
    $sql = "SELECT * FROM Users WHERE id=:userid";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":userid", $id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return null;
    }
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
