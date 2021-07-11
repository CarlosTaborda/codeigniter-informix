<?php

namespace App\Libraries;

class Informix_lib{

    private $dsn;
    private $user;
    private $password;
    private $connection;
    private $emulation=false;
    public $file_exec="";
    public $results=[];
    protected $sql="";



    function __construct( $dsn ="", $user="", $password=""){
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;

        $this->connection = odbc_connect($dsn, $user, $password);
        
    }

    public function getCredentials(){
        echo "dsn:{$this->dsn} user:{$this->user} password:{$this->password}";
    }

    public function setAutocommit( $enable){
        odbc_autocommit($this->connection, $enable);
    }

    public function commit(){
        
        odbc_commit($this->connection);
    }
    
    /**
     * This function make a join with another table
     * @param string $table is the another table name
     * @param string $on_clause the conditions for join ex: myTable.id = anotherTable.myTable_id
     * @param string $type INNER, LEFT, RIGHT..
     * @return object $this Informix_lib object
    * */
    public function join($table, $on_clause, $type=""){
        $type = strtoupper($type);
        $this->sql .= "\n{$type} JOIN {$type} ON {$on_clause}";
        return $this;
    }

    public function order_by( $field, $sort = "ASC"){
        $this->sql .= "\nORDER BY {$field} {$sort}";
        return $this;
    }

    public function limit( $numRows = 1){
        $this->sql .= "\nLIMIT {$numRows} ";
        return $this;
    }

    public function group_by( $field ){
        $this->sql .= "\nGROUP BY ";
        if( is_string($field))
            $this->sql .= " {$field}";

        if( is_array($field))
            $this->sql .= " ".implode(",", $field);
        return $this;
    }

    public function rollback(){
        odbc_rollback($this->connection);
    }


    public function setEmulation( $enable){
        $this->emulation = $enable;
    }

    public function select( $fields = []){
        $this->sql ="SELECT ";

        if(empty($fields)){
            $this->sql .= "*";
        }
        else{
            $this->sql .= implode(',', $fields);
        }
        $this->sql.=" ";
        return $this;
    }

    public function set($setValues){
        $this->sql .="SET ";
        foreach($setValues as $f =>$v){
            $this->sql.="$f='{$v}',";
        }

        $this->sql .=substr($this->sql,0,-1);
    }


    public function from( $table ){
        $this->sql.="FROM {$table} ";
        return $this;
    }

    public function where( $conditions ){
        $this->sql .= "WHERE ";
        foreach( $conditions as $f =>$v){
            $this->sql .="$v";
            if( strpos(strtolower($v), " OR ")== false)
                $this->sql .="AND";
        }

        $this->sql = substr($this->sql, 0,-3);

        return $this;
    }

    public function delete($table){
        $this->sql="DELETE {$table} ";
        return $this;
    }

    public function update( $table){
        $this->sql="UPDATE {$table} ";
        return $this;
    }

    public function insert( $table, $data, $insertId=""){
        $this->sql ="INSERT INTO {$table} (".implode(",",array_keys($data)).")";
        $this->sql .=" VALUES('".implode("','", $data)."');";
        $this->get();

        if(!empty($insertId)){
            $this->sql="select max({$insertId}) id from {$table}";
            $this->get();
            return $this->results[0]["id"];
        }
       
    }

    public function query( $sql ){
        $this->sql = $sql;
        return $this;
    }

    /**
     * This function execute a sql query in the database loaded
     * @param bool $printError if true print sql errors
     * @return object $this Informix_lib object
     */
    public function get( $printError = true){
        if(!$this->emulation){

            $result = odbc_exec($this->connection, $this->sql);

            if(!empty(odbc_errormsg($this->connection)) && $printError ===true ){
                echo "<pre>{$this->sql}\n".odbc_errormsg($this->connection)."\n{$this->file_exec}</pre>";
            }
            else if(!empty(odbc_errormsg($this->connection))){//solo para transacciones
               return false; 
            }


            while($r = odbc_fetch_array($result)){
                array_push($this->results, $r);
            }

            array_walk_recursive($this->results, function(&$item, $key){
                if(!mb_detect_encoding($item, 'utf-8', true)){
                    $item = utf8_encode($item);
                }
            });    

            return $this;
        }
        else{

            echo "<pre>$this->sql</pre>";
        }
         
    }

    public function getRows(){
        return $this->results;
    }

    public function last_query(){
        return $this->sql;
    }
}


?>
