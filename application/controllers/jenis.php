<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Jenis extends CI_Controller
{

	/**
	 * @author : Deddy Rusdiansyah,S.Kom
	 * @web : http://deddyrusdiansyah.blogspot.com
	 * @keterangan : Controller untuk halaman profil
	 **/

	public function index()
	{
		$cek = $this->session->userdata('logged_in');
		if (!empty($cek)) {
			$cari = $this->input->post('txt_cari');
			if (empty($cari)) {
				$where = ' ';
			} else {
				$where = " WHERE no_rek LIKE '%$cari%' OR nama_rek LIKE '%$cari%'";
			}

			$d['prg'] = $this->config->item('prg');
			$d['web_prg'] = $this->config->item('web_prg');

			$d['nama_program'] = $this->config->item('nama_program');
			$d['instansi'] = $this->config->item('instansi');
			$d['usaha'] = $this->config->item('usaha');
			$d['alamat_instansi'] = $this->config->item('alamat_instansi');


			$d['judul'] = "Rekening";

			//paging
			$page = $this->uri->segment(3);
			$limit = $this->config->item('limit_data');
			if (!$page) :
				$offset = 0;
			else :
				$offset = $page;
			endif;

			$text = "SELECT * FROM rekening $where ";
			$tot_hal = $this->app_model->manualQuery($text);

			$d['tot_hal'] = $tot_hal->num_rows();

			$config['base_url'] = site_url() . '/rekening/index/';
			$config['total_rows'] = $tot_hal->num_rows();
			$config['per_page'] = $limit;
			$config['uri_segment'] = 3;
			$config['next_link'] = 'Lanjut &raquo;';
			$config['prev_link'] = '&laquo; Kembali';
			$config['last_link'] = '<b>Terakhir &raquo; </b>';
			$config['first_link'] = '<b> &laquo; Pertama</b>';
			$this->pagination->initialize($config);
			$d["paginator"] = $this->pagination->create_links();
			$d['hal'] = $offset;


			$text = "SELECT * FROM jenis_simpan ORDER BY id_jenis ASC";
			$d['data'] = $this->app_model->manualQuery($text);

			$text = "SELECT * FROM rekening";
			$d['list'] = $this->app_model->manualQuery($text);


			$d['content'] = $this->load->view('jenis_simpanan/view', $d, true);
			$this->load->view('home', $d);
		} else {
			header('location:' . base_url());
		}
	}

	public function tambah()
	{
		$cek = $this->session->userdata('logged_in');
		if (!empty($cek)) {
			$d['prg'] = $this->config->item('prg');
			$d['web_prg'] = $this->config->item('web_prg');

			$d['nama_program'] = $this->config->item('nama_program');
			$d['instansi'] = $this->config->item('instansi');
			$d['usaha'] = $this->config->item('usaha');
			$d['alamat_instansi'] = $this->config->item('alamat_instansi');

			$d['judul'] = "Rekening";

			$text = "SELECT * FROM rekening";
			$d['list'] = $this->app_model->manualQuery($text);


			$d['content'] = $this->load->view('rekening/form', $d, true);
			$this->load->view('home', $d);
		} else {
			header('location:' . base_url());
		}
	}

	public function edit()
	{
		$cek = $this->session->userdata('logged_in');
		if (!empty($cek)) {
			/*
			$d['prg']= $this->config->item('prg');
			$d['web_prg']= $this->config->item('web_prg');
			
			$d['nama_program']= $this->config->item('nama_program');
			$d['instansi']= $this->config->item('instansi');
			$d['alamat_instansi']= $this->config->item('alamat_instansi');
			
			$d['judul'] = "Surat Perintah";
			$d['message'] = '';
			*/

			$id = $this->input->post('id');  //$this->uri->segment(3);
			$text = "SELECT * FROM rekening WHERE no_rek='$id'";
			$data = $this->app_model->manualQuery($text);
			//if($data->num_rows() > 0){
			foreach ($data->result() as $db) {
				$d['no_rek']		= $db->no_rek;
				$d['rek_induk']	= $db->induk;
				$d['nama_rek']	= $db->nama_rek;
				echo json_encode($d);
			}
			//}

			//$d['content'] = $this->load->view('rekening/tambah', $d, true);		
			//$this->load->view('home',$d);
		} else {
			header('location:' . base_url());
		}
	}

	public function hapus()
	{
		$cek = $this->session->userdata('logged_in');
		if (!empty($cek)) {
			$id = $this->uri->segment(3);
			$this->app_model->manualQuery("DELETE FROM jenis_simpan WHERE id_jenis='$id'");
			echo "<meta http-equiv='refresh' content='0; url=" . base_url() . "index.php/jenis'>";
		} else {
			header('location:' . base_url());
		}
	}

	public function simpan()
	{

		$cek = $this->session->userdata('logged_in');
		if (!empty($cek)) {

			$induk = $this->input->post('rek_induk');

			if ($induk != 0) {
				$level = $this->app_model->CariLevel($induk);
				$up['induk'] = $this->input->post('rek_induk');
				$up['level'] = $level + 1;
			} else {
				$up['induk'] = 0;
				$up['level'] = 0;
			}
			$up['no_rek'] = $this->input->post('no_rek');
			$up['nama_rek'] = $this->input->post('nama_rek');

			$id['no_rek'] = $this->input->post('no_rek');

			$data = $this->app_model->getSelectedData("rekening", $id);
			if ($data->num_rows() > 0) {
				$this->app_model->updateData("rekening", $up, $id);
				echo 'Update data Sukses';
			} else {
				$this->app_model->insertData("rekening", $up);
				echo 'Simpan data Sukses';
			}
		} else {
			header('location:' . base_url());
		}
	}

	//Tambah Data Jenis Simpanan
	public function proses_tambah_data()
	{

		$data = [
			"id_jenis" => getAutoNumber('jenis_simpan', 'id_jenis', '', 2),
			"jenis_simpanan" => $this->input->post('jenis_simpanan'),
			"jumlah" => $this->input->post('jumlah'),
			"kontrol_simpanan" => $this->input->post('kontrol_simpanan'),
			"kontrol_penarikan" => $this->input->post('kontrol_penarikan'),
			"kontrol_laporan" => $this->input->post('kontrol_laporan'),
		];

		$this->db->insert('jenis_simpan', $data);

		$this->session->set_flashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Data berhasil ditambahkan!</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>');
		redirect('jenis');
	}

	//Edit Data Rekening
	public function proses_edit_data()
	{
		$data = [
			"jenis_simpanan" => $this->input->post('jenis_simpanan'),
			"jumlah" => $this->input->post('jumlah'),
			"kontrol_simpanan" => $this->input->post('kontrol_simpanan'),
			"kontrol_penarikan" => $this->input->post('kontrol_penarikan'),
			"kontrol_laporan" => $this->input->post('kontrol_laporan'),
		];

		$this->db->where('id_jenis', $this->input->post('id_jenis'));
		$this->db->update('jenis_simpan', $data);

		$this->session->set_flashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Data berhasil diubah!</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>');
		redirect('jenis');
	}

	public function hapus_data($id)
	{
		$this->db->where('id_jenis', $id);
		$this->db->delete('jenis_simpan');

		$this->session->set_flashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Data berhasil dihapus!</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>');
		redirect('jenis');
	}
}

/* End of file profil.php */
/* Location: ./application/controllers/profil.php */