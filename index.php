<?php 
    ini_set('display_errors', 1);
    ini_set('display_errors_reporting',1);
    $hostname = "localhost";
    $username = "denish";
    $password = "Denish@123";
    $database = "demo";
    $conn = new mysqli($hostname, $username, $password, $database);
    if ($conn->connect_errno) {
        die("Connection error" . $conn->connect_errno);
    }
    $file = "Customers.csv";
    $f = fopen($file, "r");
    if($f === false) {
        echo "File not found";
        die();
    }
    $row = fgetcsv($f);
    $patterns = [
                    "/[0-9]*$/", 
                    "/^[A-Z][A-Za-z]*$/", 
                    "/[0-9]*$/", 
                    "/[0-9]*$/", 
                    "/[0-9]*/", 
                    "/^[A-Z][A-Za-z]*$/",
                    "/[0-9]*$/", 
                    "/[0-9]*$/"
                ];
    $dataArr = [];
    while($row = fgetcsv($f)) {
        print_r($row);
        $len = count($row);
        for($i = 0; $i < $len; $i++) {
            if(preg_match($patterns[$i], $row[$i])) {
                if($row[$i] == "Male") {
                    $dataArr[$i] = "M";
                } elseif($row[$i] == "Female") {
                    $dataArr[$i] = "F";
                } else {
                    $dataArr[$i] = $row[$i];
                }
            } else {
                die("Validation Failed");
            }
        }
        $sql = "insert into customer (cid, gender, age, income, spending, profession, work_exp, family_member) value ($dataArr[0], '$dataArr[1]', $dataArr[2], $dataArr[3], $dataArr[4], '$dataArr[5]', $dataArr[6], $dataArr[7])";
        if($conn->query($sql)) {
            echo "row inserted";
        } else {
            die("error");
        }

    }
    fclose($f);
?>