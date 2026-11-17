<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$keyHex = $_ENV['ENCRYPTION_KEY'] ?? '';

// Check if the encryption key is valid (should be a 64-character hexadecimal string)
if (!preg_match('/\A[0-9a-fA-F]{64}\z/', $keyHex)) {
    http_response_code(500);
    exit('Encryption key is missing or invalid.');
}

$key = hex2bin($keyHex);

// Validate the filename parameter from the GET request
$storedFilename = $_GET['file'] ?? '';

// Check if the filename matches the expected pattern (32-character hexadecimal string followed by .enc)
if (!preg_match('/\A[a-f0-9]{32}\.enc\z/', $storedFilename)) {
    http_response_code(400);
    exit('Invalid filename.');
}

// Construct the full path to the encrypted file
$filePath = __DIR__ . '/files/' . $storedFilename;

if (!is_file($filePath)) {
    http_response_code(404);
    exit('File not found.');
}

// Read the encrypted file's content
$encryptedData = file_get_contents($filePath);

if ($encryptedData === false) {
    http_response_code(500);
    exit('Unable to read the encrypted file.');
}

// Validate the length of the encrypted data to ensure it contains both the IV and HMAC
$ivLength = openssl_cipher_iv_length('aes-256-cbc');
$hmacLength = 32;

// Check if the encrypted data is long enough to contain the IV and HMAC
if (strlen($encryptedData) <= $ivLength + $hmacLength) {
    http_response_code(400);
    exit('Encrypted file is invalid or corrupted.');
}

// Extract the IV, HMAC, and ciphertext from the encrypted data
$iv = substr($encryptedData, 0, $ivLength);
$hmac = substr($encryptedData, $ivLength, $hmacLength);
$ciphertext = substr($encryptedData, $ivLength + $hmacLength);

// Generate the HMAC key using the main encryption key
$hmacKey = hash_hmac('sha256', 'file-integrity', $key, true);
// Compute the expected HMAC for the extracted IV and ciphertext
$expectedHmac = hash_hmac(
    'sha256',
    $iv . $ciphertext,
    $hmacKey,
    true
);

// Compare the expected HMAC with the extracted HMAC to verify file integrity
if (!hash_equals($expectedHmac, $hmac)) {
    http_response_code(400);
    exit('File integrity verification failed.');
}

// Decrypt the ciphertext using AES-256-CBC
$plaintext = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

if($plaintext === false) {
    http_response_code(500);
    exit('Decryption failed.');
}

// Decode the decrypted JSON payload to retrieve the original document information
$document = json_decode($plaintext, true);

if (
    !is_array($document) ||
    !isset($document['filename'], $document['mime'], $document['content']) ||
    !is_string($document['filename']) ||
    !is_string($document['mime']) ||
    !is_string($document['content'])
) {
    http_response_code(400);
    exit('Invalid decrypted document.');
}

// Validate the MIME type of the document against the allowed types
$allowedMimeTypes = [
    'application/pdf',
    'image/jpeg',
    'image/png'
];

if (!in_array($document['mime'], $allowedMimeTypes, true)) {
    http_response_code(400);
    exit('Invalid document type.');
}

// Decode the base64-encoded content of the document
$fileContent = base64_decode($document['content'], true);

if ($fileContent === false) {
    http_response_code(400);
    exit('Invalid document content.');
}

// Set the original name of the document for download, ensuring it is safe for use in headers
$originalName = basename(
    str_replace('\\', '/', $document['filename'])
);

ob_clean(); // Clear the output buffer to prevent any unwanted output before sending headers
// Set the appropriate headers to prompt the user to download the file with the correct MIME type and filename
header('Content-Type: ' . $document['mime']);
header('Content-Disposition: attachment; filename="' .
    str_replace(['"', "\r", "\n"], '', $originalName) . '"');
header('Content-Length: ' . strlen($fileContent));
header('X-Content-Type-Options: nosniff');

echo $fileContent;
exit;
