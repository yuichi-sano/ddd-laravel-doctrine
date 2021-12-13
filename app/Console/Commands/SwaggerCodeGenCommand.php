<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class SwaggerCodeGenCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:swagger-codegen {--tag=} {--force}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Swagger定義から自動生成';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'SwaggerDefinition';

    private array $swaggerArr  =   array();

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return  __DIR__.'/stubs/';
    }

    protected function getStubCustom($resourceType){
        $stubName = '';
        if($resourceType == 'request'){
            $stubName = 'request.stub';
        }elseif($resourceType == 'requestDefinition'){
            $stubName = 'requestDefinition.stub';
        }elseif($resourceType == 'resultResource'){
            $stubName = 'resource.stub';
        }elseif($resourceType == 'resultDefinition'){
            $stubName = 'resultDefinition.stub';
        }
        return $this->getStub().$stubName;
    }


    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http';
    }


    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param string $name
     * @return string
     */
    protected function qualifyClassCustom(string $name, $resourceType, $tag): string
    {

        if($resourceType == 'request'){
            $customName = 'Requests/'.$tag.'/'.$name;
        }elseif($resourceType == 'requestDefinition'){
            $customName = 'Requests/Definition/'.$tag.'/'.$name;
        }elseif($resourceType == 'resultResource'){
            $customName = 'Resources/'.$tag.'/'.$name;
        }elseif($resourceType == 'resultDefinition'){
            $customName = 'Resources/Definition/'.$tag.'/'.$name;
        }
        return $this->qualifyClass($customName);
    }



    public function getDefinitionName($name){
        return str_replace('#/definitions/','',$name);
    }


    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $resource = file_get_contents( resource_path().'/swagger/sample.json');
        $json = mb_convert_encoding($resource, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $this->swaggerArr = json_decode($json,true);

        $apiPaths =  $this->swaggerArr['paths'];
        foreach($apiPaths as $apiEndPath){
            foreach ($apiEndPath as $apiEnd){
                $tag = end($apiEnd['tags']);
                if($this->option('tag') && $tag  != $this->option("tag")){
                    continue;
                }
                $request = end($apiEnd['parameters']);
                $requestName =  $request['name'];
                $this->makeDefinition($requestName, $tag, 'request');

                $requestDefinition = $this->getDefinitionName($request['schema']['$ref']);
                $this->makeDefinition($requestDefinition, $tag, 'requestDefinition');

                $resultResource = $this->getDefinitionName($apiEnd['responses']['200']["schema"]['$ref']);
                $this->makeDefinition($resultResource, $tag, 'resultResource');

                $resultDefinition = $this->getDefinitionName($this->swaggerArr['definitions'][$resultResource]["properties"]["result"]['$ref']);
                $this->makeDefinition($resultDefinition,  $tag, 'resultDefinition');

            }
        }
    }


    public function  makeDefinition( $name, $tag, $resourceType){

        if(strstr($name, 'Abstract')){
            echo 'Abstractと命名されたものは基底クラスとみなしここでは作成しません';
            return ;
        }


        $className = $this->qualifyClassCustom($name, $resourceType, $tag);
        //$name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($className);
        if ((! $this->hasOption('force') ||
                ! $this->option('force')) &&
            //$this->alreadyExists($this->getNameInput())
            $this->alreadyExists($className)
        ) {
            $this->error($this->type.' already exists!');
            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClassOne($className,$name,$resourceType,$tag));
        $this->info($this->type.' created successfully.');
    }



    public function childGen(array $children,$parentName){
        $inp_name= $parentName;
        $name = $this->qualifyClass($inp_name);
        //$name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);

        if ((! $this->hasOption('force') ||
                ! $this->option('force')) &&
            $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');

            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClassOne($name,$children));

        $this->info($this->type.' created children successfully.');
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     */
    protected function buildClassOne(string $name, $planeName, $resourceType, $tag): string
    {
        $properties = [];
        $getters = [];
        $setters = [];
        $dependencyDefinition = '';
        $stub = $this->files->get($this->getStubCustom($resourceType));

        if($resourceType == 'request'){
            $dependencyDefinition = str_replace('Request','Definition',$planeName);
            $this->replaceDefinitions($stub, $dependencyDefinition,$tag);
            return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
        }

        $swaggerArray = $this->swaggerArr['definitions'][$planeName];
        if (!array_key_exists('properties', $swaggerArray)){
            echo '$swaggerArray';
            conttinue;
        }
        foreach($swaggerArray['properties'] as $propertyName => $property){
            if(!array_key_exists('description', $property)){
                if($property['$ref']){
                    $dependencyDefinition = $this->getDefinitionName($property['$ref']);
                }
                continue;
            }
            $properties[] = $this->getPropertyStr($propertyName, $property['description']);
            $getters[] = $this->getGetterMethodStr($propertyName);
            if($property['type'] == 'object'){
                $childName = $planeName.self::camelize($propertyName);
                $setters[] = $this->getSetterMethodStr($propertyName, self::camelize($childName));
                if(array_key_exists('properties',$property) && $property['properties'] != '{}'){
                    $this->childGen($property,$childName);
                }
            }elseif($property['type'] == 'array'){
                $childName = $planeName.self::camelize($propertyName);
                $setters[] = $this->getArrayObjetSetterMethodStr($propertyName, $childName, $property['type']);
                $this->childGen($property['items'],$childName);
            }else{
                $setters[] = $this->getSetterMethodStr($propertyName, $property['type']);
            }
        }

        $this->replaceProperties($stub, implode("\n", $properties));
        $this->replaceGetters($stub, implode("\n", $getters));
        $this->replaceSetters($stub, implode("\n", $setters));

        if($resourceType == 'resultResource'){
            $this->replaceDefinitions($stub, $dependencyDefinition,$tag);
        }


        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * setters
     * @param string $stub
     * @param string $name
     * @return $this
     */
    protected function replaceDefinitions(string &$stub, string $name, $tag)
    {
        $stub = str_replace(
            ['DummyDefinitionName'],
            [$tag."\\".$name],
            $stub
        );
        $stub = str_replace(
            ['DummyDefinitionClass'],
            [$name],
            $stub
        );

    }

    /**
     * プロパティ記載
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceProperties(&$stub, $names)
    {
        $stub = str_replace(
            ['properties'],
            [$names],
            $stub
        );
    }

    /**
     * getters
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceGetters(&$stub, $names)
    {
        $stub = str_replace(
            ['getters'],
            [$names],
            $stub
        );
    }
    /**
     * setters
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceSetters(&$stub, $names)
    {
        $stub = str_replace(
            ['setters'],
            [$names],
            $stub
        );
    }

    protected function getPropertyStr($name,$description){
        $camel =  self::camelize($name);
        return "
    //{$description}
    protected \${$name};
       ";
    }
    protected function getGetterMethodStr($name){
        $camel =  self::camelize($name);
        return "
    /**
     * @return mixed
     */
    public function get{$camel}()
    {
        return \$this->{$name};
    }
    ";
    }
    protected function getSetterMethodStr($name, $type, $is_optional = false){

        $type = $type === 'integer' ? 'int' : $type;
        $type = $type === 'number' ? 'float' : $type;
        $type = $type === 'boolean' ? 'bool' : $type;
        $conv = '';
        if($type == 'int' || $type == 'float' || $type == 'string' || $type == 'bool' ){
            $conv = "({$type})";
        }
        $camel =  self::camelize($name);
        return "
    /**
     * @param mixed {$name}
     */
    public function set{$camel}({$type} \${$name}): void
    {
        \$this->{$name} = {$conv} \${$name};
    }
    ";
    }

    /**
     * array_object =  [Object];
     * のような場合に対応
     *
     * @param $name
     * @param $type
     * @param bool $is_optional
     * @return string
     */
    protected function getArrayObjetSetterMethodStr($name, $childObj, $type, $is_optional = false){
        $camel =  self::camelize($name);
        return "
    /**
     * @param mixed {$name}
     */
    public function add{$camel}({$childObj} \${$name}): void
    {
        \$this->{$name}[] = \${$name};
    }

    /**
     * @param mixed {$name}
     */
    public function set{$camel}(array \${$name}): void
    {
        foreach(\${$name} as \$unit){
           \$this->add{$camel}(\$unit);
        }
    }
    ";
    }
    /**
     * スネークからキャメルへ置換します。
     * @param string $str
     * @return string
     */
    public static function camelize($str){
        return ucfirst(strtr(ucwords(strtr($str, array('_' => ' '))), array(' ' => '')));
    }


}
