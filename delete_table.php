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
        <li class="nav-item">
            <a class="nav-link" href="/get_row_by_row_key.php">Get Row By Row Key</a>
        </li>
    </ul>
    <div class="container">
        <h1>Delete Table</h1>
        <form action="delete_table.php" method="post">
            <div class="form-group">
                <label for="table_name">Nama Table:</label>
                <select id="tbl_list" name="table_option" class="custom-select mb-3">
                    <?php list_db() ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        delete_table($_POST['table_option']);

        echo "<br>";
        echo "tabel " . $_POST['table_option'] . " berhasil dihapus";
    }

    function delete_table($table_name)
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
        curl_setopt($ch, CURLOPT_URL, "http://192.168.99.101:8081/$table_name/schema");

        // return the transfer as a string 
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        // $output contains the output string 
        curl_exec($ch);
        // tutup curl 
        curl_close($ch);
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
            echo "<option id=table_option value=" . $name . ">" . $name . "</option>";
        }
    }
    ?>
</body>

</html>