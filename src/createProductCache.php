<?php
class createProductCache extends trendyol {

   public $cacheSavePath =__DIR__."/cache/trendyol/";
   public $cacheSavePath =__DIR__."/cache/trendyol/";
  

  public function saveOnePageData(...$arguments){
                $productData = $this->getProduct(...$arguments);
        
  }


  public function allCacheMerge(){

    
  }


   public function getAllCache(){

    
  }

    public function deleteAllTempCacheFiles(){

    
  }

    public function deleteAllLogFiles(){

    
  }


   public function deleteAllLogFiles(){

    
  }

   public function runAllPageRequest(...$arguments){

       $onePageResult = $this->recursiveRequest(...$arguments); // birinci istek de kontrollü atılıyor 
           
           //birinci sayfa dan gelen istek  kontrol ediliyor ve kaydedililiyor. 
              if(!isset($onePageResult->content)){exit("Bir hata oluştu. ".serialize($onePageResult));}
           $this->saveArrayToJson($onePageResult);
      1 
         $toplamSayfaSayisi = floor($onePageResult->totalElements/$pageSize);
      
         logWrite($onePageResult->totalElements." adet ürün ".$toplamSayfaSayisi." sayfada yer alıyor. Pagesize: ".$pageSize);
         
         $sayfaIndex=1; // yukarıda 0 inci istek atıldığı  istekler 1 den başlıyor. 
         for($i = 1;$i<=$toplamSayfaSayisi;$i++){
             $url = $cacheGeneratorUrlBase."?page={$sayfaIndex}&size={$pageSize}&sellerId={$sellerId}&fileId={$sayfaIndex}&max={$toplamSayfaSayisi};
             $context = stream_context_create(['http' => ['timeout' => 3]]);
             $req = @file_get_contents($url,false,$context);
             logWrite("İstek gönderildi : ".$url);
             $sayfaIndex++;
         }    
  }






   
   public function recursiveRequest(...$arguments){
       $get = $this->getProduct(...$arguments);
          if(!isset($get->content)){
              logWrite("API: ".$page].". sayfa içeriği getirilemedi. ".serialize($get));
              if($GLOBALS["tryCount"]<=3){
                  logWrite("Tekrar deneniyor.. Deneme sayısı: ".$GLOBALS["tryCount"]);
                  $GLOBALS["tryCount"]++;
                  $get = tryGetRequest();
              }else{
                  logWrite($GLOBALS["tryCount"]." deneme başarısız oldu, başaramadık abi.");
                  exit($GLOBALS["tryCount"]." denemeden sonra içerik hala getirilemedi.");
              }
          }
          return $get;
  }


  public   function saveArrayToJson($data,$fileName,$isGzip=false){
    if($isGzip){
        $data = gzencode(json_encode($data), 9);
    }else{
        $data = json_encode($data);
    }
    $put = file_put_contents($fileName, $data);
    if($put){
        return $fileName;
    }else{
        logWrite("saveArrayToJson çalıştırılamadı : ".serialize($put));
        return false;
    }
}

   

 
  
}
