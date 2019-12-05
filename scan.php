register

	<?php
	if(isset($_GET['btnsignup'])){
		session_start();
		$_SESSION['email'] = $_GET['inputemail'];
		$data1 = array(
			"column" => base64_encode("data:name"),
			"$" => base64_encode($_GET['inputname'])
		);
		$data2 = array(
			"column" => base64_encode("data:password"),
			"$" => base64_encode($_GET['inputpassword'])
		);
		$data = [];
		array_push($data, $data1, $data2);

		$namaTable = "user";
		$kunci = $_GET['inputemail'];

		add_to_db($namaTable,$kunci,$data);
		header("Location: view.php");
	}
	?>
  
  
  
  scan
  
  	function scanFriend(){
				$scantable = scan('user');
				$json = json_decode($scantable, true);
				$row = $json["Row"];
				foreach ($row as $eachRow) {
					$key = $eachRow["key"];
					$cell = $eachRow["Cell"];

					$enKey = base64_decode($key);

					$dollar = $cell[0]["$"];
					$enDollar = base64_decode($dollar);

					$kunci = $_SESSION['email'].'_'.$enKey;

					$tempcek = get('friend',$kunci);

					if(getValue($tempcek,0)!=null){
						scanTweet($enKey);
					}
				}
			}

			function scanTweet($inputkey){
				$scantable = scan('tweet');
				$json = json_decode($scantable, true);
				$row = $json["Row"];
				foreach ($row as $eachRow) {
					$key = $eachRow["key"];
					$cell = $eachRow["Cell"];
					$dollar = $cell[0]["$"];
					$enDollar = base64_decode($dollar);

					$enKey = base64_decode($key);
					$idx = strpos($enKey,'_',0);
					$substrkey = substr($enKey, 0, $idx);

					$getUser = get('user',$substrkey);
					$nama = getValue($getUser,'0');
					if($substrkey == $inputkey){
						echo "<tr scope='row'><td>".$nama."</td><td>".$substrkey."</td><td>".$enDollar."</td></tr>";
					}
				}
			}