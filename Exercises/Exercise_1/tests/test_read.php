<?php

require_once '../database.php';
require_once '../src/GraduateThesis.php';

$thesis = GraduateThesis::create(
    "Test read",
    "Ceci est un test de la méthode read().",
    "https://example.com/read",
    888888
);

$thesis->save($pdo);

$result = GraduateThesis::read($pdo);

print_r($result);

// Test cleanup: delete the test data from the database
$statement = $pdo->prepare(
    "DELETE FROM graduate_theses WHERE identification_number = :identification_number"
);

$statement->execute([
    ':identification_number' => 888888
]);

echo "Test datas deleted." . PHP_EOL;

?>