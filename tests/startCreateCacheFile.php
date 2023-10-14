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

 $companyName = $_GET["companyName"]??"modagetir";
 $page = $_GET["page"]??"modagetir";
 $size = $_GET["size"]??"5000";


    $trendyol  = getTrendyol($companyName); 


    $productCache = new createProductCache();

    
      $productCache->pageSize  = 5000;
      $productCache->cacheFolder  = __DIR__;
      $productCache->mainCacheJsonFile  = $_ENV["mainCacheFolder"]."/".$sellerId."-".$_ENV["mainCacheFileName"];
      $productCache->extraDataFileLocatio   = __DIR__."/tmp";

   $productCache->deleteAllLogAndCacheFilesFiles(); //tmp klasörünü boşalt. logs klasörünü boşalt.
  $productCache->runAllPageRequest(["page"=>$page,"size"=>$size,"approved"=>"true"]); //1. istek atılıyor, kaydediliyor ve diğer istekler için url tetikleniyor. 




