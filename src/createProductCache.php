<?php
class createProductCache{

   public $cacheSavePath =__DIR__."resources/cache/trendyol/";
   public $trendyol =null;
   public $cacheGeneratorUrlBase ="https://product-report.inovakobi.com/saveSingleRequest.php";
  

  

  public function allCacheMerge(){

    
  }


   public function getAllCache(){

    
  }

    

   function deleteAllTempCacheFiles(){
       $files = glob($_ENV["mainCacheFolder"].'/tmp/*');
       foreach($files as $file){
         if(is_file($file)) {
           unlink($file);
         }
       }
   }

    public function deleteAllLogAndCacheFiles(){
               $this->deleteAllTempCacheFiles();
               $this->deleteAllLogFiles();
    
  }


   public function deleteAllLogFiles(){

        $files = glob('./logs/*');
       foreach($files as $file){
         if(is_file($file)) {
           unlink($file);
         }
       }
  }

   public function runAllPageRequest(...$arguments){

            $onePageResult = $this->saveSinglePage(...$arguments);
       
         $toplamSayfaSayisi = floor($onePageResult->totalElements/$pageSize);
      
         logWrite($onePageResult->totalElements." adet ürün ".$toplamSayfaSayisi." sayfada yer alıyor. Pagesize: ".$pageSize);
         
         $sayfaIndex=1; // yukarıda 0 inci istek atıldığı  istekler 1 den başlıyor. 
         for($i = 1;$i<=$toplamSayfaSayisi;$i++){

             $url = $this->cacheGeneratorUrlBase."?mod=saveSinglePage&page={$sayfaIndex}&size={$pageSize}&sellerId={$sellerId}&fileId={$sayfaIndex}&max={$toplamSayfaSayisi}";
             $context = stream_context_create(['http' => ['timeout' => 3]]);
             $req = @file_get_contents($url,false,$context);
             logWrite("İstek gönderildi : ".$url);
             $sayfaIndex++;
         }    
  }


  public function saveSinglePage(...$arguments){
              
    $pageResult = $this->recursiveRequest(...$arguments); // birinci istek de kontrollü atılıyor 
     
     //birinci sayfa dan gelen istek  kontrol ediliyor ve kaydedililiyor. 
                    $fileName = md5($arguments);
        if(!isset($pageResult->content)){exit("Bir hata oluştu. ".serialize($pageResult));}

              json::write($pageResult,"$cacheSavePath"."-sayfa-1-". $fileName);

        return $pageResult; // veri doğru geldi ve kayıt işlemi doğru ise true dönecek veya data bilgis olacak.
   }




   
   public function recursiveRequest(...$arguments){

       $get = $this->trendyol->getProduct(...$arguments);
          if(!isset($get->content)){
              logWrite("API: ".$page.". sayfa içeriği getirilemedi. ".serialize($get));
              if($GLOBALS["recursiveRequestTryCount"]<=3){
                  logWrite("Tekrar deneniyor.. Deneme sayısı: ".$GLOBALS["recursiveRequestTryCount"]);
                  $GLOBALS["recursiveRequestTryCount"]++;
                  $get = $this->recursiveRequest();
              }else{
                  logWrite($GLOBALS["recursiveRequestTryCount"]." deneme başarısız oldu, başaramadık abi.");
                  exit($GLOBALS["recursiveRequestTryCount"]." denemeden sonra içerik hala getirilemedi.");
              }
          }
          return $get;
  }


 
   

 
  
}
