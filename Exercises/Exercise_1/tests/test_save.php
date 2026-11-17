<?php

require_once '../database.php';
require_once '../src/GraduateThesis.php';

// Create a new instance of GraduateThesis using the create method
$thesis = GraduateThesis::create(
    "Test mémoire",
    "Ceci est un test.",
    "https://example.com",
    999999
);

// Save the thesis to the database and get the result
$result = $thesis->save($pdo);

// Do it an other time to test the ON DUPLICATE KEY UPDATE functionality
$thesis = GraduateThesis::create(
    "Test mémoire modifié",
    "Ceci est un test modifié.",
    "https://example.com/modified",
    999999
);
$result = $thesis->save($pdo);

// Print the result of the save operation
print_r($result);

// Test cleanup: delete the test data from the database
$statement = $pdo->prepare(
    "DELETE FROM graduate_theses WHERE identification_number = :identification_number"
);

$statement->execute([
    ':identification_number' => 999999
]);

echo "Test datas deleted." . PHP_EOL;

?>