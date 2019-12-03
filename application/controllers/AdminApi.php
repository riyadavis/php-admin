<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminApi extends CI_Controller {
	public function __construct()
	{
		parent :: __construct();
		header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');    
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
		$this->load->model('AdminDatabase');	
	}

	public function index()
	{
		echo FCPATH.'assets/images';
		
	}

	public function adminLogin()
	{
		$loginDetails = array('userName'=>$this->input->post('username'),
								'password'=>$this->input->post('password')
							);
		$data['items'] = $this->AdminDatabase->adminLogin($loginDetails);
		$this->load->view('API/json_data',$data);
	}

	public function addProduct()
	{
		$config['upload_path'] = FCPATH.'assets/images/';
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
			$product = array('category_id'=>$this->input->post('category_id'),
						  'dist_id'=>$this->input->post('dist_id'),
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

	public function deleteProduct()
	{
		// echo json_encode($_GET);
		$deleteId = $this->input->get('productId');
		$file = $this->input->post('delete_image');
		// echo json_encode($file);
		
				unlink(FCPATH.'assets/images/'.$file);
		
		$data['items'] = $this->AdminDatabase->deleteProduct($deleteId);
		$this->load->view('API/json_data',$data);
	}
	public function updateProduct()
	{
		$updateId = $this->input->post('id');

		$config['upload_path'] = './assets/images/';
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
			$data['items'] = $this->AdminDatabase->updateProduct($updateId,$product);
			$this->load->view('API/json_data',$data);
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
	
	public function searchProduct()
	{
		$search =  $this->input->get('search');
		$data['items'] = $this->AdminDatabase->searchProduct($search);
		$this->load->view('API/json_data',$data);
	}

	public function searchOrder()
	{
		$search =  $this->input->get('search');
		$data['items'] = $this->AdminDatabase->searchOrder($search);
		$this->load->view('API/json_data',$data);
	}
	public function searchCashFloat()
	{
		$search =  $this->input->get('search');
		$data['items'] = $this->AdminDatabase->searchCashFloat($search);
		$this->load->view('API/json_data',$data);
	}

	public function viewAllProducts()
	{
		$data['items'] = $this->AdminDatabase->viewAllProducts();
		$this->load->view('API/json_data',$data);
	}
	public function getProductToBeEdited()
	{
		$productId =  $this->input->get('productId');
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
		$data['items'] = $this->AdminDatabase->totalNoOrders();
		$this->load->view('API/json_data',$data);
	}
	public function totalNoAcceptedOrders()
	{
		$data['items'] = $this->AdminDatabase->totalNoAcceptedOrders();
		$this->load->view('API/json_data',$data);
	}
	public function totalNoPendingOrders()
	{
		$data['items'] = $this->AdminDatabase->totalNoPendingOrders();
		$this->load->view('API/json_data',$data);
	}
	public function totalNoCancelledOrders()
	{
		$data['items'] = $this->AdminDatabase->totalNoCancelledOrders();
		$this->load->view('API/json_data',$data);
	}
	public function viewAllOrders()
	{
		$data['items'] = $this->AdminDatabase->viewAllOrders();
		$this->load->view('API/json_data',$data);
	}
	public function viewAcceptedOrders()
	{
		$data['items'] = $this->AdminDatabase->viewAcceptedOrders();
		$this->load->view('API/json_data',$data);
	}

	public function viewCancelledOrders()
	{
		$data['items'] = $this->AdminDatabase->viewCancelledOrders();
		$this->load->view('API/json_data',$data);
	}

	public function viewPendingOrders()
	{
		$data['items'] = $this->AdminDatabase->viewPendingOrders();
		$this->load->view('API/json_data',$data);
	}

	public function cancelOrder()
	{
		$orderId = $this->input->post('orderId');
		$data['items'] = $this->AdminDatabase->cancelOrder($orderId);
		$this->load->view('API/json_data',$data);
	}

	public function acceptOrder()
	{
		// $orderId = $this->input->post('orderId');
		// $data['items'] = $this->AdminDatabase->acceptOrder($orderId);
		$this->load->library('Pusher');
		$data = "Hello World";
		$pusher = $this->pusher->push($data);
		// $this->load->view('API/json_data',$data);
	}

	public function push()
	{
		$this->load->view('acceptOrder');
	}

	public function floatingCash()
	{
		$distId = $this->input->post('distId');
		$data['items'] = $this->AdminDatabase->floatingCash($distId);
		$this->load->view('API/json_data',$data);
	}
	public function totalNoFloatingCash()
	{
		$data['items'] = $this->AdminDatabase->totalNoFloatingCash();
		$this->load->view('API/json_data',$data);
	}
	public function totalNoSentFloatingCash()
	{
		$data['items'] = $this->AdminDatabase->totalNoSentFloatingCash();
		$this->load->view('API/json_data',$data);
	}
	public function totalNoPendingFloatingCash()
	{
		$data['items'] = $this->AdminDatabase->totalNoPendingFloatingCash();
		$this->load->view('API/json_data',$data);
	}

	public function totalNoReceivedFloatingCash()
	{
		$data['items'] = $this->AdminDatabase->totalNoReceivedFloatingCash();
		$this->load->view('API/json_data',$data);
	}

	public function viewAllFloatingCash()
	{
		$data['items'] = $this->AdminDatabase->viewAllFloatingCash();
		$this->load->view('API/json_data',$data);
	}
	public function viewSentFloatingCash()
	{
		$data['items'] = $this->AdminDatabase->viewSentFloatingCash();
		$this->load->view('API/json_data',$data);
	}
	public function viewPendingFloatingCash()
	{
		$data['items'] = $this->AdminDatabase->viewPendingFloatingCash();
		$this->load->view('API/json_data',$data);
	}
	public function viewReceivedFloatingCash()
	{
		$data['items'] = $this->AdminDatabase->viewReceivedFloatingCash();
		$this->load->view('API/json_data',$data);
	}
	public function analytics()
	{
        $startDate = $this->input->get('startDate');
		$endDate = $this->input->get('endDate');	
		$data['items'] = $this->AdminDatabase->analytics($startDate, $endDate);
		$this->load->view('API/json_data',$data);		
	}

	public function getNotifications()
	{
		$data['items'] = $this->AdminDatabase-> getNotifications();
		$this->load->view('API/json_data',$data);
	}

	public function viewMoreNotifications()
	{
		$data['items'] = $this->AdminDatabase-> viewMoreNotifications();
		$this->load->view('API/json_data',$data);
	}

	public function updateNotificationStatus()
	{
		$notificationId = $this->input->get('id');
		$data['items'] = $this->AdminDatabase-> updateNotificationStatus($notificationId);
		$this->load->view('API/json_data',$data);
	}
}
