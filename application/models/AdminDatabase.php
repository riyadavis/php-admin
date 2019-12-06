<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminDatabase extends CI_Model {

    public function adminLogin($loginDetails)
    { 
        $this->db->trans_start();
                            $this->db->select('id');
            $loginStatus = $this->db->get_where('admin',$loginDetails)->result();
        $this->db->trans_complete();
        if($loginStatus)
        {
            return $loginStatus;
        }
        else
        {
            return ["error"=>"Login Failed"];
        }
    }

    public function adminProfile($userId)
    { 
        $this->db->trans_start();
            $profileData = $this->db->get_where('admin',$userId)->result();
        $this->db->trans_complete();
        $url = "http://localhost/Admin/assets/images/".$userId['id']."/";
        
        foreach($profileData as &$val)
        {
            $val->profile_image = $url.$val->profile_image;
        }
        
        if($profileData)
        {
            return $profileData;
        }
        else
        {
            return ["error"=>"Failed to Fetch Admin Details"];
        }
    }

    public function addProduct($product)
    {
        $this->db->trans_start();
            $this->db->insert('product',$product);
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return ["error"=>"Product Added"];
        }
        else
        {
            return ["error"=>"Add Product Failed"];
        }
    }

    public function categoryDetailsAddProduct()
    {
        $this->db->trans_start();
            $this->db->select('*');
            $category = $this->db->get('category')->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return $category;
        }
        else
        {
            return ["error"=>"Add Product Failed"];
        }
    }
    public function deleteProduct($deleteId)
    {
        $this->db->trans_start();
            $this->db->where('id',$deleteId);
            $this->db->delete('product');
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return ["error"=>"Product Deleted"];
        }
        else
        {
            return ["error"=>"Delete Product Failed"];
        }
    }
    public function getProductToBeEdited($productId)
    {
        $this->db->trans_start();
            $this->db->select('*');
            $this->db->where('id',$productId);
         $productDetails =  $this->db->get('product')->result_array();
            $this->db->select('*');
         $category = $this->db->get('category')->result_array();
         $result = array('productDetails'=>$productDetails, 'category'=>$category);
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($productDetails)
            {
                return $result;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"failed to get product"];
        }
    }

    public function updateProduct($updateId,$product)
    {
        $this->db->trans_start();
            $this->db->where('id',$updateId);
            $this->db->update('product',$product);
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return ["error"=>"Product Updated"];
        }
        else
        {
            return ["error"=>"Update Product Failed"];
        }
    }

    public function getCouponDetails()
    {
        $this->db->trans_start();
            $this->db->select('*');
            $this->db->order_by('time_stamp','DESC');
            $totalNo = $this->db->get('coupon')->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($totalNo)
            {
                return $totalNo;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"get Coupon Failed"];
        }
    }

    public function couponSubscribe($coupon)
    {
        $this->db->trans_start();
            $this->db->insert('distributor_coupon_subscription',$coupon);
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return ["error"=>"Coupon Subscribed"];
        }
        else
        {
            return ["error"=>"Coupon Subscription Failed"];
        }
    }

    public function totalNoProducts()
    {
        $this->db->trans_start();
            $this->db->select('*');
            $totalNo = $this->db->get('product')->num_rows();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($totalNo)
            {
                return $totalNo;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"get total no. products Failed"];
        }
    }

    public function totalNoOrders()
    {
        $this->db->trans_start();
            $this->db->select('*');
            $totalNo = $this->db->get('customer_order')->num_rows();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($totalNo)
            {
                return $totalNo;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"get total no. orders Failed"];
        }
    }
    public function totalNoAcceptedOrders()
    {
        $this->db->trans_start();
            $this->db->select('*');
            $this->db->where('Accepted','true');
            $totalNo = $this->db->get('customer_order')->num_rows();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($totalNo)
            {
                return $totalNo;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"get total no. Accepted orders Failed"];
        }
    }
    public function totalNoPendingOrders()
    {
        $whereClause = array('Accepted'=>'false',
        'Cancelled'=>'false');
        $this->db->trans_start();
            $this->db->select('*');
            $this->db->where($whereClause);
            $totalNo = $this->db->get('customer_order')->num_rows();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($totalNo)
            {
                return $totalNo;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"get total no. Pending orders Failed"];
        }
    }
    public function totalNoCancelledOrders()
    {
        $this->db->trans_start();
            $this->db->select('*');
            $this->db->where('Cancelled','true');
            $totalNo = $this->db->get('customer_order')->num_rows();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($totalNo)
            {
                return $totalNo;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"get total no. Cancelled orders Failed"];
        }
    }
    public function searchProduct($search)
    {
        $this->db->trans_start();
            $this->db->where('product_name',$search)->or_where("id",$search);
            $searchData = $this->db->get('product')->result_array();
        $this->db->trans_complete();
        $url = "http://localhost/Admin/assets/images/";
        foreach($searchData as &$product) {
            $product['product_image'] = $url.$product['product_image'];
        }
        if($this->db->trans_status() === true)
        {
            if($searchData)
            {
                return $searchData;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"get search data Failed"];
        }
    }
    public function searchOrder($search)
    {
        $this->db->trans_start();
            $this->db->where('id',$search);
            $searchData = $this->db->get('customer_order')->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($searchData)
            {
                return $searchData;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"get search data Failed"];
        }
    }
    public function searchCashFloat($search)
    {
        $this->db->trans_start();
            $this->db->where('id',$search)->or_where('order_id',$search);
            $searchData = $this->db->get('floating_cash')->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($searchData)
            {
                return $searchData;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"get search data Failed"];
        }
    }

    public function floatingCash($distId)
    {
        $this->db->trans_start();
            $this->db->where('dist_id',$distId);
            $this->db->where_not_in('hub_status',"amount sent");
            $this->db->select_sum('amount');
           $amount = $this->db->get('floating_cash')->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return ["error"=> $amount];
        }
        else
        {
            return ["error"=>"Failed to Process Floating cash"];
        }
    }

    public function totalNoSentFloatingCash()
    {
        $this->db->trans_start();
            $this->db->select('*');
            $this->db->where('hub_status','Amount Sent');
            $totalNo = $this->db->get('floating_cash')->num_rows();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($totalNo)
            {
                return $totalNo;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"get total no. sent Floatingcash Failed"];
        }
    }
    public function totalNoPendingFloatingCash()
    {
        $this->db->trans_start();
            $this->db->select('*');
            $this->db->where('hub_status','Pending');
            $totalNo = $this->db->get('floating_cash')->num_rows();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($totalNo)
            {
                return $totalNo;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"get total no. Pending Floatingcash Failed"];
        }
    }
    public function totalNoReceivedFloatingCash()
    {
        $this->db->trans_start();
            $this->db->select('*');
            $this->db->where('hub_status','Amount Received');
            $totalNo = $this->db->get('floating_cash')->num_rows();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($totalNo)
            {
                return $totalNo;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"get total no. Received Floatingcash Failed"];
        }
    }

    public function totalNoFloatingCash()
    {
        $this->db->trans_start();
            $this->db->select('*');
            $totalNo = $this->db->get('floating_cash')->num_rows();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($totalNo)
            {
                return $totalNo;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"get total no. floating cash Failed"];
        }
    }
    public function viewReceivedFloatingCash()
    {
        $pageNo = $this->input->get('page') ? $this->input->get('page') : 1;
        $noOfValue = 8;
        $offset = ($pageNo - 1)* $noOfValue;       
        $this->db->trans_start();
        $receivedCash = $this->db->select('*')
                                 ->from('floating_cash')
                                 ->order_by('time_stamp', 'DESC')
                                 ->limit($noOfValue, $offset)
                                 ->where('hub_status','Amount Received')
                                 ->get()
                                 ->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($receivedCash)
            {
                return $receivedCash;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"View Received Floatingcash  Failed"];
        }
    }
    public function viewPendingFloatingCash()
    {
        $pageNo = $this->input->get('page') ? $this->input->get('page') : 1;
        $noOfValue = 8;
        $offset = ($pageNo - 1)* $noOfValue;       
        $this->db->trans_start();
        $pendingCash = $this->db->select('*')
                                 ->from('floating_cash')
                                 ->order_by('time_stamp', 'DESC')
                                 ->limit($noOfValue, $offset)
                                 ->where('hub_status','Pending')
                                 ->get()
                                 ->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($pendingCash)
            {
                return $pendingCash;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"View Pending Floatingcash  Failed"];
        }
    }

    public function viewSentFloatingCash()
    {
        $pageNo = $this->input->get('page') ? $this->input->get('page') : 1;
        $noOfValue = 8;
        $offset = ($pageNo - 1)* $noOfValue;       
        $this->db->trans_start();
        $cashSent = $this->db->select('*')
                                 ->from('floating_cash')
                                 ->order_by('time_stamp', 'DESC')
                                 ->limit($noOfValue, $offset)
                                 ->where('hub_status','Amount Sent')
                                 ->get()
                                 ->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($cashSent)
            {
                return $cashSent;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"View sent Floatingcash  Failed"];
        }
    }
    public function viewAllFloatingCash()
    {
        $pageNo = $this->input->get('page') ? $this->input->get('page') : 1;
        $noOfValue = 8;
        $offset = ($pageNo - 1)* $noOfValue;       
        $this->db->trans_start();
            $allFloatingCash = $this->db->select('*')
                                 ->from('floating_cash')
                                 ->order_by('time_stamp', 'DESC')
                                 ->limit($noOfValue, $offset)
                                 ->get()
                                 ->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($allFloatingCash)
            {
                return $allFloatingCash;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"View All Floating cash Failed"];
        }
    }
    public function viewAllProducts()
    {
        $pageNo = $this->input->get('page') ? $this->input->get('page') : 1;
        $noOfValue = 8;
        $offset = ($pageNo - 1)* $noOfValue;        
        $this->db->trans_start();
            $allProducts = $this->db->select('*')
                                 ->from('product')
                                 ->limit($noOfValue, $offset)
                                 ->get()
                                 ->result_array();
        $this->db->trans_complete();
        $url = "http://localhost/Admin/assets/images/";
        foreach($allProducts as &$product) {
            $product['product_image'] = $url.$product['product_image'];
        }
        if($this->db->trans_status() === true)
        {
            if($allProducts)
            {
                return $allProducts;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"View All Products Failed"];
        }
    }
    public function viewAllOrders()
    {
        $pageNo = $this->input->get('page') ? $this->input->get('page') : 1;
        $noOfValue = 8;
        $offset = ($pageNo - 1)* $noOfValue;       
        $this->db->trans_start();
            $allOrder = $this->db->select('*')
                                 ->from('customer_order')
                                 ->order_by('time_stamp', 'DESC')
                                 ->limit($noOfValue, $offset)
                                 ->get()
                                 ->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($allOrder)
            {
                return $allOrder;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"View All Order Failed"];
        }
    }

    public function viewAcceptedOrders()
    {
        $pageNo = $this->input->get('page') ? $this->input->get('page') : 1;
        $noOfValue = 8;
        $offset = ($pageNo - 1)* $noOfValue;       
        $this->db->trans_start();
        $acceptedOrders = $this->db->select('*')
                                 ->from('customer_order')
                                 ->order_by('time_stamp', 'DESC')
                                 ->limit($noOfValue, $offset)
                                 ->where('Accepted','true')
                                 ->get()
                                 ->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($acceptedOrders)
            {
                return $acceptedOrders;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"View Accepted Order Failed"];
        }
    }

    public function viewCancelledOrders()
	{
        $pageNo = $this->input->get('page') ? $this->input->get('page') : 1;
        $noOfValue = 8;
        $offset = ($pageNo - 1)* $noOfValue;       
        $this->db->trans_start();
        $cancelledOrders= $this->db->select('*')
                                 ->from('customer_order')
                                 ->order_by('time_stamp', 'DESC')
                                 ->limit($noOfValue, $offset)
                                 ->where('Cancelled','true')
                                 ->get()
                                 ->result_array();
        $this->db->trans_complete();
       
        if($this->db->trans_status() === true)
        {
            if($cancelledOrders)
            {
                return $cancelledOrders;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>"Failed to load Cancelled Orders"];
        }
    }

    public function viewPendingOrders()
    {
        $whereClause = array('Accepted'=>'false',
                            'Cancelled'=>'false');
        $pageNo = $this->input->get('page') ? $this->input->get('page') : 1;
        $noOfValue = 8;
        $offset = ($pageNo - 1)* $noOfValue;       
        $this->db->trans_start();
        $pendingOrders = $this->db->select('*')
                                    ->from('customer_order')
                                    ->order_by('time_stamp', 'DESC')
                                    ->limit($noOfValue, $offset)
                                    ->where($whereClause)
                                    ->get()
                                    ->result_array();
        $this->db->trans_complete();
        
        if($this->db->trans_status() === true)
        {
            if($pendingOrders)
            {
                return $pendingOrders;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>true, "reson"=>"Database Error"];
        }
    }

    public function cancelOrder($orderId)
    {
        $this->db->trans_start();
            $this->db->where('id',$orderId);
            $this->db->set('Cancelled','true');
            $this->db->set('Accepted', 'false');
            $this->db->update('customer_order');
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return ["error"=>"Order Cancelled"];
        }
        else
        {
            return ["error"=>"Failed to cancel orders"];
        }
    }

    public function acceptOrder($orderId)
    {
        $this->db->trans_start();
            $this->db->where('id',$orderId);
            $this->db->set('Accepted','true');
            $this->db->set('Cancelled','false');
            $this->db->update('customer_order');
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return ["error"=>"Order Accepted"];
        }
        else
        {
            return ["error"=>"Failed to Accept Order"];
        }
    }

    public function getNotifications()
    {
        $this->db->trans_start();
            $this->db->where('received_status','false');
            $this->db->order_by('time_stamp','DESC');
            $this->db->limit(5, 0);
            $notifications = $this->db->get('notification')->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($notifications)
            {
                return $notifications;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>true, "reason"=>"Database Error"];
        }
    }

    public function viewMoreNotifications()
    {
        $this->db->trans_start();
            $this->db->order_by('time_stamp','DESC');
            $this->db->limit(20, 0);
            $notifications = $this->db->get('notification')->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($notifications)
            {
                return $notifications;
            }
            else
            {
                return ["error"=>"no rows"];
            }
        }
        else
        {
            return ["error"=>true, "reason"=>"Database Error"];
        }
    }

    public function updateNotificationStatus($notificationId)
    {
        $this->db->trans_start();
            $this->db->where('id',$notificationId);
            $this->db->set('received_status','true');
            $this->db->update('notification');
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return ["error"=>"Notification updated"];
        }
        else
        {
            return ["error"=>"Failed to update"];
        }
    }

    public function analytics($startDate, $endDate)
    {
        $lineChart = array();
        $pieChart = array();

        $result = $this->db->select('*')
                    ->where(' time_stamp BETWEEN "'.$startDate.'" AND "'.$endDate.'"')
                    ->order_by('time_stamp','ASC')
                    ->get('customer_order')
                    ->result_array();
        if(sizeof($result)==0){
            return array(
                'lineChart' => false,
                'pieChart' => false
            );
        }
        foreach($result as &$item){
            $date = new DateTime($item['time_stamp']);
            $item['time_stamp'] = $date->format('Y-m-d'); //key for linechart
            $json = json_decode($item['getItems']);
            foreach($json as $j){
                $key = $j->name; //key for piechart

                //This if and else is for my linechart
                if(array_key_exists($item['time_stamp'], $lineChart)==false){
                    $lineChart[$item['time_stamp']] = $j->quantity;
                }else{
                    $lineChart[$item['time_stamp']] = $lineChart[$item['time_stamp']] + $j->quantity;
                }

                //This if else is for my pie chart
                if(array_key_exists($key, $pieChart)==false){
                    $pieChart[$key] = $j->quantity;
                }else{
                    $pieChart[$key] = $pieChart[$key] + $j->quantity;
                }
            }
        }
        arsort($pieChart);
        // reset($pieChart);
        $first_key = key($pieChart);
        $mostSold = array($first_key=>$pieChart[$first_key]);
        $pieChart = array_reverse($pieChart);
        $last_key = key($pieChart);
        $leastSold = array($last_key => $pieChart[$last_key]);

        // $key = array_keys($array)[1];
        if(count($pieChart)%2!=0){
            $key = array_keys($pieChart)[ceil(count($pieChart)/2)-1];
            $interSold = array($key => $pieChart[$key]);
        }else{
            $key1 = array_keys($pieChart)[count($pieChart)/2 - 1];
            $key2 = array_keys($pieChart)[count($pieChart)/2];  
            if($pieChart[$key1] >= $pieChart[$key2]){
                $interSold = array($key1 => $pieChart[$key1]);
            }else{
                $interSold = array($key2 => $pieChart[$key2]);
            }          
                        
        }
        $return = array(
            'lineChart' => $lineChart,
            'pieChart' => array(
                 $mostSold,
                $interSold,
                $leastSold
            )
        );
        return $return;
    }
}