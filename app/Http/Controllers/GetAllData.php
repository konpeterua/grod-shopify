<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetAllData extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        Products:all();
        Collections:all();
    }

    public static function save($data, $table, $ignore = false){
        if(empty($data) || empty($table)) return false;
        //var_dump($data,$table);

        if(is_array($data)){
            if(is_array(reset($data))){
                foreach($data as $item){
                    if($ignore){
                        DB::table($table)->insertOrIgnore($item);
                    } else {
                        DB::table($table)->insert($item);
                    }
                }
            } else {
                if($ignore){
                    DB::table($table)->insertOrIgnore($data);
                } else {
                    DB::table($table)->insert($data);
                }
            }
            return true;
        }
        return false;
        
    }

    public static function prepareDateFields($data){
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
