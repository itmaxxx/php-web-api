<?php

class DbController {
  private $conn;
  
  public function __construct($conn) {
    $this->conn = $conn;

    $this->drop(['Users']);

    $this->initialize();

    $this->seed('Users', ["id", "name", "age"], [
      [1, "Max Dmitriev", 21]
    ]);
  }

  public function seed(string $table, array $fields, array $fixtures) {
    try {
      $fieldsString = implode(",", $fields);
      $valuesString = implode(",", array_fill(0, count($fields), "?"));

      foreach ($fixtures as $fixture) {
        $sql = "INSERT INTO $table ($fieldsString) VALUES ($valuesString)";
        $this->conn->prepare($sql)->execute($fixture);
      }
    } catch (Exception $ex) {
      httpException("Failed to seed db, table '$table'", 500)['end']();
    }
  }

  private function drop($tables) {
    try {
      foreach ($tables as $table) {
        $sql = "DROP TABLE IF EXISTS $table";
        $this->conn->exec($sql);
      }
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
