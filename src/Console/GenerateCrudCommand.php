<?php

 namespace Goodyweb\JetstreamCrud\Console;

 use Illuminate\Console\Command;
 use Illuminate\Support\Facades\Schema;

 class GenerateCrudCommand extends Command
 {
     /**
      * The name and signature of the console command.
      *
      * @var string
      */
     protected $signature = 'generate:crud {--model=} {--livewire=}';

     /**
      * The console command description.
      *
      * @var string
      */
     protected $description = "Generates a CRUD Livewire given a certain model.";

     /**
      * Execute the console command.
      *
      * @return int
      */
     public function handle()
     {
        // Load the variables
        $model = $this->option('model', '');
        $livewire = $this->option('livewire', '');

        // Proceed only when there is a specified model name
        if( !empty($model) ) {

            if( !str_contains($model, '\\') ) {
                $model_short = $model;
                $model_full = "App\\Models\\{$model}";
            }

            $livewire_component_name = $livewire;
            $livewire_blade_name = strtolower($livewire);
            if( !str_contains($livewire, '\\') ) {
                $livewire_full = "App\\Livewire\\{$livewire}";
            }

            if( class_exists($model_full) ) {

                // Record the current working directory just before changing it
                $pre_chdir = getcwd();

                // Change the working directory to the folder of the JetstreamCRUD package
                chdir( __DIR__ );
                chdir('../Livewire');

                $model_instance = new $model_full();
                $model_attributes = Schema::getColumnListing($model_instance->getTable());
                $fields_string = "array(" . PHP_EOL;
                $fields_shown_string = "[";
                $fields_searchable_string = "[";
                $livewire_rules_string = "[" . PHP_EOL;
                for($i=0; $i<count($model_attributes); $i++) {

                    /** Fields **/
                    $comma = ",";
                    if( $i==count($model_attributes)-1 ) {
                        $comma = "";
                    }
                    $fields_string = $fields_string . "\t\t\t". '"' . $model_attributes[$i] . '" => "' . ucfirst(strtolower(str_replace('_', ' ', $model_attributes[$i]))) . '"' . $comma . PHP_EOL;

                    /** Shown fields **/
                    $comma2 = ",";
                    if( $i<5 ) {
                        if( $i==4 ) {
                            $comma2 = "";
                        }
                        $fields_shown_string = $fields_shown_string . '"' . $model_attributes[$i] . '"' . $comma2;
                    }

                    /** Searchable fields **/
                    if( $i==1 ) {
                        $fields_searchable_string = $fields_searchable_string . '"' . $model_attributes[$i] . '"';
                    }

                    /** Livewire rules **/
                    $livewire_rules_string = $livewire_rules_string . "\t\t'item.{$model_attributes[$i]}' => ''," . PHP_EOL;

                }
                $fields_string = $fields_string . "\t\t)";
                $fields_shown_string = $fields_shown_string . ']';
                $fields_searchable_string = $fields_searchable_string . ']';
                $livewire_rules_string = $livewire_rules_string . "\t]";

                $model_attributes_final = array_slice($model_attributes, 0, count($model_attributes));


                // Load the sample Livewire Component
                $component_template = file_get_contents('Component.template.php');

                // Customization of the Livewire Component, certain parts of the template gets edited
                $component_code = str_replace( '{model_short}', $model_short, $component_template );
                $component_code = str_replace( '{model_full}', $model_full, $component_code );
                $component_code = str_replace( '{livewire}', $livewire_component_name, $component_code );
                $component_code = str_replace( '{livewire-rules}', $livewire_rules_string, $component_code );
                $component_code = str_replace( '{livewire-blade}', $livewire_blade_name, $component_code );
                $component_code = str_replace( '{fields}', $fields_string, $component_code );
                $component_code = str_replace( '{primary-key}', $model_instance->getKeyName(), $component_code );
                $component_code = str_replace( '{fields-shown}', $fields_shown_string, $component_code );
                $component_code = str_replace( '{fields-searchable}', $fields_searchable_string, $component_code );

                // Blade customizations
                $blade_input_fields_edit_string = PHP_EOL;
                $blade_verify_fields_delete_string = PHP_EOL;
                for($i=0; $i<count($model_attributes); $i++) {
                    $blade_input_fields_edit_string = $blade_input_fields_edit_string . "\t\t\t\t\t<label for=\"item-{$model_attributes[$i]}\">{{ " . '$fields[\'' . $model_attributes[$i] . '\']' . " }}</label>" . PHP_EOL;
                    $disabled_prop = ( in_array($model_attributes[$i], [$model_instance->getKeyName(),'created_at','updated_at']) ? 'disabled="disabled"' : '');
                    $blade_input_fields_edit_string = $blade_input_fields_edit_string . "\t\t\t\t\t<x-input type=\"text\" wire:model=\"item.{$model_attributes[$i]}\" id=\"item-{$model_attributes[$i]}\" class=\"col-span-1 md:col-span-2 block\" {$disabled_prop} placeholder=\"\" />" . PHP_EOL . PHP_EOL;    

                    $blade_verify_fields_delete_string = $blade_verify_fields_delete_string . "\t\t\t\t\t<label for=\"item-{$model_attributes[$i]}\">{{ " . '$fields[\'' . $model_attributes[$i] . '\']' . " }}</label>" . PHP_EOL;
                    $blade_verify_fields_delete_string = $blade_verify_fields_delete_string . "\t\t\t\t\t<x-input type=\"text\" wire:model=\"item.{$model_attributes[$i]}\" id=\"item-{$model_attributes[$i]}\" class=\"col-span-1 md:col-span-2 block\" disabled=\"disabled\" placeholder=\"\" />" . PHP_EOL . PHP_EOL;    
                }

                // Load the sample Livewire Blade
                $blade_template = file_get_contents('Blade.template.php');

                //Customization of the Livewire Blade
                $blade_code = $blade_template;
                $blade_code = str_replace( '{input-fields-edit}', $blade_input_fields_edit_string, $blade_code );
                $blade_code = str_replace( '{verify-fields-delete}', $blade_verify_fields_delete_string, $blade_code );
                

                // Make the Livewire Components folder if it doesn't exist
                $livewire_components_path = app_path('Livewire');
                if( !file_exists( $livewire_components_path ) ) {
                    mkdir($livewire_components_path, 0775, true);
                }

                // Make the Livewire Component
                file_put_contents( "{$livewire_components_path}/{$livewire_component_name}.php", $component_code );

                // Make the Livewire blades folder if it doesn't exist
                $livewire_blades_path = base_path('resources/views/livewire');
                if( !file_exists( $livewire_blades_path ) ) {
                    mkdir($livewire_blades_path, 0775, true);
                }

                // Make the Livewire Blade
                file_put_contents( "{$livewire_blades_path}/{$livewire_blade_name}.blade.php", $blade_code );

                // Prepare the route codes
                $route_line_use_code = "use {$livewire_full};";
                $route_line_registration_code = "Route::get('/{$livewire_blade_name}', {$livewire_component_name}::class);";

                $routes_path = "{$pre_chdir}/routes/web.php";
                $web_php_content = file_get_contents($routes_path);

                if( !str_contains($web_php_content, $route_line_use_code) || !str_contains($web_php_content, $route_line_registration_code) ) {
                    $web_php_content_lines = file($routes_path);
                }

                // Generate
                $web_php_custom_content = "";
                if( !str_contains($web_php_content, $route_line_use_code) ) {

                    if( count($web_php_content_lines)>0 ) {
                        $use_line_identified = false;
                        for( $i=0; $i<count($web_php_content_lines); $i++ ) {

                            $web_php_current_code_line = $web_php_content_lines[$i];

                            if( substr( trim($web_php_content_lines[$i]), 0, 4 ) == 'use ' && $use_line_identified==false ) {
                                $web_php_current_code_line = $web_php_content_lines[$i] . "use {$livewire_full};" . PHP_EOL;
                                $use_line_identified = true;
                            }

                            $web_php_custom_content = $web_php_custom_content . $web_php_current_code_line;

                        }
                    }

                }

                if( !str_contains($web_php_content, $route_line_registration_code) ) {
                    $web_php_custom_content = $web_php_custom_content . PHP_EOL. $route_line_registration_code . PHP_EOL;
                }

                // Modify the routes/web.php file
                if( !str_contains($web_php_content, $route_line_use_code) || !str_contains($web_php_content, $route_line_registration_code) ) {
                    file_put_contents( $routes_path, $web_php_custom_content );
                }

                // Return the original working directory just after anything
                chdir($pre_chdir);

            } else {
                echo "The model {$model} DOES NOT exist!";
            }
        }

        return Command::SUCCESS;
     }
 }