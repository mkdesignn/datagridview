<?php
namespace mkdesign82\datagridview;

use DB;
use Illuminate\Support\Facades\Session;

class Core{

    /**
     * @var Table name | table object
     */
    protected $table;

    /**
     * @var Columns | All columns to retrieve
     */
	protected $columns;

    /**
     * @var Columns name | all columns name which locate in the header
     */
	protected $columns_name;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var Container for saving ajax code
     */
    protected $ajax_code;

    /**
     * @var Using this id to recognize every states
     */
    protected $table_id;

    /**
     * Use this method to render the table
     *
     * @param $table
     * @param $columns
     * @param $columns_name
     * @return string
     */
    public function render($table, $columns, $columns_name , $table_id){

		if( $columns_name != "" )
			$this->columns_name = json_encode($columns_name);

		if( $columns != "" )
			$this->columns = json_encode($columns);

        $this->table = $table;
        $this->token = csrf_token();

        $this->table_id = $table_id;
        return $this->createAjax($table);
	}

	private function success(){

        $success = "var tr = '';";
		$success .= "$.each(e, function(table_key, table_row){";
            $success .= "if( table_key == 'table' ){";
                $success .= "$.each(table_row, function(row_key, row){  ";
                    $success .= "tr = $('<tr>'); ";
                        $success .= "$.each(row, function(cell_key, cell){";
                            $success .= "tr.append('<td>'+cell+'</td>')";
                        $success .= "});";
                    $success .= "$('#".$this->table_id."').append(tr);";
                $success .= "});";
            $success .= "}";
            $success .= "if( table_key == 'paginate' ){";
                $success .= " $('table').parent().parent().after(table_row); ";
            $success .= "}";
		$success .= "}); Metronic.unblockUI('#".$this->table_id."');";
        return $success;
	}

    /**
     * @param $table
     * @return string
     * @internal param $token
     */
    private function createAjax($table)
    {
        $ajax = $this->header();
        $ajax .= "<div class='table-scrollable'> <div class='table-scrollable' style='margin:0px !important;'>".
        "<table id='".$this->table_id."' class='table table-striped table-bordered table-advance table-hover'><thead><tr>";
        foreach (json_decode($this->columns_name) as $item) {
            $ajax .= "<td>".$item."</td>";
        }
        $ajax .="</tr></thead><tbody></tbody></table></div></div>";
        $ajax .= "<script>";
        $ajax .= "var result; ";
        $data = "data: {_token:'" . $this->token . "',table:'" . $table . "', columns:'" . $this->columns . "', columns_name:'" . $this->columns_name . "'},";
        $ajax .= $this->callAjax($table, $data);
        $ajax .= "$('body').on('click', '.pagination li a', function(e){";
        $ajax .= " Metronic.blockUI({ target:'#".$this->table_id."' ,animate: true});";
        $ajax .= "   e.preventDefault(); var page = $(this).text();";
        $data = "data: {_token:'" . $this->token . "', random: result.random, page: page },";
        $ajax .= $this->callAjax($table, $data);
        $ajax .= "});</script>";
        return $ajax;
    }

    private function callAjax($table, $data){
        $ajax = "$.ajax({";
        $ajax .= "url: '" . action('\mkdesign82\datagridview\TestController@postIndex') . "',";
        $ajax .= "type: 'POST',";
        $ajax .= $data;
        $ajax .= "success:function(e){ e = JSON.parse(e);  $('table tbody tr').remove(); $('table').parent().parent().parent().find('.pagination').remove();  result = e; " . $this->success() . " }";
        $ajax .= "}).error(function(e){console.log(e.responseText); document.write(' there are some errors ') });";
        return $ajax;
    }

    private function header(){
        $ajax = "<div class=\"row\">
                    <div class=\"form-group \">
                        <div class=\"col-md-3 col-sm-3 col-xs-6\">
                            <div class=\"input-group\">
                                <select name=\"sample_2_length\" aria-controls=\"sample_2\" class=\"form-control input-xsmall input-inline rows_per_page\" tabindex=\"-1\" title=\"\">
                                    <option value=\"5\">5</option>
                                    <option value=\"15\">15</option>
                                    <option value=\"20\">20</option>
                                    <option value=\"-1\">همه</option>
                                </select>
                            </div>
                        </div>
                        <div class=\"col-md-3 col-sm-3 col-xs-6 pull-right\">
                            <div class=\"input-group\">
                                <span class=\"input-group-addon\">
                                    <i class=\"fa fa-search\"></i>
                                </span>
                                <input name=\"search\" class=\"form-control search-input\" placeholder=\"جستجو\">
                            </div>
                        </div>
                    </div>
                </div>";
        return $ajax;
    }
}

?>