# datagridview

description
===========

Most of the time you face hard time to render data into datagrid and plus that using ajax to do that .
In this package I have made it so easy that with 4 arguments you can build your datagrid and use it in a very effesion way .
the theme that this datagrid is build on is metronic ver 4.

## Usage

### Step 1: Install Through Composer

With composer :

``` json
{
    ...
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

## Examples Num2

### What if you wanted to retrieve only the result and you did not interest with the view of the table, the only things you should do it's to use result instead of render
```
  $table:: 'Table_name'
  $columns:: ['column_1', 'column_2'];
  $columns_title:: ['column_1_name', 'column_2_name'];
  $data_table_id:: 'table_1'
  DataGrid::build($table, $columns, $column_title, $data_table_id)->result();
```
