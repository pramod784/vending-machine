<?php
namespace App\Helpers;
use DB;
class Query_helper {
    public static function check_is_product_id_valid($product_list)
    {
    	$result = DB::table('products')
    	->select('*')
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
    public static function insert_purchase_data($insert_array)
    {
        $res = DB::table('purchase_data')->insert($insert_array);
        if($res)
        {
           return DB::getPdo()->lastInsertId();
        }
        else
        {
            return FALSE;
        }
    }
    public static function insert_purchased_items($insert_array)
    {
        $res = DB::table('purchased_items')->insert($insert_array);
        if($res)
        {
           return DB::getPdo()->lastInsertId();
        }
        else
        {
            return FALSE;
        }
    }
    public static function is_valid_booking_id($booking_id)
    {
        $result = DB::table('purchase_data')
        ->select('*')
        ->orderBy('id','DESC')
        ->get()->first();
        if($result)
        {
            if($result->booking_id == $booking_id && $result->booking_status == 'initiated' && ((time() - strtotime($result->created_at)) / 60) <= 5)
            {
                return $result->id;
            }else
            {
                return false;
            }
        }else
        {
            return false;
        }
    }
    public static function get_purchase_detail($purchase_id)
    {
        //DB::enableQueryLog();
        $result = DB::table('purchased_items as pi')
            ->select('pd.id','pd.booking_id','pd.payable_amt','pd.submitted_currency_object','pd.returned_currency_object','pd.booking_status','pi.product_id','pi.quantity','pi.buy_price','p.id as product_id','p.sku_code','p.name','p.detail','p.price','p.available_stock','p.image')
            ->where('pd.id',$purchase_id)
            ->join('purchase_data as pd','pd.id','=','pi.booking_id')
            ->join('products as p','p.id','=','pi.product_id')
            ->groupBy('pi.id')
            ->get()->toArray();
        //dd(DB::getQueryLog());
        if($result)
        {
            return $result;
        }
        else
        {
            return array();
        }
    }
    public static function get_product_data($product_id)
    {
        $result = DB::table('products')
        ->select('*')
        ->where('id',$product_id)
        ->get()->first();
        if($result)
        {
            return $result;
        }else
        {
            return false;
        }
    }
    public static function update_purchase_data($update_array,$where_array)
    {
        $result = DB::table('purchase_data')
            ->where($where_array)
            ->update($update_array);
        return true;
    }
}
?>