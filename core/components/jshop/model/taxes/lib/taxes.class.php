<?php
/** 
 * @author SmallDog 
 * @contact dustin@smalldo.gs 
 * @created 01-27-2011 
 * @updated 01-11-2013 
 * 
 * destinationTax class 
 * Developed to use the Washington State Department of Revenue's tax API 
 * to find the correct amount of tax owed at a specific address 
 * 
 * --> Exmple Use Use 
 *      $dor = new destinationTax; 
 *      $tax = $dor->get_tax("123 Main Street", "Kirkland", "98033") 
 *      echo $tax->attributes()->rate; 
 * 
 * --> Returns 
 */  
  
class destinationTax  
{  
  private $dor_url = "http://dor.wa.gov/";  
  
  function build_url($addr, $city, $zip) {  
    $url = $this->dor_url ."AddressRates.aspx?output=xml&addr=". urlencode($addr) ."&city=". urlencode($city) ."&zip=". urlencode($zip);  
    return $url;  
  }  
  
  function get_tax($addr, $city, $zip)  
  {  
    $request = $this->build_url($addr, $city, $zip);  
    $response = $this->_make_request($request);  
  
    return $response;  
  }  
  
  private function _make_request($url)  
  {  
    if($xml = simplexml_load_file($url))  
    {  
      switch($xml->attributes()->code)  
      {  
        case 0:  
          // Code 0 means address was perfect  
          break;  
        case 1:  
          $xml->msg = "Warning: The address was not found, but the ZIP+4 was located.";  
          break;  
        case 2:  
          $xml->msg = "Warning: Neither the address or ZIP+4 was found, but  the 5-digit ZIP was located.";  
          break;  
        case 3:  
          $xml->msg = "Error: The address, ZIP+4, and ZIP could not be found.";  
          break;  
        case 4:  
          $xml->msg = "Error: Invalid arguements.";  
          break;  
        case 5:  
          $xml->msg = "Error: Internal error.";  
      }  
  
      $xml->attributes()->rate = (float) $xml->attributes()->rate;  
    }  
    else  
      $xml = "Error: Could not load XML.<br>". $url;  
  
    return  $xml;  
  }  
}