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
            $response['return_amt'] = $result['original_change'];
            $response['return_denomination_object'] = $result['return_denomination_object'];
            /* return change */
        }else
        {
            $response['return_amt'] = 0;
            $response['return_denomination_object'] = null;
        }
        $items_pay_to_users = array_map(function($arr1){
            if($arr1['image'] !="")
            {
                $arr1['image'] = url('/').$arr1['image'];
            }
            return $arr1;
        },$stock_result['available_items']);

        $response['items_pay_to_users'] = $items_pay_to_users;
        $hardware_commands = array();

        /*DB::transaction(function () {
            DB::table('users')->update(['votes' => 1]);

            DB::table('posts')->delete();
        });*/

        /* update_product_stock */
        foreach ($stock_result['available_items'] as $key => $value) {
            $updated_quantity = $value['available_stock'] - $value['quantity'];
            Query_helper::update_product_stock_by_product_id($value['product_id'],$updated_quantity);
        }
        /* update_product_stock */

        /* update currency stock */        
        // deduct returned currency from currency stock
        if($response['return_denomination_object'] != null)
        {
            $to_update_on_currency = Common::generate_update_currency_object($response['return_denomination_object'],'deduct');
            Query_helper::update_available_currency($to_update_on_currency);
        }

        // Add submitted currency to currency stock        
        $to_update_on_currency = Common::generate_update_currency_object($submitted_currency_object,'add');
        Query_helper::update_available_currency($to_update_on_currency);
        /* update currency stock */

        /* incase of any hardware involved hardware related commands will goes here */
        $response['hardware_command_to_dispatch_change'] = Common::generate_hardware_object($response['return_denomination_object']);
        
        $response['hardware_command_to_dispatch_item'] = implode('-',array_map(function($item){
            return $item['product_id'].':'.$item['quantity'];
        },$response['items_pay_to_users']));

        /* incase of any hardware involved hardware related commands will goes here */
        return $this->sendResponse($response,'Products purchased successfully.');
    }
}