<?php

namespace App\Models;

use CodeIgniter\Model;

class GeneralModel extends Model
{
    protected $db;
    protected $useTimestamps = 'true';

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function read($param)
    {
        if (is_array($param['table'])) {
            $n = 1;
            foreach ($param['table'] as $tab => $on) {
                if ($n == 1) {
                    $builder = $this->db->table($tab);
                }
                $n += 1;
            }

            $n = 1;
            foreach ($param['table'] as $tab => $on) {
                if ($n > 1) {
                    if (is_array($on)) $builder->join($tab, $on[0], $on[1]);
                    else $builder->join($tab, $on);
                }
                $n += 1;
            }
        } else {
            $builder = $this->db->table($param['table']);
        }

        if (!empty($param['select'])) $builder->select($param['select'], false);

        if (!empty($param['where'])) {
            foreach ($param['where'] as $w => $an) {
                if (is_null($an)) {
                    $builder->where($w, null, false);
                } else {
                    $builder->where($w, $an);
                }
            }
        }

        if (!empty($param['order'])) $builder->orderBy($param['order']);
        if (!empty($param['group'])) $builder->groupBy($param['group']);

        if (!empty($param['limit']) && !empty($param['offset'])) $builder->limit($param['limit'], $param['offset']);
        if (!empty($param['limit']) && empty($param['offset'])) $builder->limit($param['limit']);

        if (!empty($param['cari'])) {
            $srch = array();
            foreach ($param['cari'] as $sc => $vl) {
                if ($vl != NULL) $srch[] = $sc . " LIKE '%" . $vl . "%'";
            }
            if (count($srch) > 0) $builder->where('(' . implode(' OR ', $srch) . ')', null, false);
        }

        return $builder->get();
    }

    public function countData($param)
    {
        if (is_array($param['table'])) {
            $n = 1;
            foreach ($param['table'] as $tab => $on) {
                if ($n == 1) {
                    $builder = $this->db->table($tab);
                }
                $n += 1;
            }

            $n = 1;
            foreach ($param['table'] as $tab => $on) {
                if ($n > 1) {
                    if (is_array($on)) $builder->join($tab, $on[0], $on[1]);
                    else $builder->join($tab, $on);
                }
                $n += 1;
            }
        } else {
            $builder = $this->db->table($param['table']);
        }

        if (!empty($param['select'])) $builder->select($param['select'], false);

        if (!empty($param['where'])) {
            foreach ($param['where'] as $w => $an) {
                if (is_null($an)) {
                    $builder->where($w, null, false);
                } else {
                    $builder->where($w, $an);
                }
            }
        }

        if (!empty($param['order'])) $builder->orderBy($param['order']);
        if (!empty($param['group'])) $builder->groupBy($param['group']);

        if (!empty($param['limit']) && !empty($param['offset'])) $builder->limit($param['limit'], $param['offset']);
        if (!empty($param['limit']) && empty($param['offset'])) $builder->limit($param['limit']);

        if (!empty($param['cari'])) {
            $srch = array();
            foreach ($param['cari'] as $sc => $vl) {
                if ($vl != NULL) $srch[] = $sc . " LIKE '%" . $vl . "%'";
            }
            if (count($srch) > 0) $builder->where('(' . implode(' OR ', $srch) . ')', null, false);
        }

        return $builder->countAllResults();
    }

    public function simpan_data($par, $data = null, $column = null, $id = null)
    {
        $builder = $this->db->table($par['table']);
        if (is_array($par)) {
            if (!empty($par['where'])) {
                $builder->where($par['where']);
                return $builder->update($par['data']);
            } else {
                $builder->insert($par['data']);
                return $this->db->insertID();
            }
        } else {
            if (!empty($id)) {
                $builder->where($column, $id);
                $builder->update($par, $data);
            } else {
                $builder->insert($par, $data);
                return $this->db->insertID();
            }
        }
    }

    public function simpan_banyak($par, $data = null, $column = null, $id = null)
    {
        $builder = $this->db->table($par['table']);
        if (is_array($par)) {
            if (!empty($par['where'])) {
                $builder->where($par['where']);
                return $builder->insertBatch($par['data']);
            } else {
                $builder->insertBatch($par['data']);
                return $this->db->insertID();
            }
        } else {
            if (!empty($id)) {
                $builder->where($column, $id);
                $builder->insertBatch($par, $data);
            } else {
                $builder->insertBatch($par, $data);
                return $this->db->insertID();
            }
        }
    }

    public function hapus($table, $column = null, $id = null)
    {
        if (is_array($table)) {
            $builder = $this->db->table($table['table']);
            foreach ($table['where'] as $w => $an) {
                if (is_null($an)) $builder->where($w, null, false);
                else $builder->where($w, $an);
            }
            return $builder->delete();
        } else {
            $builder = $this->db->table($table);
            if (!empty($column)) {
                if (is_array($column)) $builder->where($column);
                else $builder->where($column, $id);
            }
            return $builder->delete();
        }
    }

    public function combo_box($param)
    {
        $combo = false;
        $data_combo = $this->read($param);
        if (@$param['pilih'] != "-") $combo = array('' => !empty($param['pilih']) ? $param['pilih'] : '-- Pilih --');
        foreach ($data_combo->getResult() as $row) {
            $valueb = array();
            foreach ($param['val'] as $v) {
                if (is_array($v)) {
                    if ($v[0] == "(") $valueb[] = "(" . $row->$v[1] . ")";
                } else {
                    $valueb[] = $row->$v;
                }
            }
            $keyb = array();
            if (is_array($param['key'])) {
                foreach ($param['key'] as $k) {
                    $keyb[] = (strlen($row->$k) > 100) ? substr($row->$k, 0, 100) . ' ...' : $row->$k;
                }
            }
            $keyv = is_array($param['key']) ? implode("|", $keyb) : $row->$param['key'];

            $combo[$keyv] = implode(" ", $valueb);
        }
        return $combo;
    }
}
