# doctrine-validator

## Requirements

- PHP 8.2 to 8.5
- Doctrine ORM 2.20 or 3.x

## Installation

	composer require paliari/doctrine-validator

## Configuration

Include the TraitValidatorModel in a base model and declare the validations in the models that extend it.

> The trait already declares the `validates_*` and callback properties, so redeclaring them in the same class
> that uses the trait is a PHP fatal error. Declare them in a subclass.

```php

abstract class BaseModel implements \Paliari\Doctrine\ModelValidatorInterface
{
    use \Paliari\Doctrine\TraitValidatorModel;

    /**
     * Override the method getEm is required.
     * You must return your EntityManager in this method
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEM()
    {
        // return EntityManager
    }

    public static function humAttribute(string $name): ?string
    {
        return ucwords($name);
    }
}

class YourModel extends BaseModel
{
    protected static array $validates_presence_of = ['email'];

    //... fields ...
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
    public static function getEM()
    {
        // return EntityManager
    }

    public static function humAttribute(string $name): ?string
    {
        return ucwords($name);
    }
}

```

### Initialize validator

Add in your bootstrap:

```php
$eventManager = $entityManager->getEventManager();
$eventManager->addEventSubscriber(new \Paliari\Doctrine\ModelValidatorEventSubscriber());
```

The subscriber validates on `prePersist`, `preUpdate` and `preRemove`. Entities that don't use the
`TraitValidatorModel` are ignored.

### Validation lifecycle

The result of a validation is kept in `$model->errors` and reused until the next successful write
(`persist`, `update` or `remove` + `flush`), so calling `$model->isValid()` before `flush()` doesn't run
the validations and callbacks twice. To validate again manually before a write (e.g. after fixing a field),
reset the errors:

```php
$model->isValid();      // false
$model->email = 'fixed@mail.com';
$model->errors = null;
$model->isValid();      // true
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

    > comparator errors use the default messages with the value, e.g. `must be greater than 5`.
    > Use the `message` option to override them (`%{count}` is replaced by the value).

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


### Custom Validators

Para adicionar validator customizado é só uar o método statico no model *ModelName::addCustomValidator* 
passando como argumento um *callable* que recebe como parametro o $model.

To add custom validator just use the static method in model ModelName :: addCustomValidator passing as argument a callable that takes $model as parameter.

Example:

```php
MyModel::addCustomValidator(function($model) {
  if ($model->isNewRecord()) {
      if (!MyValidator::instance()->isValid($model)) {
          foreach (MyValidator::instance()->getMessages() as $k => $item) {
              $model->errors->add($k, $item);
          }
      }
  }
});

// ou
MyModel::addCustomValidator('MyValidator::validate');

```


## Development

### Install dependencies

```bash
docker-compose -f docker-compose-cli.yml run --rm cli composer install
```

### Testing

The `cli` service runs PHP 8.5 and `cli82` runs PHP 8.2 (the minimum supported version).
The test suite fails on any deprecation, notice or warning and runs in random order
(reproduce a run with `--random-order-seed <seed>`).

```bash
docker-compose -f docker-compose-cli.yml run --rm cli ./vendor/bin/phpunit
docker-compose -f docker-compose-cli.yml run --rm cli82 ./vendor/bin/phpunit
```

### Coverage

```bash
docker-compose -f docker-compose-cli.yml run --rm coverage composer coverage
```

### Compatibility checks

The `composer.lock` uses Doctrine ORM 3. To test against ORM 2.20 or the lowest supported dependencies,
change the lock temporarily and restore it afterwards:

```bash
# Doctrine ORM 2.20
docker-compose -f docker-compose-cli.yml run --rm cli composer update doctrine/orm --with doctrine/orm:^2.20 -W

# Lowest supported dependencies
docker-compose -f docker-compose-cli.yml run --rm cli82 composer update --prefer-lowest --prefer-stable

# Restore
git checkout composer.lock && docker-compose -f docker-compose-cli.yml run --rm cli composer install
```

### Publish a new version

```bash
# Generate a new tag
docker-compose -f docker-compose-cli.yml run --rm cli ./vendor/bin/bump -g --version patch|minor|major

git push && git push --tags
```


## Authors

- [Marcos Paliari](http://paliari.com.br)
- [Daniel Fernando Lourusso](http://dflourusso.com.br)

