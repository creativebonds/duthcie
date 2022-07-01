<?php
/*
Plugin Name: Dutchie By Defox 
Plugin URI: http://defox.pk/plugins/dutchie/
Description: dutchie integration
Author: Muhammad Amjad
Version: 1.7.2
Author URI: http://defox.pk/
*/


if ( is_admin() ){
    require plugin_dir_path( __FILE__ ) . 'includes/dutchie-settings.php';
    $dutchie_settings = new DutchieSettings();
}
require plugin_dir_path( __FILE__ ).'includes/dutchie.php';
require plugin_dir_path( __FILE__ ).'includes/dutchie-woo.php';
require plugin_dir_path( __FILE__ ).'includes/wp-core-customize.php';

if(isset($_GET['gtest'])){
    echo '<pre>';
    $client = new dutchie;
    $client->auth();
    $menu = $client->getSpecials();

    print_r($menu);
    
   /* $checkout=$client->createCart('DELIVERY','MEDICAL',array(array(
        'Name'=>'order',
        'value'=>1
    )));
    print_r($checkout);
    // add products to cart
    print_r($client->addToCart('6266e8c97e1c6600016a41c9',1,$checkout->id,'N/A'));
    print('Location: https://dutchie.com/checkouts/'.$client->dispensary.'/'.$checkout->id.'/?externalUserDetails[email]='.urlencode('amjad.mghl@gmail.com'));
  */  die();
}


function dutchie_special_products(){
    
        ob_start();

    $client = new dutchie;
    $client->auth();
    $menu = $client->getSpecials();
    print_r();
}
function dutchie_stores(){
    
        ob_start();

    $client = new dutchie;
    $client->auth();
    $stores = $client->stores();
   if($stores){
       foreach($stores as $store){
           ?>
<div class="store">
    <h2><?php echo $store->name;?></h2>
    <div><?php echo $store->address;?></div>
    <div><b>Fulfillment Options</b><br />
    <?php 
    $options = array();
    if($store->fulfillmentOptions->curbsidePickup){
        $options[]='Curbside';
    }
    if($store->fulfillmentOptions->delivery){
        $options[]='Delivery';
    }
    if($store->fulfillmentOptions->driveThruPickup){
        $options[]='Drive Through';
    }
    if($store->fulfillmentOptions->pickup){
        $options[]='Pickup';
    }
    echo implode(', ',$options)
    ?>
    </div>
    <div><b>Payment Options</b><br />
    <?php 
    $options = array();
    foreach($store->paymentOptions as $k=>$v){
        if($v){
            $options[]=$k;
        }
    }
    echo implode(', ',$options)
    ?>
    </div>
    <div><b>Hours</b><br />
        <b>Delivery</b>:<br />
    <?php 
    $options = array();
    foreach($store->hours as $k=>$v){
        if($k=='delivery'){
            foreach($v as $kk=>$vv){
                if($vv->active){
                    $options[]=$kk.' - '.$vv->start.'-'.$vv->end;
                }
            }
        }
    }
    echo implode('<br />',$options)
    ?>
    <br /><b>Pickup</b>:<br />
    <?php 
    $options = array();
    foreach($store->hours as $k=>$v){
        if($k=='pickup'){
            foreach($v as $kk=>$vv){
                if($vv->active){
                    $options[]=$kk.' - '.$vv->start.'-'.$vv->end;
                }
            }
        }
    }
    echo implode('<br />',$options)
    ?>
    </div>
    <div class="mapouter"><iframe width="600" height="300" src="https://maps.google.com/maps?q=<?php echo $store->address ?>&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe></div>
</div>   
            <?php
       }
   }
        $content = ob_get_clean();
    return $content;
}

add_shortcode('dutchie_stores','dutchie_stores');

function dutchie_products($atts){
    $args = shortcode_atts( array(
		'type' => '',
                'limit'=>1000000,
                'loadmore'=>0,
                'filters'=>1,
                'specialid'=>''
	), $atts );
    $params = array();
    $loadmore = false;
    if(!empty($args['type'])){
        $params['menuType']=$args['type'];
    }
    if(!empty($args['limit'])){
        $params['limit']=$args['limit'];
    }
    if(!empty($args['loadmore'])){
        $loadmore=$args['loadmore'];
    }
    
    $select_cat = isset($_GET['pro_cat'])?$_GET['pro_cat']:'';
    if(!empty($select_cat)){
        $params['category']=$select_cat;
    }
    $select_weight = isset($_GET['pro_weight'])?$_GET['pro_weight']:array();
    if(!empty($select_weight)){
        $params['weight']=$select_weight;
    }
    $select_brand = isset($_GET['pro_brand'])?$_GET['pro_brand']:'';
    if(!empty($select_brand)){
        $params['brandId']=$select_brand;
    }
    $select_type = isset($_GET['pro_type'])?$_GET['pro_type']:'';
    if(!empty($select_type)){
        $params['strainType']=$select_type;
    }
    $select_effects = isset($_GET['pro_effects'])?$_GET['pro_effects']:array();
    if(!empty($select_effects)){
        $params['effects']=$select_effects;
    }
    if(!empty($args['specialid'])){
        $params['specialid']=$args['specialid'];
    }
    $select_sort = isset($_GET['pro_sort'])?$_GET['pro_sort']:'';
    if(!empty($select_sort)){
        $params['sort']=$select_sort;
    }
    
    $select_cbd = isset($_GET['pro_cbd'])?$_GET['pro_cbd']:'';
    if(!empty($select_cbd)){
        $params['cbd']=$select_cbd;
    }
    $select_thc = isset($_GET['pro_thc'])?$_GET['pro_thc']:'';
    if(!empty($select_thc)){
        $params['thc']=$select_thc;
    }
    
    ob_start();

    $client = new dutchie;
    $client->auth();
    $menu = $client->getMenuraw($params,true);
    $products = $menu->products;
    /*$params['limit']=1;
    $params['sort']='POTENCY_ASC';
    $product_min = $client->getMenuraw($params);
    $params['limit']=1;
    $params['sort']='POTENCY_DESC';
    $product_max = $client->getMenuraw($params);
    echo '<pre>';
    print_r($product_min);
    print_r($product_max);
    echo '</pre>';*/
    $thc = array(0,0);
    $cbd = array(0,0);
  
    if($products){
        foreach($products as $product){
        
         
            if($product->potencyCbd->range){
                if(isset($product->potencyCbd->range[1])){
                    if($cbd[0]>$product->potencyCbd->range[0]){
                        $cbd[0]=$product->potencyCbd->range[0];
                    }
                    if($cbd[1]<$product->potencyCbd->range[1]){
                        $cbd[1]=$product->potencyCbd->range[1];
                    }
                }else{
                    if($cbd[1]<$product->potencyCbd->range[0]){
                        $cbd[1]=$product->potencyCbd->range[0];
                    }
                }
            }
            if($product->potencyThc->range){
                if(isset($product->potencyThc->range[1])){
                    if($thc[0]>$product->potencyThc->range[0]){
                        $thc[0]=$product->potencyThc->range[0];
                    }
                    if($thc[1]<$product->potencyThc->range[1]){
                        $thc[1]=$product->potencyThc->range[1];
                    }
                }else{
                    if($thc[1]<$product->potencyThc->range[0]){
                        $thc[1]=$product->potencyThc->range[0];
                    }
                }
            }
            
           
        }
    }

    if($args['filters']){

        $brands = $menu->brands;
        $weights = $menu->weights;

 $listview=isset($_COOKIE['listview'])?$_COOKIE['listview']:0;
 ?>
 <div id="dutchie_products_container" class="<?php echo $listview==0?'':'listview'?>">
               <form id="productfilters" method="get" action="<?php echo get_permalink()?>">
    <div class="filters hidden">
        <div class="filters_header">Filters <span class="closefilters"><svg style="width:24px;height:24px" viewBox="0 0 24 24">
    <path fill="currentColor" d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" />
</svg></span></div>
 <div class="filters_body">
                <div class="filter category_filter">
                    <h2>Categories</h2>
                    <div class="options">
             
                 
                        <?php 
                        $cats = array(
                            'ACCESSORIES',
  'APPAREL',
  'CBD',
  'CLONES',
  'CONCENTRATES',
  'EDIBLES',
  'FLOWER',
  'NOT_APPLICABLE',
  'ORALS',
  'PRE_ROLLS',
  'SEEDS',
  'TINCTURES',
  'TOPICALS',
  'VAPORIZERS'
                            );
                        foreach($cats as $cat){
                            $selected = '';
                            if($select_cat==$cat){
                                $selected=' checked';
                            }
                            echo '<label><input type="checkbox" name="pro_cat" value="'.$cat.'" '.$selected.'> '.ucfirst(strtolower(str_replace('_',' ',$cat))).'</label>';
                        }
                        ?>
                
                    </div>
                </div>
                    
     <div class="filter weight_filter" style="display: none;">
               <h2>Weights</h2>
                    <div class="options">
                        <?php 
                        if($weights){
                            natsort($weights);
                            foreach($weights as $k=>$weight){
                                $selected = '';
                            if(in_array($weight, $select_weight)){
                                $selected=' checked';
                            }
                                echo '<label><input type="checkbox" name="pro_weight" value="weight_'.str_replace('.','_',$weight).'" '.$selected.'>'.$weight.'</label>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="filter brand_filter">
                 <h2>Brands</h2>
                    <div class="options">
            
                        <?php 
                        if($brands){
                            foreach($brands as $brand){
                                 $selected = '';
                            if($select_brand==$brand->id){
                                $selected=' checked';
                            }
                                echo '<label><input type="checkbox" name="pro_brand" value="'.$brand->name.'" '.$selected.'>'.$brand->name.'</label>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="filter type_filter">
                   
                      <h2>Type</h2>
                    <div class="options">
                
                        <?php 
                        $cats = array(
                            
                            'HIGH_CBD',
                            'HYBRID',
                            'INDICA',
                            'SATIVA'
                            );
                        foreach($cats as $cat){
                               $selected = '';
                            if($select_type==$cat){
                                $selected=' checked';
                            }
                            echo '<label><input type="checkbox" name="pro_type" value="'.$cat.'" '.$selected.'>'.ucfirst(strtolower(str_replace('_',' ',$cat))).'</label>';
                        }
                        ?>
                    </div>
                    
                </div> 
     
                <div class="filter">
                       <h2>CBD</h2>
                       <div class="slider-container">
                           <input name="pro_cbd" id="cbd_slider_val" value="<?php echo $cbd[0]?>,<?php echo $cbd[1]?>" data-min="<?php echo $cbd[0]?>" data-max="<?php echo $cbd[1]?>" />
                        <div id="cbd_slider"></div>
                        <div id="cbd_slider_range"></div>
                       </div>
                    <script>
                    (function($){
                        setCBDSlider(<?php echo $cbd[0]?>,<?php echo $cbd[1]?>,'');
                    })(jQuery)
                    </script>
                </div>
     <div class="filter">
                       <h2>THC</h2>
                       <div class="slider-container">
                           <input name="pro_thc" id="thc_slider_val" value="<?php echo $thc[0]?>,<?php echo $thc[1]?>" data-min="<?php echo $thc[0]?>" data-max="<?php echo $thc[1]?>" />
                    <div id="thc_slider"></div>
                    <div id="thc_slider_range"></div>
                      </div>
                    <script>
                    (function($){
                        setTHCSlider(<?php echo $thc[0]?>,<?php echo $thc[1]?>,'');
                    })(jQuery)
                    </script>
                </div>
                    <div class="filter effects_filter">
                     <h2>Effects</h2>
                    <div class="options">
                
                 
                        <?php 
                        $cats = array(
                            'CALM',
                            'CLEAR_MIND',
                            'CREATIVE',
                            'ENERGETIC',
                            'FOCUSED',
                            'HAPPY',
                            'INSPIRED',
                            'RELAXED',
                            'SLEEPY',
                            'UPLIFTED'
                            );
                        foreach($cats as $cat){
                               $selected = '';
                            if(in_array($cat, $select_effects)){
                                $selected=' checked';
                            }
                            echo '<label><input type="checkbox" name="pro_effects" value="'.$cat.'" '.$selected.'>'.ucfirst(strtolower(str_replace('_',' ',$cat))).'</label>';
                        }
                        ?>
                    </div>
                    
                </div> 
                   
 </div>
            </div>
                   <div class="filter_row">
                       <div class="filter_col">
                         
<button id="showfilters">  <svg style="width:24px;height:24px" viewBox="0 0 24 24">
    <path fill="currentColor" d="M7 3H5V9H7V3M19 3H17V13H19V3M3 13H5V21H7V13H9V11H3V13M15 7H13V3H11V7H9V9H15V7M11 21H13V11H11V21M15 15V17H17V21H19V17H21V15H15Z" />
    </svg> <span>Filters</span></button>
                           <div id='selected_filters'></div>
                       </div>
                       <div class="filter_col">
                           <div class="filter">
                   
                    <select name="pro_sort">
                        <option value="">Sort By</option>
                 
                        <?php 
                        $cats = array(
                            'NAME_ASC'=>'Alphabetically AZ',
                            'NAME_DESC'=>'Alphabetically ZA',
                            'POPULAR_DESC'=>'Popularity (High to Low)',
                            'POPULAR_ASC'=>'Popularity (Low to High)',
                            'PRICE_DESC'=>'Price (High to Low)',
                            'PRICE_ASC'=>'Price (Low to High)',
                            'POTENCY_DESC'=>'Potency (High to Low)',
                            'POTENCY_ASC'=>'Potency (Low to High)'
                            );
                        foreach($cats as $cat=>$label){
                               $selected = '';
                            if($select_sort==$cat){
                                $selected=' selected';
                            }
                            echo '<option value="'.$cat.'" '.$selected.'>'.ucfirst(strtolower(str_replace('_',' ',$label))).'</option>';
                        }
                        ?>
                    </select>
                             
                    
                </div>
                           <div class="filter product_display_style">
                                <a href="#">
                            <svg style="width:24px;height:24px" viewBox="0 0 24 24">
    <path fill="currentColor" d="M3,11H11V3H3M3,21H11V13H3M13,21H21V13H13M13,3V11H21V3" />
</svg>   </a>
                               <a href="#">
                            <svg style="width:24px;height:24px" viewBox="0 0 24 24">
    <path fill="currentColor" d="M9,5V9H21V5M9,19H21V15H9M9,14H21V10H9M4,9H8V5H4M4,19H8V15H4M4,14H8V10H4V14Z" />
</svg>   </a>
                              
                           </div>
                       </div>
                   </div>
 
   </form>
    <?php
    }
    if($products){
        echo '<div class="duthcie_product_list" id="duthcie_product_list">';
        if(!$args['filters']){
            foreach($products as $product){
                echo dutchie_product_box($product,$params['menuType']);
           }
        }
 
        echo '</div>';
           if($args['filters']){
              ?>
     <script>
     var products = <?php echo json_encode($products)?>;  
     </script>
               <?php 
           }
    if($loadmore){
        ?>
<a href="#" id="loadmoreproducts" class="btn btn-primary" data-perpage="<?php echo $args['limit']?>" data-page="1" data-type="<?php echo $args['type'];?>">Load more</a>    
        <?php
    }
    }else{
        echo '<p>No Product Found</p>';
    }
    ?></div><?php
    $content = ob_get_clean();
    return $content;
}
add_shortcode('dutchie_products','dutchie_products');

function dutchie_enqueue() {
    wp_enqueue_script( 'dutchie-confirm', plugins_url( '/js/jquery-confirm.min.js', __FILE__ ), array('jquery'), time() );
    wp_enqueue_script( 'dutchie', plugins_url( '/js/custom.js', __FILE__ ), array('jquery'), time() );
    wp_enqueue_script( 'dutchie-cookies', plugins_url( '/js/cookies.js', __FILE__ ), array('jquery'), time() );
    wp_enqueue_style( 'dutchie-css', plugins_url( '/css/custom.css', __FILE__ ), time() );
    wp_enqueue_style( 'dutchie-confirm', plugins_url( '/css/jquery-confirm.min.css', __FILE__ ) );
    wp_localize_script( 'dutchie', 'dutchie',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'dutchie_enqueue' );

function dutchie_product(){
    $code = get_query_var( 'code', '' );
        var_dump($code);
        echo 'yessss';
    ob_start();
    

    if(!empty($code)){
        $client = new dutchie;
        $client->auth();
        $product = $client->getProduct($code);
        echo '<pre>';
        print_r($product);
        echo '</pre>';
        if($product){
            ?>
              <style>
        .elementor-333 .elementor-element.elementor-element-7177a3f > .elementor-container{max-width:1440px;}.elementor-333 .elementor-element.elementor-element-7177a3f{padding:7% 0% 7% 0%;}.elementor-333 .elementor-element.elementor-element-0f7f70e{--e-image-carousel-slides-to-show:1;}.elementor-333 .elementor-element.elementor-element-6a2c172 .elementor-heading-title{font-family:"Domine", Sans-serif;font-size:60px;font-weight:500;}.elementor-333 .elementor-element.elementor-element-4905f58 .elementor-heading-title{font-family:"Mulish", Sans-serif;font-size:28px;font-weight:500;}.elementor-333 .elementor-element.elementor-element-3f1c52b .elementor-button{background-color:var( --e-global-color-astglobalcolor8 );border-radius:25px 25px 25px 25px; width: 100%;}.elementor-333 .elementor-element.elementor-element-3f1c52b{width:100%;max-width:100%;}.elementor-333 .elementor-element.elementor-element-b6dd39f{color:#000000;font-family:"Mulish", Sans-serif;font-size:18px;font-weight:400;}.elementor-333 .elementor-element.elementor-element-b6dd39f > .elementor-widget-container{padding:15px 0px 15px 0px;}.elementor-333 .elementor-element.elementor-element-c42f8c7 .elementor-button{font-family:"Raleway", Sans-serif;font-size:1.5em;font-weight:600;fill:var( --e-global-color-primary );color:var( --e-global-color-primary );background-color:#61CE7000;padding:0px 0px 0px 0px;}.elementor-333 .elementor-element.elementor-element-c42f8c7 > .elementor-widget-container{margin:0px 0px 0px 0px;padding:0px 0px 0px 0px;}
.elementor-333 .elementor-element.elementor-element-d7e7782 > .elementor-container{max-width:1440px;}.elementor-333 .elementor-element.elementor-element-61ebdfd{text-align:center;}.elementor-333 .elementor-element.elementor-element-61ebdfd .elementor-heading-title{font-family:"Domine", Sans-serif;font-size:60px;font-weight:600;line-height:1em;}.elementor-333 .elementor-element.elementor-element-7d4835d{text-align:center;}.elementor-333 .elementor-element.elementor-element-7d4835d .elementor-heading-title{color:var( --e-global-color-astglobalcolor8 );font-family:"Raleway", Sans-serif;font-size:20px;font-weight:600;line-height:1em;}
            </style>
	

		<?php 
            
          

		
                 ?>
            
            <div data-elementor-type="wp-page" data-elementor-id="333" class="elementor elementor-333">
		<section class="elementor-section elementor-top-section elementor-element elementor-element-7177a3f elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="7177a3f" data-element_type="section">
			<div class="elementor-container elementor-column-gap-default">
			<div class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-4e83228" data-id="4e83228" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<div class="elementor-element elementor-element-0f7f70e elementor-pagination-position-outside elementor-widget elementor-widget-image-carousel" data-id="0f7f70e" data-element_type="widget" data-settings="{&quot;slides_to_show&quot;:&quot;1&quot;,&quot;navigation&quot;:&quot;dots&quot;,&quot;autoplay&quot;:&quot;yes&quot;,&quot;pause_on_hover&quot;:&quot;yes&quot;,&quot;pause_on_interaction&quot;:&quot;yes&quot;,&quot;autoplay_speed&quot;:5000,&quot;infinite&quot;:&quot;yes&quot;,&quot;effect&quot;:&quot;slide&quot;,&quot;speed&quot;:500}" data-widget_type="image-carousel.default">
				<div class="elementor-widget-container">
			<style>/*! elementor - v3.6.5 - 27-04-2022 */
.elementor-widget-image-carousel .swiper-container{position:static}.elementor-widget-image-carousel .swiper-container .swiper-slide figure{line-height:inherit}.elementor-widget-image-carousel .swiper-slide{text-align:center}.elementor-image-carousel-wrapper:not(.swiper-container-initialized) .swiper-slide{max-width:calc(100% / var(--e-image-carousel-slides-to-show, 3))}</style>		<div class="elementor-image-carousel-wrapper swiper-container" dir="ltr">
			<div class="elementor-image-carousel swiper-wrapper">
                            <?php 
                          
                                if($product->images){
                                foreach( $product->images as $image ) {
		
                        ?>
                             <div class="swiper-slide"><figure class="swiper-slide-inner"><img class="swiper-slide-image" src="<?php echo $image->url;?>" alt="" /></figure></div>
                            <?php
                }
                                }
                                ?>				
                           
                        </div>
												<div class="swiper-pagination"></div>
													</div>
				</div>
				</div>
					</div>
		</div>
				<div class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-c7578dc" data-id="c7578dc" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
                            <div class="elementor-element elementor-element-6a2c172 elementor-widget elementor-widget-heading" data-id="6a2c172" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<style>/*! elementor - v3.6.5 - 27-04-2022 */
.elementor-heading-title{padding:0;margin:0;line-height:1}.elementor-widget-heading .elementor-heading-title[class*=elementor-size-]>a{color:inherit;font-size:inherit;line-height:inherit}.elementor-widget-heading .elementor-heading-title.elementor-size-small{font-size:15px}.elementor-widget-heading .elementor-heading-title.elementor-size-medium{font-size:19px}.elementor-widget-heading .elementor-heading-title.elementor-size-large{font-size:29px}.elementor-widget-heading .elementor-heading-title.elementor-size-xl{font-size:39px}.elementor-widget-heading .elementor-heading-title.elementor-size-xxl{font-size:59px}</style><h2 class="elementor-heading-title elementor-size-default"><?php echo $product->name;?></h2>		</div>
				</div>
                      
                    <div class="product-price"></div>
                             <div class="elementor-element elementor-element-4905f58 elementor-widget elementor-widget-heading" data-id="4905f58" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h5 class="elementor-heading-title elementor-size-default">
                            <?php 
            /*$pm = get_field( "price", $post->ID );
            if($pm){
            ?>
            $<?php echo $pm?>
            <?php } */?>
                        </h5>
                                </div>
				</div>
                             <div class="elementor-element elementor-element-3f1c52b elementor-widget__width-inherit elementor-widget elementor-widget-button" data-id="3f1c52b" data-element_type="widget" data-widget_type="button.default">
				<div class="elementor-widget-container">
					<div class="elementor-button-wrapper">
			<a href="#" class="elementor-button-link elementor-button elementor-size-sm" role="button">
						<span class="elementor-button-content-wrapper">
						<span class="elementor-button-text">In Stock</span>
		</span>
					</a>
		</div>
				</div>
				</div>
                             <div class="elementor-element elementor-element-b6dd39f elementor-widget elementor-widget-text-editor" data-id="b6dd39f" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
			<style>/*! elementor - v3.6.5 - 27-04-2022 */
.elementor-widget-text-editor.elementor-drop-cap-view-stacked .elementor-drop-cap{background-color:#818a91;color:#fff}.elementor-widget-text-editor.elementor-drop-cap-view-framed .elementor-drop-cap{color:#818a91;border:3px solid;background-color:transparent}.elementor-widget-text-editor:not(.elementor-drop-cap-view-default) .elementor-drop-cap{margin-top:8px}.elementor-widget-text-editor:not(.elementor-drop-cap-view-default) .elementor-drop-cap-letter{width:1em;height:1em}.elementor-widget-text-editor .elementor-drop-cap{float:left;text-align:center;line-height:1;font-size:50px}.elementor-widget-text-editor .elementor-drop-cap-letter{display:inline-block}</style>	  <?php echo $product->description;?>						</div>
				</div>
                   
                    <div class="elementor-element elementor-element-c42f8c7 elementor-align-left elementor-widget elementor-widget-button" data-id="c42f8c7" data-element_type="widget" data-widget_type="button.default">
				<div class="elementor-widget-container">
					<div class="elementor-button-wrapper">
			<a href="#" class="elementor-button-link elementor-button elementor-size-sm addtocart" role="button" data-id="<?php echo $code;?>">
						<span class="elementor-button-content-wrapper">
						<span class="elementor-button-text">ORDER NOW</span>
		</span>
					</a>
		</div>
				</div>
				</div>
                            
									</div>
		</div>
							</div>
		</section>
                
                
							</div>
            
            <?php
        }else{
            echo '<p>Product Not Found</p>';
        }
    }
    $content = ob_get_clean();
    return $content;
}
add_shortcode('dutchie_product','dutchie_product');




function dutchie_vars( $qvars ) {
    $qvars[] = 'prod';
    return $qvars;
}
add_filter( 'query_vars', 'dutchie_vars' );
function dutchie_rewrite_basic() 
{
    add_rewrite_tag( '%prod%', '([^&]+)' );
    add_rewrite_rule( '^prod/([^/]+)([/]?)(.*)', 'index.php?prod=$matches[1]','top' );
    //add_rewrite_rule('^product-page/([^/]+)([/]?)(.*)', 'index.php?pagename=product-page?code=$matches[1]', 'top');
}
add_action('init', 'dutchie_rewrite_basic');
//add_action( 'init', 'add_custom_setcookie_rewrite_endpoints' );
function add_custom_setcookie_rewrite_endpoints() {
    add_rewrite_endpoint( 'code', EP_ALL, $query_vars = false );
}
function dutchie_product_title($title){
    global $dutchie_product;
    if($dutchie_product){
        $title = $dutchie_product->name;
    }
    return $title;
}

function dutchie_product_box($product,$menyType='MEDICAL'){
    ob_start();
    if($product){
      //  echo '<pre>';
      //  print_r($product);
      //  die();
        $post_id=0;
       /*$post_id = wc_get_product_id_by_sku( $product->id );
       if(!$post_id){
           $post_id = add_dutchie_product($product);
       }*/
       $weight = '';//explode($product->name);
       $effects = array();
       if($product->effects){
           foreach ($product->effects as $effect){
               $effects[]='effect_'.str_replace(' ','_',$effect);
           }
       }
       $thc = array(0,0);
       if($product->potencyThc->range){
           if(count($product->potencyThc->range)==2){
               $thc = $product->potencyThc->range;
           }else{
               $thc[1]=$product->potencyThc->range[0];
           }
       }
              $cbd = array(0,0);
       if($product->potencyThc->range){
           if(count($product->potencyThc->range)==2){
               $cbd = $product->potencyThc->range;
           }else{
               $cbd[1]=$product->potencyThc->range[0];
           }
       }
        ?>
            <div data-thc-min="<?php echo $thc[0]?>" data-thc-max="<?php echo $thc[1]?>" data-cbd-min="<?php echo $cbd[0]?>" data-cbd-max="<?php echo $cbd[1]?>" class="featured-product-box <?php echo $product->id;?>">
    <div class="fbp_c">
          <div class="fbp_0">
        <a href="/prod/<?php echo $product->id;?>" title="<?php echo $product->name?>">
            <div class="product-box-img" style="background-image: url(<?php echo $product->image?>?w=300&h=300)">
            </div>
        </a>
          </div>
        <div class="fbp_1">
            <?php if($product->staffPick){?>
            <div class="staffpick">Staff Pick</div>
            <?php } ?>
       
    <div class="product-box-features fb1">
        <ul>
            <?php 
            $pm = $product->strainType;
            if($pm!='NOT_APPLICABLE'){
            ?>
            <li class="pm-type-<?php echo ucfirst(strtolower($pm));?>"><?php echo ucfirst(strtolower($pm))?></li>
            <?php } ?>
            <?php 
            $pm = $product->potencyThc->formatted;
            if($pm){
            ?>
            <li class="pm-thc">THC: <?php echo $pm?></li>
            <?php } ?>
              <?php 
            $pm = $product->potencyCbd->formatted;
            if($pm){
            ?>
            <li class="pm-thc">CBD: <?php echo $pm?></li>
            <?php } ?>
            
        </ul>
    </div>
               <?php if($product->brand){?>
            <div class="brandname"><?php echo $product->brand->name?></div>
          <?php } ?>
        <a href="/prod/<?php echo $product->id;?>" title="<?php echo $product->name?>">
    <div class="product-box-name">
        <?php echo substr($product->name,0,50)?>
    </div>
        </a>
    

        <div class="product-box-description">
        <?php echo apply_filters('the_content',substr($product->description,0,80))?>
    </div>
            <div class="product-box-features fb2">
        <ul>
            <?php 
            $pm = $product->strainType;
            if($pm!='NOT_APPLICABLE'){
            ?>
            <li class="pm-type-<?php echo ucfirst(strtolower($pm));?>"><?php echo ucfirst(strtolower($pm))?></li>
            <?php } ?>
            <?php 
            $pm = $product->potencyThc->formatted;
            if($pm){
            ?>
            <li class="pm-thc">THC: <?php echo $pm?></li>
            <?php } ?>
              <?php 
            $pm = $product->potencyCbd->formatted;
            if($pm){
            ?>
            <li class="pm-thc">CBD: <?php echo $pm?></li>
            <?php } ?>
            
        </ul>
    </div>
        </div>
      <div class="fbp_2">
         <div class="product-box-price">
         <?php 
         $variant='';
            if(count($product->variants)>0){
                $variant = $product->variants[0]->option;
            if($menyType=='MEDICAL'){
                $pm = $product->variants[0]->priceMed;
            }else{
                $pm = $product->variants[0]->priceRec;
            }
            if($pm){
            ?>
            $<?php echo $pm?>
            <?php } ?>
            <?php } ?>
    </div>
    <a class="baddtocart" href="#" data-id="<?php echo $product->id?>" data-variant="<?php echo $variant;?>" title="<?php echo $product->name?>" data-price="<?php echo $product->variants[0]->priceMed?>" data-img="<?php echo $product->images[0]->url?>" data-name="<?php echo $product->name?>">Add to Cart</a>
</div>    
</div>
</div>
     <?php
    }
    $content = ob_get_clean();
    return $content;
}

function dutchie_product_box_woo($menyType='MEDICAL'){
    ob_start();
      global $product;
      $client = new dutchie;
      $client->auth();
      $dp = $client->getProduct($product->get_sku());
      if($dp){

       $post_id = $product->get_id();
        ?>

    <div class="fbp_c">
          <div class="fbp_0">
        <a href="<?php echo get_permalink($post_id)?>" title="<?php echo $dp->name?>">
            <div class="product-box-img" style="background-image: url(<?php echo $dp->image?>?w=300&h=300)">
            </div>
        </a>
          </div>
        <div class="fbp_1">
            <?php if($dp->staffPick){?>
            <div class="staffpick">Staff Pick</div>
            <?php } ?>
          <?php if($dp->brand){?>
            <div class="brandname"><?php echo $dp->brand->name?></div>
          <?php } ?>
    <div class="product-box-features">
        <ul>
            <?php 
            $pm = $dp->strainType;
            if($pm!='NOT_APPLICABLE'){
            ?>
            <li class="pm-type-<?php echo ucfirst(strtolower($pm));?>"><?php echo ucfirst(strtolower($pm))?></li>
            <?php } ?>
            <?php 
            $pm = $dp->potencyThc->formatted;
            if($pm){
            ?>
            <li class="pm-thc">THC: <?php echo $pm?></li>
            <?php } ?>
              <?php 
            $pm = $dp->potencyCbd->formatted;
            if($pm){
            ?>
            <li class="pm-thc">CBD: <?php echo $pm?></li>
            <?php } ?>
            
        </ul>
    </div>
        <a href="<?php echo get_permalink($post_id)?>" title="<?php echo $dp->name?>">
    <div class="product-box-name">
        <?php echo substr($dp->name,0,50)?>
    </div>
        </a>
    

        <div class="product-box-description">
        <?php echo apply_filters('the_content',substr($dp->description,0,80))?>
    </div>
            <div class="product-box-features">
        <ul>
            <?php 
            $pm = $dp->strainType;
            if($pm!='NOT_APPLICABLE'){
            ?>
            <li class="pm-type-<?php echo ucfirst(strtolower($pm));?>"><?php echo ucfirst(strtolower($pm))?></li>
            <?php } ?>
            <?php 
            $pm = $dp->potencyThc->formatted;
            if($pm){
            ?>
            <li class="pm-thc">THC: <?php echo $pm?></li>
            <?php } ?>
              <?php 
            $pm = $dp->potencyCbd->formatted;
            if($pm){
            ?>
            <li class="pm-thc">CBD: <?php echo $pm?></li>
            <?php } ?>
            
        </ul>
    </div>
        </div>
      <div class="fbp_2">
         <div class="product-box-price">
         <?php 
         $variant ='';
            if(count($dp->variants)>0){
                $variant = $dp->variants[0]->option;
            if($menyType=='MEDICAL'){
                $pm = $dp->variants[0]->priceMed;
            }else{
                $pm = $dp->variants[0]->priceRec;
            }
            if($pm){
            ?>
            $<?php echo $pm?>
            <?php } ?>
            <?php } ?>
    </div>
        <a class="baddtocart" href="#" data-id="<?php echo $dp->id?>" data-variant="<?php echo $variant;?>" title="<?php echo $dp->name?>">Add to Cart</a>
</div>    
</div>

     <?php
    }
    $content = ob_get_clean();
    return $content;
}



function custom_addtocart(){
    if(isset($_GET['addtocart'])){
        $product_id = isset($_GET['addtocart'])?$_GET['addtocart']:0;
        $product_id = (int) $product_id;
        $product = wc_get_product($product_id);
        if($product){
            $childs = $product->get_children();

            $atrs = $product->get_variation_attributes();
            $vas = array();
            foreach($atrs as $k=>$v){
                $vas['attribute_pa_'.$k]=$v[0];
            }
           
            if( !WC()->cart->find_product_in_cart( $product_id ) ){
                WC()->cart->add_to_cart( $product->get_id(), 1, $childs[0],$vas );
                wc_add_to_cart_message(array($product->get_id() => 1), true);
            }
        }
        wp_safe_redirect( wp_get_referer() );
        exit();
    }
}
add_action('template_redirect','custom_addtocart');

function custom_product_box($atts){
    $args = shortcode_atts( array(
		'id' => 0
	), $atts );
    ob_start();
    $client = new dutchie;
    $client->auth();
    $product = $client->getProduct($args['id']);
    if($product){
    //   $post_id = wc_get_product_id_by_sku( $product->id );
    //   if(!$post_id){
          // $post_id = add_dutchie_product($product);
    //   }
        ?>
<div class="product-box">
<a href="/prod/<?php echo $product->id?>" title="<?php echo $product->name?>">
    <div class="product-box-img" style="background-image: url(<?php echo $product->image?>?w=300&h=300)">
    </div>
    <div class="product-box-features">
        <ul>
            <?php 
            $pm = $product->strainType;
            if($pm!='NOT_APPLICABLE'){
            ?>
            <li class="pm-type-<?php echo ucfirst(strtolower($pm));?>"><?php echo ucfirst(strtolower($pm))?></li>
            <?php } ?>
            <?php 
            $pm = $product->potencyThc->formatted;
            if($pm){
            ?>
            <li class="pm-thc">THC: <?php echo $pm?></li>
            <?php } ?>
              <?php 
            $pm = $product->potencyCbd->formatted;
            if($pm){
            ?>
            <li class="pm-thc">CBD: <?php echo $pm?></li>
            <?php } ?>
            
        </ul>
    </div>
    <div class="product-box-name">
        <?php echo $product->name?>
    </div>
    
    <div class="product-box-price">
         <?php 
         $variant = '';
            if(count($product->variants)>0){
                $variant = $product->variants[0]->option;
            
            $pm = $product->variants[0]->priceMed;
            if($pm){
            ?>
            $<?php echo $pm?>
            <?php } ?>
            <?php } ?>
    </div> 
</a>
     <a class="baddtocart" href="#" data-id="<?php echo $product->id?>" data-variant="<?php echo $variant;?>" title="<?php echo $product->name?>">Add to Cart</a>
</div>    
     <?php
    }
    $content = ob_get_clean();
    return $content;
}
add_shortcode('product_box_custom','custom_product_box');


function custom_futured_product_box($atts){
    $args = shortcode_atts( array(
		'id' => 0
	), $atts );
    $post = get_post($args['id']);
    return featured_product_box($post);
}
add_shortcode('featured_product_box_custom','custom_futured_product_box');



function featured_product_box($post){
    ob_start();
    if($post){
       
        ?>
<div class="featured-product-box">
    <a href="<?php echo get_permalink($post)?>">
    <div class="product-box-img">
        <?php 
        $img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
        if($img){
            echo '<img src="'.$img[0].'" alt="" />';
        }
        ?>
    </div>
    <div class="product-box-features">
        <ul>
            <?php 
            $pm = get_field( "type", $post->ID );
            if($pm){
            ?>
            <li class="pm-type"><?php echo $pm?></li>
            <?php } ?>
            <?php 
            $pm = get_field( "thc", $post->ID );
            if($pm){
            ?>
            <li class="pm-thc">THC: <?php echo $pm?></li>
            <?php } ?>
              <?php 
            $pm = get_field( "cbd", $post->ID );
            if($pm){
            ?>
            <li class="pm-thc">CBD: <?php echo $pm?></li>
            <?php } ?>
            
        </ul>
    </div>
    <div class="product-box-name">
        <?php echo $post->post_title?>
    </div>
      <div class="product-box-description">
        <?php echo $post->post_content?>
    </div>
    <div class="product-box-price">
         <?php 
            $pm = get_field( "price", $post->ID );
            if($pm){
            ?>
            $<?php echo $pm?>
            <?php } ?>
    </div>
</a>
</div>    
     <?php
    }
    $content = ob_get_clean();
    return $content;
}


function templateInclude($template)
{
    $qv = get_query_var('prod', null);
    if ( $qv !== null) {
        global $dutchie_product;
        
        $client = new dutchie;
        $client->auth();
        $product = $client->getProduct($qv);
        if($product){
            $dutchie_product = $product;
            add_filter('pre_get_document_title', 'dutchie_product_title',99);
        }
        $template =  dirname(__FILE__) . '/product.php';
    }
    return $template;
}

add_filter('template_include', 'templateInclude',1);

function addproductcatheader(){
    echo  do_shortcode("[hfe_template id='414']");
}
function addproductfooter(){
    echo  do_shortcode("[hfe_template id='335']");
}
 function add_query_vars_custom_filter( $vars ){
                $vars[] = "pro_subcat";
                $vars[] = "pro_weight";
                $vars[] = "pro_brand";
                $vars[] = "pro_type";
                $vars[] = "pro_potency";
                return $vars;
              }
add_filter( 'query_vars', 'add_query_vars_custom_filter' );


// gallery
add_action( 'admin_menu', 'product_gallery_meta_box_add' );
 
function product_gallery_meta_box_add() {
	add_meta_box('productgallry', // meta box ID
		'Photo Gallery', // meta box title
		'product_gallery_print_box', // callback function that prints the meta box HTML 
		'product', // post type where to add it
		'normal', // priority
		'high' ); // position
}
 
/*
 * Meta Box HTML
 */
function product_gallery_print_box( $post ) {
	//$meta_key = 'product_gallery';
	//echo product_gallery_gallery_field( $meta_key, get_post_meta($post->ID, $meta_key, true) );
}
 
/*
 * Save Meta Box data
 */
//add_action('save_post', 'product_gallery_save');
 
function product_gallery_save( $post_id ) {
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;
 
	$meta_key = 'product_gallery';
 
	update_post_meta( $post_id, $meta_key, $_POST[$meta_key] );
 
	return $post_id;
}
function product_gallery_gallery_field( $name, $value = '' ) {

	$html = '<div><ul class="product_gallery_gallery_mtb">';
	/* array with image IDs for hidden field */
	$hidden = array();

	
	if( $images = get_posts( array(
		'post_type' => 'attachment',
		'orderby' => 'post__in', /* we have to save the order */
		'order' => 'ASC',
		'post__in' => explode(',',$value), /* $value is the image IDs comma separated */
		'numberposts' => -1,
		'post_mime_type' => 'image'
	) ) ) {

		foreach( $images as $image ) {
			$hidden[] = $image->ID;
			$image_src = wp_get_attachment_image_src( $image->ID, array( 80, 80 ) );
			$html .= '<li data-id="' . $image->ID .  '"><span style="background-image:url(' . $image_src[0] . ');width: 50px;height: 50px;display: block;background-position: center;background-size: contain;"></span><a href="#" class="product_gallery_gallery_remove">&times;</a></li>';
		}

	}

	$html .= '</ul><div style="clear:both"></div></div>';
	$html .= '<input type="hidden" name="'.$name.'" value="' . join(',',$hidden) . '" /><a href="#" class="button product_gallery_upload_gallery_button">Add Images</a>';

	return $html;
}



add_action( 'admin_enqueue_scripts', 'product_gallery_scripts_for_gallery' );
function product_gallery_scripts_for_gallery(){
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-widget');
	wp_enqueue_script('jquery-ui-sortable');

	if ( ! did_action( 'wp_enqueue_media' ) )
		wp_enqueue_media();

	wp_enqueue_script('productgallery', plugins_url( 'js/gallery.js', __FILE__ ), array('jquery','jquery-ui-sortable') );
}


function showfeaturedproducts(){
    ?>
<div data-elementor-type="wp-page" data-elementor-id="333s" class="elementor elementor-333">
        <section class="elementor-section elementor-top-section elementor-element elementor-element-d7e7782 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="d7e7782" data-element_type="section">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-53a5625" data-id="53a5625" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<div class="elementor-element elementor-element-61ebdfd elementor-widget elementor-widget-heading" data-id="61ebdfd" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default">Featured Products</h2>		</div>
				</div>
				<div class="elementor-element elementor-element-7d4835d elementor-widget elementor-widget-heading" data-id="7d4835d" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default">Choose your favorite strains. Prices are display per gram</h2>		</div>
				</div>
					</div>
		</div>
							</div>
		</section>
</div>
        <?php
}


function custom_product_list(){
    
}
add_shortcode('custom_product_list','custom_product_list');
function brand_dropdown( $post, $box ) {
if ( ! isset( $box['args'] ) || ! is_array( $box['args'] ) ) {
        $args = array();
    } else {
        $args = $box['args'];
    }
    $defaults = array('taxonomy'=>'brand');
    $r = wp_parse_args( $args, $defaults );
    $tax_name = esc_attr( $r['taxonomy'] );
    $taxonomy = get_taxonomy( $r['taxonomy'] );
    ?>
    <div id="taxonomy-<?php echo $tax_name; ?>" class="categorydiv">

    <?php //took out tabs for most recent here ?>

        <div id="<?php echo $tax_name; ?>-all">
            <?php
            $name = ( $tax_name == 'category' ) ? 'post_category' : 'tax_input[' . $tax_name . ']';
            echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
            ?>
            <ul id="<?php echo $tax_name; ?>checklist" data-wp-lists="list:<?php echo $tax_name; ?>" class="categorychecklist form-no-clear">
                <?php //wp_terms_checklist( $post->ID, array( 'taxonomy' => $tax_name, 'popular_cats' => $popular_ids ) ); ?>
            </ul>

            <?php $term_obj = wp_get_object_terms($post->ID, $tax_name ); //_log($term_obj[0]->term_id) 
          
            ?>
            <?php wp_dropdown_categories( array( 'taxonomy' => $tax_name, 'option_none_value' => 0 ,'id'=>$term_obj[0]->term_id, 'value_field'=> 'slug' , 'hide_empty' => 0, 'name' => "{$name}[]", 'selected' => $term_obj[0]->slug, 'orderby' => 'name', 'hierarchical' => 0, 'show_option_none' => "Select $tax_name" ) ); ?>

        </div>
    <?php if ( current_user_can( $taxonomy->cap->edit_terms ) ) : 
            // removed code to add terms here dynamically, because doing so added a checkbox above the newly added drop menu, the drop menu would need to be re-rendered dynamically to display the newly added term ?>
        <?php endif; ?>

        <p><a href="<?php echo site_url(); ?>/wp-admin/edit-tags.php?taxonomy=<?php echo $tax_name ?>&post_type=product">Add New</a></p>
    </div>
    <?php
}


function cartbtn(){
     $cart=isset($_COOKIE['cart'])?json_decode(stripslashes($_COOKIE['cart'])):array();

    return '<a href="/cart/" id="cartheader"><svg  xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="32px" height="35px"><path fill-rule="evenodd"  fill="rgb(255, 255, 255)" d="M10.754,2.071 C12.143,0.879 13.958,0.194 15.790,0.180 C17.595,0.148 19.402,0.763 20.814,1.889 C22.278,3.041 23.317,4.719 23.685,6.546 C23.912,7.590 23.830,8.664 23.847,9.723 C25.961,9.728 28.076,9.719 30.191,9.728 C30.670,9.713 31.129,10.067 31.234,10.537 C31.288,10.842 31.263,11.155 31.268,11.464 C31.267,17.198 31.268,22.932 31.267,28.666 C31.265,30.124 30.715,31.578 29.733,32.659 C28.606,33.938 26.913,34.673 25.212,34.656 C19.000,34.657 12.788,34.657 6.576,34.656 C5.171,34.666 3.768,34.173 2.689,33.271 C1.330,32.155 0.512,30.419 0.524,28.658 C0.519,22.719 0.522,16.777 0.523,10.837 C0.496,10.341 0.855,9.861 1.340,9.756 C1.632,9.704 1.931,9.728 2.226,9.724 C4.131,9.724 6.037,9.725 7.942,9.723 C7.960,8.746 7.884,7.758 8.057,6.789 C8.363,4.963 9.339,3.263 10.754,2.071 ZM13.196,2.967 C11.741,3.722 10.633,5.116 10.242,6.712 C9.985,7.695 10.081,8.719 10.064,9.723 C13.951,9.724 17.837,9.725 21.724,9.723 C21.705,8.784 21.799,7.829 21.590,6.904 C21.194,4.998 19.773,3.346 17.946,2.676 C16.418,2.094 14.641,2.197 13.196,2.967 ZM2.643,11.845 C2.644,17.486 2.637,23.128 2.646,28.769 C2.649,30.744 4.395,32.504 6.369,32.525 C12.649,32.535 18.931,32.526 25.211,32.530 C26.207,32.549 27.206,32.168 27.927,31.478 C28.699,30.760 29.149,29.713 29.144,28.657 C29.148,23.054 29.145,17.450 29.145,11.845 C27.379,11.845 25.613,11.844 23.847,11.845 C23.837,12.757 23.860,13.669 23.836,14.581 C23.810,15.134 23.281,15.596 22.730,15.555 C22.174,15.538 21.703,15.024 21.727,14.470 C21.720,13.595 21.729,12.720 21.724,11.845 C17.837,11.844 13.951,11.844 10.064,11.845 C10.055,12.758 10.078,13.670 10.054,14.582 C10.027,15.130 9.508,15.589 8.962,15.556 C8.402,15.547 7.923,15.032 7.945,14.474 C7.937,13.598 7.947,12.722 7.942,11.845 C6.175,11.844 4.409,11.845 2.643,11.845 Z"/></svg><span>'.count($cart).'</span></a>';
}
add_shortcode('cartbtn','cartbtn');

function dutchie_cart(){
    ob_start();
    $cart=isset($_COOKIE['cart'])?json_decode(stripslashes($_COOKIE['cart'])):array();
    
    $products = array();
    ?>
        <table class="shop_table shop_table_responsive cart" cellspacing="0">
		<thead>
			<tr>
				<th class="product-remove">&nbsp;</th>
				<th class="product-thumbnail">&nbsp;</th>
				<th class="product-name">Product</th>
				<th class="product-price">Price</th>
				<th class="product-quantity">Quantity</th>
				<th class="product-subtotal">Subtotal</th>
			</tr>
		</thead>
		<tbody>
			<?php 
                        $total = 0;
                        foreach($cart as $k=>$item){
                            $total+=$item->price*$item->qty;
                          //  $product = dutchie_get_product();
                        ?>
								<tr class="woocommerce-cart-form__cart-item cart_item">

						<td class="product-remove">
							<a href="#" class="remove" aria-label="Remove this item" data-id="<?php echo $item->id?>" data-variant="<?php echo $item->variant?>">×</a>						</td>

						<td class="product-thumbnail">
                                                    <a href="/prod/<?php echo $item->id?>"><img src="<?php echo $item->img ?>" alt="<?php echo $item->name ?>"></a>						</td>

						<td class="product-name" data-title="Product">
						<a href="/prod/<?php echo $item->id?>"><?php echo $item->name;?></a><dl class="variation">
			<dt class="variation-option">Option:</dt>
		<dd class="variation-option"><p><?php echo $item->variant;?></p>
</dd>
	</dl>
						</td>

						<td class="product-price" data-title="Price">
							<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span><?php echo number_format($item->price,2);?></bdi></span>						</td>

						<td class="product-quantity" data-title="Quantity">
							<div class="quantity">
				<label class="screen-reader-text" for="quantity_<?php echo $k?>"><?php echo $item->name;?>< quantity</label>
                        <input type="number" id="quantity_<?php echo $k?>" class="input-text qty text" step="1" min="0" max="" name="cart[<?php echo $k ?>][qty]" value="<?php echo $item->qty;?>" title="Qty" size="4" placeholder="" inputmode="numeric" autocomplete="off">
			</div>
							</td>

						<td class="product-subtotal" data-title="Subtotal">
							<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span><?php echo number_format($item->price*$item->qty,2);?></bdi></span>						</td>
					</tr>
								
			<tr>
                        <?php } ?>
				<td colspan="6" class="actions">

											
					
					<button type="submit" class="button" name="update_cart" value="Update cart" disabled="" aria-disabled="true">Update cart</button>

					
					<input type="hidden" id="woocommerce-cart-nonce" name="woocommerce-cart-nonce" value="607b9a1654"><input type="hidden" name="_wp_http_referer" value="/cart/">				</td>
			</tr>

					</tbody>
	</table>
            <div class="cart-collaterals">
	<div class="cart_totals ">

	
	<h2>Cart totals</h2>

	<table class="shop_table shop_table_responsive" cellspacing="0">

		<tbody><tr class="cart-subtotal">
			<th>Subtotal</th>
                        <td data-title="Subtotal"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span><?php echo number_format($total,2);?></bdi></span></td>
		</tr>

		
		<tr class="order-total">
			<th>Total</th>
			<td data-title="Total"><strong><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span><?php echo number_format($total,2);?></bdi></span></strong> </td>
		</tr>

		
	</tbody></table>

	<div class="wc-proceed-to-checkout">
		
<a href="/checkout/" class="checkout-button button alt wc-forward">
	Proceed to checkout</a>
	</div>

	
</div>
</div>
        
    <?php
    $c = ob_get_clean();
    return $c;
}
add_shortcode('dutchie_cart','dutchie_cart');

function dutchie_cart_sidebar(){
    ob_start();
    $cart=isset($_COOKIE['cart'])?json_decode(stripslashes($_COOKIE['cart'])):array();
    $client = new dutchie;
    $client->auth();
    $dp = $client->store();
    $store_name = '';
    $store_address = '';
    if($dp){
        
          $store_name = $dp->name;
          $store_address = $dp->address;
    }

    ?>
            <div class="cartbox">
                <div class="cartboxcontainer">
                <a href="#" id="hidecartbox"><svg viewBox="0 0 24 24" focusable="false" class="chakra-icon css-onkibi" aria-hidden="true"><path fill="currentColor" d="M.439,21.44a1.5,1.5,0,0,0,2.122,2.121L11.823,14.3a.25.25,0,0,1,.354,0l9.262,9.263a1.5,1.5,0,1,0,2.122-2.121L14.3,12.177a.25.25,0,0,1,0-.354l9.263-9.262A1.5,1.5,0,0,0,21.439.44L12.177,9.7a.25.25,0,0,1-.354,0L2.561.44A1.5,1.5,0,0,0,.439,2.561L9.7,11.823a.25.25,0,0,1,0,.354Z"></path></svg></a>
                <div class="carboxbody">
            <div class="cartheader">
                <b>You're shopping at:</b><br />
                <b><?php echo $store_name;?></b>
                <b><?php echo $store_address;?></b>
                <div class="deliveryoptions">
                    <label><input type="radio" name="shipping_type" value="delivery"><span>Delivery</span></label>
                    <label><input type="radio" name="shipping_type" value="pickup"><span>Pickup</span></label>
                </div>
                <h3>Shopping Cart (<?php echo count($cart)?> items)</h3>
            </div>
                
        <table class="shop_table shop_table_responsive cart" cellspacing="0">
		
		<tbody>
			<?php 
                        $total = 0;
                        foreach($cart as $k=>$item){
                            $total+=$item->price*$item->qty;
                        ?>
								<tr class="woocommerce-cart-form__cart-item cart_item">

						

						<td class="product-thumbnail">
                                                    <a href="/prod/<?php echo $item->id?>"><img src="<?php echo $item->img ?>?w=80&h=80" alt="<?php echo $item->name ?>"></a>						</td>

						<td class="product-name" data-title="Product">
						<a href="/prod/<?php echo $item->id?>"><?php echo $item->name;?></a><dl class="variation">
			<dt class="variation-option">Option:</dt>
		<dd class="variation-option"><p><?php echo $item->variant;?></p>
</dd>
	</dl>
                                                <div class="quantity">
				<label class="screen-reader-text" for="quantity_<?php echo $k?>"><?php echo $item->name;?>< quantity</label>
                        <input type="number" id="quantity_<?php echo $k?>" data-index="<?php echo $k;?>" class="input-text qty text" step="1" min="0" max="" name="cart[<?php echo $k ?>][qty]" value="<?php echo $item->qty;?>" title="Qty" size="4" placeholder="" inputmode="numeric" autocomplete="off">
			</div>
						</td>

						

						

						
                                                <td class="product-price" data-title="Price">
							<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span><?php echo number_format($item->price,2);?></bdi></span>
                                                        <a href="#" class="removeit" aria-label="Remove this item" data-id="<?php echo $item->id?>" data-variant="<?php echo $item->variant?>" data-index="<?php echo $k;?>">remove</a>
                                                </td>
					</tr>
								
			
                        <?php } ?>
				
					</tbody>
	</table>
                </div>
            <div class="cart-collaterals">
	<div class="cart_totals ">


	<table class="shop_table shop_table_responsive" cellspacing="0">

		<tbody><tr class="cart-subtotal">
			<th>Subtotal</th>
                        <td data-title="Subtotal"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span><?php echo number_format($total,2);?></bdi></span></td>
		</tr>

		
		<tr class="order-total">
			<th>Total</th>
			<td data-title="Total"><strong><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span><?php echo number_format($total,2);?></bdi></span></strong> </td>
		</tr>

		
	</tbody></table>

	<div class="wc-proceed-to-checkout">
		
<a href="/checkout/" class="checkout-button button alt wc-forward">
	Proceed to checkout</a>
	</div>

	
</div>
</div>
            </div>
            </div>
    <?php
    $c = ob_get_clean();
    return $c;
}
add_action('wp_footer',function(){
    echo dutchie_cart_sidebar();
},999);

function dutchie_checkout(){
    if(is_admin()){
        return '';
    }
    $cart=isset($_COOKIE['cart'])?json_decode(stripslashes($_COOKIE['cart'])):array();
   $shipping_type=isset($_COOKIE['shipping_type'])?$_COOKIE['shipping_type']:'DELIVERY';
   $can_view = false;
   global $wp_query;
   if(get_query_var('order-received')!=''){
       $can_view = true;
   }
   if($cart){
       $can_view = true;
   }
  if($can_view){
      
      
      if(!is_user_logged_in()){

          echo '<p>Please <a href="/login">Login</a> or <a href="/register">Register</a> to checkout</p>';

      }else{
          $current_user = wp_get_current_user();
          
          $dutchie_settings = get_option( 'dutchie_option_name' );
       
          if(!isset($dutchie_settings['dutchie_checkout_active'])){
             echo do_shortcode('[woocommerce_checkout]');
          }else{
          
          
     /*      $order_data = array(
                'status' => apply_filters('woocommerce_default_order_status', 'pending'),
                'customer_id' => $current_user->ID
           );
        $new_order = wc_create_order($order_data);
    
        foreach ($cart as $key=>$values) {
       
            $item_id = $new_order->add_product(
                    $values['data'], $values['qty'], array(
                    'variation' => $values['variant'],
                    'totals' => array(
                        'subtotal' => $values['line_subtotal'],
                        'subtotal_tax' => $values['line_subtotal_tax'],
                        'total' => $values['line_total'],
                        'tax' => $values['line_tax'],
                        'tax_data' => $values['line_tax_data'] // Since 2.2
                    )
                )
            );
        }
        $new_order->calculate_totals();
        $new_order->save(); */

        $client = new dutchie;
        $client->auth();
        $checkout=$client->createCart(strtoupper($shipping_type),'MEDICAL',array());
        //update_post_meta($new_order->get_id(),'dutchie_id',$checkout->id);
          foreach ( $cart as $cart_item ) {
//              $product= $cart_item['data'];
              $client->addToCart($cart_item->id,$cart_item->qty,$checkout->id,$cart_item->variant);
          }
          unset($_COOKIE['cart']);
          unset($_COOKIE['shipping_type']);
          
          ?>
            <h2>Loading...</h2>
            <script>
         //       setTimeout(function(){
          //  window.location.href='https://dutchie.com/checkouts/<?php echo $client->dispensary;?>/<?php echo $checkout->id;?>/?r=<?php echo get_home_url()?>/thanks-order&externalUserDetails[email]=<?php echo urlencode($current_user->user_email)?>';
      //     },2000);
            </script>    
          <?php
          }
      }
  }else{
      echo '<p>Cart is empty. <a href="/shop">Shop Now</a></p>';
  }
  ?>
            <style>
                #content {
                    padding: 160px 0;
                    text-align: center;
                  }
                  #content *{
                      color: #000;
                  }
                  #content a{
                      text-decoration: underline;
                  }
            </style>   
   <?php

}
add_shortcode('dutchie_checkout','dutchie_checkout');

add_filter( 'woocommerce_locate_template', 'woo_adon_plugin_template', 1, 3 );
   function woo_adon_plugin_template( $template, $template_name, $template_path ) {
     global $woocommerce;
     $_template = $template;
     if ( ! $template_path ) 
        $template_path = $woocommerce->template_url;
 
     $plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/templates/';
 
    // Look within passed path within the theme - this is priority
    $template = locate_template(
    array(
      $template_path . $template_name,
      $template_name
    )
   );
 
   if( ! $template && file_exists( $plugin_path . $template_name ) )
    $template = $plugin_path . $template_name;
 
   if ( ! $template )
    $template = $_template;

   return $template;
}

function woo_remove_hooks(){
    remove_action('woocommerce_before_main_content','woocommerce_breadcrumb',20);
    remove_action('woocommerce_sidebar','woocommerce_get_sidebar',10);
    remove_action('woocommerce_after_single_product_summary','woocommerce_output_product_data_tabs',10);
    remove_action('woocommerce_single_product_summary','woocommerce_template_single_meta',40);
    add_action('woocommerce_single_product_summary',function(){
        global $product;
        the_content();
    },6);
   remove_action('woocommerce_after_shop_loop_item','woocommerce_template_loop_add_to_cart',10);
   add_action('woocommerce_single_product_summary','woocommerce_template_single_brand',4);
   remove_action('woocommerce_after_single_product_summary','woocommerce_output_related_products',20);
   add_action('woocommerce_after_single_product',function(){
       add_action('astra_content_after','woocommerce_output_related_products',0);
   },0);
   
}
add_action('init','woo_remove_hooks');

function woocommerce_product_related_products_heading_custom($title){
    return 'Related Products';
}
add_filter('woocommerce_product_related_products_heading','woocommerce_product_related_products_heading_custom');

function woocommerce_template_single_add_to_cart_custom(){
      global $product;
       global $dutchie_product;
      if($dutchie_product){
          ?>
            <div class="product-box-features" style="padding-bottom: 5px;">
        <ul>
            <?php 
            $pm = $dutchie_product->strainType;
            if($pm!='NOT_APPLICABLE'){
            ?>
            <li class="pm-type-<?php echo ucfirst(strtolower($pm));?>"><?php echo ucfirst(strtolower($pm))?></li>
            <?php } ?>
            <?php 
            $pm = $dutchie_product->potencyThc->formatted;
            if($pm){
            ?>
            <li class="pm-thc">THC: <?php echo $pm?></li>
            <?php } ?>
              <?php 
            $pm = $dutchie_product->potencyCbd->formatted;
            if($pm){
            ?>
            <li class="pm-thc">CBD: <?php echo $pm?></li>
            <?php } ?>
            
        </ul>
    </div>
              
              <?php
      }
}
add_action('woocommerce_single_product_summary','woocommerce_template_single_add_to_cart_custom',7);
add_action('woocommerce_single_product_summary','woocommerce_template_single_meta_custom',40);

add_action('woocommerce_before_single_product','woocommerce_before_single_product_custom');
function woocommerce_before_single_product_custom(){
      global $product;
      global $dutchie_product;
      $client = new dutchie;
      $client->auth();
      $dp = $client->getProduct($product->get_sku());
      if($dp){    
          $dutchie_product = $dp;
      }
}

function dutchie_get_product($id){
      $client = new dutchie;
      $client->auth();
      return $client->getProduct($id);
}

function dutchie_woo_product_redirect(){
   global $wp_query;
   $queried_post_type = get_query_var('post_type');
   if(is_single() && $queried_post_type =='product'){
       $queried_post = get_page_by_path($wp_query->query_vars['name'],OBJECT,'product');
       if($queried_post){
           wp_redirect( home_url('/prod/'.get_post_meta($queried_post->ID,'_sku',true)), 301 );
       }else{
           wp_redirect( home_url('/'), 301 );
       }
       
        exit;
   }
}
add_action('template_redirect','dutchie_woo_product_redirect');

function dutchie_get_product_single(){
    if(isset($_GET['procode'])){
         $res = dutchie_get_product($_GET['procode']);
         if($res){
            global $woocommerce;
            $product_id = wc_get_product_id_by_sku($res->id);
            if(!$product_id){
                $product_id = add_dutchie_product($res);
            }
            $product = wc_get_product($product_id);
            
            if($product){
                $childs = $product->get_children();

                $atrs = $product->get_variation_attributes();
                $vas = array();
                foreach($atrs as $k=>$v){
                    $vas['attribute_'.$k]=$v[0];
                }
                if( !WC()->cart->find_product_in_cart( $product_id ) ){
                    if($childs){
                        $r = WC()->cart->add_to_cart( $product->get_id(), 1, $childs[0], $vas, array() );
                    }else{
                        $r = WC()->cart->add_to_cart( $product->get_id(), 1 );
                    }
                }

                echo json_encode($res);
            }else{
                echo 'error';
            }
             
             
             
         }else{
             echo 'error';
         }
        die();
    }
}
add_action('template_redirect','dutchie_get_product_single');


function dutchie_removefromcart(){
    if(isset($_GET['proid'])){
         $res = dutchie_get_product($_GET['proid']);
         if($res){
            global $woocommerce;
            $product_id = wc_get_product_id_by_sku($res->id);
            if(!$product_id){
                $product_id = add_dutchie_product($res);
            }
            $product = wc_get_product($product_id);
            
            if($product){
                $product_cart_id = WC()->cart->generate_cart_id( $product_id );
                $cart_item_key = WC()->cart->find_product_in_cart( $product_cart_id );
                if ( $cart_item_key ) {
                  WC()->cart->remove_cart_item( $cart_item_key );
                  echo 'success';
                }else{
                    echo 'error';
                }
                
            }else{
                echo 'error';
            }
             
             
             
         }else{
             echo 'error';
         }
        die();
    }
}
add_action('template_redirect','dutchie_removefromcart');

add_action('woocommerce_before_shop_loop_item','woocommerce_before_shop_loop_item_custom');
function woocommerce_before_shop_loop_item_custom(){
      global $product;
      global $dutchie_product;
      $client = new dutchie;
      $client->auth();
      $dp = $client->getProduct($product->get_sku());
      if(!$dp){    
          $dp = $client->getProduct($product->get_sku());
      }
      if($dp){
          $dutchie_product = $dp;
      }
}

function woocommerce_template_single_brand(){
    global $dutchie_product;
    if($dutchie_product){
        if($dutchie_product->brand){
            echo $dutchie_product->brand->name;
        }
    }
}

function woocommerce_template_single_meta_custom(){
        global $product;
        global $dutchie_product;
      if($dutchie_product){

    ?>
             <?php 
        if($dutchie_product->brand){
            ?><span class="posted_in"><?php echo $dutchie_product->brand->name;?></span><?php
        }
        ?>
            <div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>

	<?php endif; ?>
                <span class="posted_in">Categories: <?php echo $dutchie_product->category?> <?php if($dutchie_product->subcategory!=''){ echo ', '.$dutchie_product->subcategory;}?></span>
	<?php 
        if($dutchie_product->effects){
            ?><span class="posted_in">Effects: <?php echo implode(', ', $dutchie_product->effects)?></span><?php
        }
        ?>
            <?php 
        if($dutchie_product->staffPick){
            ?><span class="posted_in">Staff Picked</span><?php
        }
        ?>
            
        <?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>
        <?php
      }
}


function woocommerce_template_loop_add_to_cart_custom(){
    global $product;
    
?>
            <a class="baddtocart" style="text-align: center;" href="#" data-id="" title="">Add to Cart</a>   
           <?php
   
}
add_action('woocommerce_after_shop_loop_item','woocommerce_template_loop_add_to_cart_custom',99);
function woocommerce_after_shop_loop_item_title_custom(){
    global $product;
    global $dutchie_product;
      if($dutchie_product){
          ?>
           <?php 
           if($dutchie_product->brand){
           ?>
           <span class="ast-woo-product-brand" style="color: #ccc;">
               <?php echo $dutchie_product->brand->name?>
							</span>
           <?php } ?>
           <?php 
           if($dutchie_product->category){
           ?>
           <span class="ast-woo-product-category-custom" style="color: #ccc;">
               <?php echo $dutchie_product->category?>
							</span>
           <?php } ?>
            <?php 
           if($dutchie_product->staffPick){
           ?>
            <span class="staffpick" style="color:#ccc;">Staff Pick</span>
           <?php } ?>
            <div class="product-box-features" style="padding-bottom: 5px;">
        <ul>
            <?php 
            $pm = $dutchie_product->strainType;
            if($pm!='NOT_APPLICABLE'){
            ?>
            <li class="pm-type-<?php echo ucfirst(strtolower($pm));?>"><?php echo ucfirst(strtolower($pm))?></li>
            <?php } ?>
            <?php 
            $pm = $dutchie_product->potencyThc->formatted;
            if($pm){
            ?>
            <li class="pm-thc">THC: <?php echo $pm?></li>
            <?php } ?>
              <?php 
            $pm = $dutchie_product->potencyCbd->formatted;
            if($pm){
            ?>
            <li class="pm-thc">CBD: <?php echo $pm?></li>
            <?php } ?>
            
        </ul>
    </div>
              
              <?php
      }
      
}
add_action('woocommerce_after_shop_loop_item_title','woocommerce_after_shop_loop_item_title_custom',0);

add_filter ( 'woocommerce_account_menu_items', 'woocommerce_account_menu_items_custom',11,1 );

function woocommerce_account_menu_items_custom( $menu_links ){
	unset( $menu_links['edit-address'] ); 
	unset( $menu_links['dashboard'] ); 
	unset( $menu_links['payment-methods'] ); 
	unset( $menu_links['downloads'] );    
        $logout = $menu_links['customer-logout'];
        unset( $menu_links['customer-logout'] ); 
        $menu_links['dashboard']='Account Information';
	$menu_links['orders'] = 'Order History';
        $menu_links['medical-documents'] = 'Medical Document';
        $menu_links['resources'] = 'Resources ';
        $menu_links['upload-documents'] = 'Upload Documents';
        $menu_links['tax-receipts'] = 'Tax Receipts';
        $menu_links['customer-logout'] = $logout;
	return $menu_links;
}


function dutchie_my_account(){
    ob_start();
          if(!is_user_logged_in()){
          echo '<p>Please <a href="/login">Login</a> or <a href="/register">Register</a> </p>';
      }else{
    ?>
            <div class="dutchie_account_container">
                <div class="account_nav">
                    <ul>
                        <li><a href="?ac_se=account">Account Information</a></li>
                        <li><a href="?ac_se=orders">Order History</a></li>
                        <li><a href="?ac_se=doc">Medical Document</a></li>
                        <li><a href="?ac_se=upload_doc">Upload Documents</a></li>
                        <li><a href="?ac_se=tax">Tax Receipts</a></li>
                        <li><a href="<?php echo wp_logout_url( home_url() ); ?>">Logout</a></li>
                    </ul>
                </div>
                <div class="account_nav_content">
                    <?php 
                    $ac_se = isset($_GET['ac_se'])?$_GET['ac_se']:'account';
                    if($ac_se=='account'){
                        ?><h2>Welcome</h2><?php
                    }elseif($ac_se=='orders'){
                        ?><h2>My Orders</h2><?php
                        $order_id = isset($_GET['order_id'])?$_GET['order_id']:'';
                        if($order_id){
                            
                        }else{
                            $dutchie = new dutchie;
                            $orders = $dutchie->getOrders();
                            print_r($orders);
                        }
                        
                    }
                    ?>
                </div>
            </div>    
    <?php
      }
    $c = ob_get_clean();
    return $c;
}
add_shortcode('dutchie_my_account','dutchie_my_account');


add_action( 'user_register', 'dutchie_registration_save', 10, 1 );
function dutchie_registration_save( $user_id ) {
    $user = new WP_User( $user_id );
    $user->add_role( 'customer' );
}

function dutchie_remove_woohooks(){
    add_filter( 'woocommerce_add_to_cart_redirect', '__return_empty_string' );
    add_filter( 'woocommerce_add_to_cart_form_action', '__return_empty_string' ); 
}
add_action('init','dutchie_remove_woohooks');

function woocommerce_cart_emptied_custom($contents){
    unset($_COOKIE['cart']);
}
add_action('woocommerce_cart_emptied','woocommerce_cart_emptied_custom');

function dutchie_search_product(){
    ob_start();
    ?><?php
    
    $c = ob_get_clean();
    return $c;
}
add_shortcode('dutchie_search_product','dutchie_search_product');



