# Seedling

Seedling is a tool intended to help developers generate mock-data in WordPress. By using Seedling you can prepare a
local dev environment to be ready to go data-wise. Seedling is useful in development processes where no actual data has
been submitted yet. It is also useful in cases where more complex pages with many components should be thoroughly tested
in all possible combinations.

# Requirements

- PHP 7.4 and up
- WP CLI

## Supports:

- Creation of Custom Post Types (Models)
  - Using defaults in Seedling, you can generate Models on the fly
  - Setting Acf Templates for each custom post type
  - Setting manual seeder values with key-value pairs: "my-text-field" => "My text field placeholder data"
  - WP CLI integration

- Acf Field Support:
  - Text, textArea, Wysiwyg
  - Url, Link
  - checkbox, radio, Select and ButtonGroup
  - Number, Range
  - Relationship
  - Image
  - Email, Password
  - all other fields are not (yet) supported.

## How does it work TLDR:

- Create a class for each (Custom) Post Type you want to Seed.
- Insert said class into a 'special' SeedIndex class
- run command `seed create model my-custom-post-type --limit=9001`
- you now have over 9000 pages generated and filled with field-specific data where possible.

## Getting started

There are multiple ways you can work with Seedling. Seedling creates WP CLI Commands that can be inserted in a general
commands list.

- out of the box, Seedling will index what is already registered and will automatically seed all posts, pages, custom
  post types, options, etc by using a sane set of defaults.
- It is possible to configure seedling by using the config function.
- you can use the commandBefore and commandAfter functions to run code before or after the automatic process.
- it is also possible to override the automatic Seedling process with your commands by overriding the commands function.
- each model/option instance can be overridden with your custom seeder data.

### Minimal setup using automatic mode

create the class below, for example in App\Seeders and after that load this class in your functions or in your preferred
method of loading classes.

```php
# App\Seeders\Seeder.php
namespace App\Seeders;

class Seeders extends Seedling\Abstracts\SeederIndexAbstract
{
    function seeders(): void
    {
      // No seeders defined yet.
    }
}
```

```php
# functions.php
new App\Seeders\Seeders();
```

after this you are done setting up Seedling in its most basic mode. You can now run the following Seedling Command:

``` bash
$ wp seed build-application
```

Seedling will now try and fill in all registered post types, options and taxonomies with basic data.

### configuration

Inside the SeederIndexAbstract it is possible to pass an array inside the config function, see below for all possible
config settings:

```php
# App\Seeders\Seeder.php
namespace App\Seeders;

class Seeders extends Seedling\Abstracts\SeederIndexAbstract
{
    function seeders(): void
    {
      // No seeders defined yet.
    }
    
       function config(): array
    {
        return [
            'seedlingApp' => [
                'seedlingDefaultMode' => 'fill_all', // fill_all | fill_required | fill_random_respect_required, | fill_random_disregard_required
            ],
            'models' => [
                'posts' => [
                    'enabled' => false,
                ],
                'projects' => [
                    'limit' => 10,
                    'seedlingMode' => 'fill_all', // fill_all | fill_required | fill_random_respect_required, | fill_random_disregard_required
                ],
                'example-custom' => [
                    'limit' => 15,
                    'seedlingMode' => 'fill_all', // fill_all | fill_required | fill_random_respect_required, | fill_random_disregard_required
                ]
            ],
            'taxonomies' => [
            ],
            'options' => [
                'seedlingMode' => 'fill_all', // fill_all | fill_required | fill_random_respect_required, | fill_random_disregard_required
            ]
        ];
    }
}
```

- seedlingModeDefault & seedlingMode: when generating field-data this defines how Seedling will behave when generating field data. When no seedlingMode is set on a specific model, SeedlingModeDefault will be used.
  - fill_all: does at it says, will try and fill all fields with field-type relevant data
  - fill_required: will only fill required fields
  - fill_random_respect_required: will randomize what fields will be filled, but will respect the required fields.
  - fill_random_disregard_required: will randomize what fields will be filled with values, will not respect required fields. 
- limit: specify how many items will be generated of this object. defaults to 10.
- enabled: sometimes a registered object should not be filled with demo-data. with this attribute you can disable seedling for this object.

By default seedling will process all objects unless specified otherwise in this condig.
### Setting up Model Seeders

To Start using Seedling create a new class for the (custom) post type you want to fill with data. Extend the
ModelSeederAbstract. The type function is required and should have the same name as the (custom) post type you have
defined.

For Example:

```php
namespace App\Seeders\Models;

class ProjectsSeeder extends Seedling\Abstracts\ModelSeederAbstract
{
    function type(): string
    {
        return "project";
    }
}

```

## Finishing Seedling Setup

Second, extend the seederIndexAbstract and load all ModelSeeders you have created in the previous step, optional:
provide a buildApplication function to use in WP Cli that runs all build-steps.

```php
namespace App\Seeders;

use App\Seeders\Models\ProjectsSeeder;

class Seeders extends Seedling\Abstracts\SeederIndexAbstract
{
    function seeders(): void
    {
        /*
         * Define your own ModelSeeder Classes here.
         * These will be automatically loaded when this class is constructed.
         */
        new ProjectsSeeder();
    }

      function commands(): array
    {
      /* 
       * return an array of commands that will result in a complete WordPress Application, ready to go. 
       */
      return [
          'seed create model project --limit=5',
          'seed create model project --limit=5 --blockfactory="acf/my-first-acf-gutenberg-block, acf/my-second-acf-gutenberg-block"'
      ];
    }
}

```

Last: load the seeders.php class in your theme (for example functions.php):

```php 
use App\Seeders\Seeders;
new Seeders();
```

All done, you can now use the following command: `wp seed create model project --limit=5`
This will generate 5 project pages. By default this will:

- create a published Project
- insert a title generated by faker
- if gutenberg blocks are defined in the ProjectsSeeder, they will be inserted in the page_content.
- if any Acf Fields are added to this Custom Post type Seedling will try to fill in data where-ever it can.

### Supply a list of Gutenberg Blocks to a Custom Post Type Seeder

It is possible to define a standard list of gutenberg blocks. By providing this list the Gutenberg Blocks will be
automatically added to the page if created via Seedling. For Example:

``` php
namespace App\Seeders\Models;

use App\ExternalClasses\Seedling\Abstracts\ModelSeederAbstract;

class ExampleCustomPostTypeSeeder extends ModelSeederAbstract
{
    function type(): string
    {
        return "example-custom";
    }

    public function gutenbergBlocks(): array
    {
        return [
          'acf/header',
          'wp/paragraph',
          'acf/usps'
        ];
    }
}

```

### Supply a list of Acf-Field Key Values to a Custom Post Type Seeder

Sometimes you don't want to use lorem ipsum texts or other random data for everything. For that you can use the
AcfFactory() function in the ModelSeederAbstract Class. By adding anything to the acfFactory the default random data
insertion is halted, and your manual data insertion will be used. As you can see below, it is possible to connect your
own functions to this should you need a custom implementation to a specific field.

```php
namespace App\Seeders\Models;

use Seedling\Abstracts\ModelSeederAbstract;

class ProjectsSeeder extends ModelSeederAbstract
{
    function type(): string
    {
        return "project";
    }

    public function acfFactory(): array
    {
        return [
            'my-project-text' => __("My Project Text Value"),
            'my-repeater' => [
                ['text' => "repeater text 1"], // first repeater row
                ['text' => "repeater text 2"], // second ...
                ['text' => "repeater text 3"]  // third ... etcetera.
            ]
        ];
    }
}
```

