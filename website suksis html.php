<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Student Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
        }

        #drop-zone {
            border: 2px dashed #aaa;
            padding: 40px;
            text-align: center;
            background: #fafafa;
            cursor: pointer;
            transition: 0.3s;
            margin-bottom: 20px;
        }

        #drop-zone:hover {
            background: #f0f0f0;
        }

        #file {
            display: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>

<h1>Upload Student Data CSV</h1>

<!-- Drop Zone -->
<div id="drop-zone">
    Drag & Drop CSV here or click to upload
</div>

<!-- Hidden file input -->
<input type="file" id="file" accept=".csv" onchange="handleFileSelect(event)" />

<!-- Table to display data -->
<table id="csv-table" class="hidden">
    <thead>
        <tr>
            <th>Name</th>
            <th>Gender</th>
            <th>Student ID</th>
            <th>NRIC</th>
            <th>KS ID</th>
            <th>Squad</th>
            <th>Address</th>
            <th>State of Origin</th>
            <th>Faculty</th>
            <th>Program</th>
            <th>Program Code</th>
            <th>Campus</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- JavaScript to handle CSV upload and parsing -->
<script>
    // Trigger file input on clicking drop-zone
    document.getElementById('drop-zone').addEventListener('click', function () {
        document.getElementById('file').click();
    });

    // Handle file selection
    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (file && file.type === "text/csv") {
            const reader = new FileReader();
            reader.onload = function (e) {
                const csvData = e.target.result;
                parseCSV(csvData);
            };
            reader.readAsText(file);
        } else {
            alert("Please upload a valid CSV file.");
        }
    }

    // Parse CSV and display in table
    function parseCSV(data) {
        const rows = data.split("\n").map(row => row.split(","));
        const table = document.getElementById("csv-table");
        const tbody = table.querySelector("tbody");

        // Clear previous table data
        tbody.innerHTML = "";

        let csvData = [];
        rows.forEach((row, index) => {
            if (index === 0) return;  // Skip header row
            const tr = document.createElement("tr");
            let rowData = [];
            
            row.forEach(cell => {
                const td = document.createElement("td");
                td.textContent = cell.trim();
                rowData.push(cell.trim());
                tr.appendChild(td);
            });

            tbody.appendChild(tr);
            csvData.push(rowData);
        });

        // Show the table
        table.classList.remove("hidden");

        // Send CSV data to the server
        sendDataToServer(csvData);
    }

    function sendDataToServer(csvData) {
        fetch('process_upload.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ data: csvData }),
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            alert('Data uploaded successfully!');
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('Error uploading data');
        });
    }
</script>

</body>
</html>

<?php
// Get the raw POST data (sent from JavaScript)
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is valid
if (!empty($data['data'])) {
    include 'db.php'; // Database connection file

    foreach ($data['data'] as $row) {
        // Assuming CSV order: Name, Gender, Student ID, NRIC, KS ID, etc.
        $first_name = $row[0];
        $last_name = $row[1];
        $student_id = $row[2];
        $nric = $row[3];
        $ks_id = $row[4];
        $squad = $row[5];
        $address = $row[6];
        $state_of_origin = $row[7];
        $faculty = $row[8];
        $program = $row[9];
        $program_code = $row[10];
        $campus = $row[11];

        // Prepare SQL statement to insert data into the person table
        $stmt = $conn->prepare("INSERT INTO person () VALUES ()");
        $stmt->execute();
        $personId = $conn->insert_id;

        // Insert data into respective tables (name, gender, etc.)
        $stmt = $conn->prepare("INSERT INTO name (id, first_name, last_name) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $personId, $first_name, $last_name);
        $stmt->execute();

        // Repeat for other tables (gender, nric, etc.)...
        // Insert into other tables (gender, nric, ks, address, etc.)
    }

    // Send success response
    echo json_encode(['status' => 'success', 'message' => 'Data inserted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
}
?>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "suksis_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
