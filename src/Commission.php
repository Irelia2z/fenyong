<?php

namespace WinTop;

use WinTop\Models\FyStatistical;
use WinTop\Models\FyUser;
use WinTop\Models\FyCommission;
use WinTop\Models\FyUser as UserModel;
use WinTop\Models\FyRelation;
use WinTop\Models\FyWithdrawal;

class Commission
{
	/**
	 * 用户加入分佣用户体系
	 * @param int $uid
	 * @param int $pid
	 *
	 * @throws \Exception
	 */
	public function add(int $uid, int $pid = 0)
	{
		if ($pid === 0) {
			FyRelation::query()
				->create([
					'ancestor_id'   => $uid,
					'descendant_id' => $uid,
					'distance'      => 0
				]);

			return;
		}

		$relations = FyRelation::query()
			->where('descendant_id', $pid)
			->get();

		$relationArr[] = [
			'ancestor_id'   => $uid,
			'descendant_id' => $uid,
			'distance'      => 0
		];
		foreach ($relations as $item) {
			$relationArr[] = [
				'ancestor_id'   => $item->ancestor_id,
				'descendant_id' => $uid,
				'distance'      => $item->distance + 1
			];
		}

		FyRelation::query()
			->insert($relationArr);
	}

	/**
	 * 消费
	 * @param int   $uid
	 * @param float $amount
	 *
	 * @throws \Exception
	 */
	public function consume(int $uid, float $amount)
	{
		$userInfo = UserModel::query()
			->with([
				'ancestor' => function($query) {
					return $query->where([
						['distance', '>', 0],
						['distance', '<=', 3]
					]);
				},
				'group'
			])
			->where('id', $uid)
			->first();

		if ($userInfo === null) {
			throw new \Exception("uid=$uid error");
		}

		if ($userInfo->ancestor->isEmpty()) {
			return ;
		}

		$amountArr = [];
		foreach($userInfo->ancestor as $item) {
			$time = date('Y-m-d H:i:s');
			$tmp = [
				'level_1' => 0,
				'level_2' => 0,
				'level_3' => 0,
				'user_id' => $item->ancestor_id,
				'timestamp_create' => $time,
				'timestamp_update' => $time,
			];
			switch ($item->distance) {
				case 1:
					$tmp['level_1'] = $amount*($userInfo->group->rebate_level_1);
					break;
				case 2:
					$tmp['level_2'] = $amount*$userInfo->group->rebate_level_2;
					break;
				case 3:
					$tmp['level_3'] = $amount*$userInfo->group->rebate_level_3;
			}

			$amountArr[] = $tmp;
		}
		Commission::query()
			->insert($amountArr);
	}

	/**
	 * 获取子用户及佣金比例
	 * @param $uid
	 *
	 * @return array
	 * [
	 *      'level_1' => [[uid=1,rebate=0.1],[]],
	 *      'level_2' => [[uid=2,rebate=0.1],[]],
	 *      'level_3' => [[uid=3,rebate=0.1],[]],
	 * ]
	 */
	public function getDescendant($uid)
	{
		$relations = FyRelation::query()
			->with([
				'subUser',
				'subUser.group'
			])
			->where([
				['ancestor_id', '=', $uid],
				['distance', '>', 0],
				['distance', '<=', 3]
			])
			->get();

		$relationArr['level_1'] = [];
		$relationArr['level_2'] = [];
		$relationArr['level_3'] = [];
		foreach ($relations as $relation) {
			switch ($relation->distance) {
				case 1:
					$relationArr['level_1'][] = [
						'uid' => $relation->descendant_id,
						'rebate' => $relation->subUser->group->rebate_level_1
					];
					break;
				case 2:
					$relationArr['level_2'][] = [
						'uid' => $relation->descendant_id,
						'rebate' => $relation->subUser->group->rebate_level_2
					];
					break;
				case 3:
					$relationArr['level_3'][] = [
						'uid' => $relation->descendant_id,
						'rebate' => $relation->subUser->group->rebate_level_3
					];
					break;
			}
		}
		return $relationArr;
	}

	/**
	 * @param $uid
	 *
	 * @return array
	 */
	public function withdrawalList($uid)
	{
		return FyWithdrawal::query()
			->where('user_id', $uid)
			->get()
			->toArray();
	}

	public function list($uid)
	{
		return FyStatistical::query()
			->where('user_id', $uid)
			->get()
			->toArray();
	}

	/**
	 * @param $uid
	 *
	 * @return array
	 */
	public function detail($uid)
	{
		$userInfo = FyUser::query()
			->with([
				'withdrawal',
				'commission'
			])
			->where('id', $uid)
			->first();
		$commissionSum = $userInfo->commission->sum('level_1') +
			$userInfo->commission->sum('level_2') +
			$userInfo->commission->sum('level_3');
		$withdrawalSum = $userInfo->withdrawal->sum('amount');

		return [
			'commission_sum' => $commissionSum,
			'commission_withdraw' => $withdrawalSum,
			'commission' => $commissionSum - $withdrawalSum
		];
	}
}
