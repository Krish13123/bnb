<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
</head>

<body>

    <?php
    include "checksession.php";
    checkUser();
    loginStatus();


    include "config.php";
    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()) {
        echo "Error:Unable to connect to MySql." . mysqli_connect_error();
        exit; //stop processing the page further.
    }

    //check if id exists
    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        $id = $_GET['id'];
        if (empty($id) or !is_numeric($id)) {
            echo "<h2>Invalid booking id</h2>";
            exit;
        }
    }

    $query = 'SELECT booking.bookingID, room.roomname, room.roomtype, room.beds, 
    booking.Checkin_Date, booking.Checkout_Date, booking.Contact_Number, booking.Booking_Extras, booking.Room_Review FROM `booking`
    INNER JOIN `room` ON booking.roomID=room.roomID WHERE bookingID=' . $id;

    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);
    ?>


    <h1>Booking Details View</h1>

    <h2>
        <a href="listbooking.php">[Return to the booking listing]</a>
        <a href="index.php">[Return to the main page]</a>
    </h2>

    <?php
    if ($rowcount > 0) {
        echo "<fieldset><legend>Room Detail #$id</legend><dl>";
        $row = mysqli_fetch_assoc($result);

        echo "<dt>Room name: </dt><dd>" . $row['roomname'] . "</dd>" . PHP_EOL;
        echo "<dt>Checkin date: </dt><dd>" . $row['Checkin_Date'] . "</dd>" . PHP_EOL;
        echo "<dt>Checkout date: </dt><dd>" . $row['Checkout_Date'] . "</dd>" . PHP_EOL;

        echo "<dt>Contact number: </dt><dd>" . $row['Contact_Number'] . "</dd>" . PHP_EOL;
        echo "<dt>Extras: </dt><dd>" .  $row['Booking_Extras'] . "</dd>" . PHP_EOL;
        echo "<dt>Room review: </dt><dd>" . $row['Room_Review'] . "</dd>" . PHP_EOL;
        echo '</dl></fieldset>' . PHP_EOL;

    } else
        echo "<h5>No booking found! Possbily deleted!</h5>";
    mysqli_free_result($result);
    mysqli_close($DBC);
    ?>

</body>

</html>