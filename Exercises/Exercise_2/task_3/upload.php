
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$keyHex = $_ENV['ENCRYPTION_KEY'] ?? '';

// Validate the encryption key format (should be a 64-character hexadecimal string)
if (!preg_match('/\A[0-9a-fA-F]{64}\z/', $keyHex)) {
    die('Encryption key is missing or invalid.');
}

$key = hex2bin($keyHex); // Convert the hex string to binary

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method.');
}

// Check if a file was uploaded
if (!isset($_FILES['document'])) {
    die('No file was uploaded.');
}

$file = $_FILES['document'];

// Check for upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    die('An error occurred during the upload.');
}

// Validate the file type using MIME type
$allowedMimeTypes = [
    'application/pdf',
    'image/jpeg',
    'image/png'
];

$finfo = new finfo(FILEINFO_MIME_TYPE); // Create a new finfo object to get the MIME type of the uploaded file
$mimeType = $finfo->file($file['tmp_name']); // Get the MIME type of the uploaded file

// Check if the MIME type is in the allowed list
if (!in_array($mimeType, $allowedMimeTypes, true)) {
    die('Invalid file type. Only PDF, JPEG and PNG files are allowed.');
}

// Prepare the payload for encryption
$originalName = basename(str_replace('\\', '/', $file['name']));

$payload = json_encode([
    'filename' => $originalName,
    'mime' => $mimeType,
    'content' => base64_encode(
        file_get_contents($file['tmp_name'])
    )
]);

if ($payload === false) {
    die('Unable to prepare the file for encryption.');
}

// Generate a random Initialization Vector (IV) for AES-256-CBC encryption
$ivLength = openssl_cipher_iv_length('aes-256-cbc');
$iv = random_bytes($ivLength);

// Encrypt the payload using AES-256-CBC
$ciphertext = openssl_encrypt(
    $payload,
    'aes-256-cbc',
    $key,
    OPENSSL_RAW_DATA,
    $iv
);

if ($ciphertext === false) {
    die('Encryption failed.');
}

// Create a separate HMAC for integrity verification
$hmacKey = hash_hmac('sha256', 'file-integrity', $key, true);

// Authenticate the IV and ciphertext using HMAC
$hmac = hash_hmac(
    'sha256',
    $iv . $ciphertext,
    $hmacKey,
    true
);

// Store the IV (16 bytes), HMAC (32 bytes), and ciphertext together in a single file
$encryptedData = $iv . $hmac . $ciphertext;
$storageDirectory = __DIR__ . '/files';

if (!is_dir($storageDirectory)) {
    if (!mkdir($storageDirectory, 0700, true) && !is_dir($storageDirectory)) {
        die('Unable to create storage directory.');
    }
}

// Generate a unique filename for the stored encrypted file
$storedFilename = bin2hex(random_bytes(16)) . '.enc';
$destination = $storageDirectory . '/' . $storedFilename;

if (file_put_contents($destination, $encryptedData, LOCK_EX) === false) {
    die('Unable to store the encrypted file.');
}

echo 'File encrypted and stored successfully!';

?>