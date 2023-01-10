<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Collections extends Controller
{
    public function all(){
        $shop_url = 'https://mtn-shop-eu.myshopify.com/';
        $collect_urls = [
            'custom' => [
                'count'      => 'admin/api/2022-10/custom_collections/count.json',
                'collect'    => 'api/2022-10/custom_collections.json?limit=250&fields=id&since_id=%d',
            ],
            'smart' => [
                'count'       => 'admin/api/2022-10/smart_collections/count.json',
                'collect'     => 'admin/api/2022-10/smart_collections.json?limit=250&fields=id&since_id=%d',
            ],
        ];
        $data_urls = [
            'info'     => 'admin/api/2022-10/collections/%d.json',
            'products'  => 'admin/api/2022-10/collections/%d/products.json?fields=id',
        ];

        foreach($collect_urls as $type => $endpoints){
            $collect_cnt = ApiCurl::request($shop_url.$endpoints['count']);
            if(isset($collect_cnt['count'])) $collect_cnt = $collect_cnt['count'];

            $processed_count = 0;
            $last_processed_id = 0;
            $run = 1;

            while($run = 1 && $collect_cnt > 0){
                $collects = ApiCurl::request($shop_url.sprintf($endpoints['collect'],$last_processed_id));
                if(empty($collects)) $run = 0;
                if(!empty($collects)){
                    foreach($collects as $type => $elems){
                        foreach($elems as $elem){
                            if(isset($elem['id'])){
                                $last_processed_id = $elem['id'];
                                $collection_data = ApiCurl::request($shop_url.sprintf($data_urls['info'],$elem['id']));
                                if(isset($collection_data['collection'])) {
                                    $collection_data = $collection_data['collection'];
                                } else {
                                    continue 1;
                                }
                                if($this->saveCollection($collection_data)){
                                    $processed_count++;
                                    if($processed_count == $collect_cnt) $run = 0;

                                    // сохраним связи колекции-продукты
                                    $last_product_processed_id = 0;
                                    $product_processed_count = 0;
                                    $product_run = 1;
                                    while($product_run = 1 && $collect_cnt > 0){
                                        $products_data = ApiCurl::request($shop_url.sprintf($data_urls['products'],$elem['id']));
                                        if(isset($products_data['products'])) {
                                            $products_data = $products_data['products'];
                                        } else {
                                            continue 1;
                                        }
                                        foreach($products_data as $product){
                                            if(isset($product['id'])){
                                                $tmp = [
                                                    'collection_id' => $elem['id'],
                                                    'product_id' => $product['id'],
                                                ];
                                                GetAllData::save($tmp, 'collection_products', true);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    protected function saveCollection($data){
        if(empty($data)) return false;
        
        if(is_array(reset($data))){
            foreach($data as $item){
                $this->saveCollectRules($item);
                unset($item['rules']);
                $this->saveCollectImage($item);
                unset($item['image']);
                $item = GetAllData::prepareDateFields($item);
                GetAllData::save($item,'collections');
            }
        } else {
            $this->saveCollectRules($data);
                unset($data['rules']);
                $this->saveCollectImage($item);
                unset($data['image']);
                $data = GetAllData::prepareDateFields($data);
                GetAllData::save($data,'collections');
        }
    }

    protected function saveCollectRules($data){
        if(empty($data)) return false;
        if(empty($data['rules']) || empty($data['id'])) return false;

        foreach($data['rules'] as $rule){
            $tmp = [
                'id' => $data['id'],
                'column'    => $rule['column'],
                'relation'  => $rule['relation'],
                'condition' => $rule['condition'],
            ];
            GetAllData::save($tmp,'collection_rules',true);
        }
        
        return true;
    }

    protected function saveCollectImage($data){
        if(empty($data)) return false;
        if(empty($data['image']) || empty($data['id'])) return false;

        foreach($data['image'] as $image){
            $tmp = [
                'id' => $data['id'],
                'created_at'    => $image['created_at'],
                'alt'           => $image['alt'],
                'width'         => $image['width'],
                'height'        => $image['height'],
                'src'           => $image['src'],
            ];
            $tmp = GetAllData::prepareDateFields($tmp);
            GetAllData::save($tmp,'collection_image',true);
        }
        
        return true;
    }
}
