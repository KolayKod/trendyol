<?php 

include "vendor/autoload.php";

     $apiKey ="sdfdsf";
     $apiSecret="sdfdsf";
     $partnerId ="114603";
    $trendyol           =  new trendyol($apiKey,$apiSecret,$partnerId);
    $createProductCache = new createProductCache();

    
      $createProductCache->pageSize  = 5000;
      $createProductCache->cacheFolder  = __DIR__;
      $createProductCache->mainCacheJsonFile  = $_ENV["mainCacheFolder"]."/".$sellerId."-".$_ENV["mainCacheFileName"];
      $createProductCache->extraDataFileLocatio   = __DIR__."/tmp";
