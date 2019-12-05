<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="css/bootstrap.css" rel="stylesheet" />
    <script src="js/bootstrap.min.js"></script>
</head>
<style>
    #customers {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #customers td,
    #customers th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #customers tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #customers tr:hover {
        background-color: #ddd;
    }

    #customers th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
</style>

<body>
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="/index.php">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/create_table.php">Create Table</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/delete_table.php">Delete Table</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/insert_table.php">Insert Table</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/">Filter Table</a>
        </li>
    </ul>
    <div class="container">
        <h1>Filter By Row Key</h1>
        <form action="get_table.php" method="post">
            <div class="form-group">
                <label for="table_name">Table Name:</label>
                <input type="text" class="form-control" id="table_name" name="table_name" required>
            </div>

            <div class="form-group">
                <label for="table_name">Row Key</label>
                <input type="text" class="form-control" id="insert_key" name="insert_key" required>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
    <?php

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "Filtered " . $_POST['table_name'];
        echo "<br>";
        $arr_data = [];
        $table_name = $_POST['table_name'];
        $key = $_POST['insert_key'];
  
        get_db($table_name, $key);
    } else { }

    function scan_db($table_name)
    {
        // persiapkan curl
        $ch = curl_init();

        // header
        $request_headers = array();
        $request_headers[] = 'Accept: application/json';
        $request_headers[] = 'Content-Type: application/json';

        // set header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        // set url 
        curl_setopt($ch, CURLOPT_URL, "http://192.168.99.101:8081/$table_name/*");

        // return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string 
        $output = curl_exec($ch);

        // tutup curl 
        curl_close($ch);

        // menampilkan hasil curl
        json_to_table($output);
    }



    function get_db($table_name, $key)
    {
        // persiapkan curl
        $ch = curl_init();

        // header
        $request_headers = array();
        $request_headers[] = 'Accept: application/json';
        $request_headers[] = 'Content-Type: application/json';

        // set header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        // set url 
        curl_setopt($ch, CURLOPT_URL, "http://192.168.99.101:8081/$table_name/$key");

        // return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string 
        $output = curl_exec($ch);

        // tutup curl 
        curl_close($ch);

        json_to_table($output);
    }

    function json_to_normal($datas)
    {
        $datas = json_decode($datas);
        $obj = $datas->Row;

        foreach ($obj as $data) {
            // nama key
            $key = base64_decode($data->key);
            echo $key;
            echo ": ";

            // ambil cell
            $cells = (array) $data->Cell;

            foreach ($cells as $cell) {
                // ambil column
                $column = (array) $cell;

                $kolom = base64_decode($column['column']);
                $arr_kolom = explode(':', $kolom);

                // var_dump($arr_kolom);
                $cf = $arr_kolom[0];
                $cn = $arr_kolom[1];

                $isi = base64_decode($column["\$"]);

                echo $cf;
                echo " ";
                echo $cn;
                echo " ";
                echo $isi;
                echo ", ";
            }
            echo "<br>";
        }
    }

    function json_to_table($datas)
    {
        $datas = json_decode($datas);
        $obj = $datas->Row;

        echo "<table id='customers', style='width:60%', align='center'>";
        echo "<tr>";
        echo "<th>Row</th>";
        echo "<th>Column Family:Name</th>";
        echo "<th>Values</th>";
        echo "</tr>";


        foreach ($obj as $data) {
            //nama key
            $key = base64_decode($data->key);

            $cells = (array) $data->Cell;
            foreach ($cells as $cell) {
                // ambil column
                $column = (array) $cell;

                $kolom = base64_decode($column['column']);
                $arr_kolom = explode(':', $kolom);

                // var_dump($arr_kolom);
                $cf = $arr_kolom[0];
                $cn = $arr_kolom[1];

                $isi = base64_decode($column["\$"]);

                echo "<tr>";
                echo "<td>" . $key . "</td>";
                echo "<td>" . $cf . ":" . $cn . "</td>";
                echo "<td>" . $isi . "</td>";
                echo "</tr>";
            }
        }

        echo "</table>";
    }

    class Record
    {
        public $key;
        public $Cell = [];

        function __construct($key, $cell)
        {
            $this->key = $key;
            $this->Cell = $cell;
        }
    }

    class Row
    {
        public $Row = [];

        function __construct($record)
        {
            array_push($this->Row, $record);
        }
    }
    ?>
</body>

</html>