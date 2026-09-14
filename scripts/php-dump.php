<?php
/**
 * Pure-PHP database dump — a fallback for servers that don't have the
 * mysqldump/mariadb-dump CLI installed. Uses PDO (bundled with PHP), so
 * no extra packages are needed to run this.
 *
 * Usage:
 *   php scripts/php-dump.php <host> <port> <user> <dbname> [output.sql]
 *
 * Password is read from the MYSQL_PWD environment variable so it never
 * shows up in shell history or `ps aux`:
 *   MYSQL_PWD='...' php scripts/php-dump.php 10.9.39.133 6033 crm_prod crm_prod > dump.sql
 *
 * Pipe through gzip yourself if you want a .gz:
 *   MYSQL_PWD='...' php scripts/php-dump.php 10.9.39.133 6033 crm_prod crm_prod | gzip > dump.sql.gz
 */

if ($argc < 5) {
    fwrite(STDERR, "Usage: php php-dump.php <host> <port> <user> <dbname> [output.sql]\n");
    exit(1);
}

[, $host, $port, $user, $dbname] = $argv;
$outputFile = $argv[5] ?? null;
$password = getenv('MYSQL_PWD') ?: '';

if ($password === '') {
    fwrite(STDERR, "Enter password: ");
    system('stty -echo');
    $password = trim(fgets(STDIN));
    system('stty echo');
    fwrite(STDERR, "\n");
}

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    fwrite(STDERR, "Connection failed: " . $e->getMessage() . "\n");
    exit(1);
}

$out = $outputFile ? fopen($outputFile, 'w') : STDOUT;

fwrite($out, "-- Pure-PHP dump of `$dbname` generated " . date('c') . "\n");
fwrite($out, "SET NAMES utf8mb4;\n");
fwrite($out, "SET FOREIGN_KEY_CHECKS=0;\n\n");

$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    fwrite(STDERR, "Dumping table: $table\n");

    // structure
    $createRow = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
    $createSql = $createRow['Create Table'] ?? reset($createRow);

    fwrite($out, "--\n-- Table structure for `$table`\n--\n\n");
    fwrite($out, "DROP TABLE IF EXISTS `$table`;\n");
    fwrite($out, $createSql . ";\n\n");

    // data, streamed in chunks so large tables don't blow up memory
    fwrite($out, "--\n-- Data for `$table`\n--\n\n");

    $countStmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
    $total = (int) $countStmt->fetchColumn();

    if ($total > 0) {
        $chunkSize = 500;
        for ($offset = 0; $offset < $total; $offset += $chunkSize) {
            $stmt = $pdo->query("SELECT * FROM `$table` LIMIT $chunkSize OFFSET $offset");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$rows) {
                break;
            }

            $columns = array_keys($rows[0]);
            $columnList = '`' . implode('`, `', $columns) . '`';

            $valueGroups = [];
            foreach ($rows as $row) {
                $values = array_map(function ($v) use ($pdo) {
                    if ($v === null) {
                        return 'NULL';
                    }
                    return $pdo->quote($v);
                }, $row);
                $valueGroups[] = '(' . implode(', ', $values) . ')';
            }

            fwrite($out, "INSERT INTO `$table` ($columnList) VALUES\n" . implode(",\n", $valueGroups) . ";\n");
        }
    }

    fwrite($out, "\n");
}

fwrite($out, "SET FOREIGN_KEY_CHECKS=1;\n");

if ($outputFile) {
    fclose($out);
    fwrite(STDERR, "Done: $outputFile\n");
} else {
    fwrite(STDERR, "Done.\n");
}
