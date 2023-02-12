<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\PostModel;

class Post extends Controller
{
    /**
     * @var PostModel
     */
    protected $postModel;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->postModel = new PostModel();
    }

    /**
     * index function
     */
    public function index()
    {
        //pager initialize
        $pager = \Config\Services::pager();

        $data = [
            'posts' => $this->postModel->paginate(2, 'post'),
            'pager' => $this->postModel->pager,
        ];

        return view('post-index', $data);
    }

    /**
     * create function
     */
    public function create()
    {
        return view('post-create');
    }

    /**
     * store function
     */
    public function store()
    {
        //validasi
        if (!$this->validate([
            'title' => [
                'rules' => 'required|alpha_numeric_space',
                'label' => 'Judul Post',
            ],
            'content' => [
                'rules' => 'required',
                'label' => 'Konten Post',
            ],
        ])) {
            //render view with error validation message
            return view('post-create', [
                'validation' => $this->validator
            ]);
        }

        //insert data into database
        $this->postModel->insert([
            'title'   => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
        ]);

        //flash message
        session()->setFlashdata('message', 'Post Berhasil Disimpan');

        return redirect()->to(base_url('post'));
    }

    public function edit($id)
    {
        $data = [
            'post' => $this->postModel->find($id),
        ];

        return view('post-edit', $data);
    }

    public function update($id)
    {
        //validasi
        if (!$this->validate([
            'title' => [
                'rules' => 'required|alpha_numeric_space',
                'label' => 'Judul Post',
            ],
            'content' => [
                'rules' => 'required',
                'label' => 'Konten Post',
            ],
        ])) {
            //render view with error validation message
            return view('post-edit', [
                'validation' => $this->validator
            ]);
        }

        //update data into database
        $this->postModel->update($id, [
            'title'   => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
        ]);

        //flash message
        session()->setFlashdata('message', 'Post Berhasil Diupdate');

        return redirect()->to(base_url('post'));
    }

    public function delete($id)
    {
        //cek apakah post dengan id tersebut ada atau tidak
        $post = $this->postModel->find($id);
        if (!$post) {
            //jika tidak ada, tampilkan pesan error
            session()->setFlashdata('error', 'Post tidak ditemukan');
            return redirect()->to(base_url('post'));
        }

        //hapus data dari database
        $this->postModel->delete($id);

        //tampilkan pesan sukses
        session()->setFlashdata('message', 'Post Berhasil Dihapus');

        return redirect()->to(base_url('post'));
    }
}
