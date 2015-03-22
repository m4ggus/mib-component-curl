mib/curl
========

Small wrapper class for the php curl extension

Usage
-----

	$curl = new Mib\Component\Curl\Curl();

	$response = $curl
		->setUrl('http://www.example.com/')
		->followRedirect()
		->returnTransfer()
		->post(['field' => 'search-term']);

