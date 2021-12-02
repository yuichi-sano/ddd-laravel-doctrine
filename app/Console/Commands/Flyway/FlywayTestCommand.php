<?php

namespace App\Console\Commands\Flyway;

use Illuminate\Console\ConfirmableTrait;

class FlywayTestCommand extends AbstractFlywayCommand
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'flyway:testing {--cleanOnly}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'ユニットテスト用flywayマイグレーションwrapper';


    public function handle()
    {
        $this->cdFlyDir();
        $this->clean();
        if(!$this->option('cleanOnly')){
            $cmd =  $this->flywayCmdBuild('migrate', 'local', true);
            exec($cmd, $opt);
            print_r($opt);
        }
    }

    private function clean(){
        $cmd =  $this->flywayCmdBuild('clean', 'local', true);
        print_r($cmd);
        exec($cmd, $opt);
        print_r($opt);
    }
}
