<?php
// Initialize a cURL session
$ch = curl_init();

// Set the URL
$url = "https://dataapi.moc.go.th/juristic?juristic_id=0735563005872";
curl_setopt($ch, CURLOPT_URL, $url);

// Return the transfer as a string instead of outputting it
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL session and store the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // Decode the JSON response
    $data = json_decode($response, true);

    // Print the response data
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// Close the cURL session
curl_close($ch);
?>
