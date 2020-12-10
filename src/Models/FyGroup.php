<?php
namespace WinTop\Models;

use Illuminate\Database\Eloquent\Model;

class FyGroup extends Model
{
	protected $table = 'fy_group';
	public $timestamps = false;

	public function getRebateLevel1Attribute($level1)
	{
		return $level1 / 100;
	}

	public function getRebateLevel2Attribute($level2)
	{
		return $level2 / 100;
	}

	public function getRebateLevel3Attribute($level3)
	{
		return $level3 / 100;
	}
}
