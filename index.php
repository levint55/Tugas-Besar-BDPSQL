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
    <li class="nav-item">
        <a class="nav-link" href="/insert_table.php">Insert Table</a>
    </li>
    </ul>
    <div class="container">
        <h1>Look for Tables</h1>
        <form action="index.php" method="post">
        <div class="form-group">
            <label for="table_name">Table Name:</label>
            <input type="text" class="form-control" id="table_name" name="table_name" required>
        </div>
           <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div> 
    <?php
        scan_db('mahasiswa');
        // scan database
        //scan_db('mahasiswa');
        
        // // insert data to database
        // $data1 = array(
        //     'column' => base64_encode('nilai:DAA'),
        //     '$' => base64_encode('10')
        // );
        
        // $data2 = array(
        //     'column' => base64_encode('nilai:PBO'),
        //     '$' => base64_encode('50')
        // );
        
        // $arr_data = [];
        
        // array_push($arr_data, $data1, $data2);
        
        // add_to_db('mahasiswa', 'Bebas', $arr_data);

        // // get from database
        // get_db('mahasiswa', 'Kris');
        function scan_db($table_name){
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
            json_to_normal($output);
            
        }

        function add_to_db($table_name, $key, $data){
            $record = new Record(base64_encode($key), $data);

            $row = new Row($record);

            // echo json_encode($row);

            // persiapkan curl
            $ch = curl_init(); 

            // header
            $request_headers = array();
            $request_headers[] = 'Accept: application/json';
            $request_headers[] = 'Content-Type: application/json';

            // set header
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

            // set url 
            curl_setopt($ch, CURLOPT_URL, "http://192.168.99.101:8081/$table_name/fakerow");

            // return the transfer as a string 
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($row));

            // $output contains the output string 
            curl_exec($ch);
            // tutup curl 
            curl_close($ch);
        }

        function get_db($table_name, $key){
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

            json_to_normal($output);
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

        class Record {
            public $key;
            public $Cell = [];

            function __construct($key, $cell) {
                $this->key = $key;
                $this->Cell = $cell;
              }
        }

        class Row {
            public $Row = [];

            function __construct($record) {
                array_push($this->Row, $record);
              }
        }
    ?>
</body>
</html>