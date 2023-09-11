# trendyol
trendyol api uygulaması
Bu uygulama ile trendyol satıcı paneline  bağlantı yapıp ürün ve sipariş bilgilerini alabilirsiniz
```
$ composer require kolaykod/trendyol
```


```php
$sellerId="";
$apiKey ="";
$apiSecret ="";


 $trendyol = new trendyol($sellerId,$apiKey,$apiSecret);

  $trenyolProducts =    $trendyol->getProduct(["onSale"=>"true","brandIds"=>"58545,8787"]);


```
