<?php 

namespace App\Helpers;

use DateTime;

class Helpers 
{

    public function __construct(){}

    /**
     * Skip accents in string
     * @param string $str
     * @param string $charset
     * @return string
    */
    public function skipAccents(string $str,string $charset='utf-8' ):string 
    {
        $str    = trim($str);
        $str    = htmlentities( $str, ENT_NOQUOTES, $charset );
        
        $str    = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str );
        $str    = preg_replace( '#&([A-za-z]{2})(?:lig);#', '\1', $str );
        $str    = preg_replace( '#&[^;]+;#', '', $str );
        $str    = preg_replace('/[^A-Za-z0-9\-]/', ' ', $str);
        
        return $str;
    }
    
    /**
     * Check if the password match with pattern
     * @param string $pass
     * @return boolean
     */
    public function passValid(string $pass):bool
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]){8,}/', $pass) ? true : false;
    }
    
    /**
     * Generate a token
     * @param integer $length
     * @return string
     */
    public function generateToken(int $length):string
    {
        $char_to_shuffle =  'azertyuiopqsdfghjklwxcvbnAZERTYUIOPQSDFGHJKLLMWXCVBN1234567890';
        return substr( str_shuffle($char_to_shuffle) , 0 , $length) . (new DateTime)->format('YmwdHsiu');
    }

    /**
     * Transform string in slug
     * @param string $str
     * @return string
    */
    public function generateSlug(string ...$vars) 
    {
        $str = trim( $this->putBefore(' ', func_get_args() ));
        $str = trim($this->skipAccents($str));
        return strtolower(preg_replace('/[^A-Za-z0-9\-]/', '-', $str ) );
    }
 
    /**
     * Put needle before string
     * @param string $separator
     * @param array $array
     * @return string
    */
    public function putBefore(string $separator, array $array):string
    {
        $str = "";
        if ( is_array($array) )
        {
            foreach($array as $v):
                $str .= "{$separator}{$v}";
            endforeach;
        }
        return $str;
    }

    /**
     * Put needle in a string
     * @param string $separator
     * @param array $array
     * @return string
    */
    public function putBetween(string $separator, array $array): string
    {
        $str = "";
        if ( is_array($array) )
        {
            foreach($array as $k => $v):
                $str .= $k === 0 ? $v : " {$separator} {$v}";
            endforeach;
        }
        return $str;
    }

    
    /**
     * now
     * @return string
     */
    public function now():string 
    {
        return (new DateTime('now'))->format('Y-m-d H:i:s');
    }
    
    /**
     * setJsonMessage
     * @param  string $type
     * @param  string $message
     * @return array
     */
    public function setJsonMessage(string $message, string $type = 'danger') :array
    {
        return ["type" => $type, "content" => $message];
    } 
    
    /**
     * isFilled
     * @param  $values
     * @return bool
     */
    public function isFilled($values):bool
    {
        foreach ( $values as $value ) {
            if ($value === null || $value === "") return false;
        }
        return true;
    }


}