# doctrine-validator
## Installation

	$ composer require paliari/doctrine-validator

## Configuration

Include the TraitValidatorModel in your model

```php

// Create your model extended to AbstractRansackModel.
class YourModel
{
    use \Paliari\Doctrine\TraitValidatorModel;

    //... fields ...

    /**
     * Override the method getEm is required.
     * You must return your EntityManager in this method
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEm()
    {
        // return EntityManager
    }

}

```

Or you can make your model class extends from AbstractValidatorModel:

```php

class YourModel extends \Paliari\Doctrine\AbstractValidatorModel
{
    //... fields ...

    /**
     * Override the method getEm is required.
     * You must return your EntityManager in this method
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEm()
    {
        // return EntityManager
    }

}

```


## Usage

### Validators

Create a `protected static` property inside your model to validate. See the available options and examples:


- **validates_presence_of**
  - Options: `if`, `unless`, `on`
  - Example:

    ```php
    protected static $validates_presence_of = [
        'email',
        'last_login' => ['on' => 'update'],
        'nike_name' => ['unless' => 'name']
    ];
    ```

- **validates_size_of** *alias to validates_length_of*
  - Options: `minimum`, `maximum`, `in|within`
  - Example:

    ```php
    protected static $validates_length_of = [
        'name' => ['minimum' => 10, 'maximum' => 100],
        'nike_name' => ['in' => [3, 20]]
    ];
    ```

    > the `maximum` option is automatically setted by the field doc block

- **validates_inclusion_of**
  - Options: `in|within`, `allow_nil`, `allow_blank`
  - Example:

    ```php
    protected static $validates_inclusion_of = [
        'type' => ['in' => [1, 2, 3, 4]],
        'value' => ['in' => [1, 2, 3, 4], 'allow_nil' => true],
        'field_name' => ['in' => ['a', 'b', 'c'], 'allow_blank' => true]
    ];
    ```

  > the `boolean` fields are automatically setted as `true|false`

- **validates_exclusion_of**
  - Options: `in|within`, `allow_nil`, `allow_blank`
  - Example:

    ```php
    protected static $validates_exclusion_of = [
        'field_name' => ['in' => ['a', 'b', 'c'], 'allow_blank' => true]
    ];
    ```

- **validates_format_of**
  - Options: `with`, `without`
    - Values: `email`, `url`, `integer`, `boolean`, `ip`, `/[0-9a-z]/`
  - Example:

    ```php
    protected static $validates_format_of = [
        'email' => ['with' => 'email'],
        'field_url' => ['with' => 'url'],
        'field_name' => ['without' => 'float']
    ];
    ```

- **validates_numericality_of**
  - Options: `greater_than`, `greater_than_or_equal_to`, `less_than`, `less_than_or_equal_to`, `equal_to`, `other_than`, `only_integer`
  - Example:

    ```php
    protected static $validates_numericality_of = [
        'ammount' => ['greater_than' => 5],
        'another_field' => ['only_integer' => true]
    ];
    ```

- **validates_uniqueness_of**
  - Options: `scope`
  - Example:

    ```php
    protected static $validates_uniqueness_of = [
        'email',
        'number' => ['scope' => ['year']],
    ];
    ```

- **validates_custom**
  - Example:

    ```php
    protected static $validates_custom = ['yourMethodName', 'otherYourMethodName'];

    public function yourMethodName() {
      if ($name == 'example') {
        $this->errors->add('name', '"name" cannot be "example"');
      }
    }

    public function otherYourMethodName() {
      // Do something here
    }
    ```


> There is also a list of default options supported by every validator: 'if', 'unless', 'on', 'allow_nil' or 'allow_blank'


### Callbacks

Create a `protected static` property with the callbacks inside your model. See the available options and examples:

- **before_validation**
  Execute before validation
  - Example:

    ```php
    protected static $before_validation = ['yourCallbackName'];

    public function yourCallbackName() { /* Do something here */}
    ```

- **after_validation**
  Execute after validation
  - Example:

    ```php
    protected static $after_validation = ['anotherCallbackName'];
    ```

- **before_validation_on_create**
  Execute before validation only create
  - Example:

    ```php
    protected static $before_validation_on_create = ['yourCallbackName'];
    ```

- **after_validation_on_create**
  Execute after validation only create
  - Example:

    ```php
    protected static $after_validation_on_create = ['yourCallbackName'];
    ```

- **before_validation_on_update**
  Execute before validation only update
  - Example:

    ```php
    protected static $before_validation_on_update = ['yourCallbackName'];
    ```

- **after_validation_on_update**
  Execute after validation only update
  - Example:

    ```php
    protected static $after_validation_on_update = ['yourCallbackName'];
    ```

- **before_validation_on_remove**
  Execute before validation only remove
  - Example:

    ```php
    protected static $before_validation_on_remove = ['yourCallbackName'];
    ```

- **after_validation_on_remove**
  Execute after validation only remove
  - Example:

    ```php
    protected static $after_validation_on_remove = ['yourCallbackName'];
    ```


## Authors

- [Marcos Paliari](http://paliari.com.br)
- [Daniel Fernando Lourusso](http://dflourusso.com.br)

