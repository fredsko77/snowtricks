<?php

namespace App\Helpers;

use DateTime;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Helpers
{

    public function __construct()
    {}

    /**
     * Skip accents in string
     * @param string $str
     * @param string $charset
     * @return string
     */
    public function skipAccents(string $str, string $charset = 'utf-8'): string
    {
        $str = trim($str);
        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);
        $str = preg_replace('/[^A-Za-z0-9\-]/', ' ', $str);

        return $str;
    }

    /**
     * Check if the password match with pattern
     * @param string $pass
     * @return boolean
     */
    public function passValid(string $pass): bool
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]){8,}/', $pass) ? true : false;
    }

    /**
     * Generate a token
     * @param integer $length
     * @return string
     */
    public function generateToken(int $length): string
    {
        $char_to_shuffle = 'azertyuiopqsdfghjklwxcvbnAZERTYUIOPQSDFGHJKLLMWXCVBN1234567890';
        return substr(str_shuffle($char_to_shuffle), 0, $length) . (new DateTime)->format('YmwdHsiu');
    }

    /**
     * Transform string in slug
     * @param string $str
     * @return string
     */
    public function generateSlug(?string...$vars)
    {
        $str = trim($this->putBefore(' ', func_get_args()));
        $str = trim($this->skipAccents($str));
        return strtolower(preg_replace('/[^A-Za-z0-9\-]/', '-', $str));
    }

    /**
     * Put needle before string
     * @param string $separator
     * @param array $array
     * @return string
     */
    public function putBefore(string $separator, array $array): string
    {
        $str = "";
        if (is_array($array)) {
            foreach ($array as $v):
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
        if (is_array($array)) {
            foreach ($array as $k => $v):
                $str .= $k === 0 ? $v : " {$separator} {$v}";
            endforeach;
        }
        return $str;
    }

    /**
     * now
     * @return string
     */
    public function now(): DateTime
    {
        return new DateTime('now');
    }

    /**
     * setJsonMessage
     * @param  string $type
     * @param  string $message
     * @return array
     */
    public function setJsonMessage(string $message, string $type = 'danger'): array
    {
        return ["type" => $type, "content" => $message];
    }

    /**
     * isFilled
     * @param  $values
     * @return bool
     */
    public function isFilled($values): bool
    {
        foreach ($values as $value) {
            if ($value === null || $value === "") {
                return false;
            }

        }
        return true;
    }

    /**
     * getMethod
     * @param  string $str
     * @return string
     */
    public function getMethod(string $str): string
    {
        $needle = '_';
        $method = "";
        if (preg_match("#{$needle}#", $str)) {
            $array = preg_split("#{$needle}#", $str);
            if (is_array($array)) {
                foreach ($array as $v) {
                    $method .= ucfirst($v);
                }
            }
            return $method;
        }
        return ucfirst($str);
    }

    /**
     * jsonResponse
     *
     * @param  array $data
     * @param  int $status
     * @return JsonResponse
     */
    public function jsonResponse(array $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse($data, $status);
    }

    /**
     * Encrypt the password
     * @param string $password
     * @return string
     */
    public function encodePassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2I);
    }

    /**
     * @param string $class
     *
     * @return array
     */
    public function getProperties(string $class): array
    {

        $reflect = new ReflectionClass($class);

        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

        $properties = [];

        foreach ($props as $prop) {
            array_push($properties, $prop->getName());
        }

        return $properties;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function videoFormatURL(string $url = ''): string
    {
        $parse_url = parse_url($url);

        if ($parse_url['host'] === 'youtu.be') {
            $url = str_replace($parse_url['host'], 'www.youtube.com', $url);
        }

        if ($parse_url['host'] === 'dai.ly') {
            $url = str_replace($parse_url['host'], 'www.dailymotion.com', $url);
        }

        if ($parse_url['host'] === 'vimeo.com') {
            $url = str_replace($parse_url['host'], 'player.vimeo.com', $url);
            $url = str_replace($parse_url['path'], "/video{$parse_url['path']}", $url);
        }

        if (strpos($url, 'youtube') && !strpos($parse_url['path'], '/embed')) {
            $url = str_replace($parse_url['path'], "/embed{$parse_url['path']}", $url);
        }

        if (strpos($url, 'dailymotion') && strpos($url, '/video')) {
            $url = str_replace($parse_url['path'], "/embed{$parse_url['path']}", $url);
        }

        if (strpos($url, 'dailymotion') && !strpos($url, '/embed/video')) {
            $url = str_replace($parse_url['path'], "/embed/video{$parse_url['path']}", $url);
        }

        return $url;
    }

    /**
     * @param array $elements
     * @param int $items_per_page
     * @return array
     */
    public function pagination(array $elements = [], int $items_per_page = 10, int $page = 0): array
    {
        $nb_page = count($elements) / $items_per_page;
        $nb_page = (int) ceil($nb_page);
        $last_page = $nb_page - 1;
        $first_page = 0;
        $next = $page + 1;
        $prev = $page - 1;
        $pagination = [];

        if ($page > 0) {
            $pagination[] = "<a href=\"/trick/list?page={$first_page}&items_per_page={$items_per_page}\" class=\"pagination-item\"><i class=\"icofont-double-left\"></i></a>\r\n";
            $pagination[] = "<a href=\"/trick/list?page={$prev}&items_per_page={$items_per_page}\" class=\"pagination-item\"><i class=\"icofont-rounded-left\"></i></a>\r\n";
        }

        for ($p = 0; $p < $nb_page; $p++) {
            $active = $page === $p ? ' active' : '';
            $pagination[] = "<a href=\"/trick/list?page={$p}&items_per_page={$items_per_page}\" class=\"pagination-item{$active}\">{$p}</a>\r\n";
        }

        if ($last_page > $page) {
            $pagination[] = "<a href=\"/trick/list?page={$next}&items_per_page={$items_per_page}\" class=\"pagination-item\"><i class=\"icofont-rounded-right\"></i></a>\r\n";
            $pagination[] = "<a href=\"/trick/list?page={$last_page}&items_per_page={$items_per_page}\" class=\"pagination-item\"><i class=\"icofont-double-right\"></i></a>\r\n";
        }

        return $pagination;
    }

}
