<?php


function fetch_dutchie_products($args=array()){
    if(isset($args['type'])){
        $params['menuType']=$args['type'];
    }
    $params['limit']=12;
    if(isset($args['limit'])){
        $params['limit']=$args['limit'];
    }
    if(isset($args['page'])){
        $params['offset']=$params['limit']*$args['page'];
    }else{
        $params['offset']=0;
    }
    $select_cat = isset($args['pro_cat'])?$args['pro_cat']:'';
    if(!empty($select_cat)){
        $params['category']=$select_cat;
    }
    $select_weight = isset($args['pro_weight'])?$args['pro_weight']:'';
    if(!empty($select_weight)){
        $params['weight']=$select_weight;
    }
    $select_brand = isset($args['pro_brand'])?$args['pro_brand']:'';
    if(!empty($select_brand)){
        $params['brandId']=$select_brand;
    }
    $select_type = isset($args['pro_type'])?$args['pro_type']:'';
    if(!empty($select_type)){
        $params['strainType']=$select_type;
    }
    $client = new dutchie;
    $client->auth();
    return $client->getMenuraw($params);    
}

add_action( 'wp_ajax_fetch_dutchie', 'ajax_fetch_dutchie_products' );
 add_action( 'wp_ajax_nopriv_fetch_dutchie', 'ajax_fetch_dutchie_products' );
function ajax_fetch_dutchie_products() {

    $products =  fetch_dutchie_products($_POST);
    if($products){
        foreach($products as $product){
            echo dutchie_product_box($product);
        }
    }else{
        echo 'ALL';
    }
    wp_die();
}

function dutchie_products_sync(){
    if(isset($_GET['sync'])){
        $pg = isset($_GET['pg'])?$_GET['pg']:0;
         $menutype = isset($_GET['menutype'])?$_GET['menutype']:'';
      
    $products = fetch_dutchie_products(array('page'=>$pg,'limit'=>5,'menuType'=> strtoupper($menutype)));
    if($products){
        
        foreach($products as $id=>$product){
            echo $id.'<br />';
            echo $product->id.' <br />';
            echo $product->name.'<br />';

            add_dutchie_product($product,$menutype);
        }
        ?>
        <script>
        window.location.href = "<?php echo get_home_url()?>/?sync=1&menutype=<?php echo $menutype;?>&pg=<?php echo ++$pg;?>";
        </script>    
        <?php
        die();
    }
    }
}
add_action('init','dutchie_products_sync');
function add_dutchie_product($product,$menuType=''){
    if ( ! function_exists( 'post_exists' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/post.php' );
}
        $post_id = wc_get_product_id_by_sku( $product->id );
       // echo '$post_id: '.$post_id;
     //   echo '<br />';
        
        if(!$post_id){
          $objProduct = new WC_Product_Variable();
          $objProduct->set_name($product->name);
          $objProduct->set_description($product->description);
          $objProduct->set_status('publish');
          $objProduct->set_catalog_visibility('visible');
          $objProduct->set_sku($product->id);
          $post_id = $objProduct->save();
        }
        if($product->category){
            attach_product_terms($post_id,$product->category,'product_cat');
        }
        if($product->brand){
            attach_product_terms($post_id,$product->brand->name,'brand',array('slug'=>$product->brand->id,'description'=>$product->brand->description));
        }
        if($product->effects){
            foreach($product->effects as $effect){
                attach_product_terms($post_id,$effect,'effects');
            }
        }
     
        if($product->strainType!=''){
            attach_product_terms($post_id,$product->strainType,'strain_type');
        }
        $price =  0;
        if(count($product->variants)>0){
            $price = $product->variants[0]->priceMed;
        }
        if($product->potencyCbd->range){
            update_post_meta( $post_id, 'min_cbd', $product->potencyCbd->range[0] );
            update_post_meta( $post_id, 'cbd_unit', $product->potencyCbd->unit );
            if(isset($product->potencyCbd->range[1])){
                update_post_meta( $post_id, 'max_cbd', $product->potencyCbd->range[1] );
            }
        }
        if($product->potencyThc->range){
            update_post_meta( $post_id, 'thc_unit', $product->potencyThc->unit );
            update_post_meta( $post_id, 'min_thc', $product->potencyThc->range[0] );
            if(isset($product->potencyThc->range[1])){
                update_post_meta( $post_id, 'max_thc', $product->potencyThc->range[1] );
            }
        }
        if(!empty($product->staffPick)){
            update_post_meta( $post_id, 'staff_pick', 1 );
        }
 
        if(!empty($menuType)){
            attach_product_terms($post_id,ucfirst(strtolower($menuType)),'menu_type');
        }
//        echo '<pre>';
//        print_r($product);
//        die();
        wp_set_object_terms( $post_id, 'variable', 'product_type' ); // set product is simple/variable/grouped
        update_post_meta( $post_id, '_visibility', 'visible' );
        update_post_meta( $post_id, '_stock_status', 'instock');
        update_post_meta( $post_id, 'total_sales', '0' );
        update_post_meta( $post_id, '_downloadable', 'no' );
        update_post_meta( $post_id, '_virtual', 'yes' );
        update_post_meta( $post_id, '_regular_price', '' );
        update_post_meta( $post_id, '_sale_price', '' );
        update_post_meta( $post_id, '_purchase_note', '' );
        update_post_meta( $post_id, '_product_attributes', array() );
        update_post_meta( $post_id, '_sale_price_dates_from', '' );
        update_post_meta( $post_id, '_sale_price_dates_to', '' );
        update_post_meta( $post_id, '_price', $price );
        update_post_meta( $post_id, '_sold_individually', '' );
        update_post_meta( $post_id, '_manage_stock', 'no' ); // activate stock management
        wc_update_product_stock($post_id, 10000, 'set'); // set 1000 in stock
        update_post_meta( $post_id, '_backorders', 'no' );
        $img_ids = array();
        foreach($product->images as $img){
            $fname =  dutchie_download_image($img->url);
            if($fname){
                $faname = basename($product->image);
                $fmime = mime_content_type($fname);
                $fext = mime2ext($fmime);
                rename($fname,$faname.'.'.$fext);
                $pn = $faname.'.'.$fext;
                $pe = post_exists( $pn, '', '', 'attachment' );
                if(!$pe){
                    $img_url = get_home_url().'/'.$faname.'.'.$fext;
                    $attachmentId = wp_insert_attachment_from_url($img_url,$post_id);
                    //if($attachmentId){
                    //    update_post_meta( $post_id, '_thumbnail_id', $attachmentId );  
                    //} 
                      $img_ids[]=$attachmentId;
                }else{
                    $img_ids[] = $pe;
                }
                unlink($faname.'.'.$fext);
            }
        }
        so_upload_all_images_to_product($post_id,$img_ids);
        if($product->variants){
            $vs = array();
            foreach($product->variants as $op){
                $vs[]=$op->option;
            }
            $atts = [];
            $atts[] = pricode_create_attributes('option',$vs);
            $vp = wc_get_product($post_id);
            $vp->set_attributes( $atts );
            $vp->save();
            foreach($product->variants as $variation){
                $vd = array(
                    'attributes'=>array(
                        'option'=>$variation->option
                    ),
                    'sku'=>$variation->id,
                    'regular_price'=>$variation->priceMed,
                    'priceMed'=>$variation->priceMed,
                    'priceRec'=>$variation->priceRec,
                    'specialPriceMed'=>$variation->specialPriceMed,
                    'specialPriceRec'=>$variation->specialPriceRec
                );
                create_product_variation($post_id,$vd);
            }
        }
        return $post_id;
}

function attach_product_terms($post_id,$name,$taxonomy,$attr=array()){
    $term  = get_term_by('name', $name , $taxonomy);
    if($term == false){
            $term = wp_insert_term($name, $taxonomy,$attr);
        
        $term_id = $term['term_id'] ;
    }else{
        $term_id = $term->term_id ;
    }
    $term_id = (int)$term_id;
    wp_set_object_terms($post_id,array($term_id),$taxonomy ,true);
}


function create_product_variation( $product_id, $variation_data ){
    // Get the Variable product object (parent)
    $product = wc_get_product($product_id);

    $variation_post = array(
        'post_title'  => $product->get_name(),
        'post_name'   => 'product-'.$product_id.'-variation',
        'post_status' => 'publish',
        'post_parent' => $product_id,
        'post_type'   => 'product_variation',
        'guid'        => $product->get_permalink()
    );
    
    // Creating the product variation
     $variation_id = wc_get_product_id_by_variation_sku_custom($variation_data['sku']);
     if(!$variation_id){
        $variation_id = wp_insert_post( $variation_post );
     }
    // Get an instance of the WC_Product_Variation object
    $variation = new WC_Product_Variation( $variation_id );

    // Iterating through the variations attributes
    foreach ($variation_data['attributes'] as $attribute => $term_name )
    {
        $taxonomy = 'pa_'.$attribute; // The attribute taxonomy

        // If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
        if( ! taxonomy_exists( $taxonomy ) ){
            register_taxonomy(
                $taxonomy,
               'product_variation',
                array(
                    'hierarchical' => false,
                    'label' => ucfirst( $attribute ),
                    'query_var' => true,
                    'rewrite' => array( 'slug' => sanitize_title($attribute) ), // The base slug
                ),
            );
        }

        // Check if the Term name exist and if not we create it.
        if( ! term_exists( $term_name, $taxonomy ) )
            wp_insert_term( $term_name, $taxonomy ); // Create the term

        $term_slug = get_term_by('name', $term_name, $taxonomy )->slug; // Get the term slug

        // Get the post Terms names from the parent variable product.
        $post_term_names =  wp_get_post_terms( $product_id, $taxonomy, array('fields' => 'names') );

        // Check if the post term exist and if not we set it in the parent variable product.
        if( ! in_array( $term_name, $post_term_names ) )
            wp_set_post_terms( $product_id, $term_name, $taxonomy, true );

        // Set/save the attribute data in the product variation
        update_post_meta( $variation_id, 'attribute_'.$taxonomy, $term_slug );
    }

    ## Set/save all other data

    // SKU
    if( ! empty( $variation_data['sku'] ) )
        $variation->set_sku( $variation_data['sku'] );

    // Prices
    if( empty( $variation_data['sale_price'] ) ){
        $variation->set_price( $variation_data['regular_price'] );
    } else {
        $variation->set_price( $variation_data['sale_price'] );
        $variation->set_sale_price( $variation_data['sale_price'] );
    }
    $variation->set_regular_price( $variation_data['regular_price'] );

    // Stock
    if( ! empty($variation_data['stock_qty']) ){
        $variation->set_stock_quantity( $variation_data['stock_qty'] );
        $variation->set_manage_stock(true);
        $variation->set_stock_status('');
    } else {
        $variation->set_manage_stock(false);
    }
    update_post_meta( $variation_id, 'priceMed', $variation_data['priceMed'] );
    update_post_meta( $variation_id, 'priceRec', $variation_data['priceRec'] );
    update_post_meta( $variation_id, 'specialPriceMed', $variation_data['specialPriceMed'] );
    update_post_meta( $variation_id, 'specialPriceRec', $variation_data['specialPriceRec'] );    
    $variation->set_weight(''); // weight (reseting)

    $variation->save(); // Save the data
}
function so_upload_all_images_to_product($product_id, $image_id_array) {
    set_post_thumbnail($product_id, $image_id_array[0]);
    if(sizeof($image_id_array) > 1) { 
        array_shift($image_id_array); 
        update_post_meta($product_id, '_product_image_gallery', implode(',',$image_id_array));
    }
}
function wc_get_product_id_by_variation_sku_custom($sku) {
    $args = array(
        'post_type'  => 'product_variation',
        'meta_query' => array(
            array(
                'key'   => '_sku',
                'value' => $sku,
            )
        )
    );
    // Get the posts for the sku
    $posts = get_posts( $args);
    if ($posts) {
        return $posts[0]->ID;
    } else {
        return false;
    }
}
function pricode_create_attributes( $name, $options ){
    $attribute = new WC_Product_Attribute();
    $attribute->set_id(0);
    $attribute->set_name($name);
    $attribute->set_options($options);
    $attribute->set_visible(true);
    $attribute->set_variation(true);
    return $attribute;
}

function dutchie_download_image($url) {
    $img='dutchie-'. random_int(0, 100);
    $fp = fopen($img, 'w+');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $result = curl_exec($ch);
    curl_close($ch);

    fclose($fp);
    if($result){
        return $img;
    }
    return false;
    
}
function wp_insert_attachment_from_url( $url, $parent_post_id = null ) {

	if ( ! class_exists( 'WP_Http' ) ) {
		require_once ABSPATH . WPINC . '/class-http.php';
	}

	$http     = new WP_Http();
	$response = $http->request( $url );
	if ( 200 !== $response['response']['code'] ) {
		return false;
	}

	$upload = wp_upload_bits( basename( $url ), null, $response['body'] );
	if ( ! empty( $upload['error'] ) ) {
		return false;
	}

	$file_path        = $upload['file'];
	$file_name        = basename( $file_path );
	$file_type        = wp_check_filetype( $file_name, null );
	$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
	$wp_upload_dir    = wp_upload_dir();

	$post_info = array(
		'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
		'post_mime_type' => $file_type['type'],
		'post_title'     => $attachment_title,
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	// Create the attachment.
	$attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );
	// Include image.php.
	require_once ABSPATH . 'wp-admin/includes/image.php';

	// Generate the attachment metadata.
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );

	// Assign metadata to attachment.
	wp_update_attachment_metadata( $attach_id, $attach_data );

	return $attach_id;

}
function mime2ext($mime) {
        $mime_map = [
            'video/3gpp2'                                                               => '3g2',
            'video/3gp'                                                                 => '3gp',
            'video/3gpp'                                                                => '3gp',
            'application/x-compressed'                                                  => '7zip',
            'audio/x-acc'                                                               => 'aac',
            'audio/ac3'                                                                 => 'ac3',
            'application/postscript'                                                    => 'ai',
            'audio/x-aiff'                                                              => 'aif',
            'audio/aiff'                                                                => 'aif',
            'audio/x-au'                                                                => 'au',
            'video/x-msvideo'                                                           => 'avi',
            'video/msvideo'                                                             => 'avi',
            'video/avi'                                                                 => 'avi',
            'application/x-troff-msvideo'                                               => 'avi',
            'application/macbinary'                                                     => 'bin',
            'application/mac-binary'                                                    => 'bin',
            'application/x-binary'                                                      => 'bin',
            'application/x-macbinary'                                                   => 'bin',
            'image/bmp'                                                                 => 'bmp',
            'image/x-bmp'                                                               => 'bmp',
            'image/x-bitmap'                                                            => 'bmp',
            'image/x-xbitmap'                                                           => 'bmp',
            'image/x-win-bitmap'                                                        => 'bmp',
            'image/x-windows-bmp'                                                       => 'bmp',
            'image/ms-bmp'                                                              => 'bmp',
            'image/x-ms-bmp'                                                            => 'bmp',
            'application/bmp'                                                           => 'bmp',
            'application/x-bmp'                                                         => 'bmp',
            'application/x-win-bitmap'                                                  => 'bmp',
            'application/cdr'                                                           => 'cdr',
            'application/coreldraw'                                                     => 'cdr',
            'application/x-cdr'                                                         => 'cdr',
            'application/x-coreldraw'                                                   => 'cdr',
            'image/cdr'                                                                 => 'cdr',
            'image/x-cdr'                                                               => 'cdr',
            'zz-application/zz-winassoc-cdr'                                            => 'cdr',
            'application/mac-compactpro'                                                => 'cpt',
            'application/pkix-crl'                                                      => 'crl',
            'application/pkcs-crl'                                                      => 'crl',
            'application/x-x509-ca-cert'                                                => 'crt',
            'application/pkix-cert'                                                     => 'crt',
            'text/css'                                                                  => 'css',
            'text/x-comma-separated-values'                                             => 'csv',
            'text/comma-separated-values'                                               => 'csv',
            'application/vnd.msexcel'                                                   => 'csv',
            'application/x-director'                                                    => 'dcr',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
            'application/x-dvi'                                                         => 'dvi',
            'message/rfc822'                                                            => 'eml',
            'application/x-msdownload'                                                  => 'exe',
            'video/x-f4v'                                                               => 'f4v',
            'audio/x-flac'                                                              => 'flac',
            'video/x-flv'                                                               => 'flv',
            'image/gif'                                                                 => 'gif',
            'application/gpg-keys'                                                      => 'gpg',
            'application/x-gtar'                                                        => 'gtar',
            'application/x-gzip'                                                        => 'gzip',
            'application/mac-binhex40'                                                  => 'hqx',
            'application/mac-binhex'                                                    => 'hqx',
            'application/x-binhex40'                                                    => 'hqx',
            'application/x-mac-binhex40'                                                => 'hqx',
            'text/html'                                                                 => 'html',
            'image/x-icon'                                                              => 'ico',
            'image/x-ico'                                                               => 'ico',
            'image/vnd.microsoft.icon'                                                  => 'ico',
            'text/calendar'                                                             => 'ics',
            'application/java-archive'                                                  => 'jar',
            'application/x-java-application'                                            => 'jar',
            'application/x-jar'                                                         => 'jar',
            'image/jp2'                                                                 => 'jp2',
            'video/mj2'                                                                 => 'jp2',
            'image/jpx'                                                                 => 'jp2',
            'image/jpm'                                                                 => 'jp2',
            'image/jpeg'                                                                => 'jpeg',
            'image/pjpeg'                                                               => 'jpeg',
            'application/x-javascript'                                                  => 'js',
            'application/json'                                                          => 'json',
            'text/json'                                                                 => 'json',
            'application/vnd.google-earth.kml+xml'                                      => 'kml',
            'application/vnd.google-earth.kmz'                                          => 'kmz',
            'text/x-log'                                                                => 'log',
            'audio/x-m4a'                                                               => 'm4a',
            'application/vnd.mpegurl'                                                   => 'm4u',
            'audio/midi'                                                                => 'mid',
            'application/vnd.mif'                                                       => 'mif',
            'video/quicktime'                                                           => 'mov',
            'video/x-sgi-movie'                                                         => 'movie',
            'audio/mpeg'                                                                => 'mp3',
            'audio/mpg'                                                                 => 'mp3',
            'audio/mpeg3'                                                               => 'mp3',
            'audio/mp3'                                                                 => 'mp3',
            'video/mp4'                                                                 => 'mp4',
            'video/mpeg'                                                                => 'mpeg',
            'application/oda'                                                           => 'oda',
            'audio/ogg'                                                                 => 'ogg',
            'video/ogg'                                                                 => 'ogg',
            'application/ogg'                                                           => 'ogg',
            'application/x-pkcs10'                                                      => 'p10',
            'application/pkcs10'                                                        => 'p10',
            'application/x-pkcs12'                                                      => 'p12',
            'application/x-pkcs7-signature'                                             => 'p7a',
            'application/pkcs7-mime'                                                    => 'p7c',
            'application/x-pkcs7-mime'                                                  => 'p7c',
            'application/x-pkcs7-certreqresp'                                           => 'p7r',
            'application/pkcs7-signature'                                               => 'p7s',
            'application/pdf'                                                           => 'pdf',
            'application/octet-stream'                                                  => 'pdf',
            'application/x-x509-user-cert'                                              => 'pem',
            'application/x-pem-file'                                                    => 'pem',
            'application/pgp'                                                           => 'pgp',
            'application/x-httpd-php'                                                   => 'php',
            'application/php'                                                           => 'php',
            'application/x-php'                                                         => 'php',
            'text/php'                                                                  => 'php',
            'text/x-php'                                                                => 'php',
            'application/x-httpd-php-source'                                            => 'php',
            'image/png'                                                                 => 'png',
            'image/x-png'                                                               => 'png',
            'application/powerpoint'                                                    => 'ppt',
            'application/vnd.ms-powerpoint'                                             => 'ppt',
            'application/vnd.ms-office'                                                 => 'ppt',
            'application/msword'                                                        => 'doc',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/x-photoshop'                                                   => 'psd',
            'image/vnd.adobe.photoshop'                                                 => 'psd',
            'audio/x-realaudio'                                                         => 'ra',
            'audio/x-pn-realaudio'                                                      => 'ram',
            'application/x-rar'                                                         => 'rar',
            'application/rar'                                                           => 'rar',
            'application/x-rar-compressed'                                              => 'rar',
            'audio/x-pn-realaudio-plugin'                                               => 'rpm',
            'application/x-pkcs7'                                                       => 'rsa',
            'text/rtf'                                                                  => 'rtf',
            'text/richtext'                                                             => 'rtx',
            'video/vnd.rn-realvideo'                                                    => 'rv',
            'application/x-stuffit'                                                     => 'sit',
            'application/smil'                                                          => 'smil',
            'text/srt'                                                                  => 'srt',
            'image/svg+xml'                                                             => 'svg',
            'application/x-shockwave-flash'                                             => 'swf',
            'application/x-tar'                                                         => 'tar',
            'application/x-gzip-compressed'                                             => 'tgz',
            'image/tiff'                                                                => 'tiff',
            'text/plain'                                                                => 'txt',
            'text/x-vcard'                                                              => 'vcf',
            'application/videolan'                                                      => 'vlc',
            'text/vtt'                                                                  => 'vtt',
            'audio/x-wav'                                                               => 'wav',
            'audio/wave'                                                                => 'wav',
            'audio/wav'                                                                 => 'wav',
            'application/wbxml'                                                         => 'wbxml',
            'video/webm'                                                                => 'webm',
            'audio/x-ms-wma'                                                            => 'wma',
            'application/wmlc'                                                          => 'wmlc',
            'video/x-ms-wmv'                                                            => 'wmv',
            'video/x-ms-asf'                                                            => 'wmv',
            'application/xhtml+xml'                                                     => 'xhtml',
            'application/excel'                                                         => 'xl',
            'application/msexcel'                                                       => 'xls',
            'application/x-msexcel'                                                     => 'xls',
            'application/x-ms-excel'                                                    => 'xls',
            'application/x-excel'                                                       => 'xls',
            'application/x-dos_ms_excel'                                                => 'xls',
            'application/xls'                                                           => 'xls',
            'application/x-xls'                                                         => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
            'application/vnd.ms-excel'                                                  => 'xlsx',
            'application/xml'                                                           => 'xml',
            'text/xml'                                                                  => 'xml',
            'text/xsl'                                                                  => 'xsl',
            'application/xspf+xml'                                                      => 'xspf',
            'application/x-compress'                                                    => 'z',
            'application/x-zip'                                                         => 'zip',
            'application/zip'                                                           => 'zip',
            'application/x-zip-compressed'                                              => 'zip',
            'application/s-compressed'                                                  => 'zip',
            'multipart/x-zip'                                                           => 'zip',
            'text/x-scriptzsh'                                                          => 'zsh',
        ];

        return isset($mime_map[$mime]) === true ? $mime_map[$mime] : false;
    }