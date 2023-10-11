<?php 

include "vendor/autoload.php";

function getTrendyol($companyName=""){
      $companyData = [];
      $companyData["modagetir"]      = array("apiKey"=>"GM9Y2fAEGFyN66HN0qJM","apiSecret"=>"qAMsuiOgLDag1zvZDOHz","partnerId"=>"114603");
      $companyData["irmakhome"]      = array("apiKey"=>"DW477VRYM3siFkp6uXN6","apiSecret"=>"lXIZq8TjMuQzcNpQvCip","partnerId"=>"310498");
      $companyData["rznglobal"]      = array("apiKey"=>"W5mWIXs2MUWbqYwjupA0","apiSecret"=>"r8nW3J8n20s1dZWv35XG","partnerId"=>"223881");
      $companyData["pazarova"]       = array("apiKey"=>"EjsfRge3gighM9dJK1aQ","apiSecret"=>"xgSEEoAyst8htZDquDcS","partnerId"=>"664984");
      $companyData["toridericeket"]  = array("apiKey"=>"O7NdR42uQHkAEzScsCrn","apiSecret"=>"s5iRCsUTFIUQZdabzciD","partnerId"=>"105099");
      $companyData["royalmoni"]      = array("apiKey"=>"qWUtzxGkxwiVGmpiZN7s","apiSecret"=>"1XRAaus2zTLW6yuKfK6j","partnerId"=>"807097");
      $companyData["malistore"]      = array("apiKey"=>"i96Rj0wwNslxZVxUUsid","apiSecret"=>"EBAeQBGMiDoiLmvumcTF","partnerId"=>"309562");
     
     if(!isset($companyData["$companyName"])){  echo "Sistem de bulunmayan firma kodu ile işlem yapmaya çalışıyorsunuz."; exit; }
     $trendyol = new marketPlace\trendyol\trendyol($companyData["$companyName"]);
     return $trendyol;
}




    $trendyol  = getTrendyol("modagetir"); 


    $productCache = new productCache();

    
      $productCache->pageSize  = 5000;
      $productCache->cacheFolder  = __DIR__;
      $productCache->mainCacheJsonFile  = $_ENV["mainCacheFolder"]."/".$sellerId."-".$_ENV["mainCacheFileName"];
      $productCache->extraDataFileLocatio   = __DIR__."/tmp";

   $productCache->deleteAllTempCacheFiles(); //tmp klasörünü boşalt.
  $productCache->deleteAllLogFiles(); //logs klasörünü boşalt.

$get = $trendyol-getProduct(["page"=>$sayfa,"size"=>$size,"approved"=>"true"]); //Api'den toplam ürün sayısını alınıyor.

  $trendyol
if(!isset($get->content)){exit("Bir hata oluştu. ".serialize($get));}


$toplamSayfaSayisi = floor($get->totalElements/$pageSize);
logWrite($get->totalElements." adet ürün ".$toplamSayfaSayisi." sayfada yer alıyor. Pagesize: ".$pageSize);

$sayfaIndex=0;
for($i = 0;$i<=$toplamSayfaSayisi;$i++){
    $url = $cacheGeneratorUrlBase."?page={$sayfaIndex}&size={$pageSize}&sellerId={$sellerId}&fileId={$sayfaIndex}&max={$toplamSayfaSayisi};
    $context = stream_context_create(['http' => ['timeout' => 3]]);
    $req = @file_get_contents($url,false,$context);
    logWrite("İstek gönderildi : ".$url);
    $sayfaIndex++;
}

