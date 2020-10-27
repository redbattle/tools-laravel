<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JxBuilding;
use App\Models\JxHeating;
use App\Models\JxHeatingRule;
use App\Models\JxOrg;
use App\Models\JxRoom;
use App\Models\JxUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JiaxinController extends Controller
{
    // 查看小区
    public function getList(Request $request)
    {
        $r_name = $request->input('name');
        // 查询条件
        $where = [
            'status' => 1
        ];
        if (!is_null($r_name)) {
            $where['name'] = $r_name;
        }
        $lists = JxOrg::where($where)->orderBy('id', 'desc')->get();
        return self::ok([
            'lists' => $lists,
        ]);
    }

    // 更新小区应缴暖气费
    public function update(Request $request)
    {
        // $r_name = $request->input('name');
        $r_name = ''; // 小区名
        $where = [
            'status' => 1
        ];
        if (!is_null($r_name)) {
            $where['name'] = $r_name;
        } else {
            return self::err('小区不能为空');
        }
        $org = JxOrg::where($where)->orderBy('id', 'desc')->first();
        if ($org) {
            DB::beginTransaction();
            $error = 0;
            $rule = JxHeatingRule::where(['organization_id' => $org->id, 'status' => 1])->first();
            $builds = JxBuilding::where(['organization_id' => $org->id, 'status' => 1])->get();
            foreach($builds as $build){
                $units = JxUnit::where(['building_id' => $build->id, 'status' => 1])->get();
                foreach($units as $unit){
                    $rooms = JxRoom::where(['unit_id' => $unit->id, 'status' => 1])->get();
                    foreach($rooms as $room){
                        $save_heating = JxHeating::where(['room_id' => $room->id, 'status' => 1, 'year' => 2020])->update(['due' => $room->area * $rule->price]);
                        if (!$save_heating) {
                            DB::rollBack();
                            $error++;
                            return self::err('更新失败: '.$room->id);
                        }
                    }
                }
            }
            if($error === 0){
                DB::commit();
                return self::ok([], $r_name.'成功');
            } else {
                DB::rollBack();
                return self::err('修改失败');
            }
        } else {
            return self::err('暂无小区数据');
        }
    }

}
