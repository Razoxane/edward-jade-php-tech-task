__Language :__ English

# Lunch API

Returns recipes you can make with the ingredients in the fridge which are before their use-by dates, for a given date, defaulting to the request date.

Recipes that require ingredients after their best-before date will be sorted to the bottom of the list.

## Installation

### Requirements
- PHP 7.3
- Composer 1.x

### Build Steps
1. Clone the repo to the folder of choice on your local machine.
    ```sh
    $ git clone git@github.com:Razoxane/edward-jade-php-tech-task.git
    ```

2. In the root directory of the cloned repo, run the following commands:
    ```sh
    $ compose install --dev
    $ symfony serve --port=1080
    ```

### Testing
Run the following in the root directory of the project:
```sh
$ ./bin/phpunit
```


## API Usage Instructions

### URL
```
/lunch/?date=YYYY-MM-DD
```

### Method
```
GET
```

### URL Params
Optional:
- `date=[YYYY-MM-DD]` ISO 8601 Date format.

### Success Response
- Code: 200
- Content:
    ```
    {
        "recipes": [{
            "title": "Recipe 1",
            "ingredients": [
                "Ingredient 1",
                "Ingredient 2",
            ]
        }, {
            "title": "Recipe 2",
            "ingredients": [
                "Ingredient 3",
                "Ingredient 4",
            ]
        }]
    }
    ```

### Error Response
- Code: 400
- Content:
    ```
    {
        "status": 400,
        "message": "Value provided for date is an invalid format. YYYY-MM-DD required."
    }
    ```
    OR
    ```
    {
        "status": 400,
        "message": "Value provided for date is an invalid value. Valid date in the format of YYYY-MM-DD required."
    }
    ```

### Sample Calls
#### cURL
```sh
$ curl -X GET 'http://127.0.0.1:1080/lunch?date=2019-03-19'
```
#### jQuery
```JavaScript
var settings = {
  "url": "http://127.0.0.1:1080/lunch?date=2019-03-19",
  "method": "GET",
  "timeout": 0,
};

$.ajax(settings).done(function (response) {
  console.log(response);
});
```
