<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="css/bootstrap.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
    <ul class="nav">
    <li class="nav-item">
        <a class="nav-link" href="index.php">Home</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/create_table.php">Create Table</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/delete_table.php">Delete Table</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/">Insert Table</a>
    </li>
    </ul>
    <div class="container">
        <h1>Insert Tables</h1>
        <form action="insert_table.php" method="post">
        <div class="form-group">
            <label for="table_name">Table Name:</label>
            <input type="text" class="form-control" id="table_name" name="table_name" required>
        </div>

        <div class="form-group">
            <label for="table_name">Row:</label>
            <input type="text" class="form-control" id="insert_key" name="insert_key" required>
        </div>

        <div id="columfamily">
              <div class="form-group">
                  <label for="cf_name[]">Column Family:Name 1</label>
                  <input type="text" class="form-control" id="cf_name" name="cf_name[]" required>
              </div>
              <div class="form-group">
                  <label for="value[]">value 1:</label>
                  <input type="text" class="form-control" id="value" name="value[]" required>
              </div>
        </div>

           <button type="submit" class="btn btn-primary" id="insert">Insert</button>
           <button type="button" class="btn btn-secondary" id="add">+</button>

            <script>
            $(document).ready(function() {
                let formCount = 2;
                $("button#add").click(function() {
                    let formGroup = $("<div/>", {class:"form-group"});
                    formGroup.append($("<label/>", {for: ("cf_name[]"), text: "Column Family:Name " + formCount}))
                        .append($("<input/>", {type:"text", class:"form-control", name: "cf_name[]", required:"required"}));
                    $("#columfamily").append(formGroup);

                    formGroup.append($("<label/>", {for: ("value[]"), text: "value " + formCount}))
                        .append($("<input/>", {type:"text", class:"form-control", name: "value[]", required:"required"}));
                    $("#columfamily").append(formGroup);
                    formCount++;
                })
            })
            </script>
        </form>
    </div>

    <?php
        // scan database
        //scan_db('mahasiswa');
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $arr_data = [];

            foreach (array_combine($_POST['cf_name'],$_POST['value']) as $column=>$value) {
                $data = array(
                    'column' => base64_encode($column),
                    '$'=> base64_encode($value)

                );

                array_push($arr_data, $data);
            }
           
            $table_name=$_POST['table_name'];
            $key=$_POST['insert_key'];
            add_to_db($table_name,$key,$arr_data);
            echo "data berhasil dimasukkan ke tabel ".$_POST['table_name'];
        }
      
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

        // get from database
        //get_db('mahasiswa', 'David');


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