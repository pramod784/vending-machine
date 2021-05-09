<?php
namespace App\Helpers;
use DB;
class Query_helper {
    public static function check_is_product_id_valid($product_list)
    {
    	$result = DB::table('products')
    	->select('*')
    	/*->where('unique_site_id',$unique_site_id)*/
    	->get()->first();
    	if($result)
    	{
    		return $result;
    	}else
    	{
    		return false;
    	}
    }
    public static function check_is_stock_available($product_id,$quantity)
    {
    	// DB::enableQueryLog();
    	$result = DB::table('products')
    	->select('*')
    	->where('id',$product_id)
    	->where('available_stock','>=',$quantity)
    	->get()->first();
    	// dd(DB::getQueryLog());
    	if($result)
    	{
    		return $result;
    	}else
    	{
    		return false;
    	}
    }
    public static function get_available_currency_stock()
    {
    	$result = DB::table('available_currency')
    	->select('one_rs_coin','two_rs_coin','five_rs_coin','ten_rs_coin','one_rs_note','two_rs_note','five_rs_note','ten_rs_note','twenty_rs_note','fifty_rs_note','hundread_rs_note','two_hundred_rs_note','five_hundred_rs_note','two_thousand_rs_note')
        ->where('id',1)
    	->get()->first();
    	if($result)
    	{
           return (array)$result;
    	}else
    	{
    		return false;
    	}
    }
    public static function update_product_stock_by_product_id($product_id,$updated_quantity)
    {
    	$result = DB::table('products')
            ->where('id', $product_id)
            ->update(['available_stock' => $updated_quantity]);
        return true;
    }
    public static function update_available_currency($update_array)
    {
    	$result = DB::table('available_currency')
            ->where('id', 1)
            ->update($update_array);
        return true;
    }
}
?>