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
    </ul>
    <div class="container">
        <h1>Create Table</h1>
        <form action="create_table.php" method="post">
            <div class="form-group">
                <label for="table_name">Table Name:</label>
                <input type="text" class="form-control" id="table_name" name="table_name" required>
            </div>
            <div class="form-group">
                <label for="cf_1_name">Column Family 1 Name:</label>
                <input type="text" class="form-control" id="cf_1_name" name="cf_1_name" required>
            </div>
            <div class="form-group">
                <label for="cf_2_name">Column Family 2 Name:</label>
                <input type="text" class="form-control" id="cf_2_name" name="cf_2_name" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <?php

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data1 = array(
                'name' => $_POST['cf_1_name'],
            );
    
            $data2 = array(
                'name' => $_POST['cf_2_name'],
            );

            $arr_data = [];

            array_push($arr_data, $data1, $data2);

            add_table($_POST['table_name'], $arr_data);

            echo "<br>";
            echo "tabel ".$_POST['table_name']." berhasil dimasukkan";
        }

        function add_table($table_name, $columns){
            $table = new Table($table_name, $columns);

            // persiapkan curl
            $ch = curl_init(); 

            // header
            $request_headers = array();
            $request_headers[] = 'Accept: application/json';
            $request_headers[] = 'Content-Type: application/json';

            // set header
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

            // set url 
            curl_setopt($ch, CURLOPT_URL, "http://localhost:8081/$table_name/schema");

            // return the transfer as a string 
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($table));

            // $output contains the output string 
            curl_exec($ch);
            // tutup curl 
            curl_close($ch);
        }

        function json_to_normal($datas){
            $datas = json_decode($datas);
            $obj = $datas->Row;

            foreach ($obj as $data) {
                // nama key
                $key = base64_decode($data->key);
                echo $key;
                echo ": ";

                // ambil cell
                $cells = (array)$data->Cell;

                foreach ($cells as $cell) {
                    // ambil column
                    $column = (array)$cell;

                    $kolom = base64_decode($column['column']);
                    $arr_kolom = explode(':',$kolom);

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

        class Table {
            public $name;
            public $ColumnSchema = [];

            function __construct($table_name, $columns) {
                $this->name = $table_name;
                $this->ColumnSchema = $columns;
              }
        }
    ?>
</body>
</html>