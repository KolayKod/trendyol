<?php
class createProductCache   {

  
 public function mergeProductsFromJson($data){

   
 }

public function saveTempData($data){

   
 }

  public function saveMergedProductList($data){

   
 }

 


  public  function collectData($type, $id, $name, $isSet) {
    $data = $GLOBALS[$type];
    if (isset($data[$id])) {
        $varCount = $data[$id]["variantCount"] ?? 1;
        $prodCount = $data[$id]["productCount"] ?? 1;
        $data[$id]["variantCount"] = $varCount + 1;
        if (!$isSet) {$data[$id]["productCount"] = $prodCount + 1;}
    } else {
        $data[$id] = array(
            ($type === "brands" ? "brandId" : "pimCategoryId") => $id,
            ($type === "categories" ? "brandName" : "categoryName") => $name,"productCount" => 1,"variantCount" => 1
        );
    }
    $GLOBALS[$type] = $data;
    return $id;
}


    
  }
