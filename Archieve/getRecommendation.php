    <?php
if(isset($_GET['id'])){
	$id = $_GET['id'];

    $db = new mysqli("jordanrandles.co.uk", "admin_mazzola", "SuperSecretPassword", "admin_mazzola");

    $query = $db->query("SELECT * FROM `recommendations` WHERE id='$id'");

    if($query->num_rows > 0){
    
        while($row = $query->fetch_assoc()){

            $id = $row['id'];
            $name = $row['name'];
            $subheading = $row['subtitle'];
            $message = $row['message'];
            $date = $row['date'];


            if(strlen($row['image']) === 0){

                $image = "/images/user-placeholder.png";

            }else{

                $image = $row['image'];

            };

            $response = array();
            $response['message'] = nl2br($message);
            $response['name'] = $name;
            $response['heading'] = $subheading;
            $response['id'] = $id;
            $response['image'] = $image;
            $response['date'] = date('dS M  Y', strtotime($date));;

            echo json_encode($response);

        };

    }else{

        echo'Table Empty';

    };
}else{
	echo 'Invalid request';
}
?>