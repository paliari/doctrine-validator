# doctrine-validator
#### Installation
	
	$ composer require paliari/doctrine-validator

#### Configuration

Your models class extends to AbstractValidatorModel, example

```php
<?php

// Create your model extended to AbstractValidatorModel.
class YourModel extends \Paliari\Doctrine\AbstractValidatorModel
{
    //... fields ...
     
    /**
     * Override the method getEm is required.
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEm()
    {
        // return EntityManager
    }


}

```

Or include TraitValidatorModel in your model

```php

<?php

// Create your model extended to AbstractRansackModel.
class YourModel implements \Paliari\Doctrine\InterfaceModel
{
    use \Paliari\Doctrine\TraitValidatorModel;

    //... fields ...
     
}

```

### Usage


```php

<?php

class YourModel implements \Paliari\Doctrine\InterfaceModel
{
    use \Paliari\Doctrine\TraitValidatorModel;

    protected static $validates_presence_of = [
        'email',
        'last_login' => ['on' => 'update'],
        'nike_name' => ['unless' => 'name'],
    ];

    /** 
     * $validates_size_of alias to $validates_length_of
     * the maximum is automatically setted by the configurations of the Doctrine
     * Options:
     *    'minimum': integer
     *    'maximum': integer
     *    'is': integer
     *    'within': Array [1, 100]
     *    'in': Array [1, 00]
     */
    protected static $validates_length_of = [
        'name' => ['minimum' => 10, 'maximum' => 100],
        'nike_name' => ['in' => [3, 20]],
    ];


    /** 
     * the fields types boolean is automatically setted by the configurations of the Doctrine
     * Options: 'in' alias 'within'
     * Array of values
     */
    protected static $validates_inclusion_of = [
        'type' => ['in' => [1, 2, 3, 4]],
        'value' => ['in' => [1, 2, 3, 4], 'allow_nil' => true],
        'field_name' => ['in' => ['a', 'b', 'c'], 'allow_blank' => true],
    ];

    /** 
     * Options: 'in' alias 'within'
     * Array of values
     */
    protected static $validates_exclusion_of = [
        'field_name' => ['in' => ['a', 'b', 'c'], 'allow_blank' => true],
    ];

    /** 
     * Options:
     *    'with'
     *    'without'
     * Values: 'email', 'url', 'float', 'integer', 'boolean', 'ip' or regexp ex: '/[0-9a-z]/'
     */
    protected static $validates_format_of = [
        'email' => ['with' => 'email'],
        'field_url' => ['with' => 'url'],
        'field_name' => ['without' => 'float'],
    ];


    /** 
     * Options:
     *    'greater_than'
     *    'greater_than_or_equal_to'
     *    'less_than'
     *    'less_than_or_equal_to'
     *    'equal_to'
     *    'other_than'
     */
    protected static $validates_numericality_of = [
        'value',
        'count' => ['only_integer' => true],
    ];

    /**
     * Options:
     *    'scope': Array
     */
    protected static $validates_uniqueness_of = [
        'email',
        'number' => ['scope' => ['year']],
    ];

    protected static $validates_custom = [
        'your_method_name',
        'other_your_method_name',
    ];

}

```

There is also a list of default options supported by every validator: 'if', 'unless', 'on', 'allow_nil' or 'allow_blank'


## Callbacks

  - #### before_validation
    Execute before validation
    - Example: 
  
      ```php
      <?php
      class YourModel implements \Paliari\Doctrine\InterfaceModel
      {
          use \Paliari\Doctrine\TraitValidatorModel;
          protected static $before_validation = ['your_callback_name'];
          // ...
      }
      ```
  
  - #### after_validation
    Execute after validation
    - Example: 
  
      ```php
      <?php
      class YourModel implements \Paliari\Doctrine\InterfaceModel
      {
          use \Paliari\Doctrine\TraitValidatorModel;
          protected static $after_validation = ['your_callback_name'];
          // ...
      }
      ```
  
  - #### before_validation_on_create
    Execute before validation only create
    - Example: 
  
      ```php
      <?php
      class YourModel implements \Paliari\Doctrine\InterfaceModel
      {
          use \Paliari\Doctrine\TraitValidatorModel;
          protected static $before_validation_on_create = ['your_callback_name'];
          // ...
      }
      ```
  
  - #### after_validation_on_create
    Execute after validation only create
    - Example: 
  
      ```php
      <?php
      class YourModel implements \Paliari\Doctrine\InterfaceModel
      {
          use \Paliari\Doctrine\TraitValidatorModel;
          protected static $after_validation_on_create = ['your_callback_name'];
          // ...
      }
      ```
  
  - #### before_validation_on_update
    Execute before validation only update
    - Example: 
  
      ```php
      <?php
      class YourModel implements \Paliari\Doctrine\InterfaceModel
      {
          use \Paliari\Doctrine\TraitValidatorModel;
          protected static $before_validation_on_update = ['your_callback_name'];
          // ...
      }
      ```
  
  - #### after_validation_on_update
    Execute after validation only update
    - Example: 
  
      ```php
      <?php
      class YourModel implements \Paliari\Doctrine\InterfaceModel
      {
          use \Paliari\Doctrine\TraitValidatorModel;
          protected static $after_validation_on_update = ['your_callback_name'];
          // ...
      }
      ```
  
  - #### before_validation_on_remove
    Execute before validation only remove
    - Example: 
  
      ```php
      <?php
      class YourModel implements \Paliari\Doctrine\InterfaceModel
      {
          use \Paliari\Doctrine\TraitValidatorModel;
          protected static $before_validation_on_remove = ['your_callback_name'];
          // ...
      }
      ```
  
  - #### after_validation_on_remove
    Execute after validation only remove
    - Example: 
  
      ```php
      <?php
      class YourModel implements \Paliari\Doctrine\InterfaceModel
      {
          use \Paliari\Doctrine\TraitValidatorModel;
          protected static $after_validation_on_remove = ['your_callback_name'];
          // ...
      }
      ```


## Authors

- [Marcos Paliari](http://paliari.com.br)
