<?php
include 'connection.php';

$delete_success = false;

if (isset($_POST['add_event'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];

    $sql = "INSERT INTO events (event_name, event_date) VALUES ('$event_name', '$event_date')";
    if ($conn->query($sql) === TRUE) {
        header("Location: home.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

if (isset($_POST['delete_event'])) {
    $event_id = $_POST['event_id'];

    $sql = "DELETE FROM events WHERE id = $event_id";
    if ($conn->query($sql) === TRUE) {
        $delete_success = true;
    } else {
        echo "Error: " . $conn->error;
    }
}

if (isset($_POST['edit_event'])) {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];

    $sql = "UPDATE events SET event_name = '$event_name', event_date = '$event_date' WHERE id = $event_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: home.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memori - Event Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #8B0000;
            color: white;
        }

        header {
            background-color: #610000;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #610000;
            padding: 10px 20px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .container {
            display: flex;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #610000;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        }

        .calendar-container {
            flex: 2;
            background-color: #B22222;
            padding: 20px;
            border-radius: 5px;
        }

        .event-form-container {
            flex: 1;
            margin-left: 20px;
            background-color: #610000;
            padding: 20px;
            border-radius: 5px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-header select {
            background-color: #610000;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            color: white;
        }

        .calendar div {
            background-color: #610000;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
        }

        .calendar div.header {
            font-weight: bold;
        }

        .calendar div.selected {
            background-color: #8B0000;
            color: white;
        }

        form input, form button {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            border: none;
            border-radius: 5px;
        }

        form button {
            background-color: #B22222;
            color: white;
            cursor: pointer;
        }

        .message {
            color: #FFD700;
            text-align: center;
        }

        .event-list {
            margin-top: 20px;
        }

        .event-item {
            background-color: #B22222;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .event-item button {
            background-color: #610000;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        #alertBox {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #FFD700;
            color: #610000;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        #alertBox button {
            margin-top: 10px;
            padding: 10px;
            background-color: #610000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
    <script>
function renderCalendar(year, month) {
    const calendar = document.querySelector('.calendar');
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    document.querySelector('#currentMonth').textContent = `${monthNames[month]} ${year}`;
    document.querySelector('#yearSelect').value = year;
    document.querySelector('#monthSelect').value = month;

    calendar.innerHTML = '';

    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();

    const dayHeaders = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    dayHeaders.forEach(day => {
        const dayDiv = document.createElement('div');
        dayDiv.textContent = day;
        dayDiv.classList.add('header');
        calendar.appendChild(dayDiv);
    });

    for (let i = 0; i < firstDay; i++) {
        const emptyDiv = document.createElement('div');
        calendar.appendChild(emptyDiv);
    }

    for (let day = 1; day <= lastDate; day++) {
        const dayDiv = document.createElement('div');
        dayDiv.textContent = day;
        dayDiv.onclick = function () {
            document.querySelectorAll('.calendar div').forEach(div => div.classList.remove('selected'));
            dayDiv.classList.add('selected');
            document.getElementById('event_date').value = `${year}-${(month + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        };
        calendar.appendChild(dayDiv);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const today = new Date();
    currentYear = today.getFullYear();
    currentMonth = today.getMonth();

    renderCalendar(currentYear, currentMonth);

    document.getElementById('prevMonth').onclick = function () {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar(currentYear, currentMonth);
    };

    document.getElementById('nextMonth').onclick = function () {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar(currentYear, currentMonth);
    };

    document.getElementById('yearSelect').onchange = function () {
        currentYear = parseInt(this.value);
        renderCalendar(currentYear, currentMonth);
    };

    document.getElementById('monthSelect').onchange = function () {
        currentMonth = parseInt(this.value);
        renderCalendar(currentYear, currentMonth);
    };

    document.querySelector('.event-form').onsubmit = function (e) {
        const eventName = document.querySelector('input[name="event_name"]').value.trim();
        const eventDate = document.querySelector('input[name="event_date"]').value.trim();

        if (!eventName || !eventDate) {
            e.preventDefault();
            let message = '';
            if (!eventName && !eventDate) {
                message += "Event name and Date not selected.";
            }else{
                if(!eventName){
                    message += "Event name is not selected.";
                }
                if(!eventDate){
                    message += "Event date is not selected.";
                }
            }
            alert(message); 
        }
    };
})

function populateYearDropdown() {
    const yearSelect = document.getElementById('yearSelect');
    const currentYear = new Date().getFullYear();
    const range = 100;

    for (let year = currentYear - range; year <= currentYear + range; year++) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        yearSelect.appendChild(option);
    }
}

function showAlert(message) {
    const alertBox = document.getElementById('alertBox');
    alertBox.textContent = message;
    alertBox.style.display = 'block';
}

document.addEventListener('DOMContentLoaded', populateYearDropdown);

document.addEventListener('DOMContentLoaded', function () {
    <?php if ($delete_success) { ?>
        alert("Event deleted successfully!");
        window.location.href = "home.php";
    <?php } ?>
});
    </script>
</head>
<body>
    <header>
        <h1>Memori - Event Reminder</h1>
    </header>
    <nav>
        <a href="#">Home</a>
        <a href="#">Contact Us</a>
        <a href="register.php">Login/Signup</a>
    </nav>

    <div id="alertBox">
        <button onclick="this.parentElement.style.display='none';">OK</button>
    </div>

    <div class="container">
        <div class="calendar-container">
            <div class="calendar-header">
                <select id="yearSelect"></select>
                <select id="monthSelect">
                    <option value="0">January</option>
                    <option value="1">February</option>
                    <option value="2">March</option>
                    <option value="3">April</option>
                    <option value="4">May</option>
                    <option value="5">June</option>
                    <option value="6">July</option>
                    <option value="7">August</option>
                    <option value="8">September</option>
                    <option value="9">October</option>
                    <option value="10">November</option>
                    <option value="11">December</option>
                </select>
                <button type="button" id="prevMonth">&lt;</button>
                <span id="currentMonth"></span>
                <button type="button" id="nextMonth">&gt;</button>
            </div>
            <div class="calendar"></div>
        </div>

        <div class="event-form-container">
            <form class="event-form" method="post" action="home.php">
                <input type="text" name="event_name" placeholder="Event Name" autocomplete="off">
                <input type="hidden" id="event_date" name="event_date">
                <button type="submit" name="add_event">Add Event</button>
            </form>
            <div class="message">
                <?php
                if (isset($_POST['add_event'])) {
                    $event_name = $_POST['event_name'];
                    $event_date = $_POST['event_date'];

                    $sql = "INSERT INTO events (event_name, event_date) VALUES ('$event_name', '$event_date')";
                    if ($conn->query($sql) === TRUE) {
                        echo "Event added successfully!";
                    } else {
                        echo "Error: " . $conn->error;
                    }
                }

                if (isset($_POST['delete_event'])) {
                    $event_id = $_POST['event_id'];

                    $sql = "DELETE FROM events WHERE id = $event_id";
                    if ($conn->query($sql) === TRUE) {
                        echo "Event deleted successfully!";
                    } else {
                        echo "Error: " . $conn->error;
                    }
                }

                if (isset($_POST['edit_event'])) {
                    $event_id = $_POST['event_id'];
                    $event_name = $_POST['event_name'];
                    $event_date = $_POST['event_date'];

                    $sql = "UPDATE events SET event_name = '$event_name', event_date = '$event_date' WHERE id = $event_id";
                    if ($conn->query($sql) === TRUE) {
                        echo "Event updated successfully!";
                    } else {
                        echo "Error: " . $conn->error;
                    }
                }
                ?>
            </div>
            <div class="event-list">
                <h3>Saved Events</h3>
                <?php
                $sql = "SELECT * FROM events ORDER BY event_date";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $event_id = $row['id'];
                        echo "<div class='event-item' id='event-$event_id'>";
                        echo "<span onclick='editEvent($event_id)'>" . $row['event_name'] . " - " . $row['event_date'] . "</span>";
                        echo "<form method='post' action='home.php' id='edit-form-$event_id' style='display:none;'>";
                        echo "<input type='hidden' name='event_id' value='" . $row['id'] . "'>";
                        echo "<input type='text' name='event_name' value='" . $row['event_name'] . "' required>";
                        echo "<input type='date' name='event_date' value='" . $row['event_date'] . "' required>";
                        echo "<button type='submit' name='edit_event'>Save Changes</button>";
                        echo "</form>";
                        echo "<form method='post' action='home.php' style='display:inline;'>";
                        echo "<input type='hidden' name='event_id' value='" . $row['id'] . "'>";
                        echo "<button type='submit' name='delete_event'>Delete</button>";
                        echo "</form>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No events found.</p>";
                }
                ?>
            </div>
        </div>
    </div>
    <script>
        function editEvent(eventId) {
            document.querySelectorAll('.event-item form').forEach(form => form.style.display = 'none');
            document.getElementById('edit-form-' + eventId).style.display = 'block';
        }
    </script>
</body>
</html>
