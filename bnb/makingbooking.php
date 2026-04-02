<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.14.2/jquery-ui.js"></script>

</head>
<script>
    //insert datepicker jQuery

    $(document).ready(function () {
        $.datepicker.setDefaults({
            dateFormat: 'yy-mm-dd'
        });
        $(function () {
            checkin = $("#checkin").datepicker()
            checkout = $("#checkout").datepicker()

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

<body>
    <?php
    include "checksession.php";
    checkUser();
    loginStatus();
    include "config.php"; //load in any variables
    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);



    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
        exit; //stop processing the page further
    }


    //function to clean input but not validate type and content
    function cleanInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }


    //on submit check if empty or not string and is submited by POST
    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Book')) {

        #code
        $room = cleanInput($_POST['rooms']);
        $customer = cleanInput($_POST['customers']);
        $checkin = $_POST['checkin'];
        $checkout = $_POST['checkout'];
        $Pnumbers = cleanInput($_POST['Pnumber']);
        $Bextras = cleanInput($_POST['Bextra']);

        $error = 0;
        $msg = "Error:";

        $in = new DateTime($checkin);
        $out = new DateTime($checkout);

        if ($in >= $out) {
            $error++;
            $msg .= "End date cannot be earlier or equal to start date";
            $checkin = '';
        }

        if ($error == 0) {
            $query = "INSERT INTO booking (roomID, customerID, Checkin_Date,
      Checkout_Date, Contact_Number, Booking_Extras) VALUES (?,?,?,?,?,?)";

            $stmt = mysqli_prepare($DBC, $query);

            mysqli_stmt_bind_param($stmt, 'iissss', $room, $customer, $checkin, $checkout, $Pnumbers, $Bextras);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            echo "<h5>Booking added successfully.</h5>";
        } else {
            echo "<h5>$msg</h5>" . PHP_EOL;
        }

    }




    $query = 'SELECT roomID, roomname, roomtype, beds FROM room ORDER BY roomID';
    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);

    $query1 = 'SELECT customerID, firstname, lastname, email FROM customer ORDER BY customerID';
    $result1 = mysqli_query($DBC, $query1);
    $rowcount1 = mysqli_num_rows($result1);
    ?>

    <h1>Make a booking</h1>
    <h2>
        <a href="listbooking.php">[Return to the Bookings listing]</a>
        <a href="index.php">[Return to the main page]</a>
    </h2>
    <form method="post">
        <label>Room (name,type,beds):</label>

        <select name="rooms" id="rooms">
            <?php
            if ($rowcount > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $id = $row['roomID']; ?>

                    <option value="<?php echo $row['roomID']; ?>">
                        <?php echo $row['roomname'] . ' '
                            . $row['roomtype'] . ' '
                            . $row['beds']

                            ?>
                    </option>
                <?php }
            } else
                echo "<option>No rooms found</option>";
            mysqli_free_result($result);
            ?>

        </select>
        <br><br>
        <label for="customers">Customers:</label>
        <select name="customers" id="customers">
            <?php
            if ($rowcount1 > 0) {
                while ($row = mysqli_fetch_assoc($result1)) {
                    $id = $row['roomID']; ?>

                    <option value="<?php echo $row['customerID']; ?>">
                        <?php echo $row['customerID'] . ' '
                            . $row['firstname'] . ' '
                            . $row['lastname'] . ' - '
                            . $row['email']

                            ?>
                    </option>
                <?php }
            } else
                echo "<option>No customers found</option>";
            mysqli_free_result($result1);
            ?>
        </select>

        <br>
        <p>
            <label>Checkin date: </label>
            <input type="text" id="checkin" name="checkin" required>
        </p>

        <p>
            <label>Checkout date: </label>
            <input type="text" id="checkout" name="checkout" required>
        </p>
        <p><label>Contact number: </label><input type="" id="Pnumber" name="Pnumber" placeholder="(xxx) xxx xxxx"
                required pattern="\(\d{3}\) \d{3} \d{4}">
        </p>
        <p><label>Booking extras: </label><textarea name="Bextra" id="Bextra" placeholder="nothing"></textarea></p>

        <button type="submit" name="submit" value="Book">Add</button>
        <a href="listbooking.php">[Cancel]</a>
    </form>

    <hr>
    <h2>Search for room availability</h2>
    <form id="searchForm" method="get" name="searching">
        <label>Start date: </label><input type="" id="start" name="start" required>
        <label>End date: </label><input type="" id="end" name="end" required>
        <button type="submit" onclick="searchRoomAvailability()">Search availability</button>
    </form>
    <p>
    <div class="row">
        <table border="2" id="tblbookings">
            <thead>
                <tr>
                    <th>Room #</th>
                    <th>Room name</th>
                    <th>Room type</th>
                    <th>Beds</th>
                </tr>
            </thead>
            <tbody id="result"></tbody> <!-- Display search result here -->
        </table>
    </div>
    <script>
        $(document).ready(function () {
            $("#start").datepicker({ dateFormat: "yy-mm-dd" });
            $("#end").datepicker({ dateFormat: "yy-mm-dd" });

            $("#searchForm").submit(function (event) {
                event.preventDefault(); // Prevent default form submission
                var start = $("#start").val();
                var end = $("#end").val();

                if (start > end) {
                    alert("start date cannot be later than To end date.");
                    return false; // Prevents further execution
                }

                searchRoom(); // Call searchTickets function
            });
        });

        function searchRoom() {
            var start = $("#start").val();
            var end = $("#end").val();

            $.ajax({
                url: "roomsearch.php",
                method: "GET",
                data: { start: start, end: end },
                success: function (response) {
                    $("#result").html(response);
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
            });
        }
    </script>


</body>

</html>