<?php

namespace mkdesignn\datagridview;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class TestController extends Controller
{

    protected $request;
    protected $rows_per_page;
    protected $table_name;
    protected $table_obj;
    protected $columns;
    protected $columns_name;
    protected $row_per_page;
    protected $search;
    protected $test;
    protected $random_number;


    function __construct(Request $request)
    {
        $this->request = $request;

        if( is_string($this->request->get("table")) )
            $this->table_name = $this->request->get("table");
        else if( is_object( $this->request->get("table") ) )
            $this->table_obj = $this->request->get("table");

        if( $this->request->has("columns_name") )
            $this->columns_name = $this->request->get("columns_name");

        if( $this->request->has("columns") )
            $this->columns = $this->request->get("columns");

        if( $this->request->has("rows_per_page") ){
            $row_per_page = $this->request->get("rows_per_page");
            if( $row_per_page == "-1" )
                $this->row_per_page = "99999999999";
            else
                $this->row_per_page = $this->request->get("rows_per_page");
        }
        else
            $this->row_per_page = 5;

        if( $this->request->has("search") )
            $this->search = $this->request->get("search");
        else
            $this->search = "";

        if( $this->request->has("page") ){
            $this->current_page = $this->request->get("page");
            // check whether if current page have any records
            $this->current_page_start_records = ( $this->current_page * $this->row_per_page ) - $this->row_per_page;
        }
        else
            $this->current_page = 1;

//         retrieve basic info from session
        if( $this->request->has("random") ){
            $tables = Session::get("tables.".$this->request->get("random"));
            $this->table_name = $tables[0];
            $this->columns_name = json_decode($tables[1]);
            $this->columns = json_decode($tables[2]);
            $this->random_number = $this->request->get("random");
        }
        else
            $this->random_number = $this->createRandom();

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function postIndex()
    {

        if( Schema::hasTable($this->table_name) ){
            $table = DB::table($this->table_name)->select(json_decode($this->columns))->get();
            return $this->paginate($table, "table");
        }else{
            return "no-such-table";
        }
    }

    protected function paginate($table , $table_name){
        $paginate = new LengthAwarePaginator($table, count($table), $this->row_per_page, $this->current_page);

        $total_rows = count($table);

        // we should select only posts that we want;
        $table = array_slice($table, ($this->current_page*$this->row_per_page) - $this->row_per_page, $this->row_per_page);

        $start_id = ($this->current_page*$this->row_per_page) - $this->row_per_page;
        $start_id++;

        $end_id = ($this->current_page*$this->row_per_page);

        if( $end_id > $total_rows )
            $end_id = $total_rows;

        if( $total_rows == 0 )
            $start_id = 0;

        $footer_info = "<div style='margin-top:17px;float:left;' class='footer-data-table-info'>نمایش <b>".$start_id."</b> تا <b>".$end_id."</b> ، <b>".$this->row_per_page."</b> سطر در هر صفحه ، کل سطرها  <b>$total_rows</b></div>";

        return json_encode( [$table_name=>$table, "paginate"=>$paginate->render(),
            "page_id"=>$this->current_page, "row_per_page"=>$this->row_per_page,
            "start_id"=>$start_id, "footer_info"=>$footer_info, "random"=>$this->random_number] );
    }

    private function createRandom(){
        $str_random = str_random(16);
        while( Session::has("tables.".$str_random) ){
            $str_random = str_random(16);
        }

        Session::push("tables.".$str_random, $this->table_name);
        Session::push("tables.".$str_random, json_encode($this->columns_name));
        Session::push("tables.".$str_random, json_encode($this->columns));

        return $str_random;
    }

}
