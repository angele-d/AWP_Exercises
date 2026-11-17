<?php

interface iRadio{
    public static function create(string $work_name, string $work_text, string $work_link, int $identification_number) : static;
    public static function read(PDO $pdo) : array;
    public function save(PDO $pdo) : array;
}

class GraduateThesis implements iRadio {
    public string $work_name;
    public string $work_text;
    public string $work_link;
    public int $identification_number;

    // Constructor
    public function __construct(string $work_name, string $work_text, string $work_link, int $identification_number){
        $this->work_name = $work_name;
        $this->work_text = $work_text;
        $this->work_link = $work_link;
        $this->identification_number = $identification_number;
    }

    /**
     * Creates a new instance of GraduateThesis with the provided parameters.
     * @param string $work_name The name of the graduate thesis.
     * @param string $work_text The text/content of the graduate thesis.
     * @param string $work_link The link to the graduate thesis.
     * @param integer $identification_number The identification number of the graduate thesis.
     * @return static A new instance of GraduateThesis.
     */
    public static function create(string $work_name, string $work_text, string $work_link, int $identification_number) : static {
        return new static($work_name, $work_text, $work_link, $identification_number);
    }

    /**
     * Reads a graduate thesis from the database.
     * @param PDO $pdo The PDO instance for the database connection.
     * @return array The read graduate thesis data.
     */
    public static function read(PDO $pdo) : array {
        
        $sql = "SELECT * FROM graduate_theses";
        $statement = $pdo->query($sql);

        // Fetch all results as an associative array and return them
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Saves a graduate thesis to the database.
     * @param PDO $pdo The PDO instance for the database connection.
     * @return array The saved graduate thesis data.
     */
    public function save(PDO $pdo) : array {
        
        // Prepare the SQL statement to insert a new graduate thesis into the database
        $sql = "INSERT INTO graduate_theses
        (work_name, work_text, work_link, identification_number)
        VALUES (:work_name, :work_text, :work_link, :identification_number)
        ON DUPLICATE KEY UPDATE
            work_name = VALUES(work_name),
            work_text = VALUES(work_text),
            work_link = VALUES(work_link)";
        // ON DUPLICATE = If a record with the same identification_number already exists, update the existing record instead of inserting a new one.

        $statement= $pdo->prepare($sql);

        $statement->execute([
            ':work_name' => $this->work_name,
            ':work_text' => $this->work_text,
            ':work_link' => $this->work_link,
            ':identification_number' => $this->identification_number
        ]);

        return [
            'work_name' => $this->work_name,
            'work_text' => $this->work_text,
            'work_link' => $this->work_link,
            'identification_number' => $this->identification_number
        ];
    }
}
?>