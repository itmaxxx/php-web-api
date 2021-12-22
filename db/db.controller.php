<?php

class DbController {
  private $conn;
  
  public function __constructor($conn) {
    $this->conn = $conn;

    $this->initialize();
  }

  private function drop() {
    try {
      # Drop Users
      $sql = "DROP TABLE IF EXISTS Users";
      $conn->exec($sql);
    } catch (Exception $ex) {
      httpException("Failed to drop db", 500);
    }
  }

  private function initialize() {
    try {
      # Create Users table
      $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS Users (
          id BIGINT PRIMARY KEY, 
          name VARCHAR(30),
          age INTEGER
        );
      SQL;
      $this->conn->exec($sql);
    } catch (Exception $ex) {
      httpException("Failed to initialize db", 500);
    }
  }
}
