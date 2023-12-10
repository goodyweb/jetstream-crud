![Laravel logo](https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Laravel.svg/193px-Laravel.svg.png)
![Composer logo](https://upload.wikimedia.org/wikipedia/commons/archive/2/26/20150131091334%21Logo-composer-transparent.png)
# Jetstream CRUD

## Getting started

This package makes it easy to create a full CRUD module by just issuing a single command:
```bash
php artisan generate:crud --model=Person --livewire=Persons
```
**Voila!**

See the [Installation](#nstallation) and [Usage](#usage) sections for the full details.

## Installation

```bash
composer require goodyweb/jetstream-crud dev-master
```

## Usage

1. Make a database table as you usually would:
   ```bash
   php artisan make:migration create_persons_table --create=persons
   ```
    - Make some edits
      ```php
        <?php

        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;

        return new class extends Migration
        {
            /**
            * Run the migrations.
            */
            public function up(): void
            {
                Schema::create('persons', function (Blueprint $table) {
                    $table->id();
                    // add something columns here maybe
                    $table->timestamps();
                });
            }

            /**
            * Reverse the migrations.
            */
            public function down(): void
            {
                Schema::dropIfExists('persons');
            }
        };
      ```
     - Execute the actual creation of the table
       ```php
       php artisan migrate
       ```



2. Make the Eloquent Model for the database table as you usually would:
   ```bash
   php artisan make:model Person
   ```

3. Generate the Livewire Component and Blade template for the CRUD module:
   ```bash
   php artisan generate:crud --model=Person --livewire=Persons
   ```

   | Parameter | Example | Explanation |
   |-----------|---------|-------------|
   | **Model** | `--model=Person` | `Person` refers to the Eloquent Model that will be used for the whole CRUD module. |
   | **Livewire** | `--livewire=Persons` | `Persons` refers to the class name of the Livewire that will be generated. |

