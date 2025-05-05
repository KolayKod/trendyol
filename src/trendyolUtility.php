<?php

function replacer($php_data,$ham_text){					
		$php_data = (array) $php_data;
		foreach ($php_data as $key => $value) {                				 
			if(!is_array($value)){		
				$templateVeri[]  = "{{".$key."}}";
				$dinamikVeri[]  = $php_data[$key];
			}				
		}			  	 					
		return str_replace($templateVeri, $dinamikVeri, $ham_text);	 
	}

function getUnixTime($dateTime = "now", $andDate = true) {
    if ($andDate && strpos($dateTime, ":") === false) {
        $dateTime .= " 23:59:59";
    }
    $unixTime = strtotime($dateTime) * 1000;
    return $unixTime;
}

function getRequestUrl($appendPath,$queryData=[]){	  
    $baseUrl = sprintf($this->baseUrl, $this->partnerId);
    $httpQuery = buildHttpQuery($queryData);
    $query = $baseUrl.$appendPath."?".$httpQuery; 	
    return $query;
}

function redirectVariantUrl($contentId,$variantNane,$redirectUrl=false){
	      
	      $url = "https://www.trendyol.com/brand/productname-p-".$contentId;
			        $ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$a = curl_exec($ch);
				$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
				$v = str_replace(",","-",strtolower($variantNane));
	      if($redirectUrl ==true){
		   header("Location:".$finalUrl."?v=".$v);   
	      }else{
		      return $finalUrl;
	      }
   
}
  
  

   function createAddScheme($item, $variants = null, $customData=null)
{
    // Ana ürün bilgilerini oluşturuyoruz
    $mainProduct = [
        'title' => $item->name,
        'brandId' => (int)$item->metaBrand->id,
        'productMainId' => $item->productCode,
        'description' => $item->title,
        'currencyType' => 'TRY',
        'vatRate' => $item->tax,
        'dimensionalWeight' => $item->dimensionalWeight ?? 1,
        'cargoCompanyId' => $item->cargoCompanyId ?? 27,
        'categoryId' => (int)$item->originalCategory->id
    ];

    // Eğer varyantlar belirtilmemişse tüm varyantları kullanıyoruz
    $variants = $variants ?? $item->allVariants;

    $createProduct = [];

    // Her varyant için yeni bir ürün oluşturarak ana ürün bilgileriyle birleştiriyoruz
    foreach ($variants as $variant) {
        $variantData = [
            'quantity' => (int)($variant->quantity ?? 0),
            'stockCode' => $variant->stockCode,
            'barcode' => $variant->barcode,
            'salePrice' => (float)round($variant->salePrice),
            'images' => array_map(function ($image) {
                return ["url" => "https://cdn.dsmcdn.com/" . $image];
            }, $variant->images ?? $item->images),
            'attributes' => array_map(function ($attribute) {
                return [
                    'attributeId' => $attribute->key->id,
                    'attributeValueId' => $attribute->value->id
                ];
            }, $item->attributes)
        ];

        // Ana ürün bilgileri ve varyant bilgilerini birleştirip diziye ekliyoruz
        $createProduct[] = array_merge($mainProduct, $variantData);
    }

    return $createProduct;
}


   function buildHttpQuery($queryData = []) {
      $urlQuery = "";
      if (is_array($queryData) || is_object($queryData)) {
        $urlQuery = http_build_query($queryData);
      }
      return $urlQuery;
  }



   function listPaths(array $array=[], string $parent="", array &$names=[]):array
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



    function getDetailProductData($id){
    $url = "https://apigw.trendyol.com/discovery-web-productgw-service/api/productDetail/" . $id;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode !== 200) {
        throw new Exception("Ürün detayına erişilemiyor. HTTP Kod: " . $httpCode);
    }
    $decodedData = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Geçersiz JSON formatı alındı.");
    }
    return $decodedData;
   }

   function createProductDetail($id){
    $productData = getDetailProductData($id);
      $scheme  =  createAddScheme($productData,$productData->allVariants);
     var_export($scheme);
   }

   




    

  
