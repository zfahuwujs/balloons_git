<?php
	/*
	 *	CMI Gateway for PHP
	 *	(c)2004 iPAY
	 */
	
	$CONFIG["CMI_POST_URL"]			= "https://paysrv.ipay.com/ipay.aspx";
	
	// CubeCart-specific Options
	$CONFIG["CUBECART_DOCUMENT_ROOT"]	= $GLOBALS['rootDir'];
	//$CONFIG["CUBECART_BILLING_OPTION"]	= "";
	$CONFIG["CUBECART_PRODUCT_NAME"]	= "Cart - ".$cart_order_id;
	
	// iPAY CMI PHP Gateway	
	define("REQUEST", "REQUEST");
	define("AUTHENTICATION", "AUTHENTICATION");
	define("TRANSACTION", "TRANSACTION");
	define("CARDINFO", "CARDINFO");
	define("CONSUMER", "CONSUMER");
	define("BILLING", "BILLING");
	define("YES", "YES");
	define("NO", "NO");
	
	// Global request XML object
	$rqtxml = domxml_new_doc("1.0");
	
/*
 *	Primary Request component.  This object encompases the entire Request transaction.  It immediately holds the merchantRequestID
 *	and the Authentication and Transaction objects.  See those class declarations for more details.
 */
	class Request
	{
		var $merchantRequestID = "";
		var $Authentication;
		var $Transaction;
		var $_xml = "";
		
		// Constructor
		function Request()
		{
			global $CONFIG,$module;
			
			$this->Authentication = new RequestAuthentication($module['acNo'], $module['password']);
		}
		
		// Validates the data within; if all data needed is present, XML is created
		function CreateRequestXml()
		{
			// Verify the contents of this object.  The called function calls the same function on all child objects.
			if ($this->_VerifyContents())
			{
				$this->_xml = $this->_RenderNode();
				return $this->_xml;
			}
		}
		
		// Creates the transaction subobject
		function CreateTransaction($action, $fraudScrubLevel, $effectiveDate = "NOW", $transactionDate = "NOW", $anniversaryDate = "NOW", $autoCalculateCredit = "NO")
		{
			$this->Transaction = new RequestTransaction($action, $fraudScrubLevel, $effectiveDate, $transactionDate, $anniversaryDate, $autoCalculateCredit);
		}
		
		// Creates and assigns the CardInfo object to the Trnasaction subobject
		function SetCardInfo($cardNumber, $expirationMonth, $expirationYear, $cvc2, $domesticAVS = "NONE", $internationalAVS = "NONE")
		{
			if (!empty($this->Transaction))
				$this->Transaction->CardInfo = new RequestTransactionCardInfo($cardNumber, $expirationMonth, $expirationYear, $cvc2, $domesticAVS, $internationalAVS);
		}
		
		// Creates and assigns the Consumer object to the Trnasaction subobject
		function SetConsumer($firstName, $lastName, $addr1, $addr2, $city, $state, $country, $postalCode, $emailAddress, $ipAddress, $consumerID = "")
		{
			if (!empty($this->Transaction))
				$this->Transaction->Consumer = new RequestTransactionConsumer($firstName, $lastName, $addr1, $addr2, $city, $state, $country, $postalCode, $emailAddress, $ipAddress, $consumerID);
		}
		
		// Creates and assigns the Billing object to the Trnasaction subobject
		function SetBilling($billingOption, $initialAmount, $trialPeriod, $recurringFrequency, $productName, $promotionCode)
		{
			if (!empty($this->Transaction))
				$this->Transaction->Billing = new RequestTransactionBilling($billingOption, $initialAmount, $trialPeriod, $recurringFrequency, $productName, $promotionCode);
		}
		
		// Submits a transaction via cURL as an HTTPS post
		function Submit()
		{
			global $CONFIG;
			
			// Create the XML string to be posted
			$this->CreateRequestXml();
			
			//echo $this->_xml->dump_mem();
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $CONFIG["CMI_POST_URL"]);			// URL to post to
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);				// Require host verification
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);	// Use the client user agent as the posting agent
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_xml->dump_mem());		// dump the XML string as the post data
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);				// Return the string rather than printing it
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);					// Set timeout to 100 seconds
			curl_setopt($ch, CURLOPT_POST, 1);					// Set POST as the method
			
			$resp = curl_exec($ch);
			curl_close($ch);
			
			return $resp;
		}
		
		
		// *** PRIVATE *** Creates the XML objects for this object.
		function _RenderNode()
		{
			global $rqtxml;
			
			// Create the XML element
			$node = $rqtxml->create_element(REQUEST);
			$rqtxml->append_child($node);
			
			// Set attributes
			$node->set_attribute("merchantRequestID", $this->merchantRequestID);
			
			// Now add the nodes for the Authentication and Trnasaction objects
			$node->append_child($this->Authentication->_RenderNode());
			$node->append_child($this->Transaction->_RenderNode());
			
			return $rqtxml;
		}
		
		// *** PRIVATE *** Verifies that all required data is present
		function _VerifyContents()
		{
			$passed = TRUE;
			
			// REQUIRED: $merchantRequestID
			if (strlen($this->merchantID) < 0)
				$passed = FALSE;
				
			// Check Authentication
			if (!empty($this->Authentication))
				$passed = $this->Authentication->_VerifyContents();
			else
				$passed = FALSE;
				
			// Check Transaction
			if (!empty($this->Transaction))
				$passed = $this->Transaction->_VerifyContents();
			else
				$passed = FALSE;
				
			return $passed;
		}
		
		
	}
	
	
/*
 *	Authentication component.  This object exists as a subobject of the Request object and is created indirectly via the
 * 	Request object constructor.  This is the only object that is implicitly created.
 */
	class RequestAuthentication
	{
		var $merchantID = "";
		var $password = "";
		
		// Constructor
		function RequestAuthentication($merchantID, $password)
		{
			$this->merchantID = $merchantID;
			$this->password = $password;
		}
		
		// *** PRIVATE *** Creates the XML object(s) for this item.  Called automatically from the Request->_RenderNode() function
		function _RenderNode()
		{
			global $rqtxml;
			
			// Create the XML element
			$node = $rqtxml->create_element(AUTHENTICATION);
			
			// Set attributes
			$node->set_attribute("merchantID", $this->merchantID);
			$node->set_attribute("password", $this->password);
			
			return $node;
		}
		
		// *** PRIVATE *** Verifies data.  Called automatically from the Request->_VerifyContents() function.
		function _VerifyContents()
		{
			$passed = TRUE;
			
			// REQUIRED: merchantID, password
			if (strlen($this->merchantID) < 0 || strlen($this->password) < 0)
				$passed = FALSE;
				
			return $passed;
		}
	}
	
	
/*
 *	REQUEST Transaction component (differs from the Response component).  Holds transaction type and date information.  Contains three
 * 	subobjects: CardInfo, Consumer and Billing.
 */
	class RequestTransaction
	{
		var $action 		 = "SALE";
		var $fraudScrubLevel 	 = "0";
		var $effectiveDate 	 = "NOW";
		var $transactionDate 	 = "NOW";
		var $anniversaryDate 	 = "NOW";
		var $autoCalculateCredit = NO;
		var $CardInfo;
		var $Consumer;
		var $Billing;
		
		// Constructor
		function RequestTransaction($action, $fraudScrubLevel, $effectiveDate = "NOW", $transactionDate = "NOW", $anniversaryDate = "NOW", $autoCalculateCredit = "NO")
		{
			$this->action = $action;
			$this->fraudScrubLevel = $fraudScrubLevel;
			$this->effectiveDate = $effectiveDate;
			$this->transactionDate = $transactionDate;
			$this->anniversaryDate = $anniversaryDate;
			$this->autoCalculateCredit = $autoCalculateCredit;
		}
		
		// Creates an XML element representing a CARDINFO node
		function _RenderNode()
		{
			global $rqtxml;
			
			// Create the XML element
			$node = $rqtxml->create_element(TRANSACTION);
			
			// Set attributes
			$node->set_attribute("action", $this->action);
			$node->set_attribute("fraudScrubLevel", $this->fraudScrubLevel);
			$node->set_attribute("effectiveDate", $this->effectiveDate);
			$node->set_attribute("transactionDate", $this->transactionDate);
			$node->set_attribute("anniversaryDate", $this->anniversaryDate);
			$node->set_attribute("autoCalculateCredit", $this->autoCalculateCredit);
			
			$node->append_child($this->CardInfo->_RenderNode());
			$node->append_child($this->Consumer->_RenderNode());
			$node->append_child($this->Billing->_RenderNode());
			
			return $node;
		}
		
		// *** PRIVATE *** Verifies data.  Called automatically from the Request->_VerifyData() function.
		function _VerifyContents()
		{
			$passed = TRUE;
			
			// REQUIRED: actionantID, fraudScrubLevel, autoCalculateCredit
			if (strlen($this->merchantID) < 0 || strlen($this->password) < 0 || strlen($this->autoCalculateCredit) < 0)
				$passed = FALSE;
				
			// REQUIRED: effectiveDate, transactionDate, anniversaryDate
			if (strlen($this->effectiveDate) < 0 || strlen($this->transactionDate) < 0 || strlen($this->anniversaryDate) < 0)
				$passed = FALSE;
				
			// Now verify that the subobjects have been set properly
			// Checl CardInfo
			if (!empty($this->CardInfo))
				$passed = $this->CardInfo->_VerifyContents();
			else
				$passed = FALSE;
				
			// Check Consumer
			if (!empty($this->Consumer))
				$passed = $this->Consumer->_VerifyContents();
			else
				$passed = FALSE;
				
			// Check Billing
			if (!empty($this->Billing))
				$passed = $this->Billing->_VerifyContents();
			else
				$passed = FALSE;
			
			return $passed;
		}
	}
	
	
/*
 *	Card/account information.  Exists as a subobject of the Request->Transaction object.
 */
	class RequestTransactionCardInfo
	{
		var $cardNumber 	= "";
		var $expirationMonth 	= "";
		var $expirationYear 	= "";
		var $cvc2 		= "";
		var $domesticAVS 	= "NONE";
		var $internationalAVS	= "NONE";
		
		// Constructor
		function RequestTransactionCardInfo($cardNumber, $expirationMonth, $expirationYear, $cvc2, $domesticAVS = "NONE", $internationalAVS = "NONE")
		{
			$this->cardNumber 	= $cardNumber;
			$this->expirationMonth 	= $expirationMonth;
			$this->expirationYear 	= $expirationYear;
			$this->cvc2 		= $cvc2;
			$this->domesticAVS 	= $domesticAVS;
			$this->internationalAVS	= $internationalAVS;
		}
		
		// Creates an XML element representing a CARDINFO node
		function _RenderNode()
		{
			global $rqtxml;
			
			// Create the XML element
			$node = $rqtxml->create_element(CARDINFO);
			
			// Set attributes
			$node->set_attribute("cardNumber", $this->cardNumber);
			$node->set_attribute("expirationMonth", $this->expirationMonth);
			$node->set_attribute("expirationYear", $this->expirationYear);
			$node->set_attribute("cvc2", $this->cvc2);
			$node->set_attribute("domesticAVS", $this->domesticAVS);
			$node->set_attribute("internationalAVS", $this->internationalAVS);
			
			return $node;
		}
		
		// Verifies that all required data is present
		function _VerifyContents()
		{
			$passed = TRUE;
			
			// REQUIRED: cardNumber, expirationMonth, expirationYear
			if (strlen($this->cardNumber) <= 0 || strlen($this->expirationMonth) <= 0 || strlen($this->expirationYear) <= 0)
				$passed = FALSE;
				
			// REQUIRED: cvc2, domesticAVS. internationalAVS
			if (strlen($this->cvc2) <= 0 || strlen($this->domesticAVS) <= 0 || strlen($this->internationalAVS) <= 0)
				$passed = FALSE;
				
			return $passed;
		}
	}
	
	
/*
 *	Contains consumer information for a Request transaction.  Exists as a subobject of the Request->Transaction object.
 */
	class RequestTransactionConsumer
	{
		var $firstName 		= "";
		var $lastName 		= "";
		var $addr1 		= "";
		var $addr2 		= "";
		var $city 		= "";
		var $state 		= "";
		var $country 		= "";
		var $postalCode 	= "";
		var $emailAddress 	= "";
		var $ipAddress 		= "";
		var $consumerID 	= "";
		
		// Constructor - 2-line address
		function RequestTransactionConsumer($firstName, $lastName, $addr1, $addr2, $city, $state, $country, $postalCode, $emailAddress, $ipAddress, $consumerID = "")
		{
			$this->firstName 	= $firstName;
			$this->lastName 	= $lastName;
			$this->addr1 		= $addr1;
			$this->addr2 		= $addr2;
			$this->city 		= $city;
			$this->state 		= $state;
			$this->country 		= $country;
			$this->postalCode 	= $postalCode;
			$this->emailAddress	= $emailAddress;
			$this->ipAddress 	= $ipAddress;
			$this->consumerID 	= $consumerID;
		}
		
		// Creates an XML element representing a CONSUMER node
		function _RenderNode()
		{
			global $rqtxml;
			
			// Create the XML element
			$node = $rqtxml->create_element(CONSUMER);
			
			// Set attributes
			$node->set_attribute("firstName", $this->firstName);
			$node->set_attribute("lastName", $this->lastName);
			$node->set_attribute("addr1", $this->addr1);
			
			// Only add this one if it is there
			if (strlen($this->addr2) > 0)
				$node->set_attribute("addr2", $this->addr2);			
			
			$node->set_attribute("city", $this->city);
			$node->set_attribute("state", $this->state);
			$node->set_attribute("country", $this->country);
			$node->set_attribute("postalCode", $this->postalCode);
			$node->set_attribute("emailAddress", $this->emailAddress);
			$node->set_attribute("ipAddress", $this->ipAddress);
			$node->set_attribute("consumerID", $this->consumerID);
			
			return $node;
		}
		
		// Verifies that all required data is present
		function _VerifyContents()
		{
			$passed = TRUE;
			
			// REQUIRED: firstName, lastName
			if (strlen($this->firstName) <= 0 || strlen($this->lastName) <= 0)
				$passed = FALSE;
				
			// REQUIRED: addr1, city. state, country, postalCode
			if (strlen($this->addr1) <= 0 || strlen($this->city) <= 0 || strlen($this->country) <= 0 || strlen($this->postalCode) <= 0)
				$passed = FALSE;
				
			// REQUIRED: emailAddress, ipAddress
			if (strlen($this->emailAddress) <= 0 || strlen($this->ipAddress) <= 0)
				$passed = FALSE;
				
			return $passed;
		}
	}
	
	
/*
 *	Contains billing (product) information.  Exists as a subobject of the Reuqest->Transaction object.
 */
	class RequestTransactionBilling
	{
		var $billingOption 	= "";
		var $initialAmount 	= "0.00";
		var $trialPeriod 	= "";
		var $recurringFrequency	= "";
		var $productName 	= "";
		var $promotionCode 	= "";
		
		function RequestTransactionBilling($billingOption, $initialAmount, $trialPeriod, $recurringFrequency, $productName, $promotionCode)
		{
			$this->billingOption 		= $billingOption;
			$this->initialAmount 		= $initialAmount;
			$this->trialPeriod 		= $trialPeriod;
			$this->recurringFrequency 	= $recurringFrequency;
			$this->productName 		= $productName;
			$this->promotionCode 		= $promotionCode;
		}
		
		function _RenderNode()
		{
			global $rqtxml;
			
			// Create the XML element
			$node = $rqtxml->create_element(BILLING);
			
			// Set attributes
			$node->set_attribute("billingOption", $this->billingOption);
			$node->set_attribute("initialAmount", $this->initialAmount);
			$node->set_attribute("trialPeriod", $this->trialPeriod);
			$node->set_attribute("recurringFrequency", $this->recurringFrequency);
			$node->set_attribute("productName", $this->productName);
			$node->set_attribute("promotionCode", $this->promotionCode);
			
			return $node;
		}
		
		// Verifies that all required data is present
		function _VerifyContents()
		{
			$passed = TRUE;
			
			// REQUIRED: billingOption, initialAmount, productName
			if (strlen($this->billingOption) < 0 || strlen($this->initialAmount) < 0 || strlen($this->productName) < 0)
				$passed = FALSE;
				
			return $passed;
		}
	}
	
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

/*
 *	Contains the entire RESPONSE from the server
 */
	class Response
	{
		var $systemRequestID 	= "";
		var $merchantRequestID 	= "";
		var $authenticated 	= "False";
		var $loaded 		= "0";
		var $submitted 		= "";
		var $successful 	= "0";
		var $failed 		= "0";
		var $processed 		= "0";
		var $notes 		= "";
		var $status 		= "";
		var $Transaction;
		var $Request;
		var $_xml;
		var $_xmltxt;
		
		// Constructor
		function Response($xml)
		{
			$this->_xmltext = $xml;
			
			// Open the response XML in the XML object
			if (!$this->_xml = domxml_open_mem($this->_xmltext))
			{
				echo "Error: Unable to load response";
				exit;
			}
			
			// Get the root element ("RESPONSE")
			$root = $this->_xml->document_element();

			$this->systemRequestID 	 = $root->get_attribute('systemRequestID');
			$this->merchantRequestID = $root->get_attribute("merchantRequestID");
			$this->authenticated 	 = $root->get_attribute("authenticated");
			$this->loaded 		 = $root->get_attribute("loaded");
			$this->submitted 	 = $root->get_attribute("submitted");
			$this->successful 	 = $root->get_attribute("successful");
			$this->failed 		 = $root->get_attribute("failed");
			$this->processed 	 = $root->get_attribute("processed");
			$this->notes 		 = $root->get_attribute("notes");
			$this->status 		 = $root->get_attribute("status");
			
			// Create the TRANSACTION node withing the response
			$this->_CreateResponseTransaction();
		}

		// *** PRIVATE *** Create the ResponseTransaction object		
		function _CreateResponseTransaction()
		{
			// Get the TRANSACTION node (there should be two of them; one will be in the returned REQUEST node)
			$trxs = $this->_xml->get_elements_by_tagname(TRANSACTION);
			
			// We're okay as long as there is a single node; it's the first one that we need anyway
			if (sizeof($trxs) > 0)
			{
				$trxnode = $trxs[0];
				$action 		= $trxnode->get_attribute("action");
				$amount 		= $trxnode->get_attribute("amount");
				$systemTransactionID 	= $trxnode->get_attribute("systemTransactionID");
				$subscriptionID 	= $trxnode->get_attribute("subscriptionID");
				$merchantTransactionID 	= $trxnode->get_attribute("merchantTransactionID");

				// Create the object
				$this->Transaction = new ResponseTransaction($action, $amount, $systemTransactionID, $subscriptionID, $merchantTransactionID);
				
				// Now create the ResponseTransactionResponse subobject (I didn't design the XML, just the classes to handle it)
				$this->_CreateResponseTransactionResponse();
			}
		}
		
		// *** PRIVATE *** Create the ResponseTransactionResponse object
		function _CreateResponseTransactionResponse()
		{
			// Get the RESPONSE tagnames
			$trxs = $this->_xml->get_elements_by_tagname(RESPONSE);

			// There should be TWO of these; we need the SECOND one
			if (sizeof($trxs) > 0)
			{
				$trxnode = $trxs[1];
				$success 		= $trxnode->get_attribute("success");
				$authorizationCode 	= $trxnode->get_attribute("authorizationCode");
				$responseCode 		= $trxnode->get_attribute("responseCode");
				$notes 			= $trxnode->get_attribute("notes");
			}
			
			// Create the object
			$this->Transaction->Response = new ResponseTransactionResponse($success, $authorizationCode, $responseCode, $notes);
		}
		
		// *** PRIVATE *** Create the ResponseRequest object
		function _CreateResponseRequest()
		{
		}
	}
	
/*
 *	Contains the TRANSACTION node of a response.  This differs from the Transaction in a request.
 */
	class ResponseTransaction
	{
		var $action = "";
		var $amount = "0.00";
		var $systemTransactionID = "";
		var $subscriptionID = "";
		var $merchantTransactionID = "";
		var $Response;
		
		// Constructor
		function ResponseTransaction($action, $amount, $systemTransactionID, $subscriptionID, $merchantTransactionID)
		{
			$this->action 			= $action;
			$this->amount 			= $amount;
			$this->systemTransactionID 	= $systemTransactionID;
			$this->subscriptionID 		= $subscriptionID;
			$this->merchantTransactionID 	= $merchantTransactionID;
		}
	}
	
	class ResponseTransactionResponse
	{
		var $success = "False";
		var $authorizationCode = "";
		var $responseCode = "";
		var $notes = "";
		
		function ResponseTransactionResponse($success, $authorizationCode, $responseCode, $notes)
		{
			$this->success 		 = $success;
			$this->authorizationCode = $authorizationCode;
			$this->responseCode 	 = $responseCode;
			$this->notes 		 = $notes;
		}
	}

?>
