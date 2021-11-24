<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class CustomMigrateCommand extends Command
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flyway';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'flyway操作実行';

    /**
     * Create a new migration command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        chdir( './flyway/' );
            exec('pwd',$t);
        print_r($t);
        $cmd = './gradlew -i clean processResources flywayMigrate -Dflyway.url=jdbc:postgresql://host.docker.internal:5433/sampletest -Dflyway.locations=classpath:db/migration,classpath:develop,classpath:local';
        exec($cmd, $opt);
        print_r($opt);
    }

}
