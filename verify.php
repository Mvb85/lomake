<?php
if ($_POST) {
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $remoteIp = $_SERVER['REMOTE_ADDR'];
    $secretKey = "your_secret_key";

    // Verify the reCAPTCHA response
    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, "secret={$secretKey}&response={$recaptchaResponse}&remoteip={$remoteIp}");
    curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($verify);
    $responseData = json_decode($response);

    if ($responseData->success) {
        // reCAPTCHA validation succeeded; forward data to Salesforce
        $post_data = http_build_query($_POST);
        $salesforceUrl = "https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8"; // Replace with your Salesforce form action URL
        $opts = ['http' =>
            [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data
            ]
        ];
        $context = stream_context_create($opts);
        $result = file_get_contents($salesforceUrl, false, $context);
        // Redirect or notify the user upon success
        header('Location: thank_you_page.html'); // Adjust the redirection as needed
    } else {
        // Handle the failure of reCAPTCHA validation
        echo "reCAPTCHA validation failed. Please try again.";
    }
}
?>
