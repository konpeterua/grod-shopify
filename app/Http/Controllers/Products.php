<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Products extends Controller
{
    public function all(){
        $shop_url = 'https://mtn-shop-eu.myshopify.com/';
        $urls = [
            'count' => 'admin/api/2022-10/products/count.json',
            'list'  => 'admin/api/2022-10/products.json?limit=250&since_id=%d',
        ];

        $products_count = ApiCurl::request($shop_url.$urls['count']);
        if(isset($products_count['count'])) $products_count = $products_count['count'];
        
        $processed_count = 0;
        $last_processed_id = 0;
        $run = 1;
        
        while($run = 1){
            $products = ApiCurl::request($shop_url.sprintf($urls['list'],$last_processed_id));
            if(empty($products)) $run = 0;
            if(isset($products['products'])) $products = $products['products'];
            if(!empty($products)){
                foreach($products as $product){
                    $last_processed_id = $product['id'];
                    if(isset($product['image'])) {
                        $this->saveImage($product['image']);
                        
                    }
                    if(isset($product['images'])) {
                        $this->saveImageS($product['images']);
                    }
                    if(isset($product['options'])) {
                        $this->saveOptions($product['options']);
                    }
                    
                    if(isset($product['variants'])) {
                        $this->saveVariants($product['variants']);
                    }
                    
                    unset($product['image']);
                    unset($product['images']);
                    unset($product['options']);
                    unset($product['variants']);
                    $product = GetAllData::prepareDateFields($product);
                    GetAllData::save($product, 'products');
                    $processed_count++;
                }
            }
            if($products_count == $processed_count) $run = 0;
        }
        

        echo 'Processed: '.$processed_count.' Last processed id: '.$last_processed_id;
    }

    protected function saveImage($data){
        if(empty($data)) return false;
        
        if(is_array(reset($data))){
            foreach($data as $item){
                $this->saveImageVariant($item);
                unset($item['variant_ids']);
                $item = GetAllData::prepareDateFields($item);
                GetAllData::save($item,'product_image');
            }
        } else {
            $this->saveImageVariant($data);
            unset($data['variant_ids']);
            $data = GetAllData::prepareDateFields($data);
            GetAllData::save($data,'product_image');
        }
        
        return false;
    }
    protected function saveImageS($data){
        if(empty($data)) return false;
        
        if(is_array(reset($data))){
            foreach($data as $item){
                $this->saveImageVariant($item, 'product_images_variant');
                unset($item['variant_ids']);
                $item = GetAllData::prepareDateFields($item);
                GetAllData::save($item,'product_images');
            }
        } else {
            $this->saveImageVariant($data, 'product_images_variant');
            unset($data['variant_ids']);
            $data = GetAllData::prepareDateFields($data);
            GetAllData::save($data,'product_images');
        }
        
        return false;
    }
    protected function saveImageVariant($data, $table = 'product_image_variant'){
        if(empty($data)) return false;

        if(!empty($data['variant_ids'])){
            foreach($data['variant_ids'] as $id){
                $tmp = [
                    'id'        => $id,
                    'product_id'=> $data['product_id'],
                    'image_id'  => $data['id'],
                ];
                GetAllData::save($tmp, $table);
            }
        }
    }

    protected function saveOptions($data){
        if(empty($data)) return false;
        
        if(is_array(reset($data))){
            foreach($data as $item){
                $this->saveOptionValues($item);
                unset($item['values']);
                GetAllData::save($item,'product_options');
            }
        } else {
            $this->saveOptionValues($data);
            unset($data['values']);
            GetAllData::save($data,'product_options');
        }
        return false;
    }
    protected function saveOptionValues($data){
        if(empty($data)) return false;

        if(!empty($data['values'])){
            foreach($data['values'] as $value){
                $tmp = [
                    'option_id'     => $data['id'],
                    'product_id'    => $data['product_id'],
                    'value'         => $value,
                ];
                GetAllData::save($tmp, 'product_option_values');
            }
        }
    }

    protected function saveVariants($data){
        if(empty($data)) return false;
        
        if(is_array(reset($data))){
            foreach($data as $item){
                $item = GetAllData::prepareDateFields($item);
                GetAllData::save($item,'product_variants');
            }
        } else {
            $data = GetAllData::prepareDateFields($data);
            GetAllData::save($data,'product_variants');
        }
        
        return false;
    }

    
}
