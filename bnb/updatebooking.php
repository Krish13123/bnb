<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Booking</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.14.2/jquery-ui.js"></script>
    <script>
        $(document).ready(function () {
            $.datepicker.setDefaults({
                dateFormat: 'yy-mm-dd'
            });
            $(function () {
                checkin = $("#checkin").datepicker();
                checkout = $("#checkout").datepicker();

                function getDate(element) {
                    var date;
                    try {
                        date = $.datepicker.parseDate(dateFormat, element.value);
                    } catch (error) {
                        date = null;
                    }
                    return date;
                }
            });
        });
    </script>
</head>

<?php
include "checksession.php";
checkUser();
loginStatus();
include "config.php";
$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);
if (mysqli_connect_errno()) {
    echo "Error:Unable to connect to MySQL." . mysqli_connect_error();
    exit;
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

//on submit check if empty or not string and is submited by POST
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {

    $room = cleanInput($_POST['rooms']);
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $Pnumbers = cleanInput($_POST['Pnumber']);
    $Bextras = cleanInput($_POST['Bextra']);
    $Rreviews = cleanInput($_POST['Rreview']);

    $id = cleanInput($_POST['id']);

    $upd = "UPDATE booking SET roomID=?, Checkin_Date=?, Checkout_Date=?,
    Contact_Number=?, Booking_Extras=?, Room_Review=?
    WHERE bookingID=?";

    $stmt = mysqli_prepare($DBC, $upd);
    mysqli_stmt_bind_param($stmt, 'isssssi', $room, $checkin, $checkout, $Pnumbers, $Bextras, $Rreviews, $id);

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "<h5>Booking updated successfully!</h5>";
}

$query = "SELECT * from booking
INNER JOIN room on booking.roomID = room.roomID
WHERE bookingID=" . $id;
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);

?>


<body>
    <h1>Edit a booking</h1>
    <h2>
        <a href="listbooking.php">[Return to the Bookings listing]</a>
        <a href="index.php">[Return to the main page]</a>
    </h2>



    <form action="updatebooking.php" method="POST">
        <p>
            <label for="rooms">Room (name,type,beds):</label>
            <select name="rooms" id="rooms">
                <?php
                if ($rowcount > 0) {
                    $row = mysqli_fetch_assoc($result);

                    ?>
                    <option value="<?php echo $row['roomID']; ?>">
                        <?php
                        echo $row['roomname'] . " "
                            . $row['roomtype'] . " "
                            . $row['beds'] . " "
                            ?>

                    </option>
                    <?php
                } else
                    echo "<option>No rooms found!</option>";
                ?>
            </select>
        </p>

        <p>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        </p>

        <p>
            <label for="checkin">Checkin Date:</label>
            <input type="text" id="checkin" name="checkin" required value="<?php echo $row['Checkin_Date']; ?>">
        </p>
        <p>
            <label for="checkout">Checkout Date:</label>
            <input type="text" id="checkout" name="checkout" required value="<?php echo $row['Checkout_Date']; ?>">
        </p>
        <p>
            <label for="Pnumber">Contact number: </label>
            <input type="text" id="Pnumber" name="Pnumber" required value="<?php echo $row['Contact_Number']; ?>"
                placeholder="(xxx) xxx xxxx" pattern="\(\d{3}\) \d{3} \d{4}">
        </p>
        <p>
            <label for="Bextras">Booking Extras:</label>
            <input type="text" id="Bextra" name="Bextra" value="<?php echo $row['Booking_Extras']; ?>">

        </p>

        <p>
            <label for="Rreviews">Room Review:</label>
            <input type="text" id="Rreview" name="Rreview" value="<?php echo $row['Room_Review']; ?>">
        </p>
        <button type="submit" name="submit" value="Update">Update</button>
    </form>


    <?php
    mysqli_free_result($result);
    mysqli_close($DBC);
    ?>




</body>

</html>