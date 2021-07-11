# codeigniter4-informix
Library to add suppot of informix databases in codeigniter 4 framework by odbc driver




# install
Download the `Informix_lib.php` and move to `app/Libraries`
```php
use App\Libraries\Informix_lib;
```

```php
...
$informixLib = new Informix_lib($myDsn, $myUser, $myPassword);
...
```

# user it
**Insert**
```php
...
$informixLib = new Informix_lib($myDsn, $myUser, $myPassword);
$returnInsertId=true;
$insertId = $informixLib->insert(
    "myTable",
    [
        "column1"=>"value1",
        "column1"=>"value2"
    ],
    $returnInsertId
);
...
```
**Select**
```php
...
$informixLib = new Informix_lib($myDsn, $myUser, $myPassword);
$results = $informixLib->select(["colum1","colum2"])->from("myTable")->get()->getRows();
...
```

**Update**
```php
...
$informixLib = new Informix_lib($myDsn, $myUser, $myPassword);
$results = $informixLib->update("myTable")->set([
    "colum1"=>"value1",
    "colum1"=>"value2",
])->where(["id"=>1])->get();
...
```

**Delete**
```php
...
$informixLib = new Informix_lib($myDsn, $myUser, $myPassword);
$results = $informixLib->delete("myTable")->where(["id"=>1])->get();
...
```

**For print the sql query use**
```php
...
$informixLib = new Informix_lib($myDsn, $myUser, $myPassword);
$results = $informixLib->select(["colum1","colum2"])->from("myTable")->get()->getRows();
echo $informixLib->last_query();
...
```

**For execute custom sql query**
```php
...
$informixLib = new Informix_lib($myDsn, $myUser, $myPassword);
$results = $informixLib->query("select ... from .. join...")->get()->getRows();
...
```


This library support `joins`, `order by`, `group by`, `limits`, `transactions` ...
read the code **is short**


# Licencia/License
**MIT**
