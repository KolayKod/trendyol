<?php
/*
sayfa sayfa kayıt işlemi burada gerçekleşecek.
bursda  ham veri kadı olacak. 
yani veri formatı değişmeyecek. 
zaten ön bellek verisi olduğu için 
fazla yer kaplaması önemli değil. 
fakat yine de isteğe bağlı veri kayıt formatında işlemler yapılabilir.

*/

$fileId = $_GET["fileId"]??exit("FileId belirtilmedi.");
$page = $_GET["page"]??exit("Page belirtilmedi.");
$size = $_GET["size"]??exit("Size belirtilmedi.");
$max = $_GET["max"]??exit("Max belirtilmedi.");
$queryString = $_GET["queryString"]??exit("queryString belirtilmedi.");

$fileName = $_ENV["mainCacheFolder"]."/tmp/".$sellerId."-".$fileId."-".$_ENV["mainCacheFileName"];

$sayfaIndex = 0;
$urunIndex = 0;

$categories = array();
$brands = array();

$allProductsList = array();
$start = setTimer();

$tryCount = 0;

 $productCache = new createProductCache();
 $productCache->saveSinglePage(["page"=>$sayfa,"size"=>$size,"approved"=>"true"]); //isteği gelen gelen sonucu kaydediyor. 


if($max==$sayfa){  // istek sayfası son sayfa ise birleştirmeye geç

 try{
    $c = file_get_contents($baseUrl."/cacheMerge.php?createdCacheId=".$fileId."&max=".$max."&sellerId=".$sellerId);
    logWrite($fileId." kodlu cache bildirildi");
}catch (Exception $e){
    logWrite("HATA OLUŞTU: ".$e->getMessage());
}
 
}
