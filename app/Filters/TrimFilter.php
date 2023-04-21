<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class TrimFilter implements FilterInterface {

    // before function
    public function before(RequestInterface $request, $arguments = null)
    {
        $trimmed_post = [];
        foreach($request->getPost() as $var => $val) {
            $trimmed_post[$var] = (is_array($val)) ? $this->trim_Arr_Val($val) : trim($val);
        }
        $request->setGlobal('post', $trimmed_post);
        $request->setGlobal('request', $trimmed_post);
    }

    function trim_Arr_Val($datas)
    {
        $Return = array();
        foreach($datas as $k => $data) {
            $Return[$k] = (is_array($data)) ? $this->trim_Arr_Val($data) : trim($data);
        }
        return $Return;
    }

    // after function
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}