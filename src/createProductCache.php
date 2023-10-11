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
