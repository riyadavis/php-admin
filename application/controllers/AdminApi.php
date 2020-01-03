<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminApi extends CI_Controller {
	public function __construct()
	{
		parent :: __construct();
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: *");
        header('Access-Control-Allow-Credentials: true');    
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
		$this->load->model('AdminDatabase');
		$this->authorization();	
	}

	public function authorization()
	{
		$auth = $this->input->get_request_header('Authorization', true);
		$auth = explode(':', $auth);
		$response = array();
		if( !empty($auth[0]) && !empty($auth[1]))
		{
			if($auth[0]=='loggedIn' && $auth[1]=='false')
			{
				return true;
			}else {
				// $GLOBALS['userId'] = $auth[0];
				$userId = $auth[0];
				$apiKey = $auth[1];
				$salt = md5($userId.SALT_KEY);
				if($salt == $apiKey){
					return true;
				}else {
					die(json_encode(array('Api_Key_Error'=> true)));
				}
			}
		}
		else {
			$response['Unauthorized'] = true;
			die(json_encode($response));		
		}
	}
	public function index()
	{
		echo $userId;
	}

	public function adminLogin()
	{
		$loginDetails = array('userName'=>$this->input->post('userName'),
								'password'=>$this->input->post('password')
							);
		$data['items'] = $this->AdminDatabase->adminLogin($loginDetails);
		$this->load->view('API/json_data',$data);
	}

	public function adminProfile()
	{
		$userId = array('id' => $this->input->post('userId'));
		$data['items'] = $this->AdminDatabase->adminProfile($userId);
		$this->load->view('API/json_data',$data);
	}

	public function addProduct()
	{
		$userId = $this->input->post('userId');
		if(!is_dir(FCPATH.'assets/images/'.$userId))
		{
			mkdir(FCPATH.'assets/images/'.$userId);
		}
		
		$config['upload_path'] = FCPATH.'assets/images/'.$userId;
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 2000;
        $config['max_width'] = 1500;
        $config['max_height'] = 1500;

        $this->load->library('upload', $config);

		if (!$this->upload->do_upload('product_image'))
		{
            $error['items'] = array('error' => $this->upload->display_errors());

            $this->load->view('API/json_data', $error);
		} 
		else
		{
			$imageData = array('image_metadata' => $this->upload->data());
			// echo json_encode($imageData);
			$product = array('category_id'=>$this->input->post('category_id'),
						//   'dist_id'=>$this->input->post('dist_id'),
						  'product_name'=>$this->input->post('product_name'),
						  'product_image'=>$imageData['image_metadata']['file_name'],
						  'product_price'=>$this->input->post('product_price'),
						  'max_discount'=>$this->input->post('max_discount'),
						  'min_discount'=>$this->input->post('min_discount'),
						  'product_tags'=>$this->input->post('product_tags')
						  );
			$data['items'] = $this->AdminDatabase->addProduct($product);
			$this->load->view('API/json_data',$data);
		}
	}

	public function deleteProduct($deleteId)
	{
		// echo json_encode($_GET);
		$userId = $this->input->post('userId');
		// $deleteId = $this->input->get('productId');
		$file = $this->input->post('delete_image');
		// echo json_encode($file);
		
				unlink(FCPATH.'assets/images/'.$userId.'/'.$file);
		
		$data['items'] = $this->AdminDatabase->deleteProduct($deleteId);
		$this->load->view('API/json_data',$data);
	}

	public function categoryDetailsAddProduct()
	{
		$data['items'] = $this->AdminDatabase->categoryDetailsAddProduct();
		$this->load->view('API/json_data',$data);
	}
	public function updateProduct()
	{
		$updateId = $this->input->post('id');
		$userId = $this->input->post('userId');

		if(!is_dir(FCPATH.'assets/images/'.$userId))
		{
			mkdir(FCPATH.'assets/images/'.$userId);
		}
		
		$config['upload_path'] = FCPATH.'/assets/images/'.$userId;
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 2000;
        $config['max_width'] = 1500;
        $config['max_height'] = 1500;

        $this->load->library('upload', $config);

		if (!$this->upload->do_upload('product_image'))
		{
			
            // $error['items'] = array('error' => $this->upload->display_errors());

			// $this->load->view('API/json_data', $error);
			
			$product = array('category_id'=>$this->input->post('category_id'),
							'dist_id'=>$this->input->post('dist_id'),
							'product_name'=>$this->input->post('product_name'),
							'product_price'=>$this->input->post('product_price'),
							'max_discount'=>$this->input->post('max_discount'),
							'min_discount'=>$this->input->post('min_discount'),
							'product_tags'=>$this->input->post('product_tags')
							);
			if($this->upload->display_errors() === "<p>You did not select a file to upload.</p>")
			{
				$data['items'] = $this->AdminDatabase->updateProduct($updateId,$product);
				$this->load->view('API/json_data',$data);
			}
			else {
				$data['items'] = array("status"=>$this->AdminDatabase->updateProduct($updateId,$product),
									"error"=>$this->upload->display_errors());
				$this->load->view('API/json_data',$data);
			}
		} 
		else
		{
			// echo json_encode($_POST);
			$file = $this->input->post('previous_image');
			if($file)
			{
				unlink(FCPATH.'assets/images/'.$file);
			}

			$imageData = array('image_metadata' => $this->upload->data());
			$product = array('category_id'=>$this->input->post('category_id'),
							'dist_id'=>$this->input->post('dist_id'),
							'product_name'=>$this->input->post('product_name'),
							'product_image'=>$imageData['image_metadata']['file_name'],
							'product_price'=>$this->input->post('product_price'),
							'max_discount'=>$this->input->post('max_discount'),
							'min_discount'=>$this->input->post('min_discount'),
							'product_tags'=>$this->input->post('product_tags')
							);
			$data['items'] = $this->AdminDatabase->updateProduct($updateId,$product);
			$this->load->view('API/json_data',$data);
		}
	}
	
	public function searchProduct($search="")
	{
		// $search =  $this->input->get('search');
		$data['items'] = $this->AdminDatabase->searchProduct($search);
		$this->load->view('API/json_data',$data);
	}

	public function searchOrder($search="")
	{
		// $search =  $this->input->get('search');
		$data['items'] = $this->AdminDatabase->searchOrder($search);
		$this->load->view('API/json_data',$data);
	}
	public function searchCashFloat($search="")
	{
		// $search =  $this->input->get('search');
		$data['items'] = $this->AdminDatabase->searchCashFloat($search);
		$this->load->view('API/json_data',$data);
	}

	public function getCouponDetails()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->getCouponDetails($userId);
		$this->load->view('API/json_data',$data);
	}

	public function couponSubscribe()
	{
		$coupon = array('distributor_id'=>$this->input->post('userId'),
						'coupon_id'=> $this->input->post('couponId'),
						'status'=>1
						);
		$couponVerify = array('distributor_id'=>$this->input->post('userId'),
						'coupon_id'=> $this->input->post('couponId'),
						);				
		$data['items'] = $this->AdminDatabase->couponSubscribe($coupon,$couponVerify);
		$this->load->view('API/json_data',$data);
	}

	public function couponUnsubscribe()
	{

		$couponVerify = array('distributor_id'=>$this->input->post('userId'),
						'coupon_id'=> $this->input->post('couponId'),
						);				
		$data['items'] = $this->AdminDatabase->couponUnsubscribe($couponVerify);
		$this->load->view('API/json_data',$data);
	}

	public function viewAllProducts()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->viewAllProducts($userId);
		$this->load->view('API/json_data',$data);
	}
	public function getProductToBeEdited($productId)
	{
		// $productId =  $this->input->get('productId');
		$data['items'] = $this->AdminDatabase->getProductToBeEdited($productId);
		$this->load->view('API/json_data',$data);
	}
	public function totalNoProducts()
	{
		$data['items'] = $this->AdminDatabase->totalNoProducts();
		$this->load->view('API/json_data',$data);
	}
	public function totalNoOrders()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->totalNoOrders($userId);
		$this->load->view('API/json_data',$data);
	}
	public function totalNoAcceptedOrders()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->totalNoAcceptedOrders($userId);
		$this->load->view('API/json_data',$data);
	}
	public function totalNoPendingOrders()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->totalNoPendingOrders($userId);
		$this->load->view('API/json_data',$data);
	}
	public function totalNoCancelledOrders()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->totalNoCancelledOrders($userId);
		$this->load->view('API/json_data',$data);
	}
	public function viewAllOrders()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->viewAllOrders($userId);
		$this->load->view('API/json_data',$data);
	}
	public function viewAcceptedOrders()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->viewAcceptedOrders($userId);
		$this->load->view('API/json_data',$data);
	}

	public function viewCancelledOrders()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->viewCancelledOrders($userId);
		$this->load->view('API/json_data',$data);
	}

	public function viewPendingOrders()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->viewPendingOrders($userId);
		$this->load->view('API/json_data',$data);
	}

	public function cancelOrder($orderId=1)
	{
		// $orderId = $this->input->get('orderId');
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->cancelOrder($orderId, $userId);
		$this->load->view('API/json_data',$data);
	}

	public function acceptOrder($orderId=1)
	{
		// $this->load->library('Pusher');
		// $data = "Your Order is Confirmed";
		// $pusher = $this->pusher->push($data);
		// $orderId = $this->input->get('orderId');
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->acceptOrder($orderId, $userId);
		$this->load->view('API/json_data',$data);
	}

	public function push()
	{
		$this->load->view('acceptOrder');
	}

	public function floatingCash()
	{
		$distId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->floatingCash($distId);
		$this->load->view('API/json_data',$data);
	}
	public function totalNoFloatingCash()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->totalNoFloatingCash($userId);
		$this->load->view('API/json_data',$data);
	}
	public function totalNoSentFloatingCash()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->totalNoSentFloatingCash($userId);
		$this->load->view('API/json_data',$data);
	}
	public function totalNoPendingFloatingCash()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->totalNoPendingFloatingCash($userId);
		$this->load->view('API/json_data',$data);
	}

	public function totalNoReceivedFloatingCash()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->totalNoReceivedFloatingCash($userId);
		$this->load->view('API/json_data',$data);
	}

	public function viewAllFloatingCash()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->viewAllFloatingCash($userId);
		$this->load->view('API/json_data',$data);
	}
	public function viewSentFloatingCash()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->viewSentFloatingCash($userId);
		$this->load->view('API/json_data',$data);
	}
	public function viewPendingFloatingCash()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->viewPendingFloatingCash($userId);
		$this->load->view('API/json_data',$data);
	}
	public function viewReceivedFloatingCash()
	{
		$userId = $this->input->post('userId');
		$data['items'] = $this->AdminDatabase->viewReceivedFloatingCash($userId);
		$this->load->view('API/json_data',$data);
	}
	public function analytics()
	{
        $startDate = $this->input->get('startDate');
		$endDate = $this->input->get('endDate');
		$userId = $this->input->get('userId');	
		$data['items'] = $this->AdminDatabase->analytics($startDate, $endDate, $userId);
		$this->load->view('API/json_data',$data);		
	}

	public function getNotifications()
	{
		$userId = $this->input->post('userId');	
		$data['items'] = $this->AdminDatabase-> getNotifications($userId);
		$this->load->view('API/json_data',$data);
	}

	public function viewMoreNotifications()
	{
		$userId = $this->input->post('userId');	
		$data['items'] = $this->AdminDatabase-> viewMoreNotifications($userId);
		$this->load->view('API/json_data',$data);
	}

	public function updateNotificationStatus($notificationId=1)
	{
		$userId = $this->input->post('userId');	
		// $notificationId = $this->input->get('id');
		$data['items'] = $this->AdminDatabase-> updateNotificationStatus($notificationId,$userId);
		$this->load->view('API/json_data',$data);
	}
}
