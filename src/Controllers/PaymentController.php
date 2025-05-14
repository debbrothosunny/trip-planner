<?php

namespace App\Controllers;

use App\Models\Payment;
use App\Models\Accommodation;
use App\Models\Trip;
use App\Models\TripParticipant;
use Core\Database;
use PDO;
use PDOException;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\AmountBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\ItemBuilder;
use PaypalServerSdkLib\Models\ItemCategory;
use PaypalServerSdkLib\Models\RefundRequest;
use PaypalServerSdkLib\Models\Builders\ShippingDetailsBuilder;
use PaypalServerSdkLib\Models\Builders\ShippingNameBuilder;
use PaypalServerSdkLib\Request;
use PaypalServerSdkLib\Models\Builders\RefundRequestBuilder;
use PaypalServerSdkLib\Http\HttpResponse;

class PaymentController
{
    private $paymentModel;
    private $accommodationModel;
    private $tripModel;
    private $tripParticipantModel;
    private $apiContext;
    private $db;
    private $paypalClient;

    public function __construct()
    {
        $database = Database::getInstance();
        $this->db = $database->getConnection();

        $this->paymentModel = new Payment($this->db);
        $this->tripModel = new Trip($this->db);
        $this->tripParticipantModel = new TripParticipant($this->db);
        $this->accommodationModel = new Accommodation($this->db);

        session_start();

        $PAYPAL_CLIENT_ID = getenv("PAYPAL_CLIENT_ID");
        $PAYPAL_CLIENT_SECRET = getenv("PAYPAL_CLIENT_SECRET");

        $this->paypalClient = PaypalServerSdkClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init(
                    $PAYPAL_CLIENT_ID,
                    $PAYPAL_CLIENT_SECRET
                )
            )
            ->environment(Environment::SANDBOX)
            ->build();
    }



    private function handlePaypalResponse($response)
    {
        $jsonResponse = json_decode($response->getBody(), true);
        return [
            "jsonResponse" => $jsonResponse,
            "httpStatusCode" => $response->getStatusCode(),
        ];
    }


    private function createPaypalOrder(float $amount, string $description): array
    {
        $orderBody = [
            "body" => OrderRequestBuilder::init("CAPTURE", [
                PurchaseUnitRequestBuilder::init(
                    AmountWithBreakdownBuilder::init("USD", (string)$amount) // Use string for amount
                        ->breakdown(
                            AmountBreakdownBuilder::init()
                                ->itemTotal(
                                    MoneyBuilder::init("USD", (string)$amount)->build() // Use string
                                )
                                ->build()
                        )
                        ->build()
                )
                    ->items([ //This item should come from database
                        ItemBuilder::init(
                            "Item",  // Name
                            MoneyBuilder::init("USD", (string)$amount)->build(), // Use string
                            "1" // Quantity
                        )
                            ->description($description)
                            ->sku("ITEM_SKU") // unique
                            ->build(),
                    ])
                    ->build(),
            ])
                ->build(),
        ];

        $apiResponse = $this->paypalClient->getOrdersController()->createOrder($orderBody);
        return $this->handlePaypalResponse($apiResponse);
    }


    public function initiatePayment()
    {
        // Check if user is logged in and is a participant
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'participant') {
            header("Location: /"); // Redirect if not logged in or wrong role
            exit();
        }

        if (!isset($_POST['trip_id'])) {
            // Handle case where trip ID is missing
            $_SESSION['error_message'] = "Error: Trip ID not provided.";
            header("Location: /participant/trips"); // Redirect back to trips list
            exit();
        }

        $tripId = $_POST['trip_id'];
        $userId = $_SESSION['user_id'];

        // Fetch trip details to get the amount
        $trip = $this->tripModel->getTripById($tripId); // Implement this in your Trip model

        if (!$trip) {
            $_SESSION['error_message'] = "Error: Trip not found.";
            header("Location: /participant/trips");
            exit();
        }

        $amount = $trip['budget']; // Or a specific payment amount
        $currency = 'USD'; // Set your currency

        // 1. Create a payment record in your database (status 'pending', transaction_id is NULL initially)
        $paymentData = [
            'user_id' => $userId,
            'trip_id' => $tripId,
            'payment_gateway' => 'paypal', // Or your chosen gateway
            'transaction_id' => null, // Initially null
            'amount' => $amount,
            'currency' => $currency,
            'payment_status' => 'pending',
            'payer_id' => null,
            'payment_method' => 'paypal', // Make sure this line exists
        ];
        $localPaymentId = $this->paymentModel->createPayment($paymentData); // Get the ID of our local payment record

        if (!$localPaymentId) {
            $_SESSION['error_message'] = "Error: Could not create payment record.";
            header("Location: /participant/trips");
            exit();
        }
        error_log("initiatePayment - Session after setting local_payment_id: " . print_r($_SESSION, true));
        // 2.  Create PayPal Order
        try {
            $orderResponse = $this->createPaypalOrder($amount, "Payment for Trip ID: " . $tripId);
            if ($orderResponse['httpStatusCode'] != 201) { //check the status code
                throw new \Exception("Failed to create PayPal order: " . json_encode($orderResponse));
            }
            $paypalOrderId = $orderResponse['jsonResponse']['id'];

            //update the local payment
            $this->paymentModel->updatePaymentId($localPaymentId, $paypalOrderId);

            // Store relevant data in session for use in handlePaymentSuccess()
            $_SESSION['paypal_order_id'] = $paypalOrderId;
            $_SESSION['payment_amount'] = $amount;
            $_SESSION['payment_currency'] = $currency;
            $_SESSION['local_payment_id'] = $localPaymentId; //VERY IMPORTANT
            // return $paypalOrderId; // Pass the order ID back to the JavaScript
                header('Content-Type: application/json');
                echo json_encode(['orderId' => $paypalOrderId, 'localPaymentId' => $localPaymentId]); // Return as JSON
                exit();

        } catch (\Exception $e) {
            error_log("PayPal Order Creation Error: " . $e->getMessage());
            $_SESSION['error_message'] = "Error initiating payment with PayPal: " . $e->getMessage();
            $this->paymentModel->updatePaymentStatus($localPaymentId, 'failed');
            header("Location: /participant/trips");
            exit();
        }
    }

    public function handlePaymentSuccess()
    {
        echo "handlePaymentSuccess: Function entry\n";
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "handlePaymentSuccess - Method not POST\n";
            header("HTTP/1.0 405 Method Not Allowed");
            echo "Method Not Allowed";
            exit();
        }
    
        // Get and decode JSON data
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);
        echo "handlePaymentSuccess - Raw received data: " . $rawData . "\n";
    
        if (!$data || !is_array($data)) {
            echo "handlePaymentSuccess - Invalid JSON data received.\n";
            $this->sendErrorResponse("Invalid data received.");
            return;
        }
    
        // Sanitize and validate inputs
        $tripId = isset($data['trip_id']) ? intval($data['trip_id']) : 0;
        $amount = isset($data['amount']) ? floatval($data['amount']) : 0.00;
        $paypalCaptureId = isset($data['transaction_id']) ? trim($data['transaction_id']) : '';
        $payerId = isset($data['payer_id']) ? trim($data['payer_id']) : '';
        $paymentMethod = isset($data['payment_method']) ? trim($data['payment_method']) : '';
    
        if (!$tripId || !$amount || !$paypalCaptureId || !$payerId || !$paymentMethod) {
            echo "handlePaymentSuccess - Missing required fields.\n";
            $this->sendErrorResponse("Required payment data is incomplete.");
            return;
        }
    
        echo "handlePaymentSuccess - Trip ID: $tripId, Amount: $amount, Capture ID: $paypalCaptureId, Payer ID: $payerId, Method: $paymentMethod\n";
    
        // Retrieve trip details to verify expected amount (optional but recommended)
        $trip = $this->tripModel->getTripById($tripId);
        if (!$trip) {
            echo "handlePaymentSuccess - Trip not found.\n";
            $this->sendErrorResponse("Trip not found.");
            return;
        }
    
        // OPTIONAL: Verify amount matches trip budget
        if (floatval($trip['budget']) != $amount) {
            echo "handlePaymentSuccess - Amount mismatch. Expected: {$trip['budget']}, Received: $amount\n";
            $this->sendErrorResponse("Amount mismatch detected.");
            return;
        }
    
        $userId = $_SESSION['user_id']; // Or get from auth context
        $payment = null;
    
        // Check if payment record exists in session
        if (isset($_SESSION['local_payment_id'])) {
            $localPaymentId = intval($_SESSION['local_payment_id']);
            $payment = $this->paymentModel->getPaymentById($localPaymentId);
            echo "handlePaymentSuccess - Payment record found by ID: " . print_r($payment, true) . "\n";
            unset($_SESSION['local_payment_id']); // Clean up session
        }
    
        if ($payment) {
            // Prepare update data
            $updateData = [
                'payment_status' => 0, // 0 = completed
                'payer_id' => $payerId,
                'transaction_id' => $paypalCaptureId,
                'amount' => $amount,
                'currency' => 'USD',
                'payment_method' => $paymentMethod,
                'payment_date' => date('Y-m-d H:i:s')
            ];
            echo "handlePaymentSuccess - Update data: " . print_r($updateData, true) . "\n";
    
            $updated = $this->paymentModel->updatePaymentDetails($payment['id'], $updateData);
            echo "handlePaymentSuccess - Update result: " . ($updated ? 'true' : 'false') . "\n";
    
            if ($updated) {
                $this->sendSuccessResponse("Payment details updated successfully.");
            } else {
                $this->sendErrorResponse("Failed to update payment details.");
            }
    
        } else {
            echo "handlePaymentSuccess - No local payment record found. Inserting new payment.\n";
    
            // Insert new payment record
            $newPaymentData = [
                'user_id' => $userId,
                'trip_id' => $tripId,
                'payment_gateway' => 'paypal',
                'transaction_id' => $paypalCaptureId,
                'amount' => $amount,
                'currency' => 'USD',
                'payment_status' => 1, // completed
                'payment_date' => date('Y-m-d H:i:s'),
                'payer_id' => $payerId,
                'payment_method' => $paymentMethod
            ];
    
            $insertedId = $this->paymentModel->insertPayment($newPaymentData);
            echo "handlePaymentSuccess - New payment inserted ID: $insertedId\n";
    
            if ($insertedId) {
                $this->sendSuccessResponse("Payment recorded successfully.");
            } else {
                $this->sendErrorResponse("Failed to save new payment record.");
            }
        }
    
        echo "handlePaymentSuccess: Function finished\n";
    }
    
    

    public function cancelPaidTrip()
    {
        $tripId = $_POST['trip_id'] ?? null;
        $userId = $_SESSION['user_id'] ?? null; // Assuming user ID is in session

        if (!$tripId || !$userId) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Invalid request.'];
            header('Location: /participant/trips');
            exit();
        }

        $participant = $this->tripParticipantModel->getParticipantByTripId($tripId, $userId);
        if (!$participant || $participant['trip_status'] !== 'accepted') {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'You are not an accepted participant for this trip.'];
            header('Location: /participant/trips');
            exit();
        }

        $payment = $this->paymentModel->getPaymentByUserAndTrip($userId, $tripId);
        if (!$payment || $payment['payment_status'] != 0) { // Assuming 0 means 'completed'
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'No accepted payment found for this trip.'];
            header('Location: /participant/trips');
            exit();
        }

        $originalAmount = $payment['amount_paid']; // Ensure this column exists and is populated
        $cancellationFee = $originalAmount * 0.05;
        $refundAmount = $originalAmount - $cancellationFee;

        $paymentUpdateData = [
            'payment_status' => 'cancelled_with_fee',
            'refund_amount' => $refundAmount,
        ];

        // Update payment status and record refund details
        $updatedPayment = $this->paymentModel->update($payment['id'], $paymentUpdateData);

        // Update participant trip status
        $updatedParticipant = $this->tripParticipantModel->updateStatus($userId, $tripId, 'cancelled'); // Corrected line

        if ($updatedPayment && $updatedParticipant) {
            // --- OPTIONAL: Initiate PayPal Refund ---
            if ($payment['transaction_id']) {
                $refundRequestBuilder = \PaypalServerSdkLib\Models\Builders\RefundRequestBuilder::init();
                $amount = new \PaypalServerSdkLib\Models\Money(
                    'USD', // Currency Code
                    number_format($refundAmount, 2, '.', '') // Value
                );
    
                $refundRequestBuilder->amount($amount);
                $refundRequest = $refundRequestBuilder->build();
    
                try {
                    $request = new \PaypalServerSdkLib\Models\RefundRequest($payment['transaction_id']);
                    $request->body = $refundRequest;
                    $response = $this->paypalClient->execute($request);

                    if ($response->statusCode === 202) {
                        // Refund initiated successfully in PayPal
                        $_SESSION['message'] = ['type' => 'success', 'text' => "Your trip has been cancelled. A 5% fee of $" . number_format($cancellationFee, 2) . " was deducted. Refund of $" . number_format($refundAmount, 2) . " initiated."];
                    } else {
                        // Refund initiation failed in PayPal, but local status updated
                        $_SESSION['message'] = ['type' => 'warning', 'text' => "Your trip has been cancelled. A 5% fee of $" . number_format($cancellationFee, 2) . " was deducted. Refund of $" . number_format($refundAmount, 2) . " will be processed manually. PayPal refund initiation failed: " . $response->statusCode . " " . json_encode($response->result)];
                        // You might want to log this failure for manual processing
                    }
                } catch (\Exception $e) {
                    // Error during PayPal refund initiation
                    $_SESSION['message'] = ['type' => 'warning', 'text' => "Your trip has been cancelled. A 5% fee of $" . number_format($cancellationFee, 2) . " was deducted. Refund of $" . number_format($refundAmount, 2) . " will be processed manually. Error initiating PayPal refund: " . $e->getMessage()];
                    // Log the error
                }
            } else {
                $_SESSION['message'] = ['type' => 'success', 'text' => "Your trip has been cancelled. A 5% fee of $" . number_format($cancellationFee, 2) . " was deducted. You may be eligible for a refund of $" . number_format($refundAmount, 2) . "."];
            }
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'An error occurred while processing your cancellation.'];
        }

        header('Location: /participant/trips');
        exit();
    }

    




    
        private function sendSuccessResponse($message)
    {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => $message]);
        exit();
    }

    private function sendErrorResponse($message)
    {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $message]);
        exit();
    }
    


    
    // You would also have a handlePaymentCancel() function here
    public function handlePaymentCancel()
    {
        $_SESSION['cancel_message'] = "Your payment was cancelled.";
        header('Location: /payment/cancel'); // Adjust your cancel route
        exit();
    }








    // user Payment for hotel booking 
    public function initiateAccommodationPayment() {

        var_dump("Function is being called");

        // Check if user is logged in

        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /"); // Redirect if not logged in
            exit();
        }
    
        
        if (!isset($_POST['accommodation_id']) || !isset($_POST['amount'])) {
            $_SESSION['error_message'] = "Error: Accommodation ID or amount not provided.";
            header("Location: /user/accommodation"); // Redirect back to accommodations list
            exit();
        }
    
        // Check if trip_id is provided
     
        if (!isset($_POST['trip_id'])) {
            // Handle the case where trip_id is missing for accommodation
            $_SESSION['error_message'] = "Error: Trip ID not provided for accommodation payment.";
            header("Location: /user/accommodation"); // Or a more appropriate redirect
            exit();
        }
    
        $accommodationId = $_POST['accommodation_id'];
        $amount = $_POST['amount'];
        $userId = $_SESSION['user_id'];
        $tripId = $_POST['trip_id']; // Get the trip ID from the POST request
        $currency = 'USD'; // Set your currency
    
        // 1. Fetch accommodation details to ensure it exists and belongs to the user (optional security check)
        $accommodation = $this->accommodationModel->findById($accommodationId); // Implement this in your Accommodation model
    
        if (!$accommodation || $accommodation['user_id'] !== $userId) {
            $_SESSION['error_message'] = "Error: Accommodation not found or does not belong to you.";
            header("Location: /user/accommodation");
            exit();
        }
    
        // 2. Create a payment record in your database for the accommodation
        $paymentData = [
            'user_id' => $userId,
            'trip_id' => $tripId, // Use the trip ID from the POST request
            'payment_gateway' => 'paypal',
            'payment_id' => null,
            'amount' => $amount,
            'currency' => $currency,
            'payment_status' => 'pending',
            'payer_id' => null,
            'payment_method' => 'paypal',
        ];
        
        var_dump($paymentData);
        $localPaymentId = $this->paymentModel->createPayment($paymentData);
        var_dump($localPaymentId);

        
    
        if (!$localPaymentId) {
            $_SESSION['error_message'] = "Error: Could not create payment record.";
            header("Location: /user/accommodation");
            exit();
        }
    
        // 3. Set up PayPal payment request (similar to your existing logic)
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");
    
        $amountObj = new Amount();
        $amountObj->setCurrency($currency)
            ->setTotal($amount);
    
        $transaction = new Transaction();
        $transaction->setAmount($amountObj)
            ->setDescription("Payment for Accommodation Booking ID: " . $accommodationId . " (Trip ID: " . $tripId . ")");
    
        $redirectUrls = new RedirectUrls();
        $returnUrl = "http://localhost:8000/payment/success-accommodation?accommodation_id=" . $accommodationId . "&payment_id=" . $localPaymentId; // Adjust URL
        $cancelUrl = "http://localhost:8000/payment/cancel-accommodation?accommodation_id=" . $accommodationId; // Adjust URL
        $redirectUrls->setReturnUrl($returnUrl)
            ->setCancelUrl($cancelUrl);
    
        $payment = new PayPalPayment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions([$transaction]);
    
        // 4. Create the PayPal payment and get the approval URL
        try {
            $payment->create($this->apiContext);
            $approvalUrl = $payment->getApprovalLink();
            $paypalPaymentId = $payment->getId();
    
            if ($paypalPaymentId) {
                $this->paymentModel->updatePaymentId($localPaymentId, $paypalPaymentId);
            }
    
            header("Location: " . $approvalUrl);
            exit();
    
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            error_log("PayPal API Error (Accommodation Payment): " . $ex->getMessage() . "\n" . $ex->getData());
            $_SESSION['error_message'] = "Error initiating payment with PayPal. Please try again.";
            $this->paymentModel->updatePaymentStatus($localPaymentId, 'failed');
            header("Location: /user/accommodation");
            exit();
        }
    }


    public function getPaymentDetails(): void
    {
        error_log("getPaymentDetails method called! Accommodation ID: " . ($_GET['accommodation_id'] ?? 'null'));
        if (isset($_GET['accommodation_id']) && is_numeric($_GET['accommodation_id'])) {
            $accommodationId = (int) $_GET['accommodation_id'];
            $paymentDetails = $this->accommodationModel->getPaymentDetailsByAccommodationId($accommodationId);
            error_log("Payment Details retrieved: " . print_r($paymentDetails, true));
            header('Content-Type: application/json');
            echo json_encode($paymentDetails);
        } else {
            header('Content-Type: application/json');
            echo json_encode(null); // Or an error message
        }
    }



    public function updatePaymentStatus(): void
    {
        if (isset($_POST['accommodation_id']) && is_numeric($_POST['accommodation_id'])) {
            $accommodationId = (int) $_POST['accommodation_id'];

            // Update the payment status in your payments table (assuming you have a payment_status column)
            $updated = $this->paymentModel->markAsPaidByTripId(
                $this->accommodationModel->getTripIdByAccommodationId($accommodationId)
            );

            if ($updated) {
                // Optionally, update the accommodation status as well
                // $this->accommodationModel->confirmAccommodation($accommodationId);

                $_SESSION['sweetalert'] = [
                    'title' => 'Payment Updated',
                    'text' => 'Payment status has been marked as paid.',
                    'icon' => 'success'
                ];
            } else {
                $_SESSION['error'] = 'Failed to update payment status.';
            }
        } else {
            $_SESSION['error'] = 'Invalid accommodation ID.';
        }
        header('Location: /admin/bookings'); // Redirect back to the bookings page
        exit();
    }


    
    

}