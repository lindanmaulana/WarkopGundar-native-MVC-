<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
  private static $instance = null;
  private $pdo;

  private function __construct()
  {
    $host = 'localhost';
    $dbname = 'db_warkop_gundar';
    $username = 'root';
    $password = '';

    try {
      $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $err) {
      die("Koneksi database gagal: " . $err->getMessage());
    }
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new Database();
    }

    return self::$instance->pdo;
  }
}
