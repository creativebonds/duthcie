<?php

require plugin_dir_path( __FILE__ ) . '../vendor/autoload.php';
use GraphQL\Variable;
use GraphQL\Client;
use GraphQL\Query;

class dutchie{
    
   
    
    public $client=null;
    public $query = array();
    public $retailerId='';
    public $checkout = null;
    public $dispensary='';
    public $dutchie_api_url = '';
    public $dutchie_api_public_key = '';
    
    public function __construct() {
        $dutchie_settings = get_option( 'dutchie_option_name' );
        $this->retailerId = $dutchie_settings['dutchie_retailer_id'];
        $this->dispensary = $dutchie_settings['dutchie_dispensary_id'];
        $this->dutchie_api_url = $dutchie_settings['dutchie_api_url'];
        $this->dutchie_api_public_key = $dutchie_settings['dutchie_api_public_key'];
    }
    public function auth(){
        $this->client = new Client(
            $this->dutchie_api_url,
            ['Authorization' => 'Bearer '.$this->dutchie_api_public_key]
        );
    }

    public function getRetailers(){
        $gql = (new Query('Retailer'))
        ->setSelectionSet(
            [
                'id',
                'name'
            ]
        );
        if(is_null($this->client)){
            $this->auth();
        }
        return $this->client->runQuery($gql);
    }
    public function getMenu(){
        $gql = (new Query('menu'))
        ->setVariables([new Variable('retailerId', 'ID', true)])
        ->setArguments(['retailerId' => '$retailerId'])
        ->setSelectionSet([
            (new Query('products'))->setSelectionSet([
                'id',
                'name',
                'image',
                'description',
                (new Query('images'))->setSelectionSet([
                    'id','url','label','description'
                ])
            ])
        ]);
        if(is_null($this->client)){
            $this->auth();
        }
        
        try {
            $variablesArray = ['retailerId' => '57a01b5e-005a-44f3-80c6-893fdd8a3615'];
            return $this->client->runQuery($gql, true, $variablesArray);
        }
        catch (GraphQL\Exception\QueryError $exception) {
        
            // Catch query error and desplay error details
            print_r($exception->getErrorDetails());
            exit;
        }
    }
    
    public function getMenuraw($args = array(),$extra=false){

        $filters = array();
        $menuType='';
        if(isset($args['category'])){
            $filters[]='category:'.$args['category'];
        }
        if(isset($args['weight'])){
            if(is_array($args['weight'])){
                $w=array();
                foreach($args['weight'] as $ww){
                    $w[]='"'.$ww.'"';
                }
                $filters[]='weights:['. implode(',', $w).']';
            }else{
                $filters[]='weights:"'.$args['weight'].'"';
            }
            
        }
        if(isset($args['brandId'])){
            $filters[]='brandId:"'.$args['brandId'].'"';
        }
        if(isset($args['strainType'])){
                $filters[]='strainType:'.$args['strainType'];
        }
        if(isset($args['effects'])){
            if(is_array($args['effects'])){
                $filters[]='effects:['.join(',',$args['effects']).']';
            }else{
                $filters[]='effects:['.$args['effects'].']';
            }
            
        }
        if(isset($args['subcategory'])){
            $filters[]='subcategory:'.$args['subcategory'];
        }
        if(isset($args['subcategory'])){
            $filters[]='menuSection:{type: CUSTOM_SECTION, name:"'.$args['subcategory'].'"}';
        }
        if(isset($args['specialid'])){
            $filters[]='menuSection:{type: SPECIALS, specialId:"'.$args['specialid'].'"}';
        }
        if(isset($args['cbd'])){
            $cbd = explode(',',$args['cbd']);
            if(count($cbd)==2){
                $filters[]='potencyCbd: { min: '.$cbd[0].', max: '.$cbd[1].', unit: PERCENTAGE }';
            }
        }
        if(isset($args['thc'])){
            $thc = explode(',',$args['thc']);
            if(count($thc)==2){
                $filters[]='potencyThc: { min: '.$thc[0].', max: '.$thc[1].', unit: PERCENTAGE }';
            }
        }
        if(isset($args['menuType'])){
            $menuType='menuType:'.$args['menuType'];
        }
        
        
    

        $filter='';
        if($filters){
            $filter.='filter:{';
                foreach($filters as $f){
                    $filter.=$f.PHP_EOL;
                }
            $filter.='}';    
        }
        $offset=0;
        $limit=12;
        if(isset($args['offset'])){
            $offset=$args['offset'];
        }
        if(isset($args['limit'])){
            $limit=$args['limit'];
        }
        $pagination='pagination:{ offset: '.$offset.', limit: '.$limit.' }';
        $sortby='';
        if(isset($args['sort'])){
            $sort = explode('_',$args['sort']);
            if(count($sort)==2){
                $sortby='sort: { direction: '.$sort[1].', key: '.$sort[0].' }';
            }
        }
        $gql = <<<QUERY
                fragment terpeneFragment on Terpene {
                    aliasList
                    aromas
                    description
                    effects
                    id
                    name
                    potentialHealthBenefits
                    unitSymbol
                  }

                  fragment activeTerpeneFragment on ActiveTerpene {
                    id
                    terpene {
                      ...terpeneFragment
                    }
                    name
                    terpeneId
                    unit
                    unitSymbol
                    value
                  }

                  fragment activeCannabinoidFragment on ActiveCannabinoid {
                    cannabinoidId
                    cannabinoid {
                      description
                      id
                      name
                    }
                    unit 
                    value
                  }
                    fragment brandFragment on Brand {
                        id
                        name
                  }
                  fragment productFragment on Product {
                    brand {
                      description
                      id
                      imageUrl
                      name
                    }
                    category
                    description
                    descriptionHtml
                    effects
                    id
                    productBatchId
                    image
                    images {
                      id
                      url
                      label
                      description
                    }
                    name
                    posId
                    potencyCbd {
                      formatted
                      range
                      unit
                    }
                    potencyThc {
                      formatted
                      range
                      unit
                    }
                    slug
                    staffPick
                    strainType
                    subcategory
                    variants {
                      id
                      option
                      priceMed
                      priceRec
                      specialPriceMed
                      specialPriceRec
                    }
                    terpenes {
                      ...activeTerpeneFragment
                    }
                    cannabinoids {
                      ...activeCannabinoidFragment
                    }
                  }

        
            query {
            menu( 
                retailerId:"$this->retailerId"
                $menuType
                $filter
                $pagination
                $sortby
                ) 
                {
                    weights
                    brands {
                        ...brandFragment
                    }
                    products {
                    ...productFragment
                    }
                }
            }
        QUERY;
        if(is_null($this->client)){
            $this->auth();
        }
        try {
            if($extra){
                return $this->client->runRawQuery($gql, false)->getData()->menu;
            }else{
                return $this->client->runRawQuery($gql, false)->getData()->menu->products;
            }
            
        }
        catch (GraphQL\Exception\QueryError $exception) {

            print_r($exception->getErrorDetails());
            exit;
        }
    }
    public function getProduct($productId){

        $gql = <<<QUERY
       
        fragment terpeneFragment on Terpene {
                    aliasList
                    aromas
                    description
                    effects
                    id
                    name
                    potentialHealthBenefits
                    unitSymbol
                  }

                  fragment activeTerpeneFragment on ActiveTerpene {
                    id
                    terpene {
                      ...terpeneFragment
                    }
                    name
                    terpeneId
                    unit
                    unitSymbol
                    value
                  }

                  fragment activeCannabinoidFragment on ActiveCannabinoid {
                    cannabinoidId
                    cannabinoid {
                      description
                      id
                      name
                    }
                    unit 
                    value
                  }
            query {
            product( 
                retailerId:"$this->retailerId"
                id:"$productId"
                ) 
                {
                    brand {
                        id
                        name
                      }
                      category
                      description
                      descriptionHtml
                      id
                      image
                      images {
                        id
                        url
                        label
                        description
                      }
                      name
                      posId
                      effects
                      potencyCbd {
                        formatted
                        range
                        unit
                      }
                      potencyThc {
                        formatted
                        range
                        unit
                      }
                      staffPick
                      strainType
                      subcategory
                      variants {
                        id
                        option
                        priceMed
                        priceRec
                        specialPriceMed
                        specialPriceRec
                      }
                    terpenes {
                      ...activeTerpeneFragment
                    }
                    cannabinoids {
                      ...activeCannabinoidFragment
                    }
                }
            }
        QUERY;
        if(is_null($this->client)){
            $this->auth();
        }
        
        try {
            return $this->client->runRawQuery($gql, false)->getData()->product;
        }
        catch (GraphQL\Exception\QueryError $exception) {
        
            // Catch query error and desplay error details

            //print_r($exception->getErrorDetails());
            return false;
        }
    }
    public function getOrders($menu=''){
        $menuType='';
        if(!empty($menu)){
            $menuType='menuType:'.$menu;
        }
        $gql = <<<QUERY


            fragment terpeneFragment on Terpene {
              aliasList
              aromas
              description
              effects
              id
              name
              potentialHealthBenefits
              unitSymbol
            }

            fragment activeTerpeneFragment on ActiveTerpene {
              id
              terpene {
                ...terpeneFragment
              }
              name
              terpeneId
              unit
              unitSymbol
              value
            }

            fragment activeCannabinoidFragment on ActiveCannabinoid {
              cannabinoidId
              cannabinoid {
                description
                id
                name
              }
              unit 
              value
            }

            fragment productFragment on Product {
              brand {
                description
                id
                imageUrl
                name
              }
              category
              description
              descriptionHtml
              effects
              id
              productBatchId
              image
              images {
                id
                url
                label
                description
              }
              name
              posId
              potencyCbd {
                formatted
                range
                unit
              }
              potencyThc {
                formatted
                range
                unit
              }
              slug
              staffPick
              strainType
              subcategory
              variants {
                id
                option
                priceMed
                priceRec
                specialPriceMed
                specialPriceRec
              }
              terpenes {
                ...activeTerpeneFragment
              }
              cannabinoids {
                ...activeCannabinoidFragment
              }
            }

            fragment orderFragment on Order {
              createdAt
              customerId
              delivery
              dispensaryName
              foreignId
              id
              items {
                option
                price
                product {
                  ...productFragment
                }
                productId
                quantity
                subtotal
              }
              medical
              metadata
              orderNumber
              pickup
              recreational
              reservationDate {
                startTime
                endTime
              }
              status
              subtotal
              tax
              total
            }



                query OrderQuery{
                orders( 
                    retailerId:"$this->retailerId"
                ) {
                    orders {
                    ...orderFragment
                    }
                }
                }
            
        QUERY;
        if(is_null($this->client)){
            $this->auth();
        }
        
        try {
            return $this->client->runRawQuery($gql, false)->getData()->orders->orders;
        }
        catch (GraphQL\Exception\QueryError $exception) {
        
            // Catch query error and desplay error details

            print_r($exception->getErrorDetails());
            return false;
        }
    }
    public function getBrands($menu=''){
        $menuType='';
        if(!empty($menu)){
            $menuType='menuType:'.$menu;
        }
        $gql = <<<QUERY

            fragment brandFragment on Brand {
                id
                name
                }

                query BrandsQuery{
                menu( 
                    retailerId:"$this->retailerId"
                    sort: { direction: ASC, key: NAME }
                    $menuType
                ) {
                    brands {
                    ...brandFragment
                    }
                }
                }
            
        QUERY;
        if(is_null($this->client)){
            $this->auth();
        }
        
        try {
            return $this->client->runRawQuery($gql, false)->getData()->menu->brands;
        }
        catch (GraphQL\Exception\QueryError $exception) {
        
            // Catch query error and desplay error details

            //print_r($exception->getErrorDetails());
            return false;
        }
    }
    public function getWeights($menu=''){
        $menuType='';
        if(!empty($menu)){
            $menuType='menuType:'.$menu;
        }

        $gql = <<<QUERY

        fragment productFragment on Product {
            brand {
              id
              name
            }
            category
            description
            descriptionHtml
            id
            image
            images {
              id
              url
              label
              description
            }
            name
            posId
            potencyCbd {
              formatted
              range
              unit
            }
            potencyThc {
              formatted
              range
              unit
            }
            staffPick
            strainType
            subcategory
            variants {
              id
              option
              priceMed
              priceRec
              specialPriceMed
              specialPriceRec
            }
          }

                query weightsQuery{
                menu( 
                    retailerId:"$this->retailerId"
                    $menuType
                ) {
                    weights
                    products {
                    ...productFragment
                    }
                }
                }
            
        QUERY;
        if(is_null($this->client)){
            $this->auth();
        }
        
        try {
            return $this->client->runRawQuery($gql, false)->getData()->menu->weights;
        }
        catch (GraphQL\Exception\QueryError $exception) {
        
            // Catch query error and desplay error details

            //print_r($exception->getErrorDetails());
            return false;
        }
        
    }

    public function getEffects($menu=''){
        $menuType='';
        if(!empty($menu)){
            $menuType='menuType:'.$menu;
        }

        $gql = <<<QUERY

        fragment productFragment on Product {
            brand {
              id
              name
            }
            category
            description
            descriptionHtml
            id
            image
            images {
              id
              url
              label
              description
            }
            name
            posId
            potencyCbd {
              formatted
              range
              unit
            }
            potencyThc {
              formatted
              range
              unit
            }
            staffPick
            strainType
            subcategory
            variants {
              id
              option
              priceMed
              priceRec
              specialPriceMed
              specialPriceRec
            }
          }

                query weightsQuery{
                menu( 
                    retailerId:"$this->retailerId"
                    $menuType
                ) {
                    effects
                    products {
                    ...productFragment
                    }
                }
                }
            
        QUERY;
        if(is_null($this->client)){
            $this->auth();
        }
        
        try {
            return $this->client->runRawQuery($gql, false)->getData()->menu->effects;
        }
        catch (GraphQL\Exception\QueryError $exception) {
        
            // Catch query error and desplay error details

            print_r($exception->getErrorDetails());
            return false;
        }
    }
    public function getSubcategory($menu=''){
        $menuType='';
        if(!empty($menu)){
            $menuType='menuType:'.$menu;
        }

        $gql = <<<QUERY

        fragment productFragment on Product {
            brand {
              id
              name
            }
            category
            description
            descriptionHtml
            id
            image
            images {
              id
              url
              label
              description
            }
            name
            posId
            potencyCbd {
              formatted
              range
              unit
            }
            potencyThc {
              formatted
              range
              unit
            }
            staffPick
            strainType
            subcategory
            variants {
              id
              option
              priceMed
              priceRec
              specialPriceMed
              specialPriceRec
            }
          }

                query weightsQuery{
                menu( 
                    retailerId:"$this->retailerId"
                    $menuType
                ) {
                    subcategory
                    products {
                    ...productFragment
                    }
                }
                }
            
        QUERY;
        if(is_null($this->client)){
            $this->auth();
        }
        
        try {
            return $this->client->runRawQuery($gql, false)->getData()->menu->subcategory;
        }
        catch (GraphQL\Exception\QueryError $exception) {
        
            // Catch query error and desplay error details

            print_r($exception->getErrorDetails());
            return false;
        }
    }
    public function getSpecials($menu=''){
        $menuType='';
        if(!empty($menu)){
            $menuType='menuType:'.$menu;
        }
        $gql = <<<QUERY

            fragment specialFragment on Special {
                        id
                        name
                        type
                        redemptionLimit
                        menuType
                        emailConfiguration {
                          description
                          descriptionHtml
                          subject
                          heading
                          enabled
                        }
                        scheduleConfiguration {
                          startStamp
                          endStamp
                          days
                          startTime
                          endTime
                          setEndDate
                          endDate
                        }
                        menuDisplayConfiguration {
                          name
                          description
                          image
                        }
                      }

                query SpecialsQuery{
                menu( 
                    retailerId:"$this->retailerId"
                    $menuType
                ) {
                   ...specialFragment
                }
                }
            
        QUERY;
        if(is_null($this->client)){
            $this->auth();
        }
        
        try {
            return $this->client->runRawQuery($gql, false)->getData()->menu->brands;
        }
        catch (GraphQL\Exception\QueryError $exception) {
        
            // Catch query error and desplay error details

            //print_r($exception->getErrorDetails());
            return false;
        }
    }

    public function addToCart($productId,$quantity=1,$checkoutId,$option=''){
            $options = '';
            if(!empty($option)){
                $options='option:"'.$option.'"';
            }
        
            $gql = <<<QUERY
                    
                  fragment productFragment on Product {
                    brand {
                      id
                      name
                    }
                    category
                    description
                    descriptionHtml
                    id
                    image
                    images {
                      id
                      url
                      label
                      description
                    }
                    name
                    posId
                    potencyCbd {
                      formatted
                      range
                      unit
                    }
                    potencyThc {
                      formatted
                      range
                      unit
                    }
                    staffPick
                    strainType
                    subcategory
                    variants {
                      id
                      option
                      priceMed
                      priceRec
                      specialPriceMed
                      specialPriceRec
                    }
                  }  
                    
           
                    
                    fragment itemFragment on Item {
            id
            errors
            option
            product {
              ...productFragment
            }
            productId
            quantity
            valid
            isDiscounted
            basePrice
            discounts {
              total
            }
            taxes {
              total
              cannabis
              sales
            }
          }
                 fragment checkoutFragment on Checkout {
                    createdAt
                    id
                    orderType
                    pricingType
                    redirectUrl
                    updatedAt
                    items {
                        ...itemFragment
                      }
                  }


                    
                    
                mutation {
                  addItem(
                    retailerId: "$this->retailerId"
                    checkoutId: "$checkoutId"
                    quantity: $quantity
                    $options
                    productId: "$productId"
                  ) {
                    ...checkoutFragment
                  }
                }
         QUERY;
        if(is_null($this->client)){
            $this->auth();
        }
        try {
            return $this->client->runRawQuery($gql, false)->getData();
        }
        catch (GraphQL\Exception\QueryError $exception) {
        
            // Catch query error and desplay error details

            print_r($exception->getErrorDetails());
            return false;
        }   
    }
    public function createCart($orderType,$pricingType,$metadatas=array()){
        $metadata='';
        if(!empty($metadatas)){
        $metadata='metadata:';
        $mks = array();
        foreach($metadatas as $k=>$v){
            $mks[]='{'.$k.':'.$v.'}';
        }
        $metadata.= implode(',', $mks);
        $metadata.='';
        }
         $gql = <<<QUERY
                 fragment checkoutFragment on Checkout {
                    createdAt
                    id
                    orderType
                    pricingType
                    redirectUrl
                    updatedAt
                  }
                 
                 
                 
                mutation {
                  createCheckout(
                    retailerId:"$this->retailerId"
                    orderType: $orderType
                    pricingType: $pricingType
                    $metadata
                  ) {
                    ...checkoutFragment
                  }
                }
         QUERY;
        if(is_null($this->client)){
            $this->auth();
        }
      
        try {
            return $this->client->runRawQuery($gql, false)->getData()->createCheckout;
        }
        catch (GraphQL\Exception\QueryError $exception) {
        
            // Catch query error and desplay error details

            print_r($exception->getErrorDetails());
            return false;
        }        
    }
     public function stores(){
       
         $gql = <<<QUERY
                 fragment addressFragment on AddressObject {
                    line1
                    line2
                    city
                    postalCode
                    state
                    country
                  }

                  fragment bannerColorsFragment on BannerColorConfiguration {
                    background
                    border
                    color
                    id
                  }

                  fragment deliverySettingsFragment on DeliverySettings {
                    afterHoursOrderingForDelivery
                    afterHoursOrderingForPickup
                    deliveryArea
                    deliveryFee
                    deliveryMinimum
                    disablePurchaseLimits
                    limitPerCustomer
                    pickupMinimum
                    scheduledOrderingForDelivery
                    scheduledOrderingForPickup
                  }

                  fragment hoursDayFragment on HoursDay {
                    active
                    start
                    end
                  }

                  fragment hoursFragment on Hours {
                    Sunday {
                      ...hoursDayFragment
                    }
                    Monday {
                      ...hoursDayFragment
                    }
                    Tuesday {
                      ...hoursDayFragment
                    }
                    Wednesday {
                      ...hoursDayFragment
                    }
                    Thursday {
                      ...hoursDayFragment
                    }
                    Friday {
                      ...hoursDayFragment
                    }
                    Saturday {
                      ...hoursDayFragment
                    }
                  }

                  fragment paymentOptionsFragment on PaymentOptions {
                    aeropay
                    alt36
                    canPay
                    cashless
                    cashOnly
                    check
                    creditCard
                    creditCardAtDoor
                    creditCardByPhone
                    debitOnly
                    hypur
                    linx
                    merrco
                    payInStore
                    paytender
                  }

                  fragment retailerFragment on Retailer {
                    address
                    addressObject {
                      ...addressFragment
                    }
                    banner {
                      colors {
                        ...bannerColorsFragment
                      }
                      html
                    }
                    coordinates {
                      latitude
                      longitude
                    }
                    deliverySettings {
                      ...deliverySettingsFragment
                    }
                    description
                    fulfillmentOptions {
                      curbsidePickup
                      delivery
                      driveThruPickup
                      pickup
                    }
                    hours {
                      delivery {
                        ...hoursFragment
                      }
                      pickup {
                        ...hoursFragment
                      }
                      special {
                        startDate
                        endDate
                        hoursPerDay {
                          date
                          deliveryHours {
                            ...hoursDayFragment
                          }
                          pickupHours {
                            ...hoursDayFragment
                          }
                        }
                        name
                      }
                    }
                    id
                    menuTypes
                    name
                    paymentOptions {
                      ...paymentOptionsFragment
                    }
                    settings {
                      menuWeights
                    }
                  }

            
                  query RetailersQuery {
                    retailers {
                      ...retailerFragment
                    }
                  }
         QUERY;
        if(is_null($this->client)){
            $this->auth();
        }
      
        try {
            return $this->client->runRawQuery($gql, false)->getData()->retailers;
        }
        catch (GraphQL\Exception\QueryError $exception) {
        
            // Catch query error and desplay error details

            print_r($exception->getErrorDetails());
            return false;
        }        
    }
    
    public function store(){
       
         $gql = <<<QUERY
                 fragment addressFragment on AddressObject {
                    line1
                    line2
                    city
                    postalCode
                    state
                    country
                  }

                  fragment bannerColorsFragment on BannerColorConfiguration {
                    background
                    border
                    color
                    id
                  }

                  fragment deliverySettingsFragment on DeliverySettings {
                    afterHoursOrderingForDelivery
                    afterHoursOrderingForPickup
                    deliveryArea
                    deliveryFee
                    deliveryMinimum
                    disablePurchaseLimits
                    limitPerCustomer
                    pickupMinimum
                    scheduledOrderingForDelivery
                    scheduledOrderingForPickup
                  }

                  fragment hoursDayFragment on HoursDay {
                    active
                    start
                    end
                  }

                  fragment hoursFragment on Hours {
                    Sunday {
                      ...hoursDayFragment
                    }
                    Monday {
                      ...hoursDayFragment
                    }
                    Tuesday {
                      ...hoursDayFragment
                    }
                    Wednesday {
                      ...hoursDayFragment
                    }
                    Thursday {
                      ...hoursDayFragment
                    }
                    Friday {
                      ...hoursDayFragment
                    }
                    Saturday {
                      ...hoursDayFragment
                    }
                  }

                  fragment paymentOptionsFragment on PaymentOptions {
                    aeropay
                    alt36
                    canPay
                    cashless
                    cashOnly
                    check
                    creditCard
                    creditCardAtDoor
                    creditCardByPhone
                    debitOnly
                    hypur
                    linx
                    merrco
                    payInStore
                    paytender
                  }

                  fragment retailerFragment on Retailer {
                    address
                    addressObject {
                      ...addressFragment
                    }
                    banner {
                      colors {
                        ...bannerColorsFragment
                      }
                      html
                    }
                    coordinates {
                      latitude
                      longitude
                    }
                    deliverySettings {
                      ...deliverySettingsFragment
                    }
                    description
                    fulfillmentOptions {
                      curbsidePickup
                      delivery
                      driveThruPickup
                      pickup
                    }
                    hours {
                      delivery {
                        ...hoursFragment
                      }
                      pickup {
                        ...hoursFragment
                      }
                      special {
                        startDate
                        endDate
                        hoursPerDay {
                          date
                          deliveryHours {
                            ...hoursDayFragment
                          }
                          pickupHours {
                            ...hoursDayFragment
                          }
                        }
                        name
                      }
                    }
                    id
                    menuTypes
                    name
                    paymentOptions {
                      ...paymentOptionsFragment
                    }
                    settings {
                      menuWeights
                    }
                  }

            
                  query RetailersQuery {
                    retailer (
                            id:"$this->retailerId"
                        ) {
                      ...retailerFragment
                    }
                  }
         QUERY;
        if(is_null($this->client)){
            $this->auth();
        }
      
        try {
            return $this->client->runRawQuery($gql, false)->getData()->retailer;
        }
        catch (GraphQL\Exception\QueryError $exception) {
        
            // Catch query error and desplay error details

            print_r($exception->getErrorDetails());
            return false;
        }        
    }
    
   /* public function checkout($menuType){
        $menuType='';
        if(!empty($menu)){
            $menuType='menuType:'.$menu;
        }

        $gql = <<<QUERY
                fragment productFragment on Product {
                    brand {
                      id
                      name
                    }
                    category
                    description
                    descriptionHtml
                    id
                    image
                    images {
                      id
                      url
                      label
                      description
                    }
                    name
                    posId
                    potencyCbd {
                      formatted
                      range
                      unit
                    }
                    potencyThc {
                      formatted
                      range
                      unit
                    }
                    staffPick
                    strainType
                    subcategory
                    variants {
                      id
                      option
                      priceMed
                      priceRec
                      specialPriceMed
                      specialPriceRec
                    }
                  }
       fragment addressFragment on CheckoutAddress {
            city
            deliverable
            formatted
            geometry {
              coordinates
              type
            }
            state
            street1
            street2
            valid
            zip
          }

          fragment itemFragment on Item {
            id
            errors
            option
            product {
              ...productFragment
            }
            productId
            quantity
            valid
            isDiscounted
            basePrice
            discounts {
              total
            }
            taxes {
              total
              cannabis
              sales
            }
          }

          fragment priceSummaryFragment on PriceSummary {
            discounts
            fees
            mixAndMatch
            rewards
            subtotal
            taxes
            total
          }

          fragment checkoutFragment on Checkout {
            address {
              ...addressFragment
            }
            createdAt
            id
            items {
              ...itemFragment
            }
            orderType
            priceSummary {
              ...priceSummaryFragment
            }
            pricingType
            redirectUrl
            updatedAt
          }

             

                mutation addItemToCart(
                  $retailerId: ID!
                  $checkoutId: ID!
                  $quantity: Int!
                  $option: String!
                  $productId: ID!
                ) {
                  addItem(
                    retailerId: $retailerId
                    checkoutId: $checkoutId
                    quantity: $quantity
                    option: $option
                    productId: $productId
                  ) {
                    ...checkoutFragment
                  }
                }
                query weightsQuery{
                menu( 
                    retailerId:"$this->retailerId"
                    $menuType
                ) {
                    subcategory
                    products {
                    ...productFragment
                    }
                }
                }
            
        QUERY;
        if(is_null($this->client)){
            $this->auth();
        }
        
        try {
            return $this->client->runRawQuery($gql, false)->getData()->menu->subcategory;
        }
        catch (GraphQL\Exception\QueryError $exception) {
        
            // Catch query error and desplay error details

            print_r($exception->getErrorDetails());
            return false;
        }
            
    } */
}
