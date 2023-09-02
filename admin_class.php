<?php
session_start();
ini_set('display_errors', 1);
class Action
{
	private $db;

	public function __construct()
	{
		ob_start();
		include 'db_connect.php';

		$this->db = $conn;
	}
	function __destruct()
	{
		$this->db->close();
		ob_end_flush();
	}

	function login()
	{



		extract($_POST);
		$type2 = array("", "admin", "faculty", "student");
		$type3 = array("", "ibsmain" => 1, "ibs1" => 2, "ibs2" => 3);

		$qry = $this->db->query("SELECT *,concat(firstname,' ',lastname) as name 
		FROM all_users where email = '" . $email . "' and password = '" . md5($password) . "' limit 1 ");
		// return $qry;

		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $key => $value) {
				if ($key != 'password' && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			if (isset($_SESSION['login_typeofuser'])) {
				$t = $_SESSION['login_typeofuser'];
				$login = $type3[$t];
			}
			// $login value of typeofuser 1 , 2, 3 
			$_SESSION['login_type'] = $login;
			$_SESSION['login_view_folder'] = $type2[$login] . '/';
			$academic = $this->db->query("SELECT * FROM academic_list where is_default = 1 ");
			if ($academic->num_rows > 0) {
				foreach ($academic->fetch_array() as $k => $v) {
					if (!is_numeric($k))
						$_SESSION['academic'][$k] = $v;
				}
			}

			return 1;
		} else {
			return 2;
		}
	}
	function logout()
	{
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function login2()
	{
		extract($_POST);
		$qry = $this->db->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM students where student_code = '" . $student_code . "' ");
		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $key => $value) {
				if ($key != 'password' && !is_numeric($key))
					$_SESSION['rs_' . $key] = $value;
			}
			return 1;
		} else {
			return 3;
		}
	}
	function save_user()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass', 'password')) && !is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		if (!empty($password)) {
			$data .= ", password=md5('$password') ";
		}
		//newly
		// restrict duplicated emails on allusers table
		$check = $this->db->query("SELECT * FROM all_users 
		where email ='$email'")->num_rows;
		if ($check > 0 && empty($id)) {
			return 2;
			exit;
		}
		//end
		$check = $this->db->query("SELECT * FROM users where email ='$email' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", avatar = '$fname' ";
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO users set $data");

			//newlyadded codes
			$rowww = $this->db->query("SELECT id FROM users 
		where email ='$email'")->num_rows;
			if ($rowww > 0) {

				$inherit_id = $this->db->query("SELECT * FROM users  
			where email ='$email'")->fetch_assoc(); //get users list id 

				$data .= ", id= '" . $inherit_id['id'] . "' , typeofuser='ibsmain'"; //concat 
				$save = $this->db->query("INSERT INTO all_users set $data"); // inserting users list data with the inherited pr id
			}
		} else {
			$save = $this->db->query("UPDATE users set $data where id = $id");
			$save = $this->db->query("UPDATE all_users set $data where id = $id");
		}

		if ($save) {
			return 1;
		}
	}
	function signup()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass')) && !is_numeric($k)) {
				if ($k == 'password') {
					if (empty($v))
						continue;
					$v = md5($v);
				}
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}

		$check = $this->db->query("SELECT * FROM users where email ='$email' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", avatar = '$fname' ";
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO users set $data");
		} else {
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if ($save) {
			if (empty($id))
				$id = $this->db->insert_id;
			foreach ($_POST as $key => $value) {
				if (!in_array($key, array('id', 'cpass', 'password')) && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			$_SESSION['login_id'] = $id;
			if (isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
				$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}

	function update_user()
	{
		extract($_POST);
		$data = "";
		$type = array("", "users", "faculty_list", "student_list");
		$type3 = array("", "ibsmain", "ibs1", "ibs2");

		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass', 'table', 'password')) && !is_numeric($k)) {

				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM {$type[$_SESSION['login_type']]} where email ='$email' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", avatar = '$fname' ";
		}
		if (!empty($password))
			$data .= " ,password=md5('$password') ";
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO {$type[$_SESSION['login_type']]} set $data");
		} else {
			$save = $this->db->query("UPDATE {$type[$_SESSION['login_type']]} set $data where id = $id");
			$save = $this->db->query("UPDATE all_users set $data 
			where id = $id and typeofuser = '{$type3[$_SESSION['login_type']]}'");
		}

		if ($save) {
			foreach ($_POST as $key => $value) {
				if ($key != 'password' && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			if (isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
				$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}

	function delete_user()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = " . $id);
		$delete = $this->db->query("DELETE FROM all_users where id = " . $id);

		if ($delete)
			return 1;
	}
	function save_system_settings()
	{
		extract($_POST);
		$data = '';
		foreach ($_POST as $k => $v) {
			if (!is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		if ($_FILES['cover']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'], '../assets/uploads/' . $fname);
			$data .= ", cover_img = '$fname' ";
		}
		$chk = $this->db->query("SELECT * FROM system_settings");
		if ($chk->num_rows > 0) {
			$save = $this->db->query("UPDATE system_settings set $data where id =" . $chk->fetch_array()['id']);
		} else {
			$save = $this->db->query("INSERT INTO system_settings set $data");
		}
		if ($save) {
			foreach ($_POST as $k => $v) {
				if (!is_numeric($k)) {
					$_SESSION['system'][$k] = $v;
				}
			}
			if ($_FILES['cover']['tmp_name'] != '') {
				$_SESSION['system']['cover_img'] = $fname;
			}
			return 1;
		}
	}
	function save_image()
	{
		extract($_FILES['file']);
		if (!empty($tmp_name)) {
			$fname = strtotime(date("Y-m-d H:i")) . "_" . (str_replace(" ", "-", $name));
			$move = move_uploaded_file($tmp_name, 'assets/uploads/' . $fname);
			$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https' : 'http';
			$hostName = $_SERVER['HTTP_HOST'];
			$path = explode('/', $_SERVER['PHP_SELF']);
			$currentPath = '/' . $path[1];
			if ($move) {
				return $protocol . '://' . $hostName . $currentPath . '/assets/uploads/' . $fname;
			}
		}
	}
	function save_subject()
	{
		extract($_POST);
		$data = "";
		$data_arr = array();
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'user_ids')) && !is_numeric($k)) {
				array_push($data_arr, $v);
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM subject_list where(`code` ='{$data_arr[0]}' and `subject`='{$data_arr[1]}') and id != '{$id}' ")->num_rows;

		if (empty($data_arr[0]) || empty($data_arr[1])) {
			return 3;
		}
		if ($chk > 0) {
			return 2;
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO subject_list set $data");
		} else {
			$save = $this->db->query("UPDATE subject_list set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	function delete_subject()
	{
		extract($_POST);

		$delete = $this->db->query("DELETE FROM subject_list where id = $id");
		if ($delete) {
			return 1;
		}
	}
	//newly added code
	function delete_subjectfromAS()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM assignedsubjects where subject_ID = $id");
		if ($delete) {
			return 1;
		}
	}
	function delete_instructorlist()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM assignedsubjects where instructor_ID = $id");
		if ($delete) {
			return 1;
		}
	}

	function save_class()
	{
		extract($_POST);
		$data = "";
		$data_arr = array();
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'user_ids')) && !is_numeric($k)) {
				array_push($data_arr, $v);
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM class_list where ( `curriculum` ='{$data_arr[0]}' and `level`='{$data_arr[1]}' and `section`='{$data_arr[2]}') and `id` != '{$id}' ")->num_rows;
		if (empty($data_arr[0]) || empty($data_arr[1]) || empty($data_arr[2])) {

			return 3;
		}
		if ($chk > 0) {

			return 2;
		}
		if (isset($user_ids)) {
			$data .= ", user_ids='" . implode(',', $user_ids) . "' ";
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO class_list set $data");
		} else {
			$save = $this->db->query("UPDATE class_list set $data where id = $id");
		}
		if ($save) {

			return 1;
		}
	}
	function delete_class()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM class_list where id = $id");
		$delete = $this->db->query("DELETE FROM restriction_list where class_id = $id");
		$delete = $this->db->query("DELETE FROM restriction_list2 where class_id = $id");
		if ($delete) {
			return 1;
		}
	}
	function save_academic()
	{
		extract($_POST);
		$data = "";
		$data_arr = array();
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'user_ids')) && !is_numeric($k)) {
				array_push($data_arr, $v);
				if (empty($data)) {

					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM academic_list where ( `year` ='{$data_arr[0]}' and `semester`='{$data_arr[1]}') and `id` != '{$id}' ")->num_rows;
		if (empty($data_arr[0])) {

			return 3;
		}
		if ($chk != 0) {
			return 2;
		}
		$hasDefault = $this->db->query("SELECT * FROM academic_list where is_default = 1")->num_rows;
		if ($hasDefault == 0) {
			$data .= " , is_default = 1 ";
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO academic_list set $data");
		} else {
			$save = $this->db->query("UPDATE academic_list set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	function delete_academic()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM academic_list where id = $id");
		$delete = $this->db->query("DELETE FROM restriction_list where academic_id = $id");
		$delete = $this->db->query("DELETE FROM restriction_list2 where academic_id = $id");


		if ($delete) {
			return 1;
		}
	}
	function make_default()
	{
		extract($_POST);
		$update = $this->db->query("UPDATE academic_list set is_default = 0");
		$update1 = $this->db->query("UPDATE academic_list set is_default = 1 where id = $id");
		$qry = $this->db->query("SELECT * FROM academic_list where id = $id")->fetch_array();
		if ($update && $update1) {
			foreach ($qry as $k => $v) {
				if (!is_numeric($k))
					$_SESSION['academic'][$k] = $v;
			}

			return 1;
		}
	}
	function save_criteria()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'user_ids')) && !is_numeric($k)) {
				if (empty($data)) {
					$v1 = $this->db->real_escape_string($v);
					$data .= " $k='$v1' ";
				} else {
					$v1 = $this->db->real_escape_string($v);

					$data .= ", $k='$v1' ";
				}
			}
		}
		// $chk = $this->db->query("SELECT * FROM criteria_list")->num_rows;
		// if ($chk <= 0) {
		// 	return 2;
		// }
		$chk = $this->db->query("SELECT * FROM criteria_list where (" . str_replace(",", 'and', $data) . ") and id != '{$id}' ")->num_rows;
		if ($chk > 0) {
			return 2;
		}

		if (empty($id)) {
			$lastOrder = $this->db->query("SELECT * FROM criteria_list order by abs(order_by) desc limit 1");
			$lastOrder = $lastOrder->num_rows > 0 ? $lastOrder->fetch_array()['order_by'] + 1 : 1;
			$data .= ", order_by='$lastOrder' ";
			$save = $this->db->query("INSERT INTO criteria_list set $data");
		} else {
			$save = $this->db->query("UPDATE criteria_list set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	function delete_criteria()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM question_list where id = $id");

		$delete = $this->db->query("DELETE FROM criteria_list where id = $id");

		if ($delete) {
			return 1;
		}
	}
	function save_criteria_order()
	{
		extract($_POST);
		$data = "";
		foreach ($criteria_id as $k => $v) {
			$update[] = $this->db->query("UPDATE criteria_list set order_by = $k where id = $v");
		}
		if (isset($update) && count($update)) {
			return 1;
		}
	}

	function save_question()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'user_ids')) && !is_numeric($k)) {
				if (empty($data)) {
					$v1 = $this->db->real_escape_string($v);
					$data .= " $k='$v1' ";
				} else {
					$v1 = $this->db->real_escape_string($v);

					$data .= ", $k='$v1' ";
				}
			}
		}

		if (empty($id)) {
			$lastOrder = $this->db->query("SELECT * FROM question_list where academic_id = $academic_id order by abs(order_by) desc limit 1");
			$lastOrder = $lastOrder->num_rows > 0 ? $lastOrder->fetch_array()['order_by'] + 1 : 0;
			$data .= ", order_by='$lastOrder' ";

			$save = $this->db->query("INSERT INTO question_list set $data");
		} else {
			$save = $this->db->query("UPDATE question_list set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	function delete_question()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM question_list where id = $id");
		if ($delete) {
			return 1;
		}
	}
	function save_question_order()
	{
		extract($_POST);
		$data = "";
		foreach ($qid as $k => $v) {
			$update[] = $this->db->query("UPDATE question_list set order_by = $k where id = $v");
		}
		if (isset($update) && count($update)) {
			return 1;
		}
	}
	function save_faculty()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass', 'password')) && !is_numeric($k)) {
				if ($k != 'subj') {
					if (empty($data)) {
						$data .= " $k='$v' ";
					} else {
						$data .= ", $k='$v' ";
					}
				}
			}
		}

		if (!empty($password)) {
			$data .= ", password=md5('$password') ";
		}
		//code to stop duplicate email from all user table
		$check = $this->db->query("SELECT * FROM all_users 
		where email ='$email' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		//end
		$check = $this->db->query("SELECT * FROM faculty_list 
		where email ='$email'")->num_rows;
		if ($check > 0 && empty($id)) {
			return 2;
			exit;
		}
		$check = $this->db->query("SELECT * FROM faculty_list where school_id ='$school_id' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 3;
			exit;
		}
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", avatar = '$fname' ";
		}

		$newdata = str_replace(", subj='Array'", "", $data);


		if (empty($id)) {
			$save = $this->db->query("INSERT INTO faculty_list set $newdata");

			//newlyadded codes
			$rowww = $this->db->query("SELECT id FROM faculty_list 
		where email ='$email'")->num_rows;
			if ($rowww > 0) {

				$inherit_id = $this->db->query("SELECT * FROM faculty_list  
			where email ='$email'")->fetch_assoc(); //get faculty list id 

				$newdata .= ", id= '" . $inherit_id['id'] . "' , typeofuser='ibs1'"; //concat 
				$save = $this->db->query("INSERT INTO all_users set $newdata"); // inserting faculty list data with the inherited pr id
			}
		} else {
			$delete = $this->db->query("DELETE FROM assignedsubjects where instructor_ID = " . $id);
			$save = $this->db->query("UPDATE faculty_list set $newdata where id = $id");
			$save = $this->db->query("UPDATE all_users set $newdata where id = $id"); //updating the allusers data

		}

		//for inserting new assigned subjects

		if (isset($_POST['school_id']) && isset($_POST['subj'])) {
			$newuser =  $this->db->real_escape_string($_POST['school_id']);
			$subpid =	$_POST['subj'];
			if (count($subpid) > 0) {

				$newuserPid = $this->db->query("SELECT id FROM faculty_list 
					where school_id = '$newuser'");

				$rows =	$newuserPid->fetch_assoc();

				// $_SESSION['testp3'] = $rows['id'];
				if ($rows > 0) {
					if (is_array($subpid)) {
						for ($i = 0; $i < count($subpid); $i++) {

							$this->db->query("INSERT INTO assignedsubjects set instructor_ID = " . $rows['id'] . "
						  ,subject_ID ='" . $subpid[$i] . "'
							");
						}
					}
				}
			}
		}




		if ($save) {
			return 1;
		}
	}

	function delete_faculty()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM faculty_list where id = " . $id);
		$delete = $this->db->query("DELETE FROM all_users where id = " . $id);
		if ($delete)
			return 1;
	}
	function save_student()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass', 'password')) && !is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		if (!empty($password)) {
			$data .= ", password=md5('$password') ";
		}
		//newly
		// restrict duplicated emails on allusers table
		$check = $this->db->query("SELECT * FROM faculty_list 
		where email ='$email'")->num_rows;
		if ($check > 0 && empty($id)) {
			return 2;
			exit;
		}
		// restrict duplicated school ids
		$check = $this->db->query("SELECT * FROM student_list where school_id ='$school_id' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 3;
			exit;
		}
		//end
		$check = $this->db->query("SELECT * FROM student_list where email ='$email' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", avatar = '$fname' ";
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO student_list set $data");
			//newlyadded codes
			$rowww = $this->db->query("SELECT id FROM student_list 
		where email ='$email'")->num_rows;
			if ($rowww > 0) {
				$inherit_id = $this->db->query("SELECT * FROM student_list  
			where email ='$email'")->fetch_assoc(); //get student list id 

				$data .= ", id= '" . $inherit_id['id'] . "' , typeofuser='ibs2'"; //concat 
				$save = $this->db->query("INSERT INTO all_users set $data"); // inserting student list data with the inherited pr id
			}
		} else {
			$save = $this->db->query("UPDATE student_list set $data where id = $id");
			$save = $this->db->query("UPDATE all_users set $data where id = $id");
		}

		if ($save) {
			return 1;
		}
	}
	function delete_student()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM restriction_list2 where student_id = $id");
		$delete = $this->db->query("DELETE FROM student_list where id = " . $id);
		$delete = $this->db->query("DELETE FROM all_users where id = " . $id);


		if ($delete)
			return 1;
	}
	function save_task()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id')) && !is_numeric($k)) {
				if ($k == 'description')
					$v = htmlentities(str_replace("'", "&#x2019;", $v));
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO task_list set $data");
		} else {
			$save = $this->db->query("UPDATE task_list set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	function delete_task()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM task_list where id = $id");
		if ($delete) {
			return 1;
		}
	}
	function save_progress()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id')) && !is_numeric($k)) {
				if ($k == 'progress')
					$v = htmlentities(str_replace("'", "&#x2019;", $v));
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		if (!isset($is_complete))
			$data .= ", is_complete=0 ";
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO task_progress set $data");
		} else {
			$save = $this->db->query("UPDATE task_progress set $data where id = $id");
		}
		if ($save) {
			if (!isset($is_complete))
				$this->db->query("UPDATE task_list set status = 1 where id = $task_id ");
			else
				$this->db->query("UPDATE task_list set status = 2 where id = $task_id ");
			return 1;
		}
	}
	function delete_progress()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM task_progress where id = $id");
		if ($delete) {
			return 1;
		}
	}
	function save_restriction()
	{
		extract($_POST);
		// if(empty($rid)) return 3;

		$filtered = implode(",", array_filter(isset($rid) ? $rid : []));

		if (!empty($filtered))
			$this->db->query("DELETE FROM restriction_list where id not in ($filtered) and academic_id = $academic_id");
		else
			$this->db->query("DELETE FROM restriction_list where  academic_id = $academic_id");
		if (isset($rid)) {
			foreach ($rid as $k => $v) {
				$data = " academic_id = $academic_id ";
				$data .= ", faculty_id = {$faculty_id[$k]} ";
				$data .= ", class_id = {$class_id[$k]} ";
				$data .= ", subject_id = {$subject_id[$k]} ";
				if (empty($v)) {
					$save[] = $this->db->query("INSERT INTO restriction_list set $data ");
				} else {
					$save[] = $this->db->query("UPDATE restriction_list set $data where id = $v ");
				}
			}
		}
		return 1;
	}
	function save_restriction2()
	{
		extract($_POST);
		// if(empty($rid)) $this->db->query("DELETE FROM restriction_list2 where academic_id = $academic_id");   return 3;
		$filtered = implode(",", array_filter(isset($rid) ? $rid : []));

		if (!empty($filtered))
			$this->db->query("DELETE FROM restriction_list2 where id not in ($filtered) and academic_id = $academic_id");
		else
			$this->db->query("DELETE FROM restriction_list2 where academic_id = $academic_id");
		if (isset($rid)) {
			# code...

			foreach ($rid as $k => $v) {
				$data = " academic_id = $academic_id ";
				$data .= ", student_id = {$student_id[$k]} ";
				$data .= ", class_id = {$class_id[$k]} ";
				$data .= ", subject_ids = '{$subject_ids[$k]}' ";
				if (empty($v)) {
					$save[] = $this->db->query("INSERT INTO restriction_list2 set $data ");
				} else {
					$save[] = $this->db->query("UPDATE restriction_list2 set $data where id = $v ");
				}
			}
		}

		return 1;
	}
	function save_evaluation()
	{
		extract($_POST);
		$data = " student_id = {$_SESSION['login_id']}";
		$data .= ", academic_id = $academic_id ";
		$data .= ", subject_id = $subject_id ";
		$data .= ", class_id = $class_id ";
		$data .= ", restriction_id = $restriction_id ";
		$data .= ", faculty_id = $faculty_id ";
		$save = $this->db->query("INSERT INTO evaluation_list set $data");
		if ($save) {
			$eid = $this->db->insert_id;
			foreach ($qid as $k => $v) {
				$data = " evaluation_id = $eid ";
				$data .= ", question_id = $v ";
				$data .= ", rate = {$rate[$v]} ";
				$ins[] = $this->db->query("INSERT INTO evaluation_answers set $data ");
			}
			if (isset($ins))
				return 1;
		}
	}
	function save_evaluation2()
	{
		extract($_POST);
		$data = " student_id = {$_SESSION['login_id']} ";
		$data .= ", academic_id = $academic_id ";
		$data .= ", subject_id = $subject_id ";
		$data .= ", class_id = $class_id ";
		$data .= ", restriction_id = $restriction_id ";
		$data .= ", faculty_id = $faculty_id ";
		$save = $this->db->query("INSERT INTO evaluation_list set $data");
		if ($save) {
			$eid = $this->db->insert_id;
			foreach ($qid as $k => $v) {
				$data = " evaluation_id = $eid ";
				$data .= ", question_id = $v ";
				$data .= ", rate = {$rate[$v]} ";
				$ins[] = $this->db->query("INSERT INTO evaluation_answers set $data ");
			}
			if (isset($ins))
				return 1;
		}
	}
	//query for get class by class 
	// SELECT c.id,concat(c.curriculum,' ',c.level,' - ',c.section) as class,s.id as sid,concat(s.code,' - ',s.subject) as subj FROM restriction_list r1,class_list c,subject_list s where c.id = r1.class_id and s.id = r1.subject_id and r1.faculty_id = {$fid} and r1.academic_id = {$_SESSION['academic']['id']} union SELECT c.id,concat(c.curriculum,' ',c.level,' - ',c.section) as class,s.id as sid,concat(s.code,' - ',s.subject) as subj from restriction_list2 r2 ,class_list c,subject_list s,faculty_list fl where c.id = r2.class_id and r2.academic_id = {$_SESSION['academic']['id']} and fl.id = {$fid} and fl.id in (SELECT asss.instructor_ID from restriction_list2 r2,assignedsubjects asss where find_in_set(asss.subject_ID,r2.subject_ids) > 0 and r2.academic_id = {$_SESSION['academic']['id']} and asss.instructor_ID = {$fid}) and s.id in (SELECT asss.subject_ID from restriction_list2 r2,assignedsubjects asss where find_in_set(asss.subject_ID,r2.subject_ids) > 0 and r2.academic_id = {$_SESSION['academic']['id']} and asss.instructor_ID = {$fid});

	function get_class()
	{
		extract($_POST);
		$data = array();
		$get = $this->db->query("SELECT s.id as sid,concat(s.code,' - ',s.subject) as subj FROM restriction_list r1,subject_list s where s.id = r1.subject_id and r1.faculty_id = {$fid} and r1.academic_id = {$_SESSION['academic']['id']} union SELECT s.id as sid,concat(s.code,' - ',s.subject) as subj from restriction_list2 r2,subject_list s,faculty_list fl where r2.academic_id = {$_SESSION['academic']['id']} and fl.id = {$fid} and fl.id in (SELECT asss.instructor_ID from restriction_list2 r2,assignedsubjects asss where find_in_set(asss.subject_ID,r2.subject_ids) > 0 and r2.academic_id = {$_SESSION['academic']['id']} and asss.instructor_ID = {$fid}) and s.id in (SELECT asss.subject_ID from restriction_list2 r2,assignedsubjects asss where find_in_set(asss.subject_ID,r2.subject_ids) > 0 and r2.academic_id = {$_SESSION['academic']['id']} and asss.instructor_ID = {$fid});");

		while ($row = $get->fetch_assoc()) {
			$data[] =  $row;
		}
		return json_encode($data);
	}
	function get_report()
	{
		extract($_POST);

		$data = array();



		// SELECT * FROM evaluation_answers where evaluation_id in (SELECT evaluation_id FROM evaluation_list where academic_id = {$_SESSION['academic']['id']} and faculty_id = $faculty_id and subject_id = $subject_id and class_id = $class_id )

		$get = $this->db->query("SELECT * FROM evaluation_answers where evaluation_id in (SELECT evaluation_id FROM evaluation_list where academic_id = {$_SESSION['academic']['id']} and faculty_id = $faculty_id and subject_id = $subject_id)");
		//before query for get eval
		// SELECT * FROM evaluation_list where academic_id = {$_SESSION['academic']['id']} and faculty_id = $faculty_id and subject_id = $subject_id and class_id = $class_id
		$answered = $this->db->query("SELECT * FROM evaluation_list where academic_id = {$_SESSION['academic']['id']} and faculty_id = $faculty_id and subject_id = $subject_id");
		$rate = array();
		while ($row = $get->fetch_assoc()) {
			if (!isset($rate[$row['question_id']][$row['rate']]))
				$rate[$row['question_id']][$row['rate']] = 0;
			$rate[$row['question_id']][$row['rate']] += 1;
		}
		// $data[]= $row;
		$ta = $answered->num_rows;
		$r = array();
		foreach ($rate as $qk => $qv) {
			foreach ($qv as $rk => $rv) {
				$r[$qk][$rk] = ($rate[$qk][$rk] / $ta) * 100;
			}
		}
		$data['tse'] = $ta;
		$data['data'] = $r;

		return json_encode($data);
	}
}
