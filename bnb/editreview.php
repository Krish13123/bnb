<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit room review</title>
</head>

<body>

    <?php
    include "checksession.php";
    checkUser();
    loginStatus();


    //take the details about server and database
    include "config.php"; //load in any variables
    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

    //insert DB code from here onwards
//check if the connection was good
    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
        exit; //stop processing the page further
    }


    //function to clean input but not validate type and content
    function cleanInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    //check if id exists
    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        $id = $_GET['id'];
        if (empty($id) or !is_numeric($id)) {
            echo "<h2>Invalid booking id</h2>";
            exit;
        }
    }


    //on submit check if empty or not string and is submited by POST
    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {

        $rooms = cleanInput($_POST['room']);
        $id = cleanInput($_POST['id']);


        $upd = "UPDATE `booking` SET Room_Review=? WHERE bookingID=?";

        $stmt = mysqli_prepare($DBC, $upd); //prepare the query
        mysqli_stmt_bind_param($stmt, 'si', $rooms, $id);

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        //print message
        echo "<h5>Review updated </h5>";
    }


    $query = 'SELECT  Room_Review FROM `booking` WHERE bookingID=' . $id;


    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);

    ?>



    <h1>Edit/add room review</h1>
    <h2>
        <a href="listbooking.php">[Return to the Bookings listing]</a>
        <a href="index.php">[Return to the main page]</a>
    </h2>


    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <?php
        if ($rowcount > 0) {
            $row = mysqli_fetch_assoc($result);
            ?>
            <div>
                <label for="rooms">Room review:</label>
                <textarea name="room" id="room" placeholder="nothing" required <?php echo $row['Room_Review'] ?>></textarea>
            </div>


            <?php
        } else
            echo "<h5>No Booking found!</h5>"
                ?>
            <br> <br>


            <button type="submit" name="submit" value="Update">Update </button>



        </form>
        <?php
        mysqli_free_result($result);
        mysqli_close($DBC);
        ?>







</body>

</html>