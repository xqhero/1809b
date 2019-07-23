<?php

$arr = [8,7,6,5,4,3,2,1,2];
$len = count($arr);
// 冒泡排序
// 思想： 每次挑出一个最大值放到尾部
// for($j=1;$j<$len;$j++){
// 	for($i=0;$i<$len-$j;$i++){
// 		if($arr[$i]>$arr[$i+1]){
// 			$arr[$i] = $arr[$i] ^ $arr[$i+1];
// 			$arr[$i+1] = $arr[$i] ^ $arr[$i+1];
// 			$arr[$i] = $arr[$i] ^ $arr[$i+1];
// 		}
// 	}
// }

// 选择排序
// 思想：每次挑一个最小的放到指定的位置
// for($i=0;$i<$len;$i++){
// 	$min = $arr[$i];
// 	$index = $i;
// 	for($j=$i+1;$j<$len;$j++){
// 		if($arr[$j] < $min){
// 				$min = $arr[$j];
// 				$index = $j;
// 		}
// 	}
// 	if($index != $i){
// 		$arr[$i] = $arr[$i] ^ $arr[$index];
// 		$arr[$index] = $arr[$i] ^ $arr[$index];
// 		$arr[$i] = $arr[$i] ^ $arr[$index];
// 	}
// }

// 插入排序
// 1. 逐一找到数字
// 2. 找到数字应该放到哪个位置
// 3. 插入到这个位置即可

// 两种情况：
// a. 插入的数比要比较的数的最后一个数都大，直接追加到后面
// b. 比下标为0的元素还要小，将数字填充到下标为0的位置

// [1,2,3, 5, 7     　4

// for($i=1;$i<$len;$i++){

// 	$value = $arr[$i];
// 	for($j=$i-1;$j>=0;$j--){
// 		//判断什么时候到达位置
// 		if($arr[$j] < $value){
// 			break;
// 		}
// 	}

// 	if( ($j+1) != $i ){
// 		for($index=$i;$index>($j+1);$index--){
// 				$arr[$index] = $arr[$index-1];
// 		}
		
// 		$arr[$j+1] = $value;
// 	}
// }

// 快速排序
// 思想：对任何一个数而言，比它小的数字一定在左边，比它大的数字一定在右边
// 1. 假设一个中间值
// 2. 寻找中间值的位置
// 3. 根据中间值一分为二，利用童颜搞得规则来排序

// [2,5,1,3,9,7,12,10]



function quickSort(&$arr,$start,$end){
	// 终止条件
	if($start>=$end){
		return;
	}

	// 假设中间值
	$mid = $arr[$start];
	$i = $start;
	$j = $end;
	while($i < $j){

		for(;$j>=$i && $arr[$j]>$mid ; $j--);

		for(;$i<$j && $arr[$i]<=$mid ; $i++);

		if($i<$j){
			// 交换他两的值
			swap($arr[$i],$arr[$j]);
		}

	}

	// 得到中间位置 $j
	if($start !== $j){
		swap($arr[$start],$arr[$j]);
	}

	// 根据j位置一分为二,左右递归
	// 左边
	quickSort($arr,$start,$j-1);
	// 右边
	quickSort($arr,$j+1,$end);


}

function swap(&$x,&$y){
	$x = $x ^ $y;
	$y = $x ^ $y;
	$x = $x ^ $y;
}

quickSort($arr,0,$len-1);
var_dump($arr);