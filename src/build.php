<?php

namespace mkdesignn\datagridview;


use Illuminate\Support\Facades\Event;

class build extends Core
{

    /**
     * build constructor.
     * @param $table
     * @param $columns_name
     * @param $table_id
     * @param array $type
     */
    function __construct($table, $columns_name , $table_id, $type = []){

        if( $columns_name != "" )
            $this->columns_name = json_encode($columns_name);

        if( !str_contains($table, "select") )
            $this->table = $table;
        else{
            $this->tableObject();
        }

        $this->table_id = $table_id;
        $this->token = csrf_token();
        $this->type = $type;

    }

    /**
     * render ajax call and show the datagrid
     *
     * @return string
     */
    function render(){
        return $this->ajaxCall($this->table);
    }


    /**
     * @return string
     */
    function result(){
        $this->render = true;
        return $this->ajaxCall($this->table);
    }

    public function tableObject(){



    }

}