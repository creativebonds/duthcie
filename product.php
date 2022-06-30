<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>
<style>
    .ast-primary-header-bar {
  background-color: #000;
}
</style>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script>
(function($){
    $(document).ready(function(){
        $('#productslider').slick({
            arrows:false,
            dots:true
        });
    })
})(jQuery)
</script>
<div class="product-container">
<?php 
    global $dutchie_product;
    if($dutchie_product){

        
    ?>
    <div class="ast-container-fluid">
        <div class="ast-row">
            <div class="ast-grid-common-col ast-width-50 ast-width-md-6">
                <ul id="productslider">
                            <?php 
                          
                                if($dutchie_product->images){
                                foreach( $dutchie_product->images as $image ) {
		
                        ?>
                    <li><img src="<?php echo $image->url;?>" alt="" /></li>
                            <?php
                }
                                }
                                ?>				
                </ul>
            
            </div>
            <div class="ast-grid-common-col ast-width-50 ast-width-md-6">
                <?php if($dutchie_product->staffPick){?>
            <div class="staffpick">Staff Pick</div>
            <?php } ?>
             <?php 
                if($dutchie_product->brand){
                    ?><span class="prodbrand"><?php echo $dutchie_product->brand->name;?></span><?php
                }
                ?>
                <h1 class="prodname"><?php echo $dutchie_product->name;?></h1>
               <div class="product-box-features">
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
                    <div class="prodesc">
                        <p><?php echo $dutchie_product->description;?></p>
                    </div>
                     
                <div id="price"></div>
                    <select id="varient_id">
                        <?php 
                        foreach($dutchie_product->variants as $k=>$v){
                            echo '<option value="'.$v->option.'" data-price="'.$v->priceMed.'">'.$v->option.'</option>';
                        }
                        ?>
                    </select>
                    <input type="number" value="1" min="1" id="qty" />
                    <button id="daddtocart" data-id="<?php echo $dutchie_product->id;?>">Add to Cart</button>
                    
                    
                        <span class="posted_in">Categories: <?php echo $dutchie_product->category?> <?php if($dutchie_product->subcategory!=''){ echo ', '.$dutchie_product->subcategory;}?></span>
	<?php 
        if($dutchie_product->effects){
            ?><span class="posted_in">Effects: <?php echo implode(', ', $dutchie_product->effects)?></span><?php
        }
        ?>
            </div>
        </div>
    </div>
        
    <?php

    }else{
        ?>
    <p>Product not found</p>
            <?php
    }
?>
</div>
<?php get_footer(); ?>