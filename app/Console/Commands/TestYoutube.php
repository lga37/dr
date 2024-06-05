<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestYoutube extends Command  #TestCommunications
{

    protected $signature = 'test:communications';

    protected $description = 'fazendo crawling';

    public function __construct(){
        parent::__construct();
    }
    public function handle()
    {
        $response = $this->call('dusk', [
            '--filter'=> 'test_that_dddd',
            '--path'=> 'tests/browser/buscaTest.php',
            ]);
    }
}
