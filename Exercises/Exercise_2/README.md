# Exercise 2

This project contains the three tasks of Exercise 2, implemented using PHP, MariaDB, Composer and OpenSSL.

## Requirements

- PHP with the required extensions enabled
- MariaDB
- Composer

Install the PHP dependencies from the project root with:

```bash
composer install
```

This project uses Composer to manage its PHP dependencies, including the library used to load environment variables from the `.env` file.

## Environment Configuration

Tasks 1 and 3 share a single `.env` file located at the root of the project.

Create a `.env` file in the project root with the following configuration:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=your_username
DB_PASSWORD=your_password

ENCRYPTION_KEY=your_64_character_hexadecimal_key
```

Replace the database credentials with your own MariaDB connection details.

### Generating the encryption key

Task 3 requires a 32-byte encryption key, represented as a 64-character hexadecimal string.

Generate a secure key by running the following command from the project root:

```bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

Copy the generated value into the `ENCRYPTION_KEY` variable in `.env`.

**Important**: Keep this key secret and do not change it after encrypting files. Existing files cannot be decrypted with a different key.

The `.env` file is included in `.gitignore` to prevent it from being committed to version control.

## Task 1 — Database backup

The first task creates a backup of a MariaDB database.

The database name must be provided as a command-line argument:

```bash
php task1/backup.php <database_name>
```

For example:

```bash
php task1/backup.php thesis
```

The script:

1. Connects to the specified database using the credentials stored in `.env`.
2. Retrieves all tables and their data.
3. Generates `INSERT INTO` SQL statements.
4. Saves the statements in a `.txt` file.
5. Creates a `.zip` archive containing the `.txt` file.

The generated files are stored in:

```text
task1/backups/
├── backup.txt
└── backup.zip
```

The database name is **not** stored in `.env`; it is provided when running the script.

## Task 2 — XML data display

The second task reads the `LV2.xml` file using PHP's SimpleXML extension and displays the information of the 100 people contained in the XML file.

The XML file must be located in:

```text
task2/
├── index.php
└── LV2.xml
```

### Requirements

Task 2 uses PHP's native **SimpleXML** extension. It does not require an additional Composer package.

If SimpleXML is not installed, install the XML extension corresponding to your PHP version. For PHP 8.4 on Debian:
```bash
sudo apt update
sudo apt install php8.4-xml
```

### Running the task

Start PHP's built-in development server from the root of the project:
```bash
php -S localhost:8000
```

Then open the Task 2 page in your web browser:
```
http://localhost:8000
```

The page displays, for each person:
- First name
- Last name
- Email address
- Photo
- Resume / Biography
  
The photos are displayed as images, and the resumes are displayed as text.

## Task 3

The third task implements a file upload system that encrypts documents and images before storing them on the server.

Supported file formats:

- PDF (`application/pdf`)
- JPEG (`image/jpeg`)
- PNG (`image/png`)

**How it works**

The script performs the following operations:

1. Accepts a file upload through the web interface.
2. Validates the uploaded file and checks its MIME type.
3. Retrieves the original filename, MIME type, and file content.
4. Packages the metadata and content into a JSON payload.
5. Encrypts the payload using AES-256-CBC with OpenSSL.
6. Generates an HMAC-SHA-256 to verify the integrity of the encrypted data.
7. Stores the encrypted file in the server's storage directory using a randomly generated filename.
8. Lists the stored encrypted documents and provides download links.
9.  Verifies the HMAC and decrypts the selected document when a download is requested.
10. Sends the original document to the browser with its original filename and MIME type.

Only the encrypted payload is stored in the `task_3/files/` directory. The original files are not stored on the server.

- `index.php` is the main entry point for Task 3. It displays the upload form and the list of stored encrypted documents. 
- `download.php` verifies, decrypts and serves the requested document. 
- `upload.php` validates, encrypts and stores the uploaded document.
  
### Requirements

- PHP with the OpenSSL extension enabled.
- PHP's Fileinfo extension enabled to validate the MIME type of uploaded files.
- A valid `ENCRYPTION_KEY` in the `.env` file.

### Running the task

Start PHP's built-in development server from the `task_3/` directory of the project:
```bash
php -S localhost:8000
```
Then follow the link in your web browser:
```
http://localhost:8000
```


