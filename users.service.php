<?php
class UsersService {
  private $users = [];

  function __construct()
  {
    $this->users[] = array("fullname" => "Max Dmitriev", "age" => 21);
    $this->users[] = array("fullname" => "Ilia Mihov", "age" => 21);
    $this->users[] = array("fullname" => "Matvey Gorelik", "age" => 20);
  }

  function getUsers()
  {
    return $this->users;
  }

  function getUserById($id)
  {
    if ($id < 0 || $id >= count($this->users)) {
      return null;
    }

    return $this->users[$id];
  }

  function addUser($data)
  {
    $this->users[] = $data;

    return true;
  }
}
