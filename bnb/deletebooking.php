<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Booking</title>
</head>
<?php
include "checksession.php";
checkUser();
loginStatus();
?>

<body>

    <?php


    include "config.php";
    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()) {
        echo "Error:Unable to connect to MySql." . mysqli_connect_error();
        exit; //stop processing the page further.
    }

    function cleanInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    //check if id exists
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = $_GET['id'];
        if (empty($id) or !is_numeric($id)) {
            echo "<h2>Invalid booking ID</h2>"; //simple error feedback
            exit;
        }
    }



    //delete ticket
    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {
        $error = 0;
        $msg = "Error:";

        //we try to convert to number - intval function(return to the integer of a variable)
        if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {

            //code here
            $id = cleanInput($_POST['id']);
        } else {
            //code here
    
            $error++;
            $msg .= 'Invalid booking ID';
            $id = 0;
        }

        if ($error == 0 and $id > 0) {
            $query = "DELETE FROM booking WHERE bookingID=?";
            $stmt = mysqli_prepare($DBC, $query);
            mysqli_stmt_bind_param($stmt, 'i', $id);

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo "<h5>Booking deleted!</h5>";
        } else {
            echo "<h5>$msg</h5>" . PHP_EOL;
        }
    }


    //code here
    
    $query = "SELECT * FROM booking
    INNER JOIN room on booking.roomID = room.roomID
    WHERE bookingID=" . $id;


    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);
    ?>



    <h1>Booking preview before deletion</h1>

    <h2>
        <a href="listbooking.php">[Return to the booking listing]</a>
        <a href="index.php">[Return to the main page]</a>
    </h2>

    <?php
    if ($rowcount > 0) {

        echo "<fieldset><legend>Booking Detail #$id</legend><dl>";
        $row = mysqli_fetch_assoc($result);
        $id = $row['bookingID'];

        echo "<dt>Room name: </dt><dd>" . $row['roomname'] . "</dd>" . PHP_EOL;
        echo "<dt>Checkin Date: </dt><dd>" . $row['Checkin_Date'] . "</dd>" . PHP_EOL;
        echo "<dt>Checkout Date: </dt><dd>" . $row['Checkout_Date'] . "</dd>" . PHP_EOL;

        echo '</dl></fieldset>' . PHP_EOL;


        ?>

        <form method="POST" action="deletebooking.php">

            <h4>Are you sure you want to delete this booking?</h4>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <button type="submit" name="submit" value="Delete">Delete</button>
            <a href="listbooking.php">Cancel</a>

        </form>

        <?php
    } else
        echo "<h5>No Booking found! Possbily deleted!</h5>";
    mysqli_free_result($result);
    mysqli_close($DBC);
    ?>



</body>

</html>