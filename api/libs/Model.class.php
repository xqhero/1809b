<?php
namespace libs;

class Model {

	protected $pdo;

	protected $table = "";

	public function __construct(){
		// dsn 数据源名称
		$config = config("database.");
		$this->pdo = new \PDO($config['dsn'],$config['root'],$config['password']);
		if($this->table == "") {
			$this->getTableName();
		}
		
	}

	// 获取默认的表名字
	public function getTableName(){
		$name = get_called_class();
		$this->table = strtolower(str_replace("Model","",substr($name,strpos($name,"\\")+1)));
	}

	public function setTableName($tableName=''){
		$this->table = $tableName;
	}


	public function replaceTableName($sql){
		return str_replace('__table__', $this->table, $sql);
	}
	// 查询
	public function query($sql,$data=[]){

		$sql = $this->replaceTableName($sql);
		// 预处理
		$stm = $this->pdo->prepare($sql);

		$stm->execute($data);
		// 返回所有的结果
		$res = $stm->fetchAll(\PDO::FETCH_ASSOC);
		if(count($res) == 1){
			return $res[0];
		}
		return $res;

	}

	// 增删改
	public function exec($sql,$data=[]){

		$sql = $this->replaceTableName($sql);
		$stm = $this->pdo->prepare($sql);
		$res = $stm->execute($data);
		return $res;
	}

}