# PDOXpress

## PWhat is this?

This is a simple class that enables convinience methods when working with
PHP's PDO class. Note that it has it's limits, but for simple applications with
no requirement of ORM or related concepts, it does it's job.

## Features

### Prepared statements

The methods of the class will convert your inputs to prepared statements to mitigate
SQL injection attacks. Of course you also can run SQL queries the old style, by simply
string concats.

### Escaping HTML spectial chars

The select methods have a parameter, which escapes single and double quotes for usage
in html forms or other places.

### Strip tags

The insert and update methods have a parameter, to strip XML tags, before storing user
inputs to the database.

## Inputs welcome

Have you found bugs or have you encountered errors in edge cases?
Please open an issue then.
