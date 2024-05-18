<?php     
    date_default_timezone_set("Asia/Bangkok");
    $start_time = microtime(true);
    $dateTimeNow = date("Y-m-d H:i:s");
    header("Content-Type: application/json; charset=UTF-8");
    $error = array();
    $data = array();
    $get_data = array();
    $status = 400;
    $version = '1';
    $json = NULL;
    $juristic_id = $_GET['juristic_id'] ?? NULL;
    if(!$juristic_id){
        $error['juristic_id'] = 'not found';
    }
    $get_data['juristic_id'] = $juristic_id;
    
    //
    if(empty($error)){
        //ไม่มี error ทำการหาข้อมูลมาได้
        //$url = "https://dataapi.moc.go.th/juristic?juristic_id=".$juristic_id;
        $url = "https://openapi.dbd.go.th/api/v1/juristic_person/".$juristic_id;
        $get_data['OrgURL1'] = $url;
        $max_attempts = 1; // จำนวนครั้งสูงสุดที่ทำงานได้
        $attempts = 0; // ตัวแปรเก็บจำนวนครั้งที่ทำงานได้
        while (true) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            $html = curl_exec($ch);
            if ($html) {
                curl_close($ch);
                break;
            } else {
                $attempts++;
                if ($attempts >= $max_attempts) {
                    curl_close($ch);
                    break;
                } else {
                    curl_close($ch);
                    sleep(5);
                }
            }
        }
        $json = json_decode($html , JSON_UNESCAPED_UNICODE);
        if(!empty($json['status']) && $json['status']['code'] == '1000'){
            //error            
            $status = 200;            
            //วันที่จัดตั้งบริษัทให้ใช้เป็น คศ-เดือน-วัน
            $OrganizationJuristicRegisterDate = substr($json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicRegisterDate'],0,4);
            $OrganizationJuristicRegisterDate.= '-'.substr($json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicRegisterDate'],4,2);
            $OrganizationJuristicRegisterDate.= '-'.substr($json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicRegisterDate'],6,2);
            $pathfile = 'data-address/geography.json'; 
            $jsonAddress = file_get_contents($pathfile);
            $jsonAddress = json_decode($jsonAddress,true); 
            $CitySubDivisionTextTH=$json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicAddress']['cr:AddressType']['cd:CitySubDivision']['cr:CitySubDivisionTextTH'];
            $key = array_search($CitySubDivisionTextTH, array_column($jsonAddress, 'subdistrictNameTh'));
            $CitySubDivisionTextEN = $jsonAddress[$key]['subdistrictNameEn'];

            $CityTextTH = $jsonAddress[$key]['districtNameTh'];
            $CityTextEN = $jsonAddress[$key]['districtNameEn'];

            $CountrySubDivisionTextTH = $jsonAddress[$key]['provinceNameTh'];
            $CountrySubDivisionTextEN = $jsonAddress[$key]['provinceNameEn'];
            
            $postalCode = $jsonAddress[$key]['postalCode'];

            $fullAddressTH = "เลขที่ ".$json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicAddress']['cr:AddressType']['cd:Address'];
            $fullAddressTH.= " ตำบล/แขวง ".$CitySubDivisionTextTH;
            $fullAddressTH.= " อำเภอ/เขต ".$CityTextTH;
            $fullAddressTH.= " จังหวัด ".$CountrySubDivisionTextTH;
            $fullAddressTH.= " ".$postalCode; 
            $fullAddressEN = "".$CitySubDivisionTextEN." ".$CityTextEN." ".$CountrySubDivisionTextEN." ".$postalCode." THAILAND"; 
            $OrganizationJuristicAddress = array(
                'fullAddressTH' => $fullAddressTH,
                'fullAddressEN' => strtoupper($fullAddressEN),
                'AddressTH' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicAddress']['cr:AddressType']['cd:Address'],
                'AddressEN' => '',
                'BuildingTH' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicAddress']['cr:AddressType']['cd:Building'],
                'BuildingEN' => '',
                'RoomNoTH' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicAddress']['cr:AddressType']['cd:RoomNo'],
                'RoomNoEN' => '',
                'FloorTH' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicAddress']['cr:AddressType']['cd:Floor'],
                'FloorEN' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicAddress']['cr:AddressType']['cd:Floor'],
                'AddressNo' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicAddress']['cr:AddressType']['cd:AddressNo'],
                'Moo' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicAddress']['cr:AddressType']['cd:Moo'],
                'Yaek' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicAddress']['cr:AddressType']['cd:Yaek'],
                'Soi' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicAddress']['cr:AddressType']['cd:Soi'],
                'Trok' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicAddress']['cr:AddressType']['cd:Trok'],
                'Village' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicAddress']['cr:AddressType']['cd:Village'],
                'Road' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicAddress']['cr:AddressType']['cd:Road'],

                'CitySubDivisionTextTH' => $CitySubDivisionTextTH,
                'CitySubDivisionTextEN' => $CitySubDivisionTextEN,
                'CityTextTH' => $CityTextTH,
                'CityTextEN' => $CityTextEN,
                'CountrySubDivisionTextTH' => $CountrySubDivisionTextTH,
                'CountrySubDivisionTextEN' => $CountrySubDivisionTextEN,
                'postalCode' => $postalCode,
                
            );
            $data = array(array(
                'OrganizationJuristicNameTH' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicNameTH'],
                'OrganizationJuristicNameEN' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicNameEN'],
                'OrganizationJuristicType' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicType'],
                'OrganizationJuristicRegisterDate' => $OrganizationJuristicRegisterDate,
                'OrganizationJuristicStatus' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicStatus'],
                'JuristicObjectiveTextTH' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicObjective']['td:JuristicObjective']['td:JuristicObjectiveTextTH'],
                'JuristicObjectiveTextEN' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicObjective']['td:JuristicObjective']['td:JuristicObjectiveTextEN'],
                'OrganizationJuristicBranchName' => $json['data'][0]['cd:OrganizationJuristicPerson']['cd:OrganizationJuristicBranchName'],
                'OrganizationJuristicAddress' => $OrganizationJuristicAddress,
            ));
        }else{
            //API v2
            $version = '2';
            $url = "https://dataapi.moc.go.th/juristic?juristic_id=".$juristic_id;
            $get_data['OrgURL2'] = $url;
            $max_attempts = 1; // จำนวนครั้งสูงสุดที่ทำงานได้
            $attempts = 0; // ตัวแปรเก็บจำนวนครั้งที่ทำงานได้
            while (true) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_ENCODING, '');
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
                curl_setopt($ch, CURLOPT_TIMEOUT, 7);
                $html = curl_exec($ch);
                if ($html) {
                    curl_close($ch);
                    break;
                } else {
                    $attempts++;
                    if ($attempts >= $max_attempts) {
                        curl_close($ch);
                        break;
                    } else {
                        curl_close($ch);
                        sleep(5);
                    }
                }
            }
            $json = json_decode($html , JSON_UNESCAPED_UNICODE);
            if( !empty($json['juristicID']) ){
                $status = 200;
                //วันที่จัดตั้งบริษัทให้ใช้เป็น คศ-เดือน-วัน
                $OrganizationJuristicRegisterDate = ((substr($json['registerDate'],0,4)*1)-543);
                $OrganizationJuristicRegisterDate.= '-'.substr($json['registerDate'],4,2);
                $OrganizationJuristicRegisterDate.= '-'.substr($json['registerDate'],6,2);
                $pathfile = 'data-address/geography.json'; 
                $jsonAddress = file_get_contents($pathfile);
                $jsonAddress = json_decode($jsonAddress,true); 
                $CitySubDivisionTextTH = $json['addressDetail']['subDistrict'];
                $key = array_search($CitySubDivisionTextTH, array_column($jsonAddress, 'subdistrictNameTh'));
                $CitySubDivisionTextEN = $jsonAddress[$key]['subdistrictNameEn'];

                $CityTextTH = $jsonAddress[$key]['districtNameTh'];
                $CityTextEN = $jsonAddress[$key]['districtNameEn'];

                $CountrySubDivisionTextTH = $jsonAddress[$key]['provinceNameTh'];
                $CountrySubDivisionTextEN = $jsonAddress[$key]['provinceNameEn'];
                
                $postalCode = $jsonAddress[$key]['postalCode'];

                $fullAddressTH = "";
                if($json['addressDetail']['houseNumber']){
                    $fullAddressTH.= "เลขที่ ".$json['addressDetail']['houseNumber']." ";
                }
                if($json['addressDetail']['buildingName']){
                    $fullAddressTH.= "อาคาร".$json['addressDetail']['buildingName']." ";
                }
                if($json['addressDetail']['floor']){
                    $fullAddressTH.= "ชั้น ".$json['addressDetail']['floor']." ";
                }
                if($json['addressDetail']['soi']){
                    $fullAddressTH.= "ซอย".$json['addressDetail']['soi']." ";
                }
                if($json['addressDetail']['street']){
                    $fullAddressTH.= "ถนน".$json['addressDetail']['street']." ";
                }
                if($json['addressDetail']['villageName']){
                    $fullAddressTH.= "หมู่บ้าน ".$json['addressDetail']['villageName']." ";
                }
                if($json['addressDetail']['moo']){
                    $fullAddressTH.= "หมู่ที่ ".$json['addressDetail']['moo']." ";
                }                
                $fullAddressTH.= "ตำบล/แขวง ".$CitySubDivisionTextTH." ";
                $fullAddressTH.= "อำเภอ/เขต ".$CityTextTH." ";
                $fullAddressTH.= "จังหวัด".$CountrySubDivisionTextTH;
                $fullAddressTH.= " ".$postalCode; 
                $fullAddressEN = "";

                $fullAddressEN.= $json['addressDetail']['houseNumber']." ".$CitySubDivisionTextEN." Sub-district ".$CityTextEN." District ".$CountrySubDivisionTextEN." ".$postalCode." THAILAND"; 
                $OrganizationJuristicAddress = array(
                    'fullAddressTH' => $fullAddressTH,
                    'fullAddressEN' => strtoupper($fullAddressEN),
                    'AddressTH' => $json['addressDetail']['houseNumber'],
                    'AddressEN' => $json['addressDetail']['houseNumber'],
                    'BuildingTH' => $json['addressDetail']['buildingName'],
                    'BuildingEN' => '',
                    'RoomNoTH' => $json['addressDetail']['roomNo'],
                    'RoomNoEN' => '',
                    'FloorTH' => $json['addressDetail']['floor'],
                    'FloorEN' => '',
                    'AddressNo' => '',
                    'Moo' => $json['addressDetail']['moo'],
                    'Yaek' => '',
                    'Soi' => $json['addressDetail']['soi'],
                    'Trok' => '',
                    'Village' => $json['addressDetail']['villageName'],
                    'Road' => $json['addressDetail']['street'],

                    'CitySubDivisionTextTH' => $CitySubDivisionTextTH,
                    'CitySubDivisionTextEN' => $CitySubDivisionTextEN,
                    'CityTextTH' => $CityTextTH,
                    'CityTextEN' => $CityTextEN,
                    'CountrySubDivisionTextTH' => $CountrySubDivisionTextTH,
                    'CountrySubDivisionTextEN' => $CountrySubDivisionTextEN,
                    'postalCode' => $postalCode,
                    
                );
                $data = array(array(
                    'OrganizationJuristicNameTH' => $json['juristicNameTH'],
                    'OrganizationJuristicNameEN' => $json['juristicNameEN'],
                    'OrganizationJuristicType' => $json['juristicType'],
                    'OrganizationJuristicRegisterDate' => $OrganizationJuristicRegisterDate,
                    'OrganizationJuristicStatus' => $json['juristicStatus'],
                    'JuristicObjectiveTextTH' => $json['standardObjectiveDetail']['objectiveDescription'],
                    'JuristicObjectiveTextEN' => '',
                    'OrganizationJuristicBranchName' => $json['addressDetail']['addressName'],
                    'OrganizationJuristicAddress' => $OrganizationJuristicAddress,
                ));
            }else{
                $error['juristic_id'] = 'not found';
                $status = 204;
            }
        }
    }    
    $url = "https://api.egov.go.th/ws/dbd/juristic/v4/profile/information?JuristicID=".$juristic_id;
    $get_data['OrgURL3'] = $url;
    $end_time = microtime(true);
    $execution_time=number_format(($end_time-$start_time),8,'.','');
    $arrayData = array(
        'status' => $status,
        'version' => $version,
        'execution_time' => $execution_time.' seconds',
        'get_data' => $get_data,
        'data' => $data,
        'error' => $error,
    );
    echo json_encode($arrayData);
?>