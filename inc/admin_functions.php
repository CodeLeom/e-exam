<?php

//SUBLIST
if(isset($_POST['editSub'])) {

    // $course_code = sanitize(strtoupper($_POST['course_code']));
    $name = sanitize($_POST['name']);
    $class_id = sanitize($_POST['id']);


    $query = "UPDATE subjects SET name = '$name' WHERE id=$class_id";

    //Run query

    $update_row = $mysqli->query($query) or die($mysqli->error.__LINE__);


}

function add_new_subject(){

    global $connection;
    if (isset($_POST['add_subject'])) {
//                    $errors = array();


//        if (empty($_POST) === false) {


        $name = sanitize($_POST['name']);
        $code = sanitize($_POST['code']);


        $required_fields = array('name');

        foreach ($_POST as $key => $value) {
            if (empty($value) && in_array($key, $required_fields) === true) {
                $errors[] = 'Fields marked with an asterisk Are Required';
                break 1;
            }
        }

        if (empty($errors) === true) {


            if (user_exit('subjects', 'name', $name ) > 0) {

                $errors[] = 'Sorry, The Subject \'' . $name . '\' already exit.';

            }

            if (user_exit('subjects', 'code', $code ) > 0) {

                $errors[] = 'Sorry, The Subject Code \'' . $code . '\' already exit.';

            }


        }


        if (empty($errors) === true && empty($_POST) === false) {


            $query = "INSERT INTO subjects(name, code) VALUES(?,?)";

            $stmt = mysqli_prepare($connection, $query);

            mysqli_stmt_bind_param($stmt, 'ss', $name, $code);

            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);

            if (!$stmt) {
                die("QUERY FAILED" . mysqli_error($connection));
            }else{
                echo '<div class="alert-success text-center">
                                <p>Success!!! <br>

                                    The New Subject Has been Added Successfully <br>

                                   

                                </p>
                                
                            </div>';
            }
        } else {
            echo output_errors($errors);
        }
    }

}


function upload_pins_csv()
{
//    $sub = $_GET['sub'];
//    $course_id = $_GET['id'];
    global $connection,$mysqli;
    if (isset($_POST['sub'])) {
//    die($csv->import($_FILES['file']['tmp_name']));

        if ($_FILES['file']['name']) {
            $filename = explode('.', $_FILES['file']['name']);
            if ($filename[1] == 'csv') {

                $handle = fopen($_FILES['file']['tmp_name'], "r");
                $row = 1;
                while ($data = fgetcsv($handle)) {


                    if(!isset($data[2])) {
                        $errors[] = "OPPs, CSV File Was Modified";
                    }

                    $pin = mysqli_real_escape_string($connection, $data[0]);
                    $serial = mysqli_real_escape_string($connection, $data[1]);
                    $date_added = mysqli_real_escape_string($connection, $data[2]);





                    if ($row == 1) {
                        if ($pin != "PIN") {
                            $errors[] = "Only Pin Generated By Benx Technologies Can Be Uploaded";
                        }
                        if ($serial != "SERIAL") {
                            $errors[] = "Only Pin Generated By Benx Technologies Can Be Uploaded";
                        }
                        if ($date_added != "DATE ADDED") {
                            $errors[] = "Only Pin Generated By Benx Technologies Can Be Uploaded";
                        }
                    } else {

                        // check if question number is integer
                        if (is_numeric(onlyNums($pin)) == false) {
                            $errors[] = "CSV File Modified";
                        }

                        // check for repeated pins


                        if (user_exit('pins','pin',onlyNums($pin))) {
                            $errors[] = "Pin $pin already Uploaded, Make Sure You Are Uploading The Right CSV";
                        }

                        if (empty($errors) === true && empty($_POST) === false) {
                            $query = "INSERT INTO pins(pin, serial, date_added) VALUES(?,?,?)";

                            $stmt = mysqli_prepare($connection, $query);

                            $the_pin = onlyNums($pin);

                            mysqli_stmt_bind_param($stmt, 'sss',$the_pin , $serial, $date_added);

                            mysqli_stmt_execute($stmt);

                            mysqli_stmt_close($stmt);

                            if (!$stmt) {
                                die("QUERY FAILED" . mysqli_error($connection));
                            }
                        }


                    }

                    ++$row;


                }
                fclose($handle);

            } else {
                $errors[] = "The File You Selected is Not A CSV file";

            }
            if (empty($errors)) {
                $num = $row - 2;
                echo "<div class=\"alert alert-success alert-dismissible\">
                                    <a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>
                                    <strong>Success!</strong> You have Successfully Uploaded $num Pins.
                                </div>";

            } else{

                echo output_errors($errors);
            }
        }
    }

}


function update_inst_info(){
    global $connection;
    if(isset($_POST['save_inst_settings'])) {


        $name = sanitize($_POST['institution_name']);
        $slogan = sanitize($_POST['institution_slogan']);

        $image = sanitize($_FILES['logo']['name']);
        $image_temp = sanitize($_FILES['logo']['tmp_name']);

        if($image == ''){
            $image = $_SESSION['institution_logo'];
        }
        $target_dir = "../images/";
        $target_file = $target_dir . basename($image);


        $move = move_uploaded_file($image_temp, $target_file);

        updateSetting($name,'4');
        updateSetting($slogan,'6');
        updateSetting($image,'5');

        echo "<div class='alert alert-success alert-dismissible text-center'>
<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                    <strong>Success!</strong> You Have Updated Your Institutions Information.<br>
                                    <p class='text-center'>Pls Refresh To See Changes <a href=''> <i class='fa fa-refresh'></i></a></p>
</div>";

        if(!empty($errors)){
            echo output_errors($errors);
        }

    }
}

function update_access_setting(){
    global $connection;
    if(isset($_POST['save_access_settings'])){
        $cvps = sanitize($_POST['cvps']);
        $cdps = sanitize($_POST['cdps']);
        $cvms = sanitize($_POST['cvms']);
        $cdms = sanitize($_POST['cdms']);
        $exam_type = sanitize($_POST['exam_type']);

        updateSetting($cdms,10);
        updateSetting($cvms,9);
        updateSetting($cdps,11);
        updateSetting($cvps,8);
        updateSetting($exam_type,2);


        echo "<div class='alert alert-success alert-dismissible text-center'>
<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                    <strong>Success!</strong> You Have Updated The Access Control.<br>
                                    <p class='text-center'>Pls Refresh To See Changes <a href=''> <i class='fa fa-refresh'></i></a></p>
</div>";

        if(!empty($errors)){
            echo output_errors($errors);
        }
    }
}
function update_settings(){
    global $connection;
    if(isset($_POST['save_settings'])){


        $name = sanitize($_POST['institution_name']);
        $exam_int = sanitize($_POST['exam_int']);
        $slogan = sanitize($_POST['institution_slogan']);

        $image = sanitize($_FILES['logo']['name']);
        $image_temp = sanitize($_FILES['logo']['tmp_name']);

        if($image == ''){
            $image = $_SESSION['institution_logo'];
        }
        $target_dir = "../images/";
        $target_file = $target_dir . basename($image);


        $move = move_uploaded_file($image_temp, $target_file);


        if(!$move){

            $errors[] = 'Logo Not Uploaded';

        }

        $update_query = "UPDATE settings SET institution_name = '$name', institution_logo = '$image', institution_slogan = '$slogan' ";

        $update_settings = $connection->query($update_query) or die($connection->error.__LINE__);

        $_SESSION['institution_name'] = $name;
        $_SESSION['institution_logo'] = $image;
        $_SESSION['institution_slogan'] = $slogan;

        echo "<div class='row alert-success text-center'>
                                <p>Success!!! <br>

                                    You Have Successfully Updated This Systems Settings <br>
                                    
                                    Pls Reload to Effect Changes



                                </p><br>

                            </div>";
        if(!empty($errors)){
            echo output_errors($errors);

        }

    }

}

function reset_pin(){
    global $mysqli;
    if(isset($_POST['reset'])){
        $query = "UPDATE pin_logins SET usage_count='0' WHERE pin='7781295023477520' AND reg_no='jamb2'";

        //Run query

        $update_row = $mysqli->query($query) or die($mysqli->error.__LINE__);

        echo "<div class='row alert-success text-center'>
                                <p>Success!!! <br>

                                    You Have Successfully Reset The Login detail of Pin  <strong>7781-2950-2347-7520</strong> and reg No. <strong>jamb2</strong><br>

                                </p><br>

                            </div>";
    }
}

function update_exam_settings(){
    global $connection;
    if(isset($_POST['save_exam_settings'])){
        $exam_inst = sanitize($_POST['exam_inst']);
        $exam_time = sanitize($_POST['exam_time']);
        $exam_type = sanitize($_POST['exam_type']);

        updateSetting($exam_time,3);
//        updateSetting($exam_inst,7);
        updateInstructions($exam_inst,1);
        updateSetting($exam_type,2);


        echo "<div class='alert alert-success alert-dismissible text-center'>
<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                    <strong>Success!</strong> You Have Updated The Exam Settings.<br>
                                    <p class='text-center'>Pls Refresh To See Changes <a href=''> <i class='fa fa-refresh'></i></a></p>
</div>";

        if(!empty($errors)){
            echo output_errors($errors);
        }
    }
}


?>