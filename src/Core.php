<?php
namespace mkdesignn\datagridview;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Core{

    /**
     * Select array which contains the sql and the parameters
     *
     * @var array
     */
    protected $select = [];

    /**
     * Columns name to replace the real columns
     *
     * @var string
     */
    protected $columns = [];

    /**
     * table_id to attach to the wrapper
     *
     * @var string
     */
    protected $table_id;


    protected $search_timing;

    /**
     * @param array $select
     * @param $columns
     * @param $table_id
     * @param int $search_timing
     * @return build
     */
    public function build($select = [], $columns , $table_id, $search_timing = 0){

        $this->select = $select;
        $this->columns = $columns;
        $this->table_id = $table_id;
        $this->search_timing = $search_timing;
        return $this->render();
    }

    public function render(){
        $url = action('\mkdesignn\datagridview\DataGridViewController@postIndex');
        $data = json_encode($this->select);
        return "<script> var ".$this->table_id." = ''; jQuery(function($){ "
        . $this->ajaxRequest($url, $data, $this->columns) .
            "triggerAjax();
            var search = '',
                page_id = '',
                row_per_page = '',
                time = 0;
            $('#".$this->table_id." .search').on('keyup', function(){
                window.clearInterval(time);
                time = window.setInterval(function(){
                    search = $(this).val();
                    triggerAjax(search, page_id, row_per_page);
                }, ".$this->search_timing.")
            })

            $('body').on('click', '.pagination li a', function(e){
                e.preventDefault();
                page_id = $(this).text();
                triggerAjax(search, page_id, row_per_page);
            })

            $('body').on('click', '#".$this->table_id." .row_per_page', function(e){
                e.preventDefault();
                var row_per_page = $(this).val();
                triggerAjax(search, page_id, row_per_page)
            })
        })</script>";
    }

    function ajaxRequest($url, $select, $columns){

        return "function triggerAjax(search, page_id, row_per_page){
            $.ajax({ url:'".$url."',".
            "data:{select:'".$select."', search:search, page:page_id, row_per_page:row_per_page, columns:'".json_encode($columns)."'},".
            " type:'POST',".
                "success:function(e){ ".$this->table_id." = e; } });
            }";
    }

//action('\mkdesignn\datagridview\DataGridViewController@postIndex')

}

?>