<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Acs extends CI_Controller
{

    public $data = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_main');
        $this->load->model('m_acs');
        $this->load->library('pagination');
        $this->data['setting'] = $this->m_main->getWhereRow('setting', 'is_active=1');

        $group_id = $this->session->userdata('group_id');
        $this->data['list_menus_access'] = $this->m_main->getAllJoinWhere(
            "user",
            "group_access_permission",
            "menus",
            "id as user_id",
            "group_id as group_access_permission_group_id",
            "*",
            "group_access_permission.group_id=user.group_id",
            "group_access_permission.menu_id=menus.id",
            $group_id
        );
        foreach ($this->data['list_menus_access'] as $k => $menu) {
            $this->data['list_menus_access'][$k]->parent_menu = $this->m_main->getWhere('menus', 'menus.parent_id=' . $menu->id);
        }

        $this->data['list_menus'] = $this->m_main->getWhere('menus', 'parent_id=0');
        foreach ($this->data['list_menus'] as $k => $menu) {
            $this->data['list_menus'][$k]->parent_menu = $this->m_main->getWhere('menus', 'menus.parent_id=' . $menu->id);
        }

        $userID = $this->session->userdata('user_id');
        $this->data['list_user'] = $this->m_main->getWhere('user', 'id !=' . $userID);
        $this->data['groups'] = $this->m_main->getAll('groups');
        foreach ($this->data['list_user'] as $k => $user) {
            $this->data['list_user'][$k]->groups = $this->m_main->getWhereJoin('user', 'groups', '*', 'name', 'user.group_id=groups.id', 'user.id=' . $user->id);
        }
        // $this->data['csrf'] = $this->_get_csrf_nonce();
        // $this->output->enable_profiler(TRUE);
    }

    public function index()
    {
        $this->template->load('template', 'acs/menus', $this->data);
        // $this->load->view('acs/home', $this->data);
        // $this->load->view('_partials/footer');
    }

    public function menu()
    {
        $this->data['list_menus'] = $this->m_main->getAll('tblmenus');
        $this->template->load('template', 'acs/menus', $this->data);
    }

    public function orders()
    {
        $this->data['list_orders'] = $this->m_main->getAll('tblorders');
        $this->template->load('template', 'acs/orders', $this->data);
    }
    public function create_orders()
    {
        $this->data['list_suku'] = $this->m_main->getAll('tblsuku');
        $this->data['list_agama'] = $this->m_main->getAll('tblagama');
        $this->data['list_patients'] = $this->m_main->getAllJoin('tblpasien', 'tblsuku', 'tblagama', '*', 'suku', 'agama', 'tblpasien.idsuku=tblsuku.idsuku', 'tblpasien.idagama=tblagama.idagama');
        $this->data['list_menus'] = $this->m_main->getAll('tblmenus');
        $this->template->load('template', 'acs/create-orders', $this->data);
    }

    public function patients()
    {
        $this->data['list_suku'] = $this->m_main->getAll('tblsuku');
        $this->data['list_agama'] = $this->m_main->getAll('tblagama');
        $this->data['list_patients'] = $this->m_main->getAllJoin('tblpasien', 'tblsuku', 'tblagama', '*', 'suku', 'agama', 'tblpasien.idsuku=tblsuku.idsuku', 'tblpasien.idagama=tblagama.idagama');
        // var_dump($this->data['data']);
        $this->template->load('template', 'acs/patients', $this->data);
    }

    public function ajax_list()
    {
        $list = $this->m_acs->get_datatables();
        // var_dump($this->data['list']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $patient) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $patient->no_rm;
            $row[] = $patient->nama;
            $row[] = $patient->tgl_lahir;
            $row[] = $patient->alamat;
            $row[] = $patient->sex == 'L' ? 'Laki- Laki' : 'Perempuan';
            $row[] = $patient->suku;
            $row[] = $patient->agama;
            //add html for action
            $row[] = '
                    <a href="javascript:void(0);" class="e_patient" data-id="' . $patient->id . '" data-role="' . $patient->nama . '" data-toggle="tooltip" data-placement="top" title="Edit patient" > 
                    <span class="fa fa-edit"></span>
                    </a>
                    <a href="javascript:void(0);" class="d_patient" data-id="' . $patient->id . '" data-toggle="tooltip" data-placement="top" title="Delete patient">
                    <span class="fa fa-trash"></span>
                    </a>';
            // '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_patient(' . "'" . $patient->id . "'" . ')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
            // <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_patient(' . "'" . $patient->id . "'" . ')"><i class="glyphicon glyphicon-trash"></i> Delete</a>
            // <a class="btn btn-sm btn-default" href="javascript:void(0)" title="View" onclick="view_patient(' . "'" . $patient->id . "'" . ')"><i class="glyphicon glyphicon-file"></i> View</a>';
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_acs->count_all(),
            "recordsFiltered" => $this->m_acs->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function update($table)
    {

        if ($table == 'tblpasien') {
            $this->data = array(
                "no_rm" => $this->input->post('no_rm'),
                "nama" => $this->input->post('nama'),
                "alamat" => $this->input->post('alamat'),
                "sex" => $this->input->post('sex'),
                "tgl_lahir" => $this->input->post('tgl_lahir'),
                "idsuku" => $this->input->post('suku'),
                "idagama" => $this->input->post('agama')
            );
            $url = 'acs/patients';
        } else if ($table == 'tblmenus') {

            $config['upload_path'] = './assets/images/uploads/menus';
            $config['allowed_types'] = 'jpg|png|jpeg';
            $config['max_size']  = '1024';
            // $config['max_width'] = '500';
            // $config['max_height'] = '500';
            $config['remove_space'] = TRUE;
            $config['encrypt_name'] = TRUE;

            $url = 'acs/menu';
            $image_old = $this->input->post('image_old');
            $image = $this->input->post('image');
            $this->load->library('upload', $config);
            if (!$_FILES['image']['name']) {
                $image = $image_old;
            } else {
                if (!$this->upload->do_upload('image')) {
                    $this->session->set_flashdata('message', 'Image Display' . $this->upload->display_errors());
                    redirect($url);
                } else {
                    $image =  $this->upload->data('file_name');
                }
            }
            $this->data = array(
                "name" => $this->input->post('name'),
                "description" => $this->input->post('description'),
                "image" => $image
            );
        }

        $this->db->update($table, $this->data, array('id' => $this->input->post('id')));
        $this->session->set_flashdata('message', "Data Berhasil Diupdate");
        redirect($url);
        // var_dump($this->data);
    }


    public function add($table)
    {
        if ($table == 'tblpasien') {
            $this->data = array(
                "no_rm" => $this->input->post('no_rm'),
                "nama" => $this->input->post('nama'),
                "alamat" => $this->input->post('alamat'),
                "sex" => $this->input->post('sex'),
                "tgl_lahir" => $this->input->post('tgl_lahir'),
                "idsuku" => $this->input->post('suku'),
                "idagama" => $this->input->post('agama')
            );
            $url = 'acs/patients';
        } else if ($table == 'tblmenus') {
            $config['upload_path'] = './assets/images/uploads/menus';
            $config['allowed_types'] = 'jpg|png|jpeg|image|ico';
            $config['max_size']  = '1024';
            // $config['max_width'] = '500';
            // $config['max_height'] = '500';
            $config['remove_space'] = TRUE;
            $config['encrypt_name'] = TRUE;

            $url = 'acs/menu';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('image')) {
                $this->session->set_flashdata('message', 'Image Display' . $this->upload->display_errors());
                redirect($url);
            } else {
                $image =  $this->upload->data('file_name');
            }
            $this->data = array(
                "name" => $this->input->post('name'),
                "description" => $this->input->post('description'),
                "image" => $image
            );
        }
        if ($this->db->insert($table, $this->data)) {
            $this->session->set_flashdata('message', "Data Berhasil Disimpan");
        } else {
            $this->session->set_flashdata('message', "Data Gagal Disimpan");
        }
        redirect($url);
        // var_dump($this->data);
    }
    public function req_data($table, $id)
    {
        if ($id != 0) {
            $this->data = $this->m_main->getById($table, $id);
        } else {
            $this->data = ['id' => 0];
        }
        echo json_encode($this->data);
    }

    public function delete($table, $id)
    {
        $this->db->where('id', $id);
        $hapus = $this->db->delete($table);
        if ($hapus) {
            $this->session->set_flashdata('message', "Data Berhasil Dihapus");
        } else {
            echo json_encode(array('message' => 'Data Gagal Dihapus'));
        }
    }

    // public function get_all()
    // {
    //     $patient = $this->m_acs->getAllJoin('tblpasien', 'tblsuku', 'tblagama', '*', 'suku', 'agama', 'tblpasien.idsuku=tblsuku.idsuku', 'tblpasien.idagama=tblagama.idagama');
    //     $this->output->set_content_type('application/json')->set_output(json_encode($patient));
    // }
    // public function getSuku()
    // {
    //     $suku = $this->m_acs->getAll('tblsuku');
    //     $this->output->set_content_type('application/json')->set_output(json_encode($suku));
    // }

    // public function getAgama()
    // {
    //     $agama = $this->m_acs->getAll('tblagama');
    //     $this->output->set_content_type('application/json')->set_output(json_encode($agama));
    // }

    // public function insert()
    // {
    //     $postdata = json_decode(file_get_contents('php://input'), TRUE);
    //     if (isset($postdata['patient'])) {
    //         $no_rm = (isset($postdata['patient']['no_rm']) ? $postdata['patient']['no_rm'] : NULL);
    //         $no_reg = (isset($postdata['patient']['no_reg']) ? $postdata['patient']['no_reg'] : NULL);
    //         $nama = (isset($postdata['patient']['nama']) ? $postdata['patient']['nama'] : NULL);
    //         $idsuku = (isset($postdata['patient']['idsuku']) ? $postdata['patient']['idsuku'] : NULL);
    //         $idagama = (isset($postdata['patient']['idagama']) ? $postdata['patient']['idagama'] : NULL);
    //         if ($no_rm == NULL) {
    //             http_response_code(400);
    //             $this->output->set_content_type('application/json')->set_output(json_encode(['errors' => ["Name Field is required"]]));
    //         } else {
    //             $data = array(
    //                 'no_rm'   => $no_rm,
    //                 'no_reg'   => $no_reg,
    //                 'nama'    => $nama,
    //                 'idsuku'     => $idsuku,
    //                 'idagama'      => $idagama
    //             );
    //             $this->m_acs->insert('tblpasien', $data);
    //         }
    //     }
    // }
    // public function update()
    // {
    //     $postdata = json_decode(file_get_contents('php://input'), TRUE);
    //     if (isset($postdata['patient'])) {
    //         $id = (isset($postdata['patient']['id']) ? $postdata['patient']['id'] : NULL);
    //         $no_rm = (isset($postdata['patient']['no_rm']) ? $postdata['patient']['no_rm'] : NULL);
    //         $no_reg = (isset($postdata['patient']['no_reg']) ? $postdata['patient']['no_reg'] : NULL);
    //         $nama = (isset($postdata['patient']['nama']) ? $postdata['patient']['nama'] : NULL);
    //         $idsuku = (isset($postdata['patient']['idsuku']) ? $postdata['patient']['idsuku'] : NULL);
    //         $idagama = (isset($postdata['patient']['idagama']) ? $postdata['patient']['idagama'] : NULL);
    //         if ($no_rm == NULL) {
    //             http_response_code(400);
    //             $this->output->set_content_type('application/json')->set_output(json_encode(['errors' => ["Name Field is required"]]));
    //         } else {
    //             $data = array(
    //                 'no_rm'   => $no_rm,
    //                 'no_reg'   => $no_reg,
    //                 'nama'    => $nama,
    //                 'idsuku'     => $idsuku,
    //                 'idagama'      => $idagama,
    //                 'id'      => $id
    //             );
    //             $this->m_acs->update($data);
    //         }
    //     }
    // }
    // public function delete()
    // {
    //     $postdata = json_decode(file_get_contents("php://input"));
    //     if (isset($postdata) && !empty($postdata)) {
    //         $id = (int) $postdata->ID;
    //         $this->m_acs->delete($id);
    //     }
    // }
}
