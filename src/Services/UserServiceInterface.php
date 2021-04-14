<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;

interface UserServiceInterface
{

    /**
     * @param Request $request
     * 
     * @return array
     */
    public function store(Request $request):array;

}
