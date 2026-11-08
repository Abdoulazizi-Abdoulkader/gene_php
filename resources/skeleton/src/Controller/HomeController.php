<?php

declare(strict_types=1);

namespace App\Controller;

use App\Framework\Controller;
use App\Framework\Response;

final class HomeController extends Controller
{
    public function index(): Response
    {
        return $this->render('home');
    }
}
