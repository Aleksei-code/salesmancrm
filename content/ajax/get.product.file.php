<?php
$n_id = $_GET['n_id'];

$rootpath = dirname(__DIR__, 2);

require_once $rootpath . "/inc/config.php";
require_once $rootpath . "/inc/dbconnector.php";
require_once $rootpath . "/inc/auth.php";
require_once $rootpath . "/inc/func.php";
require_once $rootpath . "/inc/settings.php";
require_once $rootpath . "/inc/language/" . $language . ".php";

$thisfile = basename(__FILE__);

if($n_id >0){
    $resFiles = $db->getAll("select * from {$sqlname}price_files where price_id=?i and identity = ?i", $n_id, $identity);
    foreach ($resFiles as $resFile) {
        $file = $db->getRow("select * from " . $sqlname . "file where fid=?i and identity = ?i", $resFile["file_id"], $identity);
        $localFilesProduct[] = [
            'id' => $resFile["id"],
            'file_id' => $file['fid'],
            'file_name' => $file['ftitle'],
            'datum' => $file['datum'],
            'ftag' => mb_substr($file['ftag'],0,100),
            'size' => (string)$file['size'],
        ];

       // $localFilesProductValue[] = (int)$file['fid'];
    }
}

echo json_encode_cyr($localFilesProduct);