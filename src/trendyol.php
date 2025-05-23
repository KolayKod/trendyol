<?php namespace trendyol;
use trendyolHelpers;
// Versiyon: 0.5 = 05-05-2025 19:13

   /*
 
	(new trendyol("114603","sdfgsf","643545"))->getorder();
	trendyol::getOrder();

   */
  class trendyol{
	//const facade = 'tendyol'; 
    private $partnerId   = null;
    private $apiKey      = null;   
    private $apiSecret   = null;
    private $baseUrl      = "https://apigw.trendyol.com/sapigw/suppliers/%s";
	
  
    public $result       = null;
    public $query        = null;
    public $requestData  = null;
    public $version      = 2;  
    public $getinfo      = 2;
	
	
    public function setOption($params=[]){

		if(!empty($params)){
			$this->apiKey     = $params["apiKey"]??"";
			$this->apiSecret  = $params["apiSecret"]??"";
			$this->partnerId  = $params["partnerId"]??"";
		  }
		return $this;
    }

	public function __construct(array $options = []) {
	    if (!empty($options)) {
	        $this->setOption($options);
	    }
	}
	
	
   
	
	
	public function setApiData($partnerId,$apiKey,$apiSecret){

		    $this->partnerId   = $partnerId??"";
		    $this->apiKey      = $apiKey??"";
			$this->apiSecret   = $apiSecret??"";
		$this->baseUrl      = sprintf($this->baseUrl, $this->partnerId);
		return $this; 
	}
	
	 
	
	    
	


	
	

	public function __call($variable,$pars){
	    
		$newQuery = $this->query.$variable;
		$result_curl = $this->sendRequest();
		return $this->result = json_decode($result_curl,1);
	}
	
   public static function create(string $user, string $pass, string $supplier_id)
    {
        return new static($user, $pass, $supplier_id);
    }
	  
	
	
	public function orderProductSchemeConvert($systemData)
	{
		return $this->result = json_decode($systemData);       
	}

	public function productSchemeConvert($systemData){

		$product = (object)null;
		$createProduct = null;

		if(!is_array($systemData)){
        
			  $product->title            = $systemData->title; 
			  $product->brandId          = (integer)$systemData->brandId; 
              
			  $product->quantity          = (integer) $systemData->quantity??0;
              $product->stockCode         = $systemData->stockCode;
              $product->dimensionalWeight = $systemData->dimensionalWeight??1;
              $product->description       = $systemData->name;
              $product->currencyType      = "TRY";
                   
              //$product->listPrice        = (float)round($systemData->salePrice*1.38);
              $product->salePrice        = (float)round($systemData->salePrice);
              $product->vatRate         = $systemData->tax;
              $product->cargoCompanyId    = $systemData->cargoCompanyId??27;
              $product->images =$systemData->images;
              
              if(isset($systemData->categoryid )){
				$product->categoryId = $systemData->categoryid;
			  }elseif(isset($systemData->categoryName )){
				$product->categoryId  = (integer) getTrendyolCategoryIdByName($systemData->categoryName); //"419"; //$product->category->id;
			  }
              
			  $createProduct[0] = $product;
			  
		}else{

			foreach($systemData  as  $systemDataSingleKey=>$systemDataSingleValue){
			    
				$createProduct[$systemDataSingleKey] =	$this->productSchemeConvert($systemDataSingleValue);
			}
			  
		}
		      		
		return  json_decode($createProduct);       
	} 

	

    public function getTrendyolCategoryIdByName($categoryName){
		$trendyolCategories = $this->getAllCategories();
		if (!isset($trendyolCategories['categories'])) {
			throw new Exception("'categories' anahtarı eksik.");
		}
		$categories = $trendyolCategories['categories'];
		$listPaths = $this->listPaths($categories);
		$keys = $this->arrayFind($categoryName, $listPaths);
		return $keys[0] ?? "";
	}




	public function createProduct($items =[]){
            
		
		$this->setRequestUrl("/v2/products");
		$this->requestData  = ['items'=>$items];
		$result_curl 		 = $this->sendRequest("POST");
		return $this->result = json_decode($result_curl);
   } 
   
   
   
   public function updateProduct($requestData =""){
	$this->requestData  = $requestData;	
	       $requestUrl  = $this->getRequestUrl("/products");
	      $result_curl  = $this->sendRequest($requestUrl,"PUT",$requestData);
	return $this->result = json_decode($result_curl);
   } 

	
   public function getProduct(array $filter =[]){
	    // burada parsametre kontrolü olabilir yanlış parametre girişlerini önlemek için.
	   //dateQueryType =CREATED_DATE , LAST_MODIFIED_DATE 
	   $queryDataKeys = [
					'approved', 'barcode', 'startDate', 'endDate', 'page',
					'dateQueryType', 'size', 'supplierId', 'stockCode', 'archived',
					'productMainId', 'onSale', 'rejected', 'blacklisted', 'brandIds'
				];
	
	$queryData = array_fill_keys($queryDataKeys, null);

	   
			if(is_array($filter) and !is_null($filter)){
				$queryData = array_merge($queryData, $filter);
			}

	   
	    $this->setRequestUrl("/products",$queryData);
      $resultCurl = $this->sendRequest();
     $resultData =  json_decode($resultCurl);
     
	     if(isset($resultData->errors)){
		    throw new Exception("trendyol veri getirme hatası: " . var_export($resultData->errors,true));
		}
 

	   return $this->result = json_decode($resultData);       
   } 
   
 
   public function productGroupByModelCode($partnerProducts =""){
                            
	       $return   =[];
	    $productMain =[];
	      foreach( $partnerProducts as  $product){
	                
		      $productMain["title"] =$product->title;
		      $productMain["vatRate"] =$product->vatRate;
		      $productMain["images"] =$product->images;
		      $productMain["image"] =$product->images[0]->url;
		      $productMain["description"] =$product->description;
		       $productMain["brand"] =$product->brand;
		       $productMain["categoryName"] =$product->categoryName;
		       $productMain["pimCategoryId"] =$product->pimCategoryId;
		       $productMain["brandId"] =$product->brandId;
		       $productMain["productContentId"] =$product->productContentId;
		       $productMain["productMainId"] =$product->productMainId;
	                   /*
                        $return["brands"][$product->brandId] =array("brandName"=>$product->brand,"brandId"=>$product->brandId);
                        $return["categories"][$product->pimCategoryId] =array("categoryName"=>$product->categoryName,"categoryId"=>$product->pimCategoryId);
                        $return["totalQuantity"]  += $product->quantity; 
                        $return["totalVariant"]   += 1; 
		          */   
                        $return[$product->productContentId]["totalQuantity"]   += $product->quantity; 
                        $return[$product->productContentId]["totalVariant"]   += 1; 
	                $return[$product->productContentId]["variants"][$product->productCode] = [
													"barcode"=>$product->barcode,
													"salePrice"=>$product->salePrice,
													"listPrice"=>$product->listPrice,
													"quantity"=>$product->quantity,
													"stockCode"=>$product->stockCode,
													"productCode"=>$product->productCode,
													"lastPriceChangeDate"=>$product->lastPriceChangeDate,
													"lastStockChangeDate"=>$product->lastStockChangeDate,
													"stockId"=>$product->stockId,
													"onSale"=>$product->onSale,
													"locked"=>$product->locked,
													"approved"=>$product->approved,
													"blacklisted"=>$product->blacklisted,
													"productContentId"=>$product->productContentId,
													"stockUnitType"=>$product->stockUnitType,
													"attributes"=>$this->filterVariantAttributes($product->attributes),
													];
					
                     $return[$product->productContentId] =  array_merge($return[$product->productContentId], $productMain);
	      }
	   return $return;
   } 

   public  function filterVariantAttributes($attributeData){
	$return =(object)[];
	$idToValue = [
					"47"=>"color",
					"338"=>"size",
					"92"=>"boyutEbat"
				];
	foreach($attributeData as $attribute){		
			if(isset($idToValue[$attribute->attributeId])){
				$return->{$idToValue[$attribute->attributeId]} = $attribute->attributeValue;
			}
	}
	return  $return; 
	}

   public function updatePriceAndInventory($requestData =""){
	        $requestUrl   = $this->getRequestUrl("/products/price-and-inventory");
		$postData     =json_encode($requestData);
		$result_curl  = $this->sendRequest($requestUrl,"POST",$postData);
	return $this->result = json_decode($result_curl);
		/*
		Burada bir hata kontrolü yok 
		veriler de hata olup olmadığını anlaamk için
		hata tespit ve loglaam işlemi yapmak gerekiyor.
		   bütün hata tesip ve loglamalar tek fonksiyon da birleştirilebilir. 
		   dönüş formatları aynı olduğu sürece sorun yok <div class=""></div>
		*/       
   }
	
	
	
  public function batchRequestData($batchRequestId =""){ 
	   $requestUrl  = $this->getRequestUrl("/products/batch-requests/$batchRequestId");
		$result_curl = $this->sendRequest($requestUrl);
		return $this->result = json_decode($result_curl); 
   }

  
   public function getAllCategories($cache = true)
   {
	  // _stroga_dir_."platforms/trendyol/trendyolCategory.json";
	   $jsonFile = 'trendyolCategory.json';
	   if (!$cache || !file_exists($jsonFile)) {
		   $url = "https://api.trendyol.com/sapigw/product-categories";
		   $result_curl = $this->sendRequest($url);
		   
		   $decoded_result = json_decode($result_curl, true);   
		   if (json_last_error() === JSON_ERROR_NONE) {
			   file_put_contents($jsonFile, $result_curl);
		   } else {
			   throw new Exception("Geçersiz JSON formatı alındı.");
		   }
		   return $decoded_result;
	   }
	   $jsonFileData = file_get_contents($jsonFile);
	   //$jsonFileData['categories'] doğrudan categories verisini dönebiliriz.
	   return json_decode($jsonFileData, true);
   }

	
   public function getCategoryAttributes( int $id,$onlyRequired=false)
   {
	  
		   $url = "https://apigw.trendyol.com/sapigw/product-categories/${id}/attributes";
		   $response = $this->sendRequest($url);
		   
		   $attributes = json_decode($response); 

		   if (json_last_error() !== JSON_ERROR_NONE) {
				throw new Exception("Geçersiz JSON formatı alındı: " . json_last_error_msg());
			}

         
			 if(issets($attributes->categoryAttributes)){

		        if ($onlyRequired) {
					return array_filter($attributes->categoryAttributes, function($attribute) {
						return $attribute->required === true;
					});
				}else{

					$attributes->categoryAttributes;
				}

			 }else{
                return $attributes;
			 }


		   
   }


	public function getBarcodeToField($barcode, $getField)
	{
		// productMainId de kullanım için opsiyon koyabiliriz.
		$result = $this->getProduct(["barcode" => $barcode]);
	
		// Eğer ürün bulunduysa
		if (isset($result->totalElements) && $result->totalElements > 0) {
			$product = $result->content[0];
	
			// Talep edilen alanı döndürmek için
			switch ($getField) {
				case 'modelCode':
					return $product->modelCode;  // Model kodu döner
				case 'imageUrl':
					return $product->images[0]->url;  // İlk resim URL'sini döner
				case 'title':
					return $product->title;  // Ürün başlığını döner
				case 'link':
					return $product->link;  // Ürün linkini döner
				case 'contentid':
					return $product->contentId;  // İçerik ID'sini döner
				default:
					return false;
			}
		}
	
		return "No product found.";  // Ürün bulunamazsa
	}
    

	
   
   public function getOrder($query_data=[]){	
	    // //status=Created&orderByDirection=DESC&size=10   
			$return =null;
			
		
			$this->setRequestUrl("/orders",$query_data);
			   // exit($this->query);
			
			$result_curl  = $this->sendRequest("GET");  
			$returnData   = json_decode($result_curl);
		  
		  
		   
		     if($this->errorControl($returnData)){
		         
		       return  $returnData->content;
		     }else{
		         
		         
		          stop_r($returnData);
		     }

   } 
   
   public function errorControl($returnData){
       if(!isset($returnData->errors) && !isset($returnData->error) && isset($returnData->content) ){
                    return true;
	}else( isset($returnData->status) && ($returnData->status ==404) ){
			 return false;
	} 

   }
   

	
		
	public function searchBrands($searchName) {
		$url = "https://apigw.trendyol.com/sapigw/brands/by-name?name=" . urlencode($searchName);
		$response = $this->sendRequest($url);
		$data = json_decode($response, true);
		
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \Exception("Invalid JSON response received");
		}
		
		return $data ?: null;
	}
   

       
	public function getBrandIdByName($exactBrandName) {
		try {
			$brands = $this->searchBrands($exactBrandName);
			
			if (!$brands) {
				return null;
			}
			
			foreach ($brands as $brand) {
				if (strtolower($brand['name']) === strtolower($exactBrandName)) {
					return (int)$brand['id'];
				}
			}
			
			return null;
		} catch (\Exception $e) {
			return null;
		}
	}


      
   
      
    

	public function sendRequest($url,$method = "GET",$requestData=""){
	    
	  

		$header = [];
		$header[] = "Authorization: Basic ".base64_encode($this->apiKey.":".$this->apiSecret);
		$header[] = "Content-Type: application/json";
		$header[] = "User-Agent: $this->partnerId - SelfIntegration";

		if ($method == 'POST') { $header[] = "Content-Length: " . strlen($this->requestData); }

		$this->curl = curl_init(); 

		curl_setopt($this->curl, CURLOPT_URL,$url);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header); 
		
		if ($method == 'POST') { curl_setopt($this->curl, CURLOPT_POSTFIELDS, $requestData); }	
		if ($method == 'PUT') { curl_setopt($this->curl, CURLOPT_POSTFIELDS, $requestData); }	
			
		$this->result = curl_exec($this->curl);
		$this->getinfo = curl_getinfo($this->curl);
		if ($this->result === false) {
	             $this->result = curl_error($this->curl);
	        }

                  curl_close($this->curl);
		 return $this->result;  	 
	}



    function __destruct(){
		
		
		/* if (curl_errno($this->curl)) {
			 echo 'Hata Meydana Geldi:' . curl_error($this->curl);
		 }

      curl_close($this->curl); */

    }

  }



