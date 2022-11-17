<?php


namespace core\helper;


class XMLHelper
{
    /**
     * 数组转xml
     * @param $arr
     * @return string
     */
    public static function arrayToXml($arr){
        $xml = "<xml>";
        foreach ($arr as $key=>$val){
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * 作用：将xml转为array
     * @param $xml
     * @return mixed
     */
    public static function xmlToArray($xml){
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }
    public static function toArrayToXml($arr){
        $xml = '';
        foreach ($arr as $key=>$val){
            if (is_int($key)){
                $xml.="<traveller>";
            }else {
                $xml .= "<" . $key . ">";
            }
            if (is_numeric($val)){
                $xml.=$val;
            }elseif(is_array($val)){
                $xml.=self::toArrayToXml($val);
            }else{
                $xml.=$val;
            }
            if (is_int($key)){
                $xml.="</traveller>";
            }else{
                $xml.="</".$key.">";
            }
        }
        // $xml.="</xml>";
        return $xml;
    }
}