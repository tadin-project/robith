<?php

namespace App\Services\Impl;

use App\Models\AppSettings;
use App\Models\MsKategoriUsaha;
use App\Models\MsUsers;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Support\Facades\DB;

/**
 * Summary of TenantServiceImpl
 */
class TenantServiceImpl implements TenantService
{
    /**
     * Summary of __id_tenant
     * @var
     */
    private $__id_tenant;
    /**
     * Summary of __construct
     */
    public function __construct()
    {
        $appSetting = AppSettings::get();
        foreach ($appSetting as $v) {
            if ($v->as_key == "id_tenant") {
                $this->__id_tenant = $v->as_value ? $v->as_value : $v->as_default;
            }
        }
    }

    /**
     * Summary of getUsers
     * @param string $act
     * @param string $oldUser
     * @return array
     */
    public function getUsers(string $act = 'add', string $oldUser = "0"): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $user = MsUsers::where("user_status", true)
                ->where("group_id", $this->__id_tenant)
                ->whereNotIn("user_id", function ($q) use ($act, $oldUser) {
                    $q->select("user_id")
                        ->from("tenant");
                    if ($act == "edit") {
                        $q->where("user_id", "!=", $oldUser);
                    }
                })
                ->orderBy("user_fullname", "asc")->get();
            $data = [];
            if ($user->count() > 0) {
                foreach ($user as $v) {
                    $data[] = [
                        "user_id" => $v->user_id,
                        "user_name" => $v->user_name,
                        "user_fullname" => $v->user_fullname,
                    ];
                }
            }

            $res["data"] = $data;
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * Summary of getKategoriUsaha
     * @return array
     */
    public function getKategoriUsaha(): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $ku = MsKategoriUsaha::where("mku_status", true)
                ->orderBy("mku_nama", "asc")->get();
            $data = [];
            if ($ku->count() > 0) {
                foreach ($ku as $v) {
                    $data[] = [
                        "id" => $v->mku_id,
                        "nama" => $v->mku_nama,
                    ];
                }
            }

            $res["data"] = $data;
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * Summary of __cekData
     * @param string $id
     * @return array
     */
    private function __cekData(string $id): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $dt = Tenant::find($id);
            if (!$dt) {
                $res = [
                    'status' => false,
                    'msg' => 'Data tidak ditemukan!',
                ];
            }
            $res['data'] = $dt;
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * Summary of getTotal
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
                            count(t.tenant_id) as total
                        from
                            tenant t
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
     * Summary of getData
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
                    "t.tenant_id",
                    "t.tenant_nama",
                    "t.tenant_desc",
                    "t.tenant_status",
                    "t.user_id",
                    "t.mku_id",
                ];
            }

            $slc = implode(',', $cols);
            $qdata = "SELECT
                            $slc
                        from
                            tenant t
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
     * Summary of validateData
     * @param mixed $req
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

                if (empty($req['tenant_desc']) || empty($req['tenant_nama']) || empty($req['user_id']) || empty($req['mku_id'])) {
                    $res = [
                        'status' => false,
                        'msg' => 'Nama, deskripsi, user, dan kategori usaha tidak boleh kosong!',
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

                if (empty($req->tenant_desc) || empty($req->tenant_nama) || empty($req->user_id) || empty($req->mku_id)) {
                    $res = [
                        'status' => false,
                        'msg' => 'Nama, deskripsi, user, dan kategori usaha tidak boleh kosong!',
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
     * Summary of add
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
            $dt = Tenant::create($data);
            if (!isset($dt->tenant_id)) {
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
     * Summary of edit
     * @param mixed $id
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
            $cekData = $this->__cekData($id);
            if (!$cekData['status']) {
                return $cekData;
            }

            $dt = $cekData['data'];

            $dt->tenant_nama = $data["tenant_nama"];
            $dt->tenant_desc = $data["tenant_desc"];
            $dt->user_id = $data["user_id"];
            $dt->mku_id = $data["mku_id"];
            if (!is_null($data["tenant_status"])) {
                $dt->tenant_status = $data["tenant_status"];
            }

            $d = $dt->save();
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
     * Summary of del
     * @param mixed $id
     * @return array
     */
    public function del($id): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $cekData = $this->__cekData($id);
            if (!$cekData['status']) {
                return $cekData;
            }

            $d = $cekData['data']->delete();
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
     * Summary of getById
     * @param mixed $id
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
}
