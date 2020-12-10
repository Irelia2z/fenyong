<?php

namespace WinTop\Models;

use Illuminate\Database\Eloquent\Model;

class FyRelation extends Model
{
	protected $table = 'fy_relation';
	public $timestamps = false;
	protected $guarded = [];

	//关联后代
	public function subUser()
	{
		return $this->belongsTo(FyUser::class, 'ancestor_id');
	}
}