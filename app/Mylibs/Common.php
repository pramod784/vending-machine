<?php
namespace App\Mylibs;
use DB;
use Carbon\Carbon;
use App\Helpers\Query_helper;
class Common {
 	public static function check_is_stock_available($item_list)
    {
        $not_available_items = array();
        $available_items = array();
        $available = true;
        foreach ($item_list as $key => $value)
        {
            $stock_result = Query_helper::check_is_stock_available((int)$value['product_id'],(int)$value['quantity']);
            if(!$stock_result)
            {
                $available = false;
                $not_available_items[] = $value; 
            }else{
                $value['available_stock'] = $stock_result->available_stock;
                $value['price'] = $stock_result->price;
                $value['sku_code'] = $stock_result->sku_code;
                $value['name'] = $stock_result->name;
                $value['detail'] = $stock_result->detail;
                $value['image'] = $stock_result->image;
                $available_items[] = $value;
            }
        }
        return array('available' => $available,'available_items' => $available_items,'not_available_items' => $not_available_items);
    }
    public static function generate_cleaned_currency_object($currency)
    {
        $temp = array();
        $temp['one_rs_coin'] = isset($currency["one_rs_coin"])?(int)$currency["one_rs_coin"]:0;
        $temp['two_rs_coin'] = isset($currency['two_rs_coin'])?(int)$currency["two_rs_coin"]:0;
        $temp['five_rs_coin'] = isset($currency['five_rs_coin'])?(int)$currency["five_rs_coin"]:0;
        $temp['ten_rs_coin'] = isset($currency['ten_rs_coin'])?(int)$currency["ten_rs_coin"]:0;
        $temp['one_rs_note'] = isset($currency['one_rs_note'])?(int)$currency["one_rs_note"]:0;
        $temp['two_rs_note'] = isset($currency['two_rs_note'])?(int)$currency["two_rs_note"]:0;
        $temp['five_rs_note'] = isset($currency['five_rs_note'])?(int)$currency["five_rs_note"]:0;
        $temp['ten_rs_note'] = isset($currency['ten_rs_note'])?(int)$currency["ten_rs_note"]:0;
        $temp['twenty_rs_note'] = isset($currency['twenty_rs_note'])?(int)$currency["twenty_rs_note"]:0;
        $temp['fifty_rs_note'] = isset($currency['fifty_rs_note'])?(int)$currency["fifty_rs_note"]:0;
        $temp['hundread_rs_note'] = isset($currency['hundread_rs_note'])?(int)$currency["hundread_rs_note"]:0;
        $temp['two_hundred_rs_note'] = isset($currency['two_hundred_rs_note'])?(int)$currency["two_hundred_rs_note"]:0;
        $temp['five_hundred_rs_note'] = isset($currency['five_hundred_rs_note'])?(int)$currency["five_hundred_rs_note"]:0;
        $temp['two_thousand_rs_note'] = isset($currency['two_thousand_rs_note'])?(int)$currency["two_thousand_rs_note"]:0;
        return $temp;
    }
    public static function generate_currency_denomination_key_as_number($currency)
    {
        $currency_available_denomination = array();
        if($currency["one_rs_coin"]+$currency["one_rs_note"] > 0)
        {
            $currency_available_denomination['1'] = $currency["one_rs_coin"]+$currency["one_rs_note"];    
        }
        if($currency["two_rs_coin"]+$currency["two_rs_note"] > 0)
        {
            $currency_available_denomination['2'] = $currency["two_rs_coin"]+$currency["two_rs_note"];    
        }
        if($currency["five_rs_coin"]+$currency["five_rs_note"] > 0)
        {
            $currency_available_denomination['5'] = $currency["five_rs_coin"]+$currency["five_rs_note"];    
        }
        if($currency["ten_rs_coin"]+$currency["ten_rs_note"] > 0)
        {
            $currency_available_denomination['10'] = $currency["ten_rs_coin"]+$currency["ten_rs_note"];    
        }
        if($currency["twenty_rs_note"] > 0)
        {
            $currency_available_denomination['20'] = $currency["twenty_rs_note"];    
        }
        if($currency["fifty_rs_note"] > 0)
        {
            $currency_available_denomination['50'] = $currency["fifty_rs_note"];    
        }
        if($currency["hundread_rs_note"] > 0)
        {
            $currency_available_denomination['100'] = $currency["hundread_rs_note"];    
        }
        if($currency["two_hundred_rs_note"] > 0)
        {
            $currency_available_denomination['200'] = $currency["two_hundred_rs_note"];    
        }
        if($currency["five_hundred_rs_note"] > 0)
        {
            $currency_available_denomination['500'] = $currency["five_hundred_rs_note"];    
        }
        if($currency["two_thousand_rs_note"] > 0)
        {
            $currency_available_denomination['2000'] = $currency["two_thousand_rs_note"];    
        }
        return $currency_available_denomination;
    }
    public static function generate_sorted_note_coin_stack($currency_object)
    {
        //echo 'Here';
        //print_r($currency_object);
        $currency_stack = array();
        foreach ($currency_object as $key => $value) {
            for ($i=0; $i < $value; $i++) { 
                $currency_stack[] = $key;
            }
        }
        return $currency_stack;
        /*echo '<br>';
        print_r($currency_stack);
        exit;*/
    }
    public static function calculate_amount_using_currency_object($currency_object)
    {
        $total = 0;
        $object_multiplier = array(1,2,5,10,1,2,5,10,20,50,100,200,500,2000);
        $currency = array_values($currency_object);
        /*echo " <br>multiplier ".count($object_multiplier);
        echo " <br>currency ".count($currency);*/
        $total_pay = array_sum(array_map(function($amt, $quantity) {
            return $amt * $quantity;
        }, $object_multiplier, $currency));
        return $total_pay;
    }
    public static function calculate_items_payable_amount($item_price,$item_quantity)
    {
        $payable_amt = array_sum(array_map(function($amt, $quantity) {
            return $amt * $quantity;
        }, $item_price, $item_quantity));
        return $payable_amt;
    }
    public static function generate_change_currency_denominations($change_amt,$denomination_type = null)
    {
        $currency_stock = Query_helper::get_available_currency_stock();
        $common = new Common;
        $currency_key_stock = $common::generate_currency_denomination_key_as_number($currency_stock);
        $currency_stack = $common::generate_sorted_note_coin_stack($currency_key_stock);
        $count_original_stack = count($currency_stack);
        $denomination = array();
        $original_change = $change_amt;
        if($denomination_type == null)
        {
            $denomination_type = "max_possible";
        }
        switch ($denomination_type) {
            case 'only_coins':
                rsort($currency_stack);
                $loop = 1;
                do
                {
                    foreach ($currency_stack as $key => $value) {
                        if($change_amt >= $value && $value <= 10){
                            $denomination[] = $value;
                            $change_amt = $change_amt-$value;
                            unset($currency_stack[$key]);
                            //continue;
                            break;
                        }
                    }
                    if($change_amt == 0)
                    {
                        break;
                    }
                    $loop++;
                } while ($loop <= count($currency_stack));                
                sort($denomination);
                $change_object = $common::convert_notes_array_to_curreny_object($denomination);
                
                $result = array(
                    'original_change'=>$original_change,
                    'denomination_sum'=>array_sum($denomination),
                    'remaining_change_amt'=>$change_amt,
                    'return_denomination_object'=>$change_object,
                );
                return $result;
                break;
            case 'mix_denomination':
                rsort($currency_stack);
                $loop = 1;
                do
                {
                    foreach ($currency_stack as $key => $value) {
                        if($change_amt >= $value){
                            $denomination[] = $value;
                            $change_amt = $change_amt-$value;
                            unset($currency_stack[$key]);
                            continue;
                            //break;
                        }
                    }
                    if($change_amt == 0)
                    {
                        break;
                    }
                    $loop++;
                } while ($loop <= count($currency_stack));                
                sort($denomination);
                $change_object = $common::convert_notes_array_to_curreny_object($denomination);
                
                $result = array(
                    'original_change'=>$original_change,
                    'denomination_sum'=>array_sum($denomination),
                    'remaining_change_amt'=>$change_amt,
                    'return_denomination_object'=>$change_object,
                );
                return $result;
                break;
            default:
                // case max_possible
                rsort($currency_stack);
                $loop = 1;
                do
                {
                    foreach ($currency_stack as $key => $value) {
                        if($change_amt >= $value){
                            $denomination[] = $value;
                            $change_amt = $change_amt-$value;
                            unset($currency_stack[$key]);
                            //continue;
                            break;
                        }
                    }
                    if($change_amt == 0)
                    {
                        break;
                    }
                    $loop++;
                } while ($loop <= count($currency_stack));                
                sort($denomination);
                $change_object = $common::convert_notes_array_to_curreny_object($denomination);
                
                $result = array(
                    'original_change'=>$original_change,
                    'denomination_sum'=>array_sum($denomination),
                    'remaining_change_amt'=>$change_amt,
                    'return_denomination_object'=>$change_object,
                );
                return $result;
                break;
        }
    }
    public static function convert_notes_array_to_curreny_object($change_denomination)
    {
        $currency_stock = Query_helper::get_available_currency_stock();
        $final_currency_object = array(
            'one_rs_coin'=>0,
            'two_rs_coin'=>0,
            'five_rs_coin'=>0,
            'ten_rs_coin'=>0,
            'one_rs_note'=>0,
            'two_rs_note'=>0,
            'five_rs_note'=>0,
            'ten_rs_note'=>0,
            'twenty_rs_note'=>0,
            'fifty_rs_note'=>0,
            'hundread_rs_note'=>0,
            'two_hundred_rs_note'=>0,
            'five_hundred_rs_note'=>0,
            'two_thousand_rs_note'=>0,
        );
        foreach ($change_denomination as $key => $value) {
            switch ($value) {
                case 1:
                    if($currency_stock['one_rs_coin'] > 0)
                    {
                        $final_currency_object['one_rs_coin'] = $final_currency_object['one_rs_coin']+1;
                    }elseif ($currency_stock['one_rs_note'] > 0) {
                        $final_currency_object['one_rs_note'] = $final_currency_object['one_rs_note']+1;
                    }
                    break;
                case 2:
                    if($currency_stock['two_rs_coin'] > 0)
                    {
                        $final_currency_object['two_rs_coin'] = $final_currency_object['two_rs_coin']+1;
                    }elseif ($currency_stock['two_rs_note'] > 0) {
                        $final_currency_object['two_rs_note'] = $final_currency_object['two_rs_note']+1;
                    }
                    break;
                case 5:
                    if($currency_stock['five_rs_coin'] > 0)
                    {
                        $final_currency_object['five_rs_coin'] = $final_currency_object['five_rs_coin']+1;
                    }elseif ($currency_stock['five_rs_note'] > 0) {
                        $final_currency_object['five_rs_note'] = $final_currency_object['five_rs_note']+1;
                    }
                    break;
                case 10:
                    if($currency_stock['ten_rs_coin'] > 0)
                    {
                        $final_currency_object['ten_rs_coin'] = $final_currency_object['ten_rs_coin']+1;
                    }elseif ($currency_stock['ten_rs_note'] > 0) {
                        $final_currency_object['ten_rs_note'] = $final_currency_object['ten_rs_note']+1;
                    }
                    break;
                case 20:
                    if($currency_stock['twenty_rs_note'] > 0)
                    {
                        $final_currency_object['twenty_rs_note'] = $final_currency_object['twenty_rs_note']+1;
                    }
                    break;
                case 50:
                    if($currency_stock['fifty_rs_note'] > 0)
                    {
                        $final_currency_object['fifty_rs_note'] = $final_currency_object['fifty_rs_note']+1;
                    }
                    break;
                case 100:
                    if($currency_stock['hundread_rs_note'] > 0)
                    {
                        $final_currency_object['hundread_rs_note'] = $final_currency_object['hundread_rs_note']+1;
                    }
                    break;
                case 200:
                    if($currency_stock['two_hundred_rs_note'] > 0)
                    {
                        $final_currency_object['two_hundred_rs_note'] = $final_currency_object['two_hundred_rs_note']+1;
                    }
                    break;
                case 500:
                    if($currency_stock['five_hundred_rs_note'] > 0)
                    {
                        $final_currency_object['five_hundred_rs_note'] = $final_currency_object['five_hundred_rs_note']+1;
                    }
                    break;
                case 2000:
                    if($currency_stock['two_thousand_rs_note'] > 0)
                    {
                        $final_currency_object['two_thousand_rs_note'] = $final_currency_object['two_thousand_rs_note']+1;
                    }
                    break;
                    # code...
                    break;
            }
        }
        return $final_currency_object;
    }
    public static function generate_update_currency_object($currency_object,$action)
    {
        $currency_current_stock = Query_helper::get_available_currency_stock();
        if($action == 'add')
        {
            $currency_current_stock['one_rs_coin'] = $currency_current_stock['one_rs_coin'] + $currency_object['one_rs_coin'];
            $currency_current_stock['two_rs_coin'] = $currency_current_stock['two_rs_coin'] + $currency_object['two_rs_coin'];
            $currency_current_stock['five_rs_coin'] = $currency_current_stock['five_rs_coin'] + $currency_object['five_rs_coin'];
            $currency_current_stock['ten_rs_coin'] = $currency_current_stock['ten_rs_coin'] + $currency_object['ten_rs_coin'];
            $currency_current_stock['one_rs_note'] = $currency_current_stock['one_rs_note'] + $currency_object['one_rs_note'];
            $currency_current_stock['two_rs_note'] = $currency_current_stock['two_rs_note'] + $currency_object['two_rs_note'];
            $currency_current_stock['five_rs_note'] = $currency_current_stock['five_rs_note'] + $currency_object['five_rs_note'];
            $currency_current_stock['ten_rs_note'] = $currency_current_stock['ten_rs_note'] + $currency_object['ten_rs_note'];
            $currency_current_stock['twenty_rs_note'] = $currency_current_stock['twenty_rs_note'] + $currency_object['twenty_rs_note'];
            $currency_current_stock['fifty_rs_note'] = $currency_current_stock['fifty_rs_note'] + $currency_object['fifty_rs_note'];
            $currency_current_stock['hundread_rs_note'] = $currency_current_stock['hundread_rs_note'] + $currency_object['hundread_rs_note'];
            $currency_current_stock['two_hundred_rs_note'] = $currency_current_stock['two_hundred_rs_note'] + $currency_object['two_hundred_rs_note'];
            $currency_current_stock['five_hundred_rs_note'] = $currency_current_stock['five_hundred_rs_note'] + $currency_object['five_hundred_rs_note'];
            $currency_current_stock['two_thousand_rs_note'] = $currency_current_stock['two_thousand_rs_note'] + $currency_object['two_thousand_rs_note'];
        }
        else if($action == 'deduct')
        {
            $currency_current_stock['one_rs_coin'] = $currency_current_stock['one_rs_coin'] - $currency_object['one_rs_coin'];
            $currency_current_stock['two_rs_coin'] = $currency_current_stock['two_rs_coin'] - $currency_object['two_rs_coin'];
            $currency_current_stock['five_rs_coin'] = $currency_current_stock['five_rs_coin'] - $currency_object['five_rs_coin'];
            $currency_current_stock['ten_rs_coin'] = $currency_current_stock['ten_rs_coin'] - $currency_object['ten_rs_coin'];
            $currency_current_stock['one_rs_note'] = $currency_current_stock['one_rs_note'] - $currency_object['one_rs_note'];
            $currency_current_stock['two_rs_note'] = $currency_current_stock['two_rs_note'] - $currency_object['two_rs_note'];
            $currency_current_stock['five_rs_note'] = $currency_current_stock['five_rs_note'] - $currency_object['five_rs_note'];
            $currency_current_stock['ten_rs_note'] = $currency_current_stock['ten_rs_note'] - $currency_object['ten_rs_note'];
            $currency_current_stock['twenty_rs_note'] = $currency_current_stock['twenty_rs_note'] - $currency_object['twenty_rs_note'];
            $currency_current_stock['fifty_rs_note'] = $currency_current_stock['fifty_rs_note'] - $currency_object['fifty_rs_note'];
            $currency_current_stock['hundread_rs_note'] = $currency_current_stock['hundread_rs_note'] - $currency_object['hundread_rs_note'];
            $currency_current_stock['two_hundred_rs_note'] = $currency_current_stock['two_hundred_rs_note'] - $currency_object['two_hundred_rs_note'];
            $currency_current_stock['five_hundred_rs_note'] = $currency_current_stock['five_hundred_rs_note'] - $currency_object['five_hundred_rs_note'];
            $currency_current_stock['two_thousand_rs_note'] = $currency_current_stock['two_thousand_rs_note'] - $currency_object['two_thousand_rs_note'];
        }
        return $currency_current_stock;
    }
    public static function generate_raw_object($currency)
    {
        $currency_counts_array = array();
        $currency_counts_array[] = isset($currency["one_rs_coin"])?(int)$currency["one_rs_coin"]:0;
        $currency_counts_array[] = isset($currency['two_rs_coin'])?(int)$currency["two_rs_coin"]:0;
        $currency_counts_array[] = isset($currency['five_rs_coin'])?(int)$currency["five_rs_coin"]:0;
        $currency_counts_array[] = isset($currency['ten_rs_coin'])?(int)$currency["ten_rs_coin"]:0;
        $currency_counts_array[] = isset($currency['one_rs_note'])?(int)$currency["one_rs_note"]:0;
        $currency_counts_array[] = isset($currency['two_rs_note'])?(int)$currency["two_rs_note"]:0;
        $currency_counts_array[] = isset($currency['five_rs_note'])?(int)$currency["five_rs_note"]:0;
        $currency_counts_array[] = isset($currency['ten_rs_note'])?(int)$currency["ten_rs_note"]:0;
        $currency_counts_array[] = isset($currency['twenty_rs_note'])?(int)$currency["twenty_rs_note"]:0;
        $currency_counts_array[] = isset($currency['fifty_rs_note'])?(int)$currency["fifty_rs_note"]:0;
        $currency_counts_array[] = isset($currency['hundread_rs_note'])?(int)$currency["hundread_rs_note"]:0;
        $currency_counts_array[] = isset($currency['two_hundred_rs_note'])?(int)$currency["two_hundred_rs_note"]:0;
        $currency_counts_array[] = isset($currency['five_hundred_rs_note'])?(int)$currency["five_hundred_rs_note"]:0;
        $currency_counts_array[] = isset($currency['two_thousand_rs_note'])?(int)$currency["two_thousand_rs_note"]:0;
        /* this will create object something like "1-1-1-1-1-1-1-1-1-1-1-1-12-1" */
        return implode('-',$currency_counts_array);
    }
    public static function convert_raw_currency_object_to_normal_object($raw_object)
    {
        $final_currency_object = array(
            'one_rs_coin'=>0,
            'two_rs_coin'=>0,
            'five_rs_coin'=>0,
            'ten_rs_coin'=>0,
            'one_rs_note'=>0,
            'two_rs_note'=>0,
            'five_rs_note'=>0,
            'ten_rs_note'=>0,
            'twenty_rs_note'=>0,
            'fifty_rs_note'=>0,
            'hundread_rs_note'=>0,
            'two_hundred_rs_note'=>0,
            'five_hundred_rs_note'=>0,
            'two_thousand_rs_note'=>0,
        );
        $keys = array_keys($final_currency_object);
        $values = explode('-',$raw_object);
        return array_combine($keys,$values);

        /*$final_currency_object = array(
            'one_rs_coin' => $$values[0],
            'two_rs_coin' => $$values[1],
            'five_rs_coin' => $$values[2],
            'ten_rs_coin' => $$values[3],
            'one_rs_note' => $$values[4],
            'two_rs_note' => $$values[5],
            'five_rs_note' => $$values[6],
            'ten_rs_note' => $$values[7],
            'twenty_rs_note' => $$values[8],
            'fifty_rs_note' => $$values[9],
            'hundread_rs_note' => $$values[10],
            'two_hundred_rs_note' => $$values[11],
            'five_hundred_rs_note' => $$values[12],
            'two_thousand_rs_note' => $$values[13]
        );*/
    }
}