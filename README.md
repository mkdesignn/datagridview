# datagridvieww


Most of the time you face hard time to render data into datatables and much more harder thing is to use ajax.
In this package I have made it so easy that with 4 arguments you can build your datagrid and use it in a very efficient way .
the theme that this datagrid is build on is metronic ver.4.

## Usage

### Step 1: Install Through Composer

With composer :

``` json
{
    
    "require": {
        "mkdesignn/datagridview": "1.4"
    }
}
```

### Step 2: Add service provider


```
  mkdesignn\datagridview\MkDatagridviewServiceProvider::class
```

### Step 3: Add Facade 

```
  "DataGrid" => mkdesignn\datagridview\DataGrid::class
```

## Example Num1

```
  $table:: 'Table_name'
  $columns:: ['column_1', 'column_2'];
  $columns_title:: ['column_1_name', 'column_2_name'];
  $data_table_id:: 'table_1'
  echo DataGrid::build($table, $columns, $column_title, $data_table_id)->render();
```

the above code will give you full dynamic datatable .


## Examples Num2

What if you wanted to retrieve only the result and you did not interest with the view of the table, the only things you should do it's to use result instead of render
```
  $table:: 'Table_name'
  $columns:: ['column_1', 'column_2'];
  $columns_title:: ['column_1_name', 'column_2_name'];
  $data_table_id:: 'table_1'
  DataGrid::build($table, $columns, $column_title, $data_table_id)->result();
```

the above code will return you result which you can access it by ajax.complete method which this result is derived of you'r data_table_id.


### access result
```
 $.ajaxComplete(function(event, xhr, data){
    console.log(data_table_id) // will show you the result
 })
```

the result its an object which contains records, column per page, current_page, and much more ... 

## One more feature - Examples Num3

what if you want to use filtering on your datatable, well its very easy in using this datagrid.

```
  $table:: 'Table_name'
  $columns:: ['column_1', 'column_2'];
  $columns_title:: ['column_1_name', 'column_2_name'];
  $data_table_id:: 'table_1'
  $type = ['column_to_use_it_as_filter']
  DataGrid::build($table, $columns, $column_title, $data_table_id, $type)->result();
```

and thats it , now you can use it with one filter on your datatable. which you can filter throught it and also search by choosin that one single filtering .


## more feature on the way 


