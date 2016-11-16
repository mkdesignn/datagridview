<?php

namespace mkdesignn\datagridview;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class DataGridViewController extends Controller
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var
     */
    protected $select;

    /**
     * @var integer Contains total rows per page
     */
    protected $row_per_page;

    /**
     * @var string
     */
    public $table_name;

    /**
     * @var object use current table object or @see DataGridViewController::table_name
     */
    protected $table_obj;

    /**
     * @var array contains columns to which be selected
     */
    protected $columns;

    /**
     * @var array contains names which replace with selected columns name
     */
    protected $columns_name;

    /**
     * @var string contains searched keyword
     */
    protected $search;

    /**
     * @var string contains random number to contains All 4 above properties
     */
    protected $random_number;

    /**
     * @var string current page number
     */
    protected $current_page;

    /**
     * type of columns to filter all fields by them
     *
     * @var array
     */
    protected $type;



    function __construct(Request $request)
    {
        $this->request = $request;

        // set the default properties
        $this->setDefaults();

        // retrieve basic info from session
        //$this->setOrGetSession();

        $this->select = $this->request->select;

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function postIndex()
    {

        $select = json_decode($this->select);

        if( count( $select[1] ) > 0 ){

            foreach ($select[1] as $item) {
                $select[0] = preg_replace('/\?/', "'".$item."'", $select[0], 1);
            }
        }

        $select[0] = str_replace('NULL', $this->search, $select[0]);
        // select all selected columns by two above condition & filter
        $table = DB::select(DB::raw($select[0]));

        return $this->paginate($table, "table");
    }

    /**
     * Search all selected columns
     *
     * @return string
     */
    private function searchAllColumns($select){

        // if we have found any where statment in the context
        // then we should bring our where after that

        foreach (json_decode($this->columns) as $column) {
                $select .= $column." LIKE '%".$this->search."%' or ";
        }

        $select = substr($select, 0, strlen($select)-3)." ) ";
        return $select;
    }

    /**
     * filter select by choosing multiple/single type
     *
     * @param $select
     * @return string
     */
    private function filterByTypes($select){

        // iterate over all types and append to search select

        if( count($this->type) > 0 ) {
            foreach ($this->type as $key => $type) {
                foreach ($type as $key_1 => $t) {
                    if( $t != "" )
                        $select .= " and ".$key_1." = '".$t."'";
                }
            }
        }

        return $select;
    }

    private function findKey($column){
        if( count( $this->type ) > 0 ){
            foreach ($this->type as $types) {
                foreach ($types as $key => $item) {
                    if( $key == $column && $item != "")
                        return true;
                }
            }
            return false;
        }
        return false;
    }

    /**
     * paginate to create my table
     *
     *
     * @param $table
     * @param $table_name
     * @return string
     */
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

        if( $end_id < $this->row_per_page )
            $this->row_per_page = $end_id;


        $footer_info = "<div style='margin-top:17px;float:left;' class='footer-data-table-info'>نمایش <b>".$start_id."</b> تا <b>".$end_id."</b> ، <b>".$this->row_per_page."</b> سطر در هر صفحه ، کل سطرها  <b>$total_rows</b></div>";

        return json_encode( [$table_name=>$table, "paginate"=>$paginate->render(),
            "page_id"=>$this->current_page, "row_per_page"=>$this->row_per_page,
            "start_id"=>$start_id, "footer_info"=>$footer_info, "random"=>$this->random_number] );
    }

    /**
     * create new random number to save the grid info on it
     *
     * @return string
     */
    private function createRandomNumber(){
        $str_random = str_random(16);
        while( Session::has("tables.".$str_random) ){
            $str_random = str_random(16);
        }
        Session::push("tables.".$str_random, $this->table_name);
        Session::push("tables.".$str_random, json_encode($this->columns_name));
        Session::push("tables.".$str_random, json_encode($this->columns));

        return $str_random;
    }

    /**
     *  Set all defaults | shortcut for constructor
     */
    private function setDefaults()
    {

        /*
         |------------------------
         |  First we should check if we try to select from scratch table or
         |  using object to build our DataGrid
         | -----------------------
         */

        if (is_string($this->request->get("table")))
            $this->table_name = $this->request->get("table");
        else if (is_object($this->request->get("table")))
            $this->table_obj = $this->request->get("table");

        // get all selected columns
        if ($this->request->has("columns"))
            $this->columns = $this->request->get("columns");

        // get all selected columns name
        if ($this->request->has("columns_name"))
            $this->columns_name = $this->request->get("columns_name");

        // get rows per page
        if ($this->request->has("row_per_page")) {
            $row_per_page = $this->request->get("row_per_page");
            if ($row_per_page == "-1")
                $this->row_per_page = "99999999999";
            else
                $this->row_per_page = $this->request->get("row_per_page");
        } else
            $this->row_per_page = 5;

        // get search value
        if ($this->request->has("search"))
            $this->search = $this->request->get("search");
        else
            $this->search = "";

        // get the page
        if ($this->request->has("page")) {
            $this->current_page = $this->request->get("page");
        } else
            $this->current_page = 1;

        // get all types 
        if( $this->request->has("type") ){
            $this->type = $this->request->get("type");
        }
    }

    /**
     * set into session if there was not random number
     *
     * get from session if there was any random number
     */
    private function setOrGetSession()
    {
        if ($this->request->has("random")) {
            $tables = Session::get("tables." . $this->request->get("random"));
            $this->table_name = $tables[0];
            $this->columns_name = json_decode($tables[1]);
            $this->columns = json_decode($tables[2]);
            $this->random_number = $this->request->get("random");
        } else
            $this->random_number = $this->createRandomNumber();
    }

}
