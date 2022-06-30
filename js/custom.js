/* range slider */
/*! =======================================================
                      VERSION  11.0.2              
========================================================= */
!function(){"use strict";var t=function(t){this.input=null,this.inputDisplay=null,this.slider=null,this.sliderWidth=0,this.sliderLeft=0,this.pointerWidth=0,this.pointerR=null,this.pointerL=null,this.activePointer=null,this.selected=null,this.scale=null,this.step=0,this.tipL=null,this.tipR=null,this.timeout=null,this.valRange=!1,this.values={start:null,end:null},this.conf={target:null,values:null,set:null,range:!1,width:null,scale:!0,labels:!0,tooltip:!0,step:null,disabled:!1,onChange:null},this.cls={container:"rs-container",background:"rs-bg",selected:"rs-selected",pointer:"rs-pointer",scale:"rs-scale",noscale:"rs-noscale",tip:"rs-tooltip"};for(var i in this.conf)t.hasOwnProperty(i)&&(this.conf[i]=t[i]);this.init()};t.prototype.init=function(){return"object"==typeof this.conf.target?this.input=this.conf.target:this.input=document.getElementById(this.conf.target.replace("#","")),this.input?(this.inputDisplay=getComputedStyle(this.input,null).display,this.input.style.display="none",this.valRange=!(this.conf.values instanceof Array),!this.valRange||this.conf.values.hasOwnProperty("min")&&this.conf.values.hasOwnProperty("max")?this.createSlider():console.log("Missing min or max value...")):console.log("Cannot find target element...")},t.prototype.createSlider=function(){return this.slider=i("div",this.cls.container),this.slider.innerHTML='<div class="rs-bg"></div>',this.selected=i("div",this.cls.selected),this.pointerL=i("div",this.cls.pointer,["dir","left"]),this.scale=i("div",this.cls.scale),this.conf.tooltip&&(this.tipL=i("div",this.cls.tip),this.tipR=i("div",this.cls.tip),this.pointerL.appendChild(this.tipL)),this.slider.appendChild(this.selected),this.slider.appendChild(this.scale),this.slider.appendChild(this.pointerL),this.conf.range&&(this.pointerR=i("div",this.cls.pointer,["dir","right"]),this.conf.tooltip&&this.pointerR.appendChild(this.tipR),this.slider.appendChild(this.pointerR)),this.input.parentNode.insertBefore(this.slider,this.input.nextSibling),this.conf.width&&(this.slider.style.width=parseInt(this.conf.width)+"px"),this.sliderLeft=this.slider.getBoundingClientRect().left,this.sliderWidth=this.slider.clientWidth,this.pointerWidth=this.pointerL.clientWidth,this.conf.scale||this.slider.classList.add(this.cls.noscale),this.setInitialValues()},t.prototype.setInitialValues=function(){if(this.disabled(this.conf.disabled),this.valRange&&(this.conf.values=s(this.conf)),this.values.start=0,this.values.end=this.conf.range?this.conf.values.length-1:0,this.conf.set&&this.conf.set.length&&n(this.conf)){var t=this.conf.set;this.conf.range?(this.values.start=this.conf.values.indexOf(t[0]),this.values.end=this.conf.set[1]?this.conf.values.indexOf(t[1]):null):this.values.end=this.conf.values.indexOf(t[0])}return this.createScale()},t.prototype.createScale=function(t){this.step=this.sliderWidth/(this.conf.values.length-1);for(var e=0,s=this.conf.values.length;e<s;e++){var n=i("span"),l=i("ins");n.appendChild(l),this.scale.appendChild(n),n.style.width=e===s-1?0:this.step+"px",this.conf.labels?l.innerHTML=this.conf.values[e]:0!==e&&e!==s-1||(l.innerHTML=this.conf.values[e]),l.style.marginLeft=l.clientWidth/2*-1+"px"}return this.addEvents()},t.prototype.updateScale=function(){this.step=this.sliderWidth/(this.conf.values.length-1);for(var t=this.slider.querySelectorAll("span"),i=0,e=t.length;i<e;i++)t[i].style.width=this.step+"px";return this.setValues()},t.prototype.addEvents=function(){var t=this.slider.querySelectorAll("."+this.cls.pointer),i=this.slider.querySelectorAll("span");e(document,"mousemove touchmove",this.move.bind(this)),e(document,"mouseup touchend touchcancel",this.drop.bind(this));for(var s=0,n=t.length;s<n;s++)e(t[s],"mousedown touchstart",this.drag.bind(this));for(var s=0,n=i.length;s<n;s++)e(i[s],"click",this.onClickPiece.bind(this));return window.addEventListener("resize",this.onResize.bind(this)),this.setValues()},t.prototype.drag=function(t){if(t.preventDefault(),!this.conf.disabled){var i=t.target.getAttribute("data-dir");return"left"===i&&(this.activePointer=this.pointerL),"right"===i&&(this.activePointer=this.pointerR),this.slider.classList.add("sliding")}},t.prototype.move=function(t){if(this.activePointer&&!this.conf.disabled){var i=("touchmove"===t.type?t.touches[0].clientX:t.pageX)-this.sliderLeft-this.pointerWidth/2;return(i=Math.round(i/this.step))<=0&&(i=0),i>this.conf.values.length-1&&(i=this.conf.values.length-1),this.conf.range?(this.activePointer===this.pointerL&&(this.values.start=i),this.activePointer===this.pointerR&&(this.values.end=i)):this.values.end=i,this.setValues()}},t.prototype.drop=function(){this.activePointer=null},t.prototype.setValues=function(t,i){var e=this.conf.range?"start":"end";return t&&this.conf.values.indexOf(t)>-1&&(this.values[e]=this.conf.values.indexOf(t)),i&&this.conf.values.indexOf(i)>-1&&(this.values.end=this.conf.values.indexOf(i)),this.conf.range&&this.values.start>this.values.end&&(this.values.start=this.values.end),this.pointerL.style.left=this.values[e]*this.step-this.pointerWidth/2+"px",this.conf.range?(this.conf.tooltip&&(this.tipL.innerHTML=this.conf.values[this.values.start],this.tipR.innerHTML=this.conf.values[this.values.end]),this.input.value=this.conf.values[this.values.start]+","+this.conf.values[this.values.end],this.pointerR.style.left=this.values.end*this.step-this.pointerWidth/2+"px"):(this.conf.tooltip&&(this.tipL.innerHTML=this.conf.values[this.values.end]),this.input.value=this.conf.values[this.values.end]),this.values.end>this.conf.values.length-1&&(this.values.end=this.conf.values.length-1),this.values.start<0&&(this.values.start=0),this.selected.style.width=(this.values.end-this.values.start)*this.step+"px",this.selected.style.left=this.values.start*this.step+"px",this.onChange()},t.prototype.onClickPiece=function(t){if(!this.conf.disabled){var i=Math.round((t.clientX-this.sliderLeft)/this.step);return i>this.conf.values.length-1&&(i=this.conf.values.length-1),i<0&&(i=0),this.conf.range&&i-this.values.start<=this.values.end-i?this.values.start=i:this.values.end=i,this.slider.classList.remove("sliding"),this.setValues()}},t.prototype.onChange=function(){var t=this;this.timeout&&clearTimeout(this.timeout),this.timeout=setTimeout(function(){if(t.conf.onChange&&"function"==typeof t.conf.onChange)return t.conf.onChange(t.input.value)},500)},t.prototype.onResize=function(){return this.sliderLeft=this.slider.getBoundingClientRect().left,this.sliderWidth=this.slider.clientWidth,this.updateScale()},t.prototype.disabled=function(t){this.conf.disabled=t,this.slider.classList[t?"add":"remove"]("disabled")},t.prototype.getValue=function(){return this.input.value},t.prototype.destroy=function(){this.input.style.display=this.inputDisplay,this.slider.remove()};var i=function(t,i,e){var s=document.createElement(t);return i&&(s.className=i),e&&2===e.length&&s.setAttribute("data-"+e[0],e[1]),s},e=function(t,i,e){for(var s=i.split(" "),n=0,l=s.length;n<l;n++)t.addEventListener(s[n],e)},s=function(t){var i=[],e=t.values.max-t.values.min;if(!t.step)return console.log("No step defined..."),[t.values.min,t.values.max];for(var s=0,n=e/t.step;s<n;s++)i.push(t.values.min+s*t.step);return i.indexOf(t.values.max)<0&&i.push(t.values.max),i},n=function(t){return!t.set||t.set.length<1?null:t.values.indexOf(t.set[0])<0?null:!t.range||!(t.set.length<2||t.values.indexOf(t.set[1])<0)||null};window.rSlider=t}();


function setViewCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  let expires = "expires="+d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
function setCBDSlider(min,max,values){
        
         if(jQuery('#cbd_slider').length){
             jQuery('#cbd_slider_range').html( min + "% - " + max+'%' );
             if(values!=''){
                 jQuery('#cbd_slider').slider({
                     target:'#cbd_slider',
                     range:true,
                     min:min,
                     max:max,
                     values:[values],
                    change: function( event, ui ) {
                        jQuery( "#cbd_slider_val" ).val( ui.values[ 0 ] + "," + ui.values[ 1 ] );
                      //  jQuery('#productfilters').submit();
                      jQuery('#cbd_slider_range').html( ui.values[ 0 ] + "% - " + ui.values[ 1 ]+'%' );
                       filter_products();
                    },
                 });
             }else{
                 jQuery('#cbd_slider').slider({
                     range:true,
                     min:min,
                     max:max,
                     values:[min,max],
                    change: function( event, ui ) {
                        jQuery( "#cbd_slider_val" ).val( ui.values[ 0 ] + "," + ui.values[ 1 ] );
                    //    jQuery('#productfilters').submit();
                    jQuery('#cbd_slider_range').html( ui.values[ 0 ] + "% - " + ui.values[ 1 ]+'%' );
                         filter_products();
                    },
                    
                 });
             }
             
         }    
}
function setTHCSlider(min,max,values){
         if(jQuery('#thc_slider').length){
             jQuery('#thc_slider_range').html( min + "% - " + max+'%' );
             if(values!=''){
                 jQuery('#thc_slider').slider({
                     range:true,
                     min:min,
                     max:max,
                     values:[values],
                     change: function( event, ui ) {
                       jQuery( "#thc_slider_val" ).val( ui.values[ 0 ] + "," + ui.values[ 1 ] );
                     //  jQuery('#productfilters').submit();
                     jQuery('#thc_slider_range').html( ui.values[ 0 ] + "% - " + ui.values[ 1 ]+'%' );
                     filter_products();
                    }
                 });
             }else{
                 jQuery('#thc_slider').slider({
                     range:true,
                     min:min,
                     max:max,
                     values:[min,max],
                     change: function( event, ui ) {
                        jQuery( "#thc_slider_val" ).val( ui.values[ 0 ] + "," + ui.values[ 1 ] );
                      //  jQuery('#productfilters').submit();
                        jQuery('#thc_slider_range').html( ui.values[ 0 ] + "% - " + ui.values[ 1 ]+'%' );
                      filter_products();
                    }
                 });
             }
             
         }    
}

function filter_products(){
    console.log(typeof products);
        if(typeof products !='undefined'){
            
           
    
        
         jQuery('.featured-product-box').addClass('hidden');
         var selected_and = [];
         var selected_or = [];
         var category_filters =[];
         var weight_filters =[];
         var brand_filters =[];
         var type_filters =[];
         var cbd_filters =[];
         var thc_filters =[];
         var effect_filters =[];
         
         
         jQuery('.filters input[type="radio"]:checked').each(function(i,v) {
             var val = jQuery(v).val();
             if(val!=''){
              selected_and.push(jQuery(v).val())
            }
         });
         jQuery('.filters input[type="checkbox"]:checked').each(function(i,v) {
            var val = jQuery(v).val();
             if(val!=''){
                 if(jQuery(v).attr('name')=='pro_cat'){
                     category_filters.push(jQuery(v).val());
                 }else if(jQuery(v).attr('name')=='pro_weight'){
                     weight_filters.push(jQuery(v).val());
                 }else if(jQuery(v).attr('name')=='pro_brand'){
                     brand_filters.push(jQuery(v).val());
                 }else if(jQuery(v).attr('name')=='pro_type'){
                     type_filters.push(jQuery(v).val());
                 }else if(jQuery(v).attr('name')=='pro_effects'){
                     effect_filters.push(jQuery(v).val());
                 }
              
            }
         });
         
         jQuery('.filters select',function(i,v){
            // selected.push(jQuery(v).val())
         }); 
         var filtered_products = products;
         if(category_filters.length>0){
                filtered_products = products.filter(function (entry) {
                       if(category_filters.includes(entry.category)){
                           return true
                       }
                       return false;
                })
            }
           console.log(filtered_products.length);
            if(brand_filters.length>0){
                filtered_products = filtered_products.filter(function (entry) {
                    if(entry.brand == null){
                        return false;
                    }
                        
                       if(brand_filters.includes(entry.brand.name)){
                           return true
                       }
                       return false;
                })
            }
             console.log(filtered_products.length);
            if(type_filters.length>0){
                filtered_products = filtered_products.filter(function (entry) {
                    if(entry.strainType == null){
                        return false;
                    }
                    if(type_filters.includes(entry.strainType)){
                           return true
                    }
                    return false;
                })
            }
             console.log(filtered_products.length);    
             
             if(effect_filters.length>0){
                filtered_products = filtered_products.filter(function (entry) {
                    if(entry.effects == null || entry.effects.length == 0){
                        return false;
                    }
                    var found = false;
                    for(var i=0;i<entry.effects.length;i++){
                        if(effect_filters.includes(entry.effects[i])){
                               found = true;
                               break;
                        } 
                    }
                    return found;
                   
                })
            }
             console.log(filtered_products.length);  
             
             
         var cbd_base_min = jQuery( "#cbd_slider_val" ).data('min');
         var cbd_base_max = jQuery( "#cbd_slider_val" ).data('max');
         var cbd = jQuery( "#cbd_slider_val" ).val().split(',');
         var cbd_selected_min = cbd[0];
         var cbd_selected_max = cbd[1];
         if(cbd_base_min!=cbd_selected_min || cbd_base_max!=cbd_selected_max){
             filtered_products = filtered_products.filter(function (entry) {
                var pro_cbd_min = 0;
                var pro_cbd_max = 0;
                if(entry.potencyCbd.range){
                    if(entry.potencyCbd.range.length>1){
                        pro_cbd_min = entry.potencyCbd.range[0];
                        pro_cbd_max = entry.potencyCbd.range[1];
                    }else{
                        pro_cbd_max = entry.potencyCbd.range[0];
                    }
                }
                console.log(cbd_selected_min,cbd_selected_max,pro_cbd_min,pro_cbd_max)
                if(pro_cbd_min>=cbd_selected_min && pro_cbd_max<=cbd_selected_max){
                    return true;
                }else{
                    return false;
                }
             });
         }
         
         var thc_base_min = jQuery( "#thc_slider_val" ).data('min');
         var thc_base_max = jQuery( "#thc_slider_val" ).data('max');
         var thc = jQuery( "#thc_slider_val" ).val().split(',');
         var thc_selected_min = cbd[0];
         var thc_selected_max = cbd[1];
         if(thc_base_min!=thc_selected_min || thc_base_max!=thc_selected_max){
            filtered_products = filtered_products.filter(function (entry) {
                var pro_thc_min = 0;
                var pro_thc_max = 0;
                if(entry.potencyThc.range){
                    if(entry.potencyThc.range.length>1){
                        pro_thc_min = entry.potencyThc.range[0];
                        pro_thc_max = entry.potencyThc.range[1];
                    }else{
                        pro_thc_max = entry.potencyThc.range[0];
                    }
                }
                if(pro_thc_min>=thc_selected_min && pro_thc_max<=thc_selected_max){
                    return true;
                }else{
                    return false;
                }
             });
         }
             var str = '';
             if(category_filters.length){
                 str +=' <b>Categories:</b> '+ category_filters.join(' ,');
            }
            if(brand_filters.length){
             str +=' <b>Brands:</b> '+ brand_filters.join(' ,');
         }
         if(type_filters.length){
             str +=' <b>Types:</b> '+ type_filters.join(' ,');
         }
         if(effect_filters.length){
             str +=' <b>Effects:</b> '+ effect_filters.join(' ,');
         }
             
             jQuery('#selected_filters').html(str);
             
             
             jQuery('#duthcie_product_list').html('');
             jQuery.each(filtered_products,function(i,v){
                 renderDutchieProduct(v);
             });
       //  $.each(filtered_products,function(i,v){
      //       
      //   });
            
         /*
         var filter_start = false;
          for(var i=0;i<category_filters.length;i++){
                filter_start = true;
                var selected_class = '.featured-product-box';
                selected_class += '.'+category_filters[i];
                  console.log(selected_class);
                jQuery(selected_class).removeClass('hidden'); 
            }
            
            
            if(filter_start){
                
                for(var i=0;i<weight_filters.length;i++){
                   
                   var selected_class = '.featured-product-box';
                   selected_class += '.'+weight_filters[i];
                     console.log(selected_class);
                   jQuery(selected_class+':not(.hidden)').removeClass('hidden'); 
               }
            } else {
                
            } */

        /* if(category_filters.length>0){
             selected_and.push(category_filters);
         }
         if(weight_filters.length>0){
             selected_and.push(weight_filters);
         }
         if(brand_filters.length>0){
             selected_and.push(brand_filters);
         }
         if(type_filters.length>0){
             selected_and.push(type_filters);
         }
         if(effect_filters.length>0){
             selected_and.push(effect_filters);
         }


         
         
         if(selected_or.length>0){
             for(var i=0;i<selected_or.length;i++){
                var selected_class = '.featured-product-box';
                if(selected_and.length>0){
                    selected_class += '.'+selected_and.join('.');
                }
                selected_class += '.'+selected_or[i];
                  console.log(selected_class);
                jQuery(selected_class).removeClass('hidden'); 
            }
         }else{
              var selected_class = '.featured-product-box';
             if(selected_and.length>0){
                 selected_class += '.'+selected_and.join('.');
             }
           
            console.log(selected_class);
            jQuery(selected_class).removeClass('hidden');             
         } */
        /*
         var cbd_base_min = jQuery( "#cbd_slider_val" ).data('min');
         var cbd_base_max = jQuery( "#cbd_slider_val" ).data('max');
         var cbd = jQuery( "#cbd_slider_val" ).val().split(',');
         var cbd_selected_min = cbd[0];
         var cbd_selected_max = cbd[1];
         if(cbd_base_min!=cbd_selected_min || cbd_base_max!=cbd_selected_max){
             jQuery('.featured-product-box:not(.hidden)').each(function(i,v){
                var pro_cbd_min = jQuery(v).data('cbd-min');
                var pro_cbd_max = jQuery(v).data('cbd-max');
                console.log(cbd_selected_min,cbd_selected_max,pro_cbd_min,pro_cbd_max)
                if(pro_cbd_min>=cbd_selected_min && pro_cbd_max<=cbd_selected_max){
                    // let it
                }else{
                    jQuery(v).addClass('hidden');
                }
             });
         }
         
         var thc_base_min = jQuery( "#thc_slider_val" ).data('min');
         var thc_base_max = jQuery( "#thc_slider_val" ).data('max');
         var thc = jQuery( "#thc_slider_val" ).val().split(',');
         var thc_selected_min = cbd[0];
         var thc_selected_max = cbd[1];
         if(thc_base_min!=thc_selected_min || thc_base_max!=thc_selected_max){
             jQuery('.featured-product-box:not(.hidden)').each(function(i,v){
                var pro_thc_min = jQuery(v).data('thc-min');
                var pro_thc_max = jQuery(v).data('thc-max');
                if(pro_thc_min>=thc_selected_min && pro_thc_max<=thc_selected_max){
                    // let it
                }else{
                    jQuery(v).addClass('hidden');
                }
             });
         } */
        }
     }
     
     function renderDutchieProduct(product){
         var html='';
         var thc = [0,0];
         var cbd = [0,0];
         
         html+='<div data-thc-min="'+thc[0]+'" data-thc-max="'+thc[1]+'" data-cbd-min="'+cbd[0]+'" data-cbd-max="'+cbd[1]+'" class="featured-product-box '+product.id+'">';
   html+=' <div class="fbp_c">';
   html+='       <div class="fbp_0">';
    html+='    <a href="/prod/'+product.id+'" title="'+product.name+'">';
   html+='         <div class="product-box-img" style="background-image: url('+product.image+'?w=300&h=300)">';
   html+='         </div>';
   html+='     </a>';
   html+='       </div>';
   html+='     <div class="fbp_1">';
   if(product.staffPick){
       html+='   <div class="staffpick">Staff Pick</div>';
       
   }
           
       
   html+=' <div class="product-box-features fb1">';
    html+='    <ul>';
            
            var pm = product.strainType;
            if(pm!='NOT_APPLICABLE'){
         
            html+='<li class="pm-type-'+ucfirst(pm.toLowerCase())+'">'+ucfirst(pm.toLowerCase())+'</li>';
            }
          
            pm = product.potencyThc.formatted;
            if(pm){
                html+='<li class="pm-thc">THC: '+pm+'</li>';
            } 
         
            pm = product.potencyCbd.formatted;
            if(pm){
                html+='<li class="pm-thc">CBD: '+pm+'</li>';
            }
        
            
       html+=' </ul>';
   html+=' </div>';
          if(product.brand!=null){
            html+='<div class="brandname">'+product.brand.name+'</div>';
           } 
        html+='<a href="/prod/'+product.id+'" title="'+product.name+'">';
    html+='<div class="product-box-name">'+product.name;
   html+=' </div>';
      html+='  </a>';
    

       html+=' <div class="product-box-description">';
      
    html+='</div>';
           html+='     <div class="product-box-features fb2">';
         html+='    <ul>';
            
            var pm = product.strainType;
            if(pm!='NOT_APPLICABLE'){
         
            html+='<li class="pm-type-'+ucfirst(pm.toLowerCase())+'">'+ucfirst(pm.toLowerCase())+'</li>';
            }
          
            pm = product.potencyThc.formatted;
            if(pm){
                html+='<li class="pm-thc">THC: '+pm+'</li>';
            } 
         
            pm = product.potencyCbd.formatted;
            if(pm){
                html+='<li class="pm-thc">CBD: '+pm+'</li>';
            }
        
            
       html+=' </ul>';
    html+='</div>';
    html+='    </div>';
   html+='   <div class="fbp_2">';
   html+='      <div class="product-box-price">';
        
            html+='$'+product.variants[0].priceMed;
           
 html+='   </div>';
  html+='    <a class="baddtocart" href="#" data-id="'+product.id+'" data-variant="'+product.variants[0].option+'" title="'+product.name+'" data-price="'+product.variants[0].priceMed+'" data-img="'+product.image+'" data-name="'+product.name+'">Add to Cart</a>';
html+='</div>   '; 
html+='</div>';
html+='</div>';
jQuery('#duthcie_product_list').append(html);
     }
function ucfirst(str) {
    var firstLetter = str.slice(0,1);
    return firstLetter.toUpperCase() + str.substring(1);
}
 (function($){
     $(document).ready(function(){
         if(typeof products !=undefined){
           filter_products();
       }
         $('#showfilters, .filters_header span').on('click',function(e){
             e.preventDefault();
             $('.filters').toggleClass('hidden');
         });
         $('.filters input[type="radio"], .filters input[type="checkbox"]').on('click',function(){
            // $('#productfilters').submit();
           filter_products();
         });
         $('.product_display_style a').on('click',function(e){
             e.preventDefault();
             if($('#dutchie_products_container').hasClass('listview')){
                 $('#dutchie_products_container').removeClass('listview');
                 setViewCookie('listview',0,1);
             }else{
                 $('#dutchie_products_container').addClass('listview');
                 setViewCookie('listview',1,1);
             }
         });
         
     });
     
 })(jQuery);
 
(function($){
    $(document).ready(function(){
        

       $('#loadmoreproducts').on('click',function(e){
           e.preventDefault();
           $(this).text('Loading');
           var params = {};
           params['action']='fetch_dutchie';
           params['page']=parseInt($('#loadmoreproducts').data('page'))+1;
           params['limit']=$('#loadmoreproducts').data('limit');
           params['type']=$('#loadmoreproducts').data('type');
           $('#productfilters select').each(function(i,v){
               params[$(v).prop('name')]=$(v).val();
           })
           $('#productfilters input[type="text"]').each(function(i,v){
               params[$(v).prop('name')]=$(v).val();
           })
           $('#productfilters input[type="radio"]:checked').each(function(i,v){
               params[$(v).prop('name')]=$(v).val();
           })
           $('#productfilters input[type="checked"]:checked').each(function(i,v){
               params[$(v).prop('name')]=$(v).val();
           })
           $.ajax({
               url:dutchie.ajax_url,
               method:'post',
               data:params,
               success:function(data){
                   if(data!='ALL'){
                       $('#loadmoreproducts').text('Load more');
                   $('#duthcie_product_list').append(data);
                   $('#loadmoreproducts').data('page',params['page']);
                   console.log($('#loadmoreproducts').data('page'));
                }else{
                    $('#loadmoreproducts').hide();
                }
               }
           })
       }); 
       
       $('#varient_id').on('change',function(){
           prodPrice();
       });
       prodPrice();
       function prodPrice(){
        if($('#varient_id').length>0){
            var opt = $("#varient_id option:selected");
            $('#price').text('$'+$(opt).data('price'));
        }           
       }
       
       $('.addtocart').on('click',function(e){
           e.preventDefault();
           dutchie_addtocart($(this).data('id'),1,$(this).data('variant'), this);
       });
       $(document).on('click','.baddtocart',function(e){
           e.preventDefault();
           dutchie_addtocart($(this).data('id'),1,$(this).data('variant'),this);
       });
       
       $('#daddtocart').on('click',function(e){
           e.preventDefault();
           dutchie_addtocart($(this).data('id'), $('#qty').val(), $('#varient_id').val(), this);
       });
       
       
       
       function dutchie_addtocart(id,qty,variant, bobj){
           var cart = Cookies.get('cart');
           $(bobj).text('Working...');
           if(typeof cart=='undefined'){
               cart = [];
           }else{
               cart = JSON.parse(cart);
           }
           $.ajax({
               url:'?procode='+id,
               success:function(res){
                   if(res!='error'){
                    var data = JSON.parse(res);
                   var obj = {
                        id:id,
                        qty:qty,
                        variant:variant,
                        img:data.image,
                        price:data.variants[0].priceMed,
                        name:data.name
                    };


                      var found = -1;

                    $.each(cart,function(i,v){
                       if(v.id==id && v.variant==variant){
                           found = i;
                       }
                    });
                    if(found!=-1){
                        cart[found].qty=cart[found].qty+obj.qty;
                    }else{
                        cart.push(obj);
                    }              
                     $.alert({
                         title: 'Alert!',
                         content: 'Product is added to cart successfully',
                     });
                    Cookies.set('cart',JSON.stringify(cart) , { expires: 7 })
                    $('#cartheader span').html(cart.length);
                    $(bobj).text('Added to Cart');
                    renderCart();
                }else{
                      $.alert({
                         title: 'Alert!',
                         content: 'Something went wrong. Try again',
                     });
                     $(bobj).text('Add to Cart');
                }
               }
           })
           
       }
       
       $('#cartheader').on('click',function(e){
           e.preventDefault();
           $('.cartbox').addClass('showcart');
       });
       $('#hidecartbox').on('click',function(e){
           e.preventDefault();
           $('.cartbox').removeClass('showcart');
       });
       $(document).on('change','.carboxbody .qty',function(e){
           e.preventDefault();
          var cart = Cookies.get('cart');
           if(typeof cart=='undefined'){
               cart = [];
           }else{
               cart = JSON.parse(cart);
           }
           var index = $(this).data('index')
           cart[index].qty=$(this).val();
           var total=0;
           $.each(cart,function(i,v){
               var rt =parseFloat(v.qty*v.price);
               total+=rt;
           });
           Cookies.set('cart',JSON.stringify(cart) , { expires: 7 });
            $('.cart-collaterals .amount').html('<bdi><span class="woocommerce-Price-currencySymbol">$</span>'+total.toLocaleString('en-US')+'</bdi>');
       });
       $('input[name="shipping_type"]').on('click',function(e){
           Cookies.set('shipping_type',$(this).val() , { expires: 7 })
       });
       
       function renderCart(){
           var cart = Cookies.get('cart');
           if(typeof cart=='undefined'){
               cart = [];
           }else{
               cart = JSON.parse(cart);
           }
           var html='';
           var total=0;
           $.each(cart,function(i,v){
               var rt =parseFloat(v.qty*v.price);
               total+=rt;
               html+='<tr class="woocommerce-cart-form__cart-item cart_item">';
                html+='<td class="product-thumbnail">';
                html+='<a href="/prod/'+v.id+'"><img src="'+v.img+'?w=80&amp;h=80" alt="'+v.name+'"></a>						</td>';

	html+='					<td class="product-name" data-title="Product">';
	html+='					<a href="/prod/'+v.id+'">'+v.name+'</a><dl class="variation">';
	html+='		<dt class="variation-option">Option:</dt>';
	html+='	<dd class="variation-option"><p>N/A</p>';
        html+='</dd>';
	html+='</dl>';
        html+='                                        <div class="quantity">';
	html+='			<label class="screen-reader-text" for="quantity_'+i+'">'+v.name+'&lt; quantity</label>';
        html+='                <input type="number" id="quantity_'+i+'" data-index="'+i+'" class="input-text qty text" step="1" min="0" max="" name="cart['+i+'][qty]" value="'+v.qty+'" title="Qty" size="4" placeholder="" inputmode="numeric" autocomplete="off">';
	html+='		</div>';
	html+='					</td>';
        html+='';
        html+='                                        <td class="product-price" data-title="Price">';
	html+='						<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>'+v.price.toLocaleString('en-US')+'</bdi></span>';
        html+='                                                <a href="#" class="removeit" aria-label="Remove this item" data-id="6266e8c97e1c6600016a4275" data-variant="N/A" data-index="0">remove</a>';
        html+='                                        </td>';
	html+='				</tr>';
           });
           $('.carboxbody .shop_table tbody').html(html);
           
           $('.cart-collaterals .amount').html('<bdi><span class="woocommerce-Price-currencySymbol">$</span>'+total.toLocaleString('en-US')+'</bdi>');
       }
       
       $('#updatecart').on('click',function(){
           var cart = Cookies.get('cart');
           if(typeof cart=='undefined'){
               cart = [];
           }else{
               cart = JSON.parse(cart);
           }
          
               
               $.each(cart,function(ii,vv){
                   var qty = $('#cartitem input[name="'+vv.id+'"]').val();
                   cart[ii].qty = qty;
               });
                Cookies.set('cart',JSON.stringify(cart) , { expires: 7 });
               renderCart();
          
       });
       $(document).on('click','.removeit',function(e){
           e.preventDefault();
           var that = this;
           $.ajax({
               url:'/?removeit',
               data:{
                   proid:$(this).data('id')
               },
               success:function(){
                   var cart = Cookies.get('cart');
                if(typeof cart=='undefined'){
                    cart = [];
                }else{
                    cart = JSON.parse(cart);
                }
                var index = $(that).data('index');
                cart.splice(index,1);
                Cookies.set('cart',JSON.stringify(cart) , { expires: 7 });
               }
           });
           
           renderCart();
       })
    });
})(jQuery)