<?php
namespace controllers;

use models\StudentModel;
use libs\Response;
class StudentController extends RestfulController
{

	public function list(){

		$model = new StudentModel;
		$data = $model->query("select * from __table__");
		Response::restfulResponse(200,"ok",$data);
	}

	public function store(){

		$data = request()->post();
		$model = new StudentModel;
		if($model->exec("insert into __table__ set name=:name,sex=:sex,age=:age",$data)){
			Response::restfulResponse(201,"ok",$data);
		}
	}

	public function save(){

		$data = request()->all();
		$id = request()->get('id');
		$data['id'] = $id;
		$model = new StudentModel;
		// 1. 查数据表看有没有该条数据
		$student = $model->query("select * from __table__ where id=?",[$id]);
		if(!$student){
			Response::restfulResponse(404,"",["error"=>"NOT FOUND"]);
		}
		$res = $model->exec("update __table__ set name=?,age=?,sex=? where id=?",[$data['name'],$data['age'],$data['sex'],$id]);

		if(!$res){
			Response::restfulResponse(417,"",["error"=>"修改失败"]);
		}
		$student['name']=$data['name'];
		$student['age'] = $data['age'];
		$student['sex'] = $data['sex'];
		Response::restfulResponse(200,"",$student);
	}

	public function delete(){
		$id = request()->get('id');
		//1.查找数据
		$model = new StudentModel;
		$student = $model->query("select * from __table__ where id=?",[$id]);
		if(!$student){
			Response::restfulResponse(404,"",["error"=>"NOT FOUND"]);
		}
		$res = $model->exec("delete from __table__ where id=?",[$id]);
		if(!$res){
			Response::restfulResponse(500,"",["error"=>"内部错误"]);
		}
		Response::restfulResponse(204,"","");
	}




}