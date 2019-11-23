<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <?php 
        // scan_db('mahasiswa');
        // add_to_db('mahasiswa', 'ABC', 'nilai:asd', '55');
        get_db('mahasiswa', 'ABC');

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
            curl_setopt($ch, CURLOPT_URL, "http://localhost:8081/$table_name/*");

            // return the transfer as a string 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

            // $output contains the output string 
            $output = curl_exec($ch); 

            // tutup curl 
            curl_close($ch);      

            // menampilkan hasil curl
            json_to_normal($output);
            
        }

        function add_to_db($table_name, $key, $column_name, $value){
            $data = array(
                'column' => base64_encode($column_name),
                '$' => base64_encode($value)
            );

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
            curl_setopt($ch, CURLOPT_URL, "http://localhost:8081/$table_name/fakerow");

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
            curl_setopt($ch, CURLOPT_URL, "http://localhost:8081/$table_name/$key");

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
                array_push($this->Cell, $cell);
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