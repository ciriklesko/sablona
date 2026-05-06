<?php

namespace otazkyodpovede;

error_reporting(E_ALL);
ini_set("display_errors", "On");

require_once(dirname(dirname(__FILE__)) . '/classes/databases.php');

class QnA extends \Database {

    private $conn;

    public function __construct() {
        parent::__construct();
        $this->conn = $this->getConnection();
    }

    public function insertQnA() {
        try {
            $data = json_decode(file_get_contents(dirname(dirname(__FILE__)) . '/data/datas.json'), true);

            $otazky = $data["otazky"];
            $odpovede = $data["odpovede"];

            $this->conn->exec("TRUNCATE TABLE qna");

            $this->conn->beginTransaction();

            $sql = "INSERT INTO qna (otazka, odpoved) VALUES (:otazka, :odpoved)";
            $statement = $this->conn->prepare($sql);

            for ($i = 0; $i < count($otazky); $i++) {
                $statement->bindParam(':otazka', $otazky[$i]);
                $statement->bindParam(':odpoved', $odpovede[$i]);
                $statement->execute();
            }

            $this->conn->commit();
            echo "Dáta boli vložené";

        } catch (\Exception $e) {
            echo "Chyba pri vkladaní dát do databázy: " . $e->getMessage();
            $this->conn->rollback();

        }
    }

    public function getQnA(){
        try {
          $statement = $this->conn->query("SELECT otazka, odpoved FROM qna");
          return $statement ? $statement->fetchAll(\PDO::FETCH_ASSOC) : [];
        } catch (\Exception $e) {
          echo "Chyba: " . $e->getMessage();
          return [];
        }
    }
}