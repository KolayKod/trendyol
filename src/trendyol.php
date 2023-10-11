<?php namespace marketPlace\trendyol;
   // Versiyon: 0.4 //11-09-2023

   /*
    api dönüş türleri arasında ki ortak noktalar tepit edilecek
	buna göre hata yakalama ve loglama işlemi yapılacak
   */
  class trendyol{
	//const facade = 'tendyol'; 
    private $baseUrl      = "https://api.trendyol.com/sapigw/suppliers/%s";
    public $apiKey    = "";   
    public $apiSecret   = "";
    public $partnerId   = "";
	
    public $curl         = null;
    public $result       = null;
    public $query        = null;
    public $requestData  = null;
    public $version      = 2;  
    public $getinfo      = 2;
	
	
	public function setOption($params=[]){

		if(!empty($params)){
			//foreach($params as $key => $value) {$this->$key = $value;}
	
			$this->apiKey      = $params["apiKey"]??"";
			$this->apiSecret      = $params["apiSecret"]??"";
			$this->partnerId = $params["partnerId"]??"";

		
		  }
		return $this;
	}
	
	private function setRequestUrl($appendPath,$getQueryData=[]){
	    
            if(is_array($getQueryData) && count($getQueryData)>0 ){
            			$query = http_build_query($getQueryData);
            		 }
           		 		 
	    	$baseUrl = sprintf($this->baseUrl, $this->partnerId);	
	    $this->query= 	$baseUrl.$appendPath."?".$query;	    		 
   } 
   
	
	
	public function setApiData($partnerId,$apiKey,$apiSecret){

			$this->apiKey      = $apiKey??"";
			$this->apiSecret  = $apiSecret??"";
			$this->partnerId = $partnerId??"";

		$this->baseUrl      = sprintf($this->baseUrl, $this->partnerId);
		return $this; 
	}
	
	 
	
	    
	public function __construct(array $options = []) {
	    if (!empty($options)) {
	        $this->setOption($options);
	    }
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
			  $product->brandId          = (integer) $brandId; 
              
			  $product->quantity          = (integer) $systemData->quantity;
              $product->stockCode         = "OTM-".$systemData->stockCode;
              $product->dimensionalWeight = 1;
              $product->description       = $systemData->name;
              $product->currencyType      = "TRY";
                   
              //$product->listPrice        = (float)round($systemData->salePrice*1.38);
              $product->salePrice        = (float)round($systemData->salePrice);
              $product->vatRate         = $systemData->tax;
              $product->cargoCompanyId    = 27;
              $product->images =$image;
              
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

	 

	public function createProduct($requestData =""){

	
		$this->setRequestUrl("/v2/products");
		$this->requestData  = $requestData;
		$result_curl 		 = $this->sendRequest("POST");
		return $this->result = json_decode($result_curl);
   } 
   
   
   
   public function updateProduct($requestData =""){

	   		
		 $this->setRequestUrl("/products");
		$this->requestData  = $requestData;
		$result_curl 		 = $this->sendRequest("PUT");
		return $this->result = json_decode($result_curl);
   } 

	
   public function getProduct(array $filter =[]){
	   
	   //dateQueryType =CREATED_DATE , LAST_MODIFIED_DATE 
	   
	   $queryData = [
				'approved'       => null,
				'barcode'     => null,
				'startDate'    => null,
				'endDate'       => null,
				'page'          => null,
				'dateQueryType'          => null,
				'size'          => null,
				'supplierId'          => null,
				'stockCode'          => null,
				'archived'          => null,
				'productMainId'          => null,
				'onSale'          => null,
				'rejected'          => null,
				'blacklisted'          => null,
				'brandIds'          => null,
			];
			if(is_array($filter) and !is_null($filter)){
				$queryData = array_merge($queryData, $filter);
			}

	   
	    $this->setRequestUrl("/products",$queryData);
      $resultCurl = $this->sendRequest();
      /* verinin json doğrulaması yapılacak*/
	   return $this->result = json_decode($resultCurl);       
   } 
   
   public function productCacheCreate(array $queryData =[]){     
        $products =   $this->getProduct($queryData);
		foreach($products as $product ){ }
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
					
                     $return[$product["productContentId"]] =  array_merge($return[$product->productContentId], $productMain);
	      }
	   return $return;
   } 

   public  function filterVariantAttributes($attributeData){
        
		$return =(object)[];
	$idToValue = ["47"=>"color","338"=>"size","92"=>"boyutEbat"];
	//  $idToValue = ["47","338","92"];
	
	foreach($attributeData as $attribute){
		
			if(isset($idToValue[$attribute->attributeId])){
				
				$return->{$idToValue[$attribute->attributeId]} = $attribute->attributeValue;
			}
	}
	
	return  $return; 
	}

   public function updatePriceAndInventory($requestData =""){
       
	          $this->setRequestUrl("/products/price-and-inventory");
		

		$this->requestData   = json_encode($requestData);
		$result_curl          = $this->sendRequest("POST");
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
	    
	    
	    
	    $this->setRequestUrl("/products/batch-requests/$batchRequestId");
	    
		$result_curl = $this->sendRequest();
		return $this->result = json_decode($result_curl); 
   }

  
	public function getCategories(){ 

		if(!file_exists('trendyolCategory.json') ){
           $this->query = "https://api.trendyol.com/sapigw/product-categories";
			$result_curl = $this->sendRequest();
			file_put_contents('trendyolCategory.json', $result_curl);
		}
		return  $this->result = json_decode(file_get_contents("trendyolCategory.json"),true);
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
                    
		 }elseif( isset($returnData->status) && ($returnData->status ==404) ){

			echo  $logMessage =  "404 $returnData->message İşlem de hata mevcut \n";
			// Logger::report('trendyolGetOrder',$logMessage,"trendyolGetOrder", '10 month');
			 exit;

		 }elseif(isset($returnData->errors)){
		     
			 echo "İşlem de hata mevcut</br>";
			 print_r($returnData->errors);
		
			 exit;
		 }elseif(isset($returnData->error)){
		     
			 echo "İşlem de hata mevcut</br>";

			 print_r($returnData->error);
		//	 Logger::report('trendyolGetOrder',$returnData->errors["0"]->message,"trendyolGetOrder", '10 month');
			 exit;
		 }
       
       
   }
   
    
   
   public function queryFormetter($query_data=null){
	   
	     $query ="";
	     if(is_array($query_data) && count($query_data)>0 ){
			$query = http_build_query($query_data);
		 }

	   return $query;
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
   
      public function redirectVariantUrl($contentId,$variantNane,$redirectUrl=false){
	      
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
    

	public function sendRequest($method = "GET"){
	    
	  

		$header = [];
		$header[] = "Authorization: Basic ".base64_encode($this->apiKey.":".$this->apiSecret);
		$header[] = "Content-Type: application/json";
		$header[] = "User-Agent: $this->partnerId - SelfIntegration";

		if ($method == 'POST') { $header[] = "Content-Length: " . strlen($this->requestData); }

		$this->curl = curl_init(); 

		curl_setopt($this->curl, CURLOPT_URL,$this->query);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header); 
		
		if ($method == 'POST') { curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->requestData); }	
			
		$this->result = curl_exec($this->curl);
		$this->getinfo = curl_getinfo($this->curl);
		
		
		if ($this->result === false) {
           $this->result = curl_error($this->curl);
        }

		//print_r($this->getinfo);
		 return $this->result;  
		 curl_close($this->curl);
	}



    function __destruct(){
		
		
		/* if (curl_errno($this->curl)) {
			 echo 'Hata Meydana Geldi:' . curl_error($this->curl);
		 }

      curl_close($this->curl); */

    }

  }



