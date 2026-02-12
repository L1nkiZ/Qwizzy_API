<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil avec choix de documentation
     */
    public function index()
    {
        return view('home');
    }
}
