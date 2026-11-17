<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Check if the script is run from the command line and contains the database name argument
if ($argc < 2) {
    die("Wrong number of arguments\nUsage: php backup.php <database_name>\n");
}

$databaseName = $argv[1];

echo "Database: $databaseName\n";

// Create a new instance of Dotenv and load the environment variables from the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$host = $_ENV['DB_HOST'];
$port = $_ENV['DB_PORT'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];

// Create a new PDO instance for the database connection
$pdo = new PDO("mysql:host=$host;port=$port;dbname=$databaseName;charset=utf8mb4", $username, $password);

// Set the PDO error mode to exception for better error handling
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Database connection established successfully.\n";

// Get all tables from the database
$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Tables found:\n";


/**
 * Create a SQL value from a value
 * @param PDO $pdo The PDO instance
 * @param mixed $value The value to convert
 * @return string The SQL value
 */
function valueToSql(PDO $pdo, mixed $value): string{
    if ($value === null)
        return 'NULL';
    return $pdo->quote((string) $value);
}

// Directory to save the backup file and create it if it doesn't exist
$backupDirectory = __DIR__ . '/backups';
if(!is_dir($backupDirectory)) 
    mkdir($backupDirectory, 0755, true);
$backupFile = $backupDirectory . "/backup.txt";
$file = fopen($backupFile, 'w');
if($file === false) 
    die("Failed to open backup file for writing: $backupFile\n");


// Extraction of the database structure and data
foreach ($tables as $table) {
    echo "- $table\n";

    // Get the columns of the table
    $stmt = $pdo->query("SHOW COLUMNS FROM `$table`");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($columns as $column) {
        echo "  - " . $column['Field'] . "\n";
    }

    // Get the rows of the table
    $stmt = $pdo->query("SELECT * FROM `$table`");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "  Number of Rows: " . count($rows) . "\n";

    // Create the SQL insert statements for each row
    $columnNames = array_column($columns, 'Field'); // Get the column names from the columns array
    $columnSql = implode(', ', array_map(fn($column) => "`$column`", $columnNames)); // Create a comma-separated string of column names

    foreach($rows as $row) {
        $values = [];

        // Create the SQL values for each column in the row
        foreach($columnNames as $columnName) {
            $values[] = valueToSql($pdo, $row[$columnName]);
        }
        $valuesSql = implode(', ', $values);

        // Create the SQL insert statement
        $insert = "INSERT INTO `$table` ($columnSql) VALUES ($valuesSql);";
        // Write the SQL insert statement to the backup file
        fwrite($file, $insert . PHP_EOL); // PHP_EOL = "\n" for cross-platform compatibility


    }
}

fclose($file);

echo "Backup completed successfully. Backup file created at: $backupFile\n";

// Creation of the zip file
$zipFile = $backupDirectory . "/backup.zip";
$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) // Open the zip file for writing, create it if it doesn't exist, and overwrite it if it does
    die("Unable to create zip file: $zipFile\n");
$zip->addFile($backupFile, 'backup.txt');
$zip->close();

echo "Backup zip file created successfully at: $zipFile\n";

?>