<?php

@include_once("./fixtures/users.php");

class DbController {
  private $conn;
  
  public function __construct($dbConfig) {
    $this->connectToDb($dbConfig);

    $this->drop(['Users']);

    $this->initialize();

    # Include $usersFixtures from global scope here
    global $usersFixtures;
    $this->seed('Users', ["id", "name", "age"], $usersFixtures);
  }

  public function getConnection() {
    return $this->conn;
  }

  private function connectToDb(array $dbConfig) {
    try {
      $this->conn = new PDO(
        "{$dbConfig['type']}:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']}",
        $dbConfig['user'],
        $dbConfig['pass']
      );
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $ex) {
      httpException($ex->getMessage(), 500)['end']();
    }
  }

  /**
   *  Seeds data from array in specified table and columns
   *  @param {string}   $table    Table name where we want to seed data
   *  @param {string[]} $columns  Array of fields names we want to insert
   *  @param {object[]} $fixtures Array of fixtures with fields from $fields in same order
   */
  public function seed(string $table, array $columns, array $fixtures) {
    try {
      $columnsString = implode(",", $columns);
      $valuesString = implode(",", array_fill(0, count($columns), "?"));

      foreach ($fixtures as $fixture) {
        $sql = "INSERT INTO $table ($columnsString) VALUES ($valuesString)";
        $this->conn->prepare($sql)->execute($fixture);
      }
    } catch (Exception $ex) {
      httpException("Failed to seed db, table '$table'", 500)['end']();
    }
  }

  /**
   *  Drop specified table in DB
   *  @param {string[]} $tables Array of tables names
   */
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

  /**
   *  Create all tables with hardcoded sql script
   */
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
