<?php
namespace WinTop\Models;
use WinTop\Models\FyGroup;
use WinTop\Models\FyRelation;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FyUser
 *
 * @package WinTop\Models
 * @property int $id
 * @property int $group_id
 */

class FyUser extends Model
{
	protected $table = 'user';
	protected $guarded = ['id'];
	public const CREATED_AT = 'timestamp_create';
	public const UPDATED_AT = 'timestamp_update';

	//关联祖先
	public function ancestor()
	{
		return $this->hasMany(FyRelation::class, 'descendant_id');
	}

	//关联后代
	public function descendant()
	{
		return $this->hasMany(FyRelation::class, 'ancestor_id');
	}

	//关联用户组
	public function group()
	{
		return $this->belongsTo(FyGroup::class, 'group_id');
	}
	
	//已提取佣金
	public function withdrawal()
	{
		return $this->hasMany(FyWithdrawal::class, 'user_id');
	}

	//佣金总数
	public function commission()
	{
		return $this->hasMany(FyStatistical::class, 'user_id');
	}
}

