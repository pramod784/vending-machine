<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Product;
use Validator;
use Helper;
use App\Helpers\Query_helper;
use App\Mylibs\Common;

class TransactionController extends BaseController
{
    public function index()
    {
        
    }
    public function buy(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'item_list' => 'required',
            'currency' => 'required',
            'continue_with_available_items'=>'required'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        foreach ($input['item_list'] as $key => $value) {
            if($value['quantity'] <= 0 || $value['product_id'] == "" || $value['product_id'] == null)
            {
                unset($input['item_list'][$key]);
            }
        }
        $input['item_list'] = array_values($input['item_list']); // This help to reindexing of array in case of middle element is deleted
        $change_priority = null;
        if(isset($input['change_priority']))
        {
            $change_priority = $input['change_priority']; 
        }
        /*clean the object in case of any missing input or sequence */
        $submitted_currency_object = Common::generate_cleaned_currency_object($input['currency']);
        /*clean the object in case of any missing input or sequence */
        $submitted_amount = Common::calculate_amount_using_currency_object($submitted_currency_object);

        /* check for requested stock is available */
        $stock_result = Common::check_is_stock_available($input['item_list']);
        
        $continue_with_available_items = $input['continue_with_available_items'];

        if($continue_with_available_items == false && $stock_result['available']===false)
        {
            return $this->sendError('We are running out of stock for few items', $stock_result['not_available_items']);
        }
        /* check for requested stock is available */
        
        $item_price = array_column($stock_result['available_items'],'price');
        $item_quantity = array_column($stock_result['available_items'],'quantity');
        
        $payable_amt = round(Common::calculate_items_payable_amount($item_price,$item_quantity));
        
        /* check is submmited amount is more or equal to payable_amt*/
        $change_return = 0;
        if($submitted_amount < $payable_amt)
        {
            $remainder = $payable_amt - $submitted_amount;
            return $this->sendError("Insufficient amount You have to pay $remainder more", $remainder);
        }else
        {
            $change_return = $submitted_amount - $payable_amt;
        }
        
        /* calculate submitted currency */
        $submitted_currency = $input['currency'];
        /* calculate submitted currency */
        
        $response = array();
        $booking_id = uniqid();
        $response['booking_id'] = $booking_id;
        $response['payable_amt'] = $payable_amt;
        $response['submitted_amount'] = $submitted_amount;
        if($change_return > 0)
        {
            $currency_stock = Query_helper::get_available_currency_stock();            
            $stock_amount = Common::calculate_amount_using_currency_object($currency_stock);
            //dd($stock_amount);
            if($stock_amount < $change_return)
            {
                return $this->sendError("We are running out of cash. Please provide exact change!");
            }
            $result = Common::generate_change_currency_denominations($change_return,$change_priority);
            if($result['remaining_change_amt'] > 0)
            {
                return $this->sendError("We dont have sufficient denomination for change . Please provide exact change!");
            }
            /* return change */
            $response['booking_id'] = $booking_id;
            $response['return_amt'] = $result['original_change'];
            $response['return_denomination_object'] = $result['return_denomination_object'];
            /* return change */
        }else
        {
            $response['booking_id'] = $booking_id;
            $response['return_amt'] = 0;
            $response['return_denomination_object'] = null;
        }

        /* insert purchase data */
            $insert_array = array(
                'booking_id' => $booking_id,
                'payable_amt' =>  $payable_amt,
                'submitted_currency_object' => Common::generate_raw_object($submitted_currency_object),
                'returned_currency_object' => Common::generate_raw_object($response['return_denomination_object']),
                'booking_status'=>'initiated',
                'created_at' => date('Y-m-d H:i:s',time())
            );
            $inserted_id = Query_helper::insert_purchase_data($insert_array);
            //insert purchased items details
            $items = array();
            foreach ($stock_result['available_items'] as $key => $value) {
                $items[] = array(
                    'booking_id' => $inserted_id,
                    'product_id' => $value["product_id"],
                    'quantity' => $value["quantity"],
                    'buy_price' => $value["price"],
                    'created_at' => date('Y-m-d H:i:s',time())
                );
            }
            $inserted_items = Query_helper::insert_purchased_items($items);
            
        /* insert purchase data */
        
        $items_pay_to_users = array_map(function($arr1){
            if($arr1['image'] !="")
            {
                $arr1['image'] = url('/').$arr1['image'];
            }
            return $arr1;
        },$stock_result['available_items']);

        $response['items_pay_to_users'] = $items_pay_to_users;
        
        return $this->sendResponse($response,'Please review order. Current order is valid only for 5 min.');
    }
    public function review_purchase(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'booking_id' => 'required',
            'action' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $booking_id = $request->booking_id;
        $action = $request->action;
        $purchase_id = Query_helper::is_valid_booking_id($booking_id);
        if($purchase_id)
        {
            $purchase_detail = Query_helper::get_purchase_detail($purchase_id);
            $input['purchase_id'] = $purchase_id;
            if($action == 1) // Approve purchase
            {
                /* update_product_stock */
                foreach ($purchase_detail as $key => $value) {
                    $updated_quantity = $value->available_stock - $value->quantity;
                    Query_helper::update_product_stock_by_product_id($value->product_id,$updated_quantity);
                }
                /* update_product_stock */

                /* update currency stock */
                // deduct returned currency from currency stock
                $return_change_denomination = Common::convert_raw_currency_object_to_normal_object($purchase_detail[0]->returned_currency_object);
                $to_update_on_currency = Common::generate_update_currency_object($return_change_denomination,'deduct');
                Query_helper::update_available_currency($to_update_on_currency);
                
                // Add submitted currency to currency stock
                $submitted_currency_object = Common::convert_raw_currency_object_to_normal_object($purchase_detail[0]->submitted_currency_object);                
                $to_update_on_currency = Common::generate_update_currency_object($submitted_currency_object,'add');
                Query_helper::update_available_currency($to_update_on_currency);

                /* update currency stock */

                /* Mark Current purchase successfull */

                $update_array = array('booking_status'=>'complete');
                $where_array = array('id'=>$purchase_id);
                Query_helper::update_purchase_data($update_array,$where_array);

                /* Mark Current purchase successfull */

                /* incase of any hardware involved hardware related commands will goes here */
                $response['return_change_denomination'] = $return_change_denomination;
                $response['hardware_command_to_dispatch_change'] = $purchase_detail[0]->returned_currency_object;
                
                $response['hardware_command_to_dispatch_item'] = implode('-',array_map(function($item){
                    return $item->product_id.':'.$item->quantity;
                },$purchase_detail));
                /* incase of any hardware involved hardware related commands will goes here */
                return $this->sendResponse($response,'Product purchased successfull!');
            } else if($action == 0) // Cancel purchase
            {
                /* Mark Current purchase cancel */
                $update_array = array('booking_status'=>'cancelled');
                $where_array = array('id'=>$purchase_id);
                Query_helper::update_purchase_data($update_array,$where_array);
                
                /* Mark Current purchase cancel */
                $response['hardware_command_to_dispatch_submitted_currency'] = $purchase_detail[0]->submitted_currency_object;
                $response['submitted_currency_denomination'] = Common::convert_raw_currency_object_to_normal_object($purchase_detail[0]->submitted_currency_object);

                return $this->sendResponse($response,'purchase_detail!');
            } else {
                return $this->sendError('Invalid action!');
            }
            
        } else {
            return $this->sendError('Invalid or expired booking id!');
        }
    }
}