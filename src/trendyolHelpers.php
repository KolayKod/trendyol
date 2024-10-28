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


  public function getRequestUrl($appendPath,$queryData=[]){	  
    $baseUrl = sprintf($this->baseUrl, $this->partnerId);
    $httpQuery = buildHttpQuery($queryData);
    $query = $baseUrl.$appendPath."?".$httpQuery; 	
    return $query;
  }


  

  public function createProductSchemeFromDetailData($item, $variants = null, $customData)
{
    // Ana ürün bilgilerini oluşturuyoruz
    $mainProduct = [
        'title' => $item->title,
        'brandId' => (int)$item->metaBrand->id,
        'productCode' => $item->productCode,
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
barcode
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

   public function redicectImageUrl($imageUrl){

    // URL'de geçersiz karakterleri temizlemek için filter_var kullan
            $cleanUrl = filter_var($imageUrl, FILTER_SANITIZE_URL);
                
            // URL'nin geçerli olup olmadığını kontrol et
            if (filter_var($cleanUrl, FILTER_VALIDATE_URL)) {
              header("Location: $cleanUrl", true, 302);
            }

    }


    public function barcodeToLink($_requestData){
    $productContentId = $trendyol->getBarcodeToFields($barcode,"productContentId"); //
          header('Location: https://www.trendyol.com/xyz/abc-p-'.$productContentId);   
      }

      public function urlToId($url){
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





 }


    

  
