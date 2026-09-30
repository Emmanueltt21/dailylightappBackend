<?php
defined('BASEPATH') or exit('No direct script access allowed');

use GuzzleHttp\Client;

class TestComposer extends CI_Controller
{
    public function index()
    {
        require FCPATH . 'vendor/autoload.php';

        $client = new Client();
        echo "Guzzle and Google Auth are working!";
    }
}
