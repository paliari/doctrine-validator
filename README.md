# doctrine-validator

Advanced model validator for Doctrine ORM, based on ActiveRecord validations.

## Requirements

- PHP 8.2 to 8.5
- Doctrine ORM 2.20 or 3.x

## Installation

	composer require paliari/doctrine-validator

## Configuration

Include the `TraitValidatorModel` in a base model and declare the validations in the models that extend it.

> The trait already declares the `validates_*` and callback properties as `protected static array`, so:
> - redeclaring them in the same class that uses the trait is a PHP fatal error. Declare them in a subclass;
> - the subclass must keep the `array` type (`protected static array $validates_presence_of`), otherwise PHP
>   raises a fatal error (`Type of ... must be array`).

```php
abstract class BaseModel implements \Paliari\Doctrine\ModelValidatorInterface
{
    use \Paliari\Doctrine\TraitValidatorModel;

    /**
     * Required: return your EntityManager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEM()
    {
        // return EntityManager
    }

    /**
     * Required: human name of an attribute, used in the error messages (ModelException and ValidatorErrors::__toString).
     */
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

Or you can make your model class extend `AbstractValidatorModel` (a mapped superclass that already uses the trait):

```php
class YourModel extends \Paliari\Doctrine\AbstractValidatorModel
{
    protected static array $validates_presence_of = ['email'];

    //... fields ...

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

> `persist()` validates new records immediately (`prePersist`). Changes made to a new record between
> `persist()` and `flush()` are not validated again.

### Handling errors

```php
$model->isValid();      // bool
$model->isValid(true);  // throws ModelException when invalid
$model->validate();     // throws ModelException when invalid

try {
    $entityManager->persist($model);
    $entityManager->flush();
} catch (\Paliari\Doctrine\ModelException $e) {
    $e->getValidatorErrors()->toArray(); // ['email' => ['is invalid']]
    $e->getValidatorErrors()->asJson();  // ['your_model' => ['email' => ['is invalid']]]
    $e->getMessage();                    // "Email: is invalid\n" (uses humAttribute)
}
```

After a validation, the same errors are available in `$model->errors` (`toArray()`, `isValid()`, `add($attribute, $message)`).

## Usage

### Validators

Create a `protected static array` property inside your model to validate. Each entry is either the field name
or the field name with its options. See the available options and examples:

- **validates_presence_of**
  - Fails for `null`, `''`, whitespace-only strings and empty arrays (`'0'`, `0` and `false` are present).
  - Example:

    ```php
    protected static array $validates_presence_of = [
        'email',
        'last_login' => ['on' => 'update'],
        'nickname' => ['unless' => 'name'],
    ];
    ```

- **validates_length_of** (alias: **validates_size_of**)
  - Options: `minimum`, `maximum`, `is`, `in|within` (`[minimum, maximum]`)
  - Lengths are counted with `mb_strlen` (UTF-8).
  - Example:

    ```php
    protected static array $validates_length_of = [
        'name' => ['minimum' => 10, 'maximum' => 100],
        'nickname' => ['in' => [3, 20]],
        'code' => ['is' => 6],
    ];
    ```

- **validates_inclusion_of**
  - Options: `in|within`
  - The comparison is strict (`'1'` is not included in `[1, 2]`).
  - Example:

    ```php
    protected static array $validates_inclusion_of = [
        'type' => ['in' => [1, 2, 3, 4]],
        'value' => ['in' => [1, 2, 3, 4], 'allow_nil' => true],
        'field_name' => ['in' => ['a', 'b', 'c'], 'allow_blank' => true],
    ];
    ```

- **validates_exclusion_of**
  - Options: `in|within`
  - The comparison is strict.
  - Example:

    ```php
    protected static array $validates_exclusion_of = [
        'field_name' => ['in' => ['a', 'b', 'c'], 'allow_blank' => true],
    ];
    ```

- **validates_format_of**
  - Options: `with` (must match), `without` (must not match)
    - Values: `boolean`, `email`, `float`, `integer`, `ip`, `mac_address`, `url`, a regex (e.g. `/[0-9a-z]/`)
      or a custom filter (see below).
  - Example:

    ```php
    protected static array $validates_format_of = [
        'email' => ['with' => 'email'],
        'field_url' => ['with' => 'url'],
        'field_name' => ['without' => 'float'],
        'zip_code' => ['with' => '/^\d{5}-\d{3}$/'],
    ];
    ```

  - Custom filters receive the value and return `bool`. Register them in your bootstrap:

    ```php
    \Paliari\Doctrine\Validators\FilterVarValidator::add('even', fn($value) => 0 === $value % 2);

    protected static array $validates_format_of = [
        'quantity' => ['with' => 'even'],
    ];
    ```

- **validates_numericality_of**
  - Options: `greater_than`, `greater_than_or_equal_to`, `less_than`, `less_than_or_equal_to`, `equal_to`, `other_than`, `only_integer`
  - Example:

    ```php
    protected static array $validates_numericality_of = [
        'amount' => ['greater_than' => 5],
        'percent' => ['greater_than_or_equal_to' => 0, 'less_than_or_equal_to' => 100],
        'another_field' => ['only_integer' => true],
    ];
    ```

    > comparator errors use the default messages with the value, e.g. `must be greater than 5`.
    > Use the `message` option to override them (`%{count}` is replaced by the value).

- **validates_uniqueness_of**
  - Options: `scope` (list of fields that must also match)
  - Uses `getEM()->getRepository(YourModel::class)->findBy(...)` and compares the records by the `id` property,
    so the model must have an `id` primary key.
  - Example:

    ```php
    protected static array $validates_uniqueness_of = [
        'email',
        'number' => ['scope' => ['year']],
    ];
    ```

- **validates_custom**
  - Model methods called during the validation. They receive the options array and add errors to `$this->errors`.
  - Example:

    ```php
    protected static array $validates_custom = [
        'yourMethodName',
        'checkCanBeRemoved' => ['on' => 'remove'],
    ];

    public function yourMethodName(array $options)
    {
        if ('example' == $this->name) {
            $this->errors->add('name', '"name" cannot be "example"');
        }
    }

    public function checkCanBeRemoved(array $options)
    {
        // Do something here
    }
    ```

### Default options

Every validator supports:

- `if`: name of a model property; the validation runs only when it is truthy.
- `unless`: name of a model property; the validation runs only when it is falsy.
- `on`: `create`, `update`, `remove` or `save` (create and update). Default: create and update.
  On remove, only the validations with `'on' => 'remove'` run.
- `allow_nil`: skip the validation when the value is `null`.
- `allow_blank`: skip the validation when the value is blank (`null`, `''`, whitespace-only, empty array).
- `message`: custom error message.

### Validations from the mapping

Some validations are added automatically from the Doctrine mapping (except for the identifier):

| Column type | Validation |
|---|---|
| `string` and `text` with `length` | `validates_length_of` with `maximum` = length |
| `integer`, `bigint`, `smallint` | `validates_numericality_of` with `only_integer` |
| `float`, `decimal` | `validates_numericality_of` |
| `boolean` | `validates_inclusion_of` in `[true, false]` (strict: `0`, `1` and `'1'` are invalid) |

`allow_blank` is set from the column `nullable`. The options you declare for the same field are merged with them,
e.g. `'name' => ['minimum' => 3]` keeps the `maximum` from the mapping.

### Error messages

The message of an error is resolved in this order:

1. the option with the error key, e.g. `'blank' => 'is required'` (not available for the numericality comparators,
   whose option holds the value);
2. the `message` option;
3. the default message of the key.

| Validator | Keys |
|---|---|
| presence | `blank` |
| length | `too_short`, `too_long`, `wrong_length` |
| inclusion / exclusion | `inclusion` / `exclusion` |
| format | `invalid` |
| numericality | `not_a_number`, `not_a_integer`, `greater_than`, `greater_than_or_equal_to`, `less_than`, `less_than_or_equal_to`, `equal_to` |
| uniqueness | `unique` |

`%{count}` is replaced by the limit in the length and numericality messages (e.g. `is too short (minimum is %{count} characters)`).
`other_than` has no default message and uses `is invalid`.

To translate the default messages, set a callable that receives the key and returns the message:

```php
\Paliari\Doctrine\ValidatorErrors::$custom_get_message = function (string $key): ?string {
    return $translator->trans("validation.$key");
};
```

### Callbacks

Create a `protected static array` property with the callbacks (model method names) inside your model:

| Property | When |
|---|---|
| `before_validation` | before every validation |
| `after_validation` | after every successful validation |
| `before_validation_on_create` | before the validation of a new record |
| `after_validation_on_create` | after the successful validation of a new record |
| `before_validation_on_update` | before the validation of an update |
| `after_validation_on_update` | after the successful validation of an update |
| `before_validation_on_remove` | before the validation of a remove |
| `after_validation_on_remove` | after the successful validation of a remove |

They run in this order: `before_validation`, `before_validation_on_<state>`, validations, `after_validation`,
`after_validation_on_<state>`. The `after_*` callbacks don't run when the validation fails.

Example:

```php
protected static array $before_validation = ['normalizeEmail'];
protected static array $after_validation_on_create = ['generateCode'];

public function normalizeEmail()
{
    $this->email = strtolower(trim($this->email));
}

public function generateCode()
{
    // Do something here
}
```

### Custom Validators

To add a custom validator, call the static method `addCustomValidator` of the model passing a `callable`
that receives the model as parameter.

> Call it in your bootstrap, before the first validation of the model. The validations of each model class
> are cached on its first validation, so validators added later are ignored.

Example:

```php
YourModel::addCustomValidator(function ($model) {
    if ($model->isNewRecord()) {
        if (!MyValidator::instance()->isValid($model)) {
            foreach (MyValidator::instance()->getMessages() as $k => $item) {
                $model->errors->add($k, $item);
            }
        }
    }
});

// or
YourModel::addCustomValidator('MyValidator::validate');
```

### Record state

`$model->recordState()` returns `create`, `update` or `remove`, also available as `isNewRecord()`,
`isUpdateRecord()` and `isRemoveRecord()`. The subscriber sets it on the Doctrine events (new models are `create`).


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

The `version` field is part of the `composer.lock` content hash, so update the lock together with it:

```bash
# Bump the version in composer.json
docker-compose -f docker-compose-cli.yml run --rm cli ./vendor/bin/bump --version patch|minor|major

# Update the composer.lock content hash
docker-compose -f docker-compose-cli.yml run --rm cli composer update --lock

# Commit and tag
VERSION=$(php -r 'echo json_decode(file_get_contents("composer.json"))->version;')
git add composer.json composer.lock
git commit -m "Bumps package version"
git tag "$VERSION"

git push && git push --tags
```


## Authors

- [Marcos Paliari](http://paliari.com.br)
- [Daniel Fernando Lourusso](http://dflourusso.com.br)
