<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Document Storage</title>
</head>
<body>
    <h1>Secure Document Storage</h1>
    <h2>Upload a document</h2>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <label for="document">Choose a document (PDF, JPEG or PNG):</label>
        <input
            type="file"
            id="document"
            name="document"
            accept=".pdf,.jpg,.jpeg,.png"
            required
        >
        <button type="submit">Upload</button>
    </form>

    <h2>Stored documents</h2>

    <?php
    $storageDirectory = __DIR__ . '/files';
    // Take all the files with .enc extension from the storage directory
    $encryptedFiles = glob($storageDirectory . '/*.enc');

    if ($encryptedFiles === false || count($encryptedFiles) === 0) {
        echo '<p>No documents have been uploaded yet.</p>';
    } else {
        echo '<ul>';

        foreach ($encryptedFiles as $encryptedFile) {
            $storedFilename = basename($encryptedFile); // filename

            if (!preg_match('/\A[a-f0-9]{32}\.enc\z/', $storedFilename)) { // validate the filename format
                continue;
            }

            echo '<li>';
            echo '<a href="download.php?file=' .
                rawurlencode($storedFilename) . '">';
            echo 'Download document ' .
                htmlspecialchars($storedFilename, ENT_QUOTES, 'UTF-8');
            echo '</a>';
            echo '</li>';
        }

        echo '</ul>';
    }
    ?>
</body>
</html>

