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
            <a class="nav-link" href="/">Home</a>
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
            <a class="nav-link" href="/get_row_by_row_key.php">Get Row By Row Key</a>
        </li>
    </ul>

    <div class="container">
        <h1> List Of All Tables </h1>
        <?php
        list_db();
        echo "<br>";
        ?>
    </div>
    <div class="container">
        <h2>Look for Tables</h2>
        <form action="index.php" method="post">
            <div class="form-group">
                <label for="table_name">Nama Table:</label>
                <select id="tbl_list" name="table_option" class="custom-select mb-3">
                    <?php list_db_drop_down() ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
    <?php


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<br>";
        echo "<h3 class='container'>Tabel " . $_POST['table_option'] . "</h3>";
        echo "<br>";
        $arr_data = [];
        $table_name = $_POST['table_option'];
        scan_db($table_name);
    }

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

    function list_db()
    {
        $ch = curl_init();

        // header
        $request_headers = array();
        $request_headers[] = 'Accept: application/json';
        $request_headers[] = 'Content-Type: application/json';

        // set header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        // set url 
        curl_setopt($ch, CURLOPT_URL, "http://192.168.99.101:8081/");

        // return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string 
        $output = curl_exec($ch);

        // tutup curl 
        curl_close($ch);

        json_to_list($output);
    }


    function json_to_list($datas)
    {
        $datas = json_decode($datas);
        $obj = $datas->table;

        foreach ($obj as $data) {
            $name = $data->name;
            echo $name;
            echo "<br>";
        }
    }

    function list_db_drop_down()
    {
        $ch = curl_init();

        // header
        $request_headers = array();
        $request_headers[] = 'Accept: application/json';
        $request_headers[] = 'Content-Type: application/json';

        // set header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        // set url 
        curl_setopt($ch, CURLOPT_URL, "http://192.168.99.101:8081/");

        // return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string 
        $output = curl_exec($ch);

        // tutup curl 
        curl_close($ch);

        json_to_list_dropdown($output);
    }

    function json_to_list_dropdown($datas)
    {
        $datas = json_decode($datas);
        $obj = $datas->table;

        foreach ($obj as $data) {
            $name = $data->name;
            echo "<option id=table_option value=" . $name . ">" . $name . "</option>";
        }
    }


    function json_to_table($datas)
    {
        $datas = json_decode($datas);
        $obj = $datas->Row;

        echo "<div class='container'>";
        echo "<table class ='table table-striped'>";
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
        echo "</div>";
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