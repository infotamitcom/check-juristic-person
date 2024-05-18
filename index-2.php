<?php 
    $curl = curl_init();     
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.egov.go.th/ws/dbd/juristic/v4/profile/information?JuristicID=0745566012519",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "api-key: <EV0AMxgvdoZgtUZjJ1KHGDghsDhqcS7F>"
        )
    ));     
    $response = curl_exec($curl);
    $err = curl_error($curl);     
    curl_close($curl);     
    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo $response;
    }
?>