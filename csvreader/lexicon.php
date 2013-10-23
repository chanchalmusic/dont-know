<?php
header("Content-Type: text/html;charset=utf-8");

/**
 *  Please adjust configuration here
 */
$user = 'root';
$pass = 'xxxxxx';
$host = 'localhost';
$database_name = 'csvtest';
$csvFilePath = $_SERVER['DOCUMENT_ROOT']. "/assets/lexicon_entries7.csv";

if (!file_exists($csvFilePath)) {
    echo "Your specified CSV file do not exists!!";
    exit;
}

$csvFileContents = file_get_contents($csvFilePath);
$arrCsvContentsByComma = explode(',', $csvFileContents);
$arrCsvContentsByColon = explode(';', $csvFileContents);

if ($arrCsvContentsByComma[0] == 'name') {
    $csvSeperator = ',';
} elseif ($arrCsvContentsByColon[0] == 'name') {
    $csvSeperator = ';';
} else {
    echo "Separator of csv file can not be determined.";
    exit;
}

$dbh = new PDO("mysql:host=$host;dbname=$database_name", $user, $pass);
$dbh->exec("set names utf8");

// file path of csv file
$handle = fopen($csvFilePath, "r");

$stmt = $dbh->prepare("SELECT * FROM modx_lexicon_entries where name = :name and namespace = :namespace
    AND topic = :topic");
$stmt->bindParam(':name', $name);
$stmt->bindParam(':namespace', $namespace);
$stmt->bindParam(':topic', $topic);

$insertStmt = $dbh->prepare("INSERT INTO modx_lexicon_entries (name, value, topic, namespace, language)
    VALUES (:name, :value, :topic, :namespace, :language)");
$insertStmt->bindParam(':name', $name);
$insertStmt->bindParam(':value', $value);
$insertStmt->bindParam(':namespace', $namespace);
$insertStmt->bindParam(':topic', $topic);
$insertStmt->bindParam(':language', $language);


$lineNo = 1;
while (!feof($handle)) {
    $numRows = 0;
    $arrLine = fgetcsv($handle, null, $csvSeperator);

    if ($lineNo == 1) {
        echo "Do nothing with following array";
        echo "<br />";
        echo "<pre>";
        print_r($arrLine);
    } else {
        if (!empty($arrLine)) {
            $name = trim($arrLine[0]);
            $topic = trim($arrLine[2]);
            $namespace = trim($arrLine[3]);

            // check this row exists in database with combination of namespace, name and topic
            $stmt->execute();
            $numRows = $stmt->rowCount();
            //echo $numRows;

            if ($numRows) {
                echo "These is already a combination of following namespace =  $namespace and topic = $topic and name = $name";
                echo "<br />";
            } else {
                // insert to table
                if (!empty($arrLine)) {
                    echo "This one will inserted";
                    echo "<br />";
                }

                $name = trim($arrLine[0]);
                $value = trim($arrLine[1]);
                $topic = trim($arrLine[2]);
                $namespace = trim($arrLine[3]);
                $language = trim($arrLine[4]);

                if (isset($_REQUEST['insert']) && $_REQUEST['insert'] == 'true') {
                    $insertStmt->execute();
                }
                $numRows = $insertStmt->rowCount();

                if ($numRows) {
                    echo "$numRows inserted";
                    echo "<br>";
                }
            }

            echo "<pre>";
            print_r($arrLine);
        }
    }

    $lineNo++;

}

fclose($handle);

