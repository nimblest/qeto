# Qeto Query Package

*built by DanoDev*


##Purpose
Single manageable location for creating query where scopes. Originally they were being put into each table model as [Laravel scopes](https://laravel.com/docs/5.0/eloquent#query-scopes) for Eloquent queries. We then had to build out specific queries for other locations cause the scopes were just Laravel specific. These queries are a location where you can create the query using raw sql along with parameters to build out sql queries using any ORM or raw sql.

##Basic Use
First you will want to add the **QetoQueryTrait** on any of your table models that you want to query on. This will connect the query to the correct folder structure. Let's say we are using `App\User` and have included the trait.

That file then connects to a created file (in our case we already have it created)`App\Queries\UserQueries.php`. This is where you can add any query scope you want. Let's add the query

```
public function byUsername(string $parameter): array
{
    return ['users.username = "?"', [$parameter]];
}
```

This function allows us to search the users model byUsername. We can pass in the sku that we want to use and it will query the model. We have set up binding for security.

To call this function we would then invoke the facade and we would use the `qWhereRaw` to get the raw sql along with any function that you need to call as the first parameter and the second parameter is the parameter for the query.

```
user::qWhereRaw('byUsername', 'danodev');
```

##Inverses
We have added inverses that can be used to do the opposite of what you are calling. We originally had two seperate function byUsername and byNotSku (retrieve the users that don't have that sku). The inverse now let's you establish that in the same call.

```
public function byUsername(string $parameter): array
{
    return ['users.sku ' . ($this->inverse ? '!' : '') . '= "?"', [$parameter]];
}
```
Now calling the `qWhereRawInverse` function it will get the inverse of the byUsername.

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

Contact me:

https://twitter.com/danodev

http://danogillette.com