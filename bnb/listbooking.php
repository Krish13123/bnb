<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Bookings</title>
</head>

<?php
include "checksession.php";
checkUser();
loginStatus();
include "config.php"; //load in any variables
$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit; //stop processing the page further
}

//prepare a query and send it to the server
$query = 'SELECT * FROM `booking` , room , customer 
WHERE booking.roomID  = room.roomID  
and booking.customerID  = customer.customerID 
ORDER BY bookingID ';
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);
?>


<body>
    <h1>Current bookings</h1>
    <h2>
        <a href="makingbooking.php">[Make a Booking]</a>
        <a href="index.php">[Return to the main page]</a>
    </h2>

    <table border="2">
        <thead>
            <tr>
                <th>Booking (room, dates)</th>
                <th>Customer</th>
                <th>Action</th>
            </tr>
        </thead>


        <?php

        if ($rowcount > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['bookingID'];

                echo '<tr><td>'
                    . $row['roomname']
                    . ', ' . $row['Checkin_Date']
                    . ', ' . $row['Checkout_Date']
                    . '</td>';

                echo '<td>'
                    . $row['firstname']
                    . ', '
                    . $row['lastname']
                    . '</td>';

                echo '<td>
				<a href="viewbookingdetail.php?id=' . $id . '">[view] </a>';

                echo
                    '<a href="updatebooking.php?id=' . $id . '">[edit] </a>';
                echo
                    '<a href="editreview.php?id=' . $id . '">[manage review] </a>';
                echo
                    '<a href="deletebooking.php?id=' . $id . '">[delete] </a>
				</td>';

                echo '</tr>' . PHP_EOL;


            }
        } else
            echo "<h2>No Booking found!</h2>";

        mysqli_free_result($result);
        mysqli_close($DBC);







        ?>
    </table>
</body>

</html>