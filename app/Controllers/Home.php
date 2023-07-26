<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index()
    {
        return view('welcome_message');
    }

    public function savePeserta($id = NULL)
    {
        $in = $this->request->getVar();
        $par['table'] = 'peserta';
        $par['data'] = [
            'nama' => $in['nama'],
            'jenis_lomba' => $in['jenis_lomba'],
        ];
        $qry = $this->mdl->simpan_data($par);
        if ($qry) {
            session()->setFlashdata('ok', 'Data berhasil disimpan.');
        } else {
            session()->setFlashdata('fail', 'Data gagal disimpan.');
        }
        return redirect()->to('');
    }
}
