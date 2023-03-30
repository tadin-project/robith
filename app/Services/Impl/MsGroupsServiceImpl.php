<?php

namespace App\Services\Impl;

use App\Models\GroupMenus;
use App\Models\MsGroups;
use App\Services\MsGroupsService;
use Illuminate\Support\Facades\DB;

class MsGroupsServiceImpl implements MsGroupsService
{
    private $id_ms_menus = 5;
    /**
     * @param $id
     * @return array
     */
    private function __cekData($id): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $group = MsGroups::find($id);
            if (!$group) {
                $res = [
                    'status' => false,
                    'msg' => 'Data tidak ditemukan!',
                ];
            }
            $res['data'] = $group;
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param string $where
     * @return array
     */
    public function getTotal(string $where): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $qtotal = "SELECT
                            count(mg.group_id) as total
                        from
                            ms_groups mg
                        left join (
                            select
                                count(*) as tot_menu,
                                group_id
                            from
                                group_menus
                            group by
                                group_id ) gm on gm.group_id = mg.group_id
                        where
                            0 = 0 $where";
            $total = DB::select($qtotal);
            $res['total'] = $total[0]->total;
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param array $cols
     * @return array
     */
    public function getData(string $where = "", string $order = "", string $limit = "", array $cols = []): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            if (count($cols) == 0) {
                $cols = [
                    "mg.group_id",
                    "mg.group_kode",
                    "mg.group_nama",
                    "mg.group_status",
                    'coalesce(gm.tot_menu,0) as tot_menu',
                ];
            }

            $slc = implode(',', $cols);
            $qdata = "SELECT
                            $slc
                        from
                            ms_groups mg
                        left join (
                            select
                                count(*) as tot_menu,
                                group_id
                            from
                                group_menus
                            group by
                                group_id ) gm on gm.group_id = mg.group_id
                        where
                            0 = 0 $where
                        $order $limit";
            $data = DB::select($qdata);
            $res['data'] = $data;
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param $req
     * @return array
     */
    public function validateData($req): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            if (gettype($req) == "array") {
                if ($req['act'] != 'add' && $req['act'] != 'edit') {
                    $res = [
                        'status' => false,
                        'msg' => 'Request tidak dikenal!',
                    ];
                    return $res;
                }

                if (empty($req['group_kode']) || empty($req['group_nama'])) {
                    $res = [
                        'status' => false,
                        'msg' => 'Kode dan nama grup tidak boleh kosong!',
                    ];
                    return $res;
                }
            } else {
                if ($req->act != 'add' && $req->act != 'edit') {
                    $res = [
                        'status' => false,
                        'msg' => 'Request tidak dikenal!',
                    ];
                    return $res;
                }

                if (empty($req->group_kode) || empty($req->group_nama)) {
                    $res = [
                        'status' => false,
                        'msg' => 'Kode dan nama grup tidak boleh kosong!',
                    ];
                    return $res;
                }
            }
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param array $data
     * @return array
     */
    public function add(array $data): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $group = MsGroups::create($data);
            if (!isset($group->group_id)) {
                $res = [
                    'status' => false,
                    'msg' => 'Data gagal ditambahkan. Silahkan hubungi Admin!',
                ];
            }
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param $id
     * @param array $data
     * @return array
     */
    public function edit($id, array $data): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $cekGroup = $this->__cekData($id);
            if (!$cekGroup['status']) {
                return $cekGroup;
            }

            $group = $cekGroup['data'];

            $group->group_kode = $data["group_kode"];
            $group->group_nama = $data["group_nama"];
            if (!is_null($data["group_status"])) {
                $group->group_status = $data["group_status"];
            }

            $d = $group->save();
            if ($d <= 0) {
                $res = [
                    'status' => false,
                    'msg' => 'Gagal update data. Silahkan hubungi Admin!',
                ];
            }
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param $id
     * @return array
     */
    public function del($id): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $cekGroup = $this->__cekData($id);
            if (!$cekGroup['status']) {
                return $cekGroup;
            }

            $d = $cekGroup['data']->delete();
            if ($d < 0) {
                $res = [
                    'status' => false,
                    'msg' => 'Gagal hapus data. Silahkan hubungi Admin!',
                ];
            }
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param string $act
     * @param string $key
     * @param string $val
     * @param string $old
     * @return array
     */
    public function checkDuplicate(string $act, string $key, string $val, string $old = ""): string
    {
        $res = "true";

        try {
            $group = MsGroups::where($key, $val);
            if ($act == 'edit') {
                $group = $group->where($key, "!=", $old);
            }

            if ($group->count() > 0) {
                $res = "false";
            }
        } catch (\Throwable $th) {
            $res = "false";
        }

        return $res;
    }

    /**
     * @param $id
     * @return array
     */
    public function getById($id): array
    {
        $res = [
            'status' => true,
            'msg' => "",
        ];

        try {
            $res = $this->__cekData($id);
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param $groupId
     * @param $parentMenuId
     * @return array
     */
    public function getAkses($groupId, $parentMenuId): array
    {
        $res = [
            'status' => true,
            'msg' => "",
        ];

        try {
            if (empty($groupId)) {
                $res = [
                    'status' => false,
                    'msg' => "Id hak akses diperlukan!",
                ];
                return $res;
            }

            if ($parentMenuId == '#' || trim($parentMenuId) == '') {
                $parentMenuId = '0';
            }

            $sql = "SELECT
                        mm.*,
                        case
                            when gm.menu_id is not null then 1
                            else 0
                        end as checked
                    from
                        ms_menus mm
                    left join group_menus gm on
                        mm.menu_id = gm.menu_id
                        and gm.group_id = $groupId
                    where
                        mm.parent_menu_id = $parentMenuId
                        and mm.menu_id != $this->id_ms_menus
                    order by
                        menu_kode";
            // echo $sql;
            $dt = DB::select($sql);

            $data = [];
            foreach ($dt as $v) {
                $d = [
                    "id" => $v->menu_id,
                    "menu_id" => $v->menu_id,
                    "text" => $v->menu_nama . ($v->menu_status != 1 ? ' (NON AKTIF)' : ''),
                    "children" => false,
                    "state" => [
                        "opened" => true,
                    ],
                    "a_attr" => $v,
                    "li_attr" => $v,
                ];

                if ($parentMenuId == '#') {
                    $d['type'] = 'root';
                }

                $child_data = DB::select(
                    "SELECT
                        count(x.menu_id) as tot,
                        count(x.group_id > 0) as checked
                    from
                        (
                        select
                            mm.menu_id,
                            cgm.group_id 
                        from
                            ms_menus mm
                        left join group_menus cgm on
                            mm.menu_id = cgm.menu_id
                            and cgm.group_id = $groupId
                        where
                            0 = 0
                            and mm.menu_id != $this->id_ms_menus
                            and mm.menu_kode like '$v->menu_kode%'
                            and mm.menu_kode != '$v->menu_kode') x"
                );

                if ($child_data[0]->tot > 0) {
                    $d['children'] = true;
                }

                if ($v->checked == 1 && $child_data[0]->tot == $child_data[0]->checked) {
                    $d['state']['selected'] = true;
                } else {
                    $d['state']['selected'] = false;
                }

                $data[] = $d;
            }

            $res['data'] = $data;
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param $groupId
     * @return array
     */
    public function delAkses($groupId): array
    {
        $res = [
            'status' => true,
            'msg' => "",
        ];

        try {
            $q = GroupMenus::where('group_id', $groupId)->where('menu_id', '!=', $this->id_ms_menus)->delete();
            if ($q < 0) {
                $res = [
                    'status' => false,
                    'msg' => "Gagal hapus akses menu. Silahkan hubungi Admin",
                ];
            }
            $res['data'] = $q;
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param array $data
     * @return array
     */
    public function addAkses(array $data): array
    {
        $res = [
            'status' => true,
            'msg' => "",
        ];

        try {
            $d = GroupMenus::insert($data);
            if (!$d) {
                $res = [
                    'status' => false,
                    'msg' => "Gagal menambahkan akses menu. Silahkan hubungi Admin!",
                ];
            }
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param $groupId
     * @param array $listMenuId
     * @return array
     */
    public function saveAkses($groupId, $listMenuId): array
    {
        $res = [
            'status' => true,
            'msg' => "",
        ];

        try {
            DB::beginTransaction();
            if (empty($groupId)) {
                $res = [
                    'status' => false,
                    'msg' => "Id hak akses diperlukan!",
                ];
                return $res;
            }

            $listMenuId = !empty($listMenuId) ? $listMenuId : [];
            if (count($listMenuId) <= 0) {
                $res = [
                    'status' => false,
                    'msg' => "Pilih minimal 1 menu!",
                ];
                return $res;
            }

            $q = $this->delAkses($groupId);
            if (!$q['status']) {
                return $q;
            }

            $data = [];

            foreach ($listMenuId as $v) {
                $data[] = [
                    'group_id' => $groupId,
                    'menu_id' => $v,
                ];
            }

            $q = $this->addAkses($data);

            if (!$q["status"]) {
                $res = $q;
                return $res;
            }

            DB::commit();
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }
}
