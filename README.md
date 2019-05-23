# Qeto Query Package

*built by DanoDev*


##Purpose
Single manageable location for creating query where scopes. Originally they were being put into each table model as [Laravel scopes](https://laravel.com/docs/5.0/eloquent#query-scopes) for Eloquent queries. We then had to build out specific queries for other locations cause the scopes were just Laravel specific. These queries are a location where you can create the query using raw sql along with parameters to build out sql queries using any ORM or raw sql.

##Basic Use
First you will want to add the **Qeto\QueryTrait** on any of your table models that you want to query on. This will connect the query to the correct folder structure. Let's say we are using `App\User` and have included the trait. Then add the protected variable `$name` to be the name of the class.

```
public $name = 'User';
```

That file then connects to a created file (in our case we already have it created)`App\Queries\UserQueries.php`. First you will have to extend in your `App\User` file the `Qeto\BaseQuery` class. Once that has been added you can start to add any query scope you want. Let's add the query `byUsername` that queries the users by the username field.

```
use Qeto\BaseQuery;

class UserQueries extends BaseQuery 
{
    protected $name = 'User';
    
    public function byUsername(string $parameter): array
    {
        return ['users.username = "?"', [$parameter]];
    }
}
```

This function allows us to search the users model byUsername. We can pass in the sku that we want to use and it will query the model. We have set up binding for security.

To call this function we would then invoke the facade and we would use the `qWhereRaw` to get the raw sql along with any function that you need to call as the first parameter and the second parameter is the parameter for the query.

```
user::qWhereRaw('byUsername', 'danodev');
```

##Inverses
We have added inverses that can be used to do the opposite of what you are calling. We originally had two seperate function byUsername and byNotUsername (retrieve the users that don't have that sku). The inverse now let's you establish that in the same call.

```
use Qeto\BaseQuery;

class UserQueries extends BaseQuery 
{
    protected $name = 'User';

    public function byUsername(string $parameter): array
    {
        return ['users.sku ' . ($this->inverse ? '!' : '') . '= "?"', [$parameter]];
    }
}
```
Now calling the `qIWhereRaw` function it will get the inverse of the byUsername.

##Use as eloquent scopes

To use as an eloquent scope you just call the function just as you would a scope, but you won't call the raw version

```
User::qWhere('byUsername', 'danodev')->get();
```

This allows you to append other scopes and use other eloquent methods with it.

##Making sub folders
Adding all queries to one file would make for a very bloated class. So you can add child classes off the main one within a file that is namespaced after the parent. For example, let's say we want to move the user types to their own file they are a child of the user class. So you would make a `App\Queries\User` folder and create a new file `userTypeQueries.php` with a same named class and extending the UserQueries class. So now let's say we move the `byUsername` function over to the UserTypeQueries class. To call that class you put the name of the file as the first part of the camel cased function you declare in the qWhere.

```
User::qWhere('typeByUsername', 'danodev')->get();
```

#Joining queries and make Or Where statements.
If you find yourself making a group of queries together often you can join them into one scope using the `joinByAnd` function. This will join the queries into one that you can then use. Let's say we keep on looking for queries where they have a specific user type and signed up to become a member on a certain date. We can create two queries `byType` and `byCreatedDay` and join them together in one that we can always call together.

The syntax in the joinByAnd is that you pass in the name of methods you want to call. It then takes the parameters that you pass in the form of an array for each parameter that is needed.

```
use Qeto\BaseQuery;

class UserQueries extends BaseQuery 
{
    protected $name = 'User';

    public function byType(string $parameter): array
    {
        return ['users.type ' . ($this->inverse ? '!' : '') . '= "?"', [$parameter]];
    }

    /**
     * Gets the created at date between two dates
     * @param  array $dates first key will be start date, second end date
     * @return array
     */
    public function byCreatedDay(array $dates): array
    {
        return ['users.created_at ' . ($this->inverse ? 'NOT ' : '') . 'BETWEEN "?"" AND "?"', [$dates]];
    }


    /**
     * Gets all user by a user type that were made during a certain time period
     * @param  array $parameters string of type, first key will be start date, second end date
     * @return array
     */
    public function getTypesBetweenDate(array $parameters): array
    {
        return $this->joinByAnd([
            'byType',
            'byCreatedDay'
        ], $parameters);
    }
}
```

You would then call `getTypesBetweenDate`:

```
$parameters = [
    'pro',
    [
        '2018-05-12',
        '2019-05-13',
    ]
];

User::qWhere('getTypesBetweenDate', $parameters);
```

This would pass each parameter to the specific functions and return them.

#Relation Methods

To call a relation method you just pass the relation name as the first parameter on the query `qRelationWhere` as an eloquent scope.

```
User::qRelationWhere('order', 'byCity', 'Salt Lake City');
```

This would query the `OrderQueries` model you will have had to have created by the function `byCity`.

Contact me:

https://twitter.com/danodev

http://danogillette.com