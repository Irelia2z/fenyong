<?php
namespace WinTop\Models;

use Illuminate\Database\Eloquent\Model;

class FyCommission extends Model
{
	protected $table = 'fy_commission';
	protected $guarded = ['id'];
	public const UPDATED_AT =  'timestamp_update';
	public const CREATED_AT = 'timestamp_create';
}
