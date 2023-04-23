<?php

namespace App\Services\Impl;

use App\Models\MsMenus;
use App\Services\MsMenusService;
use Illuminate\Support\Facades\DB;

class MsMenusServiceImpl implements MsMenusService
{
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
            $user = MsMenus::find($id);
            if (!$user) {
                $res = [
                    'status' => false,
                    'msg' => 'Data tidak ditemukan!',
                ];
            }
            $res['data'] = $user;
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
                            count(mm.menu_id) as total
                        from
                            ms_menus mm
                        left join ms_menus as p on
                            p.menu_id = mm.parent_menu_id 
                        left join (
                            select
                                count(mm2.menu_id) tot_child,
                                mm2.parent_menu_id
                            from 
                                ms_menus mm2
                            group by
                                mm2.parent_menu_id ) as c on
                            c.parent_menu_id = mm.menu_id
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
                    "mm.menu_id",
                    "mm.menu_kode",
                    "mm.menu_nama",
                    "mm.menu_type",
                    "mm.menu_link",
                    "mm.menu_ikon",
                    "p.menu_nama as parent_menu_nama",
                    "mm.menu_status",
                    "coalesce(c.tot_child) as tot_child",
                ];
            }

            // DB::enableQueryLog();
            $slc = implode(',', $cols);
            $qdata = "SELECT
                            $slc
                        from
                            ms_menus mm
                        left join ms_menus as p on
                            p.menu_id = mm.parent_menu_id 
                        left join (
                            select
                                count(mm2.menu_id) tot_child,
                                mm2.parent_menu_id
                            from 
                                ms_menus mm2
                            group by
                                mm2.parent_menu_id ) as c on
                            c.parent_menu_id = mm.menu_id
                        where
                            0 = 0 $where
                        $order $limit";
            $data = DB::select($qdata);

            // dd(DB::getQueryLog()); // Show results of log
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

                if (empty($req['menu_kode']) || empty($req['menu_nama'])) {
                    $res = [
                        'status' => false,
                        'msg' => 'Kode dan nama menu tidak boleh kosong!',
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

                if (empty($req->menu_kode) || empty($req->menu_nama)) {
                    $res = [
                        'status' => false,
                        'msg' => 'Kode dan nama menu tidak boleh kosong!',
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
            $user = MsMenus::create($data);
            if (!isset($user->menu_id)) {
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
            $cek = $this->__cekData($id);
            if (!$cek['status']) {
                return $cek;
            }

            $menu = $cek['data'];

            $menu->menu_kode = $data["menu_kode"];
            $menu->menu_nama = $data["menu_nama"];
            $menu->menu_link = $data["menu_link"];
            $menu->menu_type = $data["menu_type"];
            $menu->menu_ikon = $data["menu_ikon"];
            $menu->parent_menu_id = $data["parent_menu_id"];
            $menu->menu_status = $data["menu_status"];

            $d = $menu->save();
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
            $cek = $this->__cekData($id);
            if (!$cek['status']) {
                return $cek;
            }

            $d = $cek['data']->delete();
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
            $dt = MsMenus::where($key, $val);
            if ($act == 'edit') {
                $dt = $dt->where($key, "!=", $old);
            }

            if ($dt->count() > 0) {
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
     * @return array
     */
    public function getOptParent(): array
    {
        $res = [
            'status' => true,
            'msg' => "",
        ];

        try {
            $data = MsMenus::orderBy("menu_kode", "asc")
                ->get();
            $res['data'] = $data;
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }
}
