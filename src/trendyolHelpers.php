<?php
class trendyolHelpers{

 
  public function replacer($php_data,$ham_text){					
		$php_data = (array) $php_data;
		foreach ($php_data as $key => $value) {                				 
			if(!is_array($value)){		
				$templateVeri[]  = "{{".$key."}}";
				$dinamikVeri[]  = $php_data[$key];
			}				
		}			  	 					
		return str_replace($templateVeri, $dinamikVeri, $ham_text);	 
	}


  private function getRequestUrl($appendPath,$queryData=[]){	  
    $baseUrl = sprintf($this->baseUrl, $this->partnerId);
    $httpQuery = buildHttpQuery($queryData);
    $query = $baseUrl.$appendPath."?".$httpQuery; 	
    return $query;
  }


  public function buildHttpQuery($queryData = []) {
      $urlQuery = "";
      if (is_array($queryData) || is_object($queryData)) {
        $urlQuery = http_build_query($queryData);
      }
      return $urlQuery;
  }



  public function listPaths(array $array=[], string $parent="", array &$names=[]):array
   {
              
	  foreach($array as $data){
		   if(count($data["subCategories"])>0){
						   if($parent ==""){
							  $name = $data['name'];
						   }else{
							   $name = $parent." | ".$data['name'];          
						   }
				   //$names[] = $name;
				   $names[$data['id']] =null;
			  $this->listPaths($data['subCategories'],$name,$names);                  
						  
		   }else{
				   //$name = $parent." | ".$data['name']."(".$data['id'].")";
				   $name = $data['name'];
				   $names[$data['id']] = $name;  
		   }    
	 }        
	   return $names;
   }

 }


 function redicectImageUrl($imageUrl){

// URL'de geçersiz karakterleri temizlemek için filter_var kullan
        $cleanUrl = filter_var($imageUrl, FILTER_SANITIZE_URL);
            
        // URL'nin geçerli olup olmadığını kontrol et
        if (filter_var($cleanUrl, FILTER_VALIDATE_URL)) {
          header("Location: $cleanUrl", true, 302);
        }

 }


 function barcodeToLink($_requestData){
 $productContentId = $trendyol->getBarcodeToFields($barcode,"productContentId"); //
      header('Location: https://www.trendyol.com/xyz/abc-p-'.$productContentId);   
  }






  function urlToId($url){
        $url = filter_var($url, FILTER_SANITIZE_URL);
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null; 
        }
        $parts = explode('-p-', $url);
        if (isset($parts[1])) {
            if (preg_match('/\d+/', $parts[1], $matches)) {
                return $matches[0]; // Eğer rakam varsa, ID'yi döndür
            }
        }

        return null;
    }


