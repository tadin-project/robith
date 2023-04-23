<?php

namespace App\Services\Impl;

use App\Models\MsGroups;
use App\Models\MsUsers;
use App\Services\MsUsersService;
use Illuminate\Support\Facades\DB;

class MsUsersServiceImpl implements MsUsersService
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
            $user = MsUsers::find($id);
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
                            count(mu.user_id) as total
                        from
                            ms_users mu
                        inner join ms_groups mg on
                            mg.group_id = mu.group_id
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
                    "mu.user_id",
                    "mu.user_name",
                    "mu.user_fullname",
                    "mu.user_email",
                    "mg.group_nama",
                    "mu.user_status",
                ];
            }

            $slc = implode(',', $cols);
            $qdata = "SELECT
                            $slc
                        from
                            ms_users mu
                        inner join ms_groups mg on
                            mg.group_id = mu.group_id
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

                if (empty($req['user_name']) || empty($req['user_email']) || ($req['act'] == "add" && empty($req['user_password'])) || empty($req['group_id'])) {
                    $res = [
                        'status' => false,
                        'msg' => 'Username, email, password, dan hak akses tidak boleh kosong!',
                    ];
                    return $res;
                }

                if ($req['act'] == 'edit' && $req["is_ganti_pass"] && empty($req["user_password"])) {
                    $res = [
                        'status' => false,
                        'msg' => 'Password tidak boleh kosong!',
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

                if (empty($req->user_name) || empty($req->user_email) || ($req->act == "add" && empty($req->user_password)) || empty($req->group_id)) {
                    $res = [
                        'status' => false,
                        'msg' => 'Username, email, password, dan hak akses tidak boleh kosong!',
                    ];
                    return $res;
                }

                if ($req->act == 'edit' && $req->is_ganti_pass && empty($req->user_password)) {
                    $res = [
                        'status' => false,
                        'msg' => 'Password tidak boleh kosong!',
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
            $user = MsUsers::create($data);
            if (!isset($user->user_id)) {
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
            $cekUser = $this->__cekData($id);
            if (!$cekUser['status']) {
                return $cekUser;
            }

            $user = $cekUser['data'];

            $user->user_name = $data["user_name"];
            $user->user_fullname = $data["user_fullname"];
            $user->user_email = $data["user_email"];
            $user->group_id = $data["group_id"];
            if (!is_null($data["user_status"])) {
                $user->user_status = $data["user_status"];
            }

            if (array_key_exists("user_password", $data)) {
                $user->user_password = $data["user_password"];
            }

            $d = $user->save();
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
            $cekUser = $this->__cekData($id);
            if (!$cekUser['status']) {
                return $cekUser;
            }

            $d = $cekUser['data']->delete();
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
            $user = MsUsers::where($key, $val);
            if ($act == 'edit') {
                $user = $user->where($key, "!=", $old);
            }

            if ($user->count() > 0) {
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
     * @param bool $isRoot
     * @return array
     */
    public function getOptGroup(bool $isRoot = false): array
    {
        $res = [
            'status' => true,
            'msg' => "",
        ];

        try {
            $data = MsGroups::where("group_status", true);
            if (!$isRoot) {
                $data = $data->where("group_id", ">", 1);
            }
            $data = $data->orderBy("group_kode", "asc")
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
