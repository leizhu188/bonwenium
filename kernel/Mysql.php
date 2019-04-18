<?php
/**
 * Created by PhpStorm.
 * User: bonwe
 * Date: 2019-04-18
 * Time: 13:51
 */

namespace Kernel;


class Mysql
{
    private $mysql;
    private $query;
    private $query_where = "";
    private $table;
    private $limit_begin = 0;
    private $limit_count = 50;

    public function __construct()
    {
        self::initDatas();
        $this->mysql = self::connect();
    }

    public function beginTransaction(){
        $this->mysql->begin_transaction();
    }

    public function rollback(){
        $this->mysql->rollback();
    }

    public function commit(){
        $this->mysql->commit();
    }

    public function table($table){
        //每新处理一个表重置sql数据
        self::initDatas();
        $this->table = $table;
        $this->query .= " `{$table}` ";
        return $this;
    }

    public function where($field,$value,$option = '='){
        $this->query_where.= " WHERE `{$this->table}`.`{$field}` {$option} {$value}";
        return $this;
    }

    public function find(){
        $this->query = "SELECT * FROM {$this->query}";
        $this->limit_begin = 0;
        $this->limit_count = 1;
        $this->query .= $this->query_where;
        $this->query .= " LIMIT {$this->limit_begin},{$this->limit_count}";
        $rs = self::handleSelectResult($this->mysql->query($this->query));

        if (!count($rs)){
            return null;
        }

        return $rs[0];
    }

    public function list(array $fields = []){
        $fieldStr = "*";
        if (count($fields)){
            foreach ($fields as &$field){
                $field = "`{$this->table}`.`{$field}`";
            }
            $fieldStr = implode(',',$fields);
        }
        $this->query = "SELECT {$fieldStr} FROM {$this->query}";
        $this->query .= $this->query_where;
        $this->query .= " LIMIT {$this->limit_begin},{$this->limit_count}";
        $rs = self::handleSelectResult($this->mysql->query($this->query));

        return $rs;
    }

    public function update(array $value = []){
        if (empty($this->query_where)){
            return false;
        }

        $this->query = "UPDATE {$this->query} ";

        $this->query .= " SET ";
        foreach ($value as $filed=>$setValue){
            $this->query.= "`{$this->table}`.`{$filed}`={$setValue}";
        }
        $this->query .= $this->query_where;
        return $this->mysql->query($this->query);
    }

    public function insert(array $value = []){
        $this->query = "INSERT INTO {$this->query} ";
        $fileds = "(".implode(',',array_keys($value)).")";
        $insertValues = "(".implode(',',array_values($value)).")";
        $this->query = implode(" ",[$this->query,$fileds,'VALUES',$insertValues]);
        return $this->mysql->query($this->query);
    }

    public function delete(){
        if (empty($this->query_where)){
            return false;
        }

        $this->query = "DELETE FROM {$this->query} ";

        $this->query .= $this->query_where;
        monolog($this->query);
        return $this->mysql->query($this->query);
    }

    public function limit($begin,$count){
        $this->limit_begin = $begin;
        $this->limit_count = $count;

        return $this;
    }


    public function sql(){
        return $this->query;
    }

    private function handleSelectResult($result){
        $return = [];

        if (!$result){
            monolog('mysql select error');
            die(1);
        }

        while($row = mysqli_fetch_array($result))
        {
            $item = [];
            foreach ($row as $key=>$value){
                if (is_string($key)){
                    $item[$key] = $value;
                }
            }
            $return []= $item;
        }
        return $return;
    }

    private function initDatas(){
        $this->query = "";
        $this->query_where = "";
        $this->table = "";
        $this->limit_begin = 0;
        $this->limit_count = 50;
    }

    private function connect(){
        $con = mysqli_connect(
            env("MYSQL_HOST","127.0.0.1"),
            env("MYSQL_USERNAME","root"),
            env("MYSQL_USERPWD","root"),
            env("MYSQL_DATABASE","database_name")
        );
        if (!$con){
            monolog("mysql connect error",'bonwenium.log');
            die(1);
        }
        return $con;
    }
}