<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

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
                    $product = $this->prepareDateFields($product);
                    $this->save($product, 'products');
                    $last_processed_id = $product['id'];
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
                $item = $this->prepareDateFields($item);
                $this->save($item,'product_image');
            }
        } else {
            $this->saveImageVariant($data);
            unset($data['variant_ids']);
            $data = $this->prepareDateFields($data);
            $this->save($data,'product_image');
        }
        
        return false;
    }
    protected function saveImageS($data){
        if(empty($data)) return false;
        
        if(is_array(reset($data))){
            foreach($data as $item){
                $this->saveImageVariant($item, 'product_images_variant');
                unset($item['variant_ids']);
                $item = $this->prepareDateFields($item);
                $this->save($item,'product_images');
            }
        } else {
            $this->saveImageVariant($data, 'product_images_variant');
            unset($data['variant_ids']);
            $data = $this->prepareDateFields($data);
            $this->save($data,'product_images');
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
                $this->save($tmp, $table);
            }
        }
    }

    protected function saveOptions($data){
        if(empty($data)) return false;
        
        if(is_array(reset($data))){
            foreach($data as $item){
                $this->saveOptionValues($item);
                unset($item['values']);
                $this->save($item,'product_options');
            }
        } else {
            $this->saveOptionValues($data);
            unset($data['values']);
            $this->save($data,'product_options');
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
                $this->save($tmp, 'product_option_values');
            }
        }
    }

    protected function saveVariants($data){
        if(empty($data)) return false;
        
        if(is_array(reset($data))){
            foreach($data as $item){
                $item = $this->prepareDateFields($item);
                $this->save($item,'product_variants');
            }
        } else {
            $data = $this->prepareDateFields($data);
            $this->save($data,'product_variants');
        }
        
        return false;
    }

    protected function save($data, $table){
        if(empty($data) || empty($table)) return false;
        //var_dump($data,$table);

        if(is_array($data)){
            if(is_array(reset($data))){
                foreach($data as $item){
                    DB::table($table)->insert($item);
                }
            } else {
                DB::table($table)->insert($data);
            }
            return true;
        }
        return false;
        
    }

    public function prepareDateFields($data){
        if(!empty($data)) {
            if(!empty($data['created_at'])){
                $data['created_at'] = date("Y-m-d H:i:s", strtotime($data['created_at']));
            }
            if(!empty($data['updated_at'])){
                $data['updated_at'] = date("Y-m-d H:i:s", strtotime($data['updated_at']));
            }
            if(!empty($data['published_at'])){
                $data['published_at'] = date("Y-m-d H:i:s", strtotime($data['published_at']));
            }
        }
        return $data;
    }
}
