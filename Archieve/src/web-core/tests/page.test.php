<?php

require_once(__DIR__.'/../inc/website.inc.php');
require(__DIR__.'/testdata.test.php');

use fearricepudding\website as website;
use fearricepudding\pager as pager;


// $raw_data = '[ { "first_name": "Baxter", "last_name": "Kinney", "email": "pellentesque.Sed@metusvitaevelit.edu", "gender": "female" }]';
// $raw_data = "[]"; // Empty Array

$test_data = json_decode($raw_data);

if(isset($_GET['page'])){
    $page = $_GET['page'];
}else{
    $page = 0;
};

$pager = new pager($test_data, $page);

//var_dump($pager->data);
$pageData = $pager->getPage();

foreach($pageData as $data){
    echo<<<END

    <div class="person">
        <div class="name">{$data->first_name} {$data->last_name}</div>
         <div>> {$data->gender}</div>
         <div>> {$data->email}</div>
    </div>
    ----

END;
}

$navData = $pager->nav();


?>
<div>
<?php

if($navData['prev']['active']){
    echo '<a href="'.$navData['prev']['link'].'"><<</a>  ';
}
foreach($navData['buttons'] as $button){
    if($button['active']){
        echo '<a class="active" href="'.$button['link'].'">'.$button['page'].'</a>  ';
    }else{
        echo '<a href="'.$button['link'].'">'.$button['page'].'</a>  ';
    }
}
if($navData['next']['active']){
    echo '<a href="'.$navData['next']['link'].'">>></a>  ';
}

var_dump($navData);
?>

</div>

<style>
    .active{
        background-color:green;
        color:#fff;
    }
</style>