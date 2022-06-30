<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>
<style>
      @media (min-width: 921px){
          
.ast-theme-transparent-header #masthead {
  position: absolute;
  left: 0;
  right: 0;
}
      }
</style>
<script>
(function($){
    $(document).ready(function(){
        $('#productfilters select').on('change',function(){
            $('#productfilters').submit();
        });
    })
})(jQuery);
</script>
	<div id="primary" <?php astra_primary_class(); ?>>
         
		<?php astra_primary_content_top(); ?>
            <div class="page-header">
            <h2>Featured Products</h2>
            <p>Choose your favorite strains. Prices are display per gram</p>
            </div>
            <div class="filters">
                
                <form id="productfilters" method="get" action="<?php echo get_term_link(get_queried_object_id())?>">
                <div class="filter">
                   
                    <select id="category" name="pro_subcat">
                        <option value="">Subcategories</option>
                        <?php 
                        $term_id = get_queried_object_id();
                        $taxonomy_name = 'product_cat';
                        $termchildren = get_term_children( $term_id, $taxonomy_name );
                        $current_id = get_query_var('pro_subcat');
                        if($termchildren){
                            foreach($termchildren as $k=>$v){
                                $term = get_term_by( 'id', $v, $taxonomy_name );
                                
                                if($term){
                                    $selected='';
                                    if($term->term_id==$current_id){
                                        $selected='selected';
                                    }
                                    echo '<option value="'.$term->term_id.'" '.$selected.'>'.$term->name.'</option>';
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="filter">
                    <select id="weight" name="pro_weight">
                        <option value="">Weight</option>
                        <?php 
                        $current_id = get_query_var('pro_weight');
                        $taxonomy_name = 'weight';
              
                        $termchildren = get_terms( array(
                            'taxonomy'=>$taxonomy_name,
                            'hide_empty'=>0
                        ) );
                        if($termchildren){
                            foreach($termchildren as $k=>$term){
                               
                                $selected='';
                                    if($term->term_id==$current_id){
                                        $selected='selected';
                                    }
                                    echo '<option value="'.$term->term_id.'" '.$selected.'>'.$term->name.'</option>';
                               
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="filter">
                    <select id="brand" name="pro_brand">
                        <option value="">Brands</option>
                        <?php 
                        $current_id = get_query_var('pro_brand');
                        $taxonomy_name = 'brand';
                        $termchildren = get_terms( array(
                            'taxonomy'=>$taxonomy_name,
                            'hide_empty' => false,
                        ) );
                        if($termchildren){
                            foreach($termchildren as $k=>$term){
                               
                                $selected='';
                                    if($term->term_id==$current_id){
                                        $selected='selected';
                                    }
                                    echo '<option value="'.$term->term_id.'" '.$selected.'>'.$term->name.'</option>';
                               
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="filter">
                    <select id="type" name="pro_type">
                        <option value="">Types</option>
                        <?php 
                        $current_id = get_query_var('pro_type');
                        $taxonomy_name = 'type';
  
                        $termchildren = get_terms( array(
                            'taxonomy'=>$taxonomy_name,
                            'hide_empty' => false,
                        ) );
                        if($termchildren){
                            foreach($termchildren as $k=>$term){
                               
                                $selected='';
                                    if($term->term_id==$current_id){
                                        $selected='selected';
                                    }
                                    echo '<option value="'.$term->term_id.'" '.$selected.'>'.$term->name.'</option>';
                               
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="filter">
                    <select id="potency" name="pro_potency">
                        <option value="">Potency</option>
                        <?php 
                        $current_id = get_query_var('pro_potency');
                        $taxonomy_name = 'potency';
                        $termchildren = get_terms( array(
                            'taxonomy'=>$taxonomy_name,
                            'hide_empty'=>0
                        ) );
                        if($termchildren){
                            foreach($termchildren as $k=>$term){
                               
                                $selected='';
                                    if($term->term_id==$current_id){
                                        $selected='selected';
                                    }
                                    echo '<option value="'.$term->term_id.'" '.$selected.'>'.$term->name.'</option>';
                               
                            }
                        }
                        ?>
                    </select>
                </div>
                </form>
            </div>
		<?php 
                 while ( have_posts() ) : the_post();
                 global $post;
                    featured_product_box($post);
                 endwhile;
                ?>

		<?php astra_primary_content_bottom(); ?>

	</div><!-- #primary -->
<?php 

?>
<?php get_footer(); ?>