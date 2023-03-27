<?php 
    ini_set('display_errors', 1);
    ini_set('display_errors_reporting',1);
    $hostname = "localhost";
    $username = "denish";
    $password = "Denish@123";
    $database = "demo2";
    $conn = new mysqli($hostname, $username, $password, $database);
    if ($conn->connect_errno) {
        die("Connection error" . $conn->connect_errno);
    }
    $file = "demo.csv";
    $f = fopen($file, "r");
    if($f === false) {
        echo "File not found";
        die();
    }
    $lineNumber = 0;
    $dataArr = [];
    $storedEmail = [];
    while($row = fgetcsv($f)) {
        $lineNumber ++;
        if(!preg_match("/[A-Za-z]{4,}/",$row[0])) {
            die("Invalid name at line " . $lineNumber  );
        }
        if(!preg_match("/[0-9]{10}/",$row[1])) {
            die("Invalid phone number at $lineNumber");
        }
        if(!in_array($row[2], $storedEmail)) {
            if(preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/",$row[2])) {
                $email_check = "select * from customer where email ='" . $row[2] . "'";
                $result = $conn->query($email_check);
                if($result->num_rows > 0) {
                    die("$row[2] email address exist in database");
                } else {
                    array_push($storedEmail, $row[2]);
                }
            } else {
                die("Invalid email at $lineNumber");
            }
        } else {
            die("Duplicate email address exist in CSV at $lineNumber");
        }
        if(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/",$row[3])) {
            die("Invalid Password at $lineNumber");
        }
        if(!preg_match("/Male|male|Female|female/",$row[4])) {
            die("Invalid Gender at $lineNumber");
        }
        if(!isset($row[5])) {
            die("Null encountered paymentdetails at $lineNumber");
        }
        if(!isset($row[6])) {
            die("Null encountered paymentmethod at $lineNumber");
        }
        if(!isset($row[7])) {
            die("Null encountered country at $lineNumber");
        } 
        if(!isset($row[8])) {
            die("Null encountered state at $lineNumber");
        }
        if(!isset($row[9])) {
            die("Null encountered status at $lineNumber");
        }
    }
    rewind($f);
    while ($row = fgetcsv($f)) {
        $dataArr[0] = $row[0];
        $dataArr[1] = $row[1];
        $dataArr[2] = $row[2];
        $dataArr[3] = md5($row[3]);
        $dataArr[4] = strtolower($row[4]);
        $dataArr[5] = $row[5];
        $paymentMethod = explode(",", $row[6]);
        $method = [];
        foreach($paymentMethod as $mode) {
            if(stripos($mode, "card") > 1) {
                array_push($method, strtolower(substr(trim($mode), 0, -5)));
            } else {
                array_push($method, strtolower(trim($mode)));
            }
        }
        $dataArr[6] = implode(",", $method);
        $dataArr[7] = strtolower($row[7]);
        $dataArr[8] = strtolower($row[8]);
        if($row[9] == "active" || $row[9] == "Active") {
            $dataArr[9] = 1; 
        } else {
            $dataArr[9] = 0; 
        }
        $sql = "insert into customer (name, phone, email, password, gender, paymentinfo, paymentmethod, country, state, status) value ('$dataArr[0]', $dataArr[1], '$dataArr[2]', '$dataArr[3]', '$dataArr[4]', '$dataArr[5]', '$dataArr[6]', '$dataArr[7]', '$dataArr[8]', $dataArr[9])";
        if($conn->query($sql)) {
            echo "row inserted";
            echo "<br>";
        } else {
            die("error");
        }
    }
    fclose($f);
?>