<?php

namespace App\Service;


class LieuService
{
    public function getLatLng($address, $city, $postalCode) {
        $address = urlencode($address);
        $city = urlencode($city);
        $postalCode = urlencode($postalCode);
        $url = "https://nominatim.openstreetmap.org/search?q=$address+$city+$postalCode&format=json";
    
        $options = [
            'http' => [
                'header' => "User-Agent: ENIBDE/1.0\r\n"
            ]
        ];
        $context = stream_context_create($options);
        
        $response = file_get_contents($url, false, $context);
        
        if ($response === false) 
           return [
            'lat' => 48.8588443,
            'lng' => 2.2943506
           ];
    
        $data = json_decode($response, true);
        
        if (empty($data)) 
            return [
            'lat' => 48.8588443,
            'lng' => 2.2943506
           ];
    
        return [
            'lat' => $data[0]['lat'],
            'lng' => $data[0]['lon']
        ];
    }
}