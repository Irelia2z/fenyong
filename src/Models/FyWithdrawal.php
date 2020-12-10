<?php
namespace WinTop\Models;
use Illuminate\Database\Eloquent\Model;

class FyWithdrawal extends Model
{
	protected $table = 'fy_withdrawal';
	public const CREATED_AT = 'timestamp_create';
	public const UPDATED_AT = 'timestamp_update';
	protected $guarded = ['id'];
}