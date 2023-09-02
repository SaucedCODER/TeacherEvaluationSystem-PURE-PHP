<?php
include '../db_connect.php';

?>
<style>

.vscomp-toggle-button{
	box-shadow:none !important;
}
.vscomp-value{
	font-size:.875rem !important;
	font-family: inherit !important;
}
.make-it-maroon{
	color:maroon !important;

}
.vscomp-options-container {
         font-size:18px !important;
       height:100%;
        min-height:200px !important;
    }
    
  .vscomp-options-list {
            height:0px !important;
    }
</style>
<div class="container-fluid">
	<form action="" id="manage-restriction">
		<div class="row">
			<div class="col-md-4 border-right" >
				
				<input type="hidden" name="academic_id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
				<div id="msg" class="form-group"></div>

				<div class="form-group">
					<label for="" class="control-label">Subjects</label><br>
					<select id="multi_option"  name="native-select" placeholder="Please select here" data-silent-initial-value-set="true">
				
						<?php 
						$subject = $conn->query("SELECT sl1.id as asid, sl1.subject as subjname, sl1.id as subid,f1.id as faid, concat(firstname,' ',lastname) as name 
						FROM assignedsubjects t1,faculty_list f1, subject_list sl1
						 WHERE t1.subject_ID = sl1.id and t1.instructor_ID = f1.id;");
						$f_arr = array();
						while($row=$subject->fetch_assoc()):
							$f_arr[$row['asid']]= $row;

						?>
						<option name='opt' value="<?php echo $row['asid'] ?>" <?php echo isset($subject_id) && $subject_id == $row['subid'] ? "selected" : "" ?>>
						<?php echo $row['subjname']." | ".$row['name'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
			
				<div class="form-group">
					<label for="" class="control-label">Class</label>
					<select name="" id="class_id" class="form-control form-control-sm select2" >
						<option value=""></option>
						<?php 
						$classes = $conn->query("SELECT id,concat(curriculum,' ',level,' - ',section) as class FROM class_list");
						$c_arr = array();
						while($row=$classes->fetch_assoc()):
							$c_arr[$row['id']]= $row;
						?>
						<option value="<?php echo $row['id'] ?>" <?php echo isset($class_id) && $class_id == $row['id'] ? "selected" : "" ?>><?php echo $row['class'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<!-- <div class="form-group">
					<label for="" class="control-label">Subject</label>
					<select name="" id="subject_id" class="form-control form-control-sm select2" >
						<option value=""></option>
						<?php 
						// $subject = $conn->query("SELECT id,concat(code,' - ',subject) as subj FROM subject_list");
						// $s_arr = array();
						// while($row=$subject->fetch_assoc()):
						// 	$s_arr[$row['id']]= $row;
						?>
						<option value="<?php //echo $row['id'] ?>" <?php //echo isset($subject_id) && $subject_id == $row['id'] ? "selected" : "" ?>><?php //echo $row['subj'] ?></option>
						<?php //endwhile; ?>
					</select>
				</div> -->
				<div class="form-group">
					<div class="d-flex w-100 justify-content-center">
						<button class="btn btn-sm btn-flat btn-primary bg-gradient-primary" id="add_to_list"type="button">Add to List</button>
					</div>
				</div>
			</div>
			<div class="col-md-8">
				<table class="table table-condensed" id="r-list">
					<thead>
						<tr>
							<th>Faculty</th>
							<th>Class</th>
							<th>Subject</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$restriction = $conn->query("SELECT * FROM restriction_list where academic_id = {$_GET['id']} order by id asc");
						while($row=$restriction->fetch_assoc()):
						?>
						<tr>
							<td>
								<b><?php echo isset($f_arr[$row['subject_id']]['faid']) ? $f_arr[$row['subject_id']]['name'] : '' ?></b>
								<input type="hidden" name="rid[]" value="<?php echo $row['id'] ?>">
								<input type="hidden" name="faculty_id[]" value="<?php echo $row['faculty_id'] ?>">
							</td>
							<td data-cl-id="<?php echo $row['class_id'] ?>">
								<b><?php echo isset($c_arr[$row['class_id']]) ? $c_arr[$row['class_id']]['class'] : '' ?></b>
								<input type="hidden" name="class_id[]" value="<?php echo $row['class_id'] ?>">
							</td>
							<td data-s-id="<?php echo $row['subject_id'] ?>">
								<b><?php echo isset($f_arr[$row['subject_id']]) ? $f_arr[$row['subject_id']]['subjname'] : '' ?></b>
								<input type="hidden" name="subject_id[]" value="<?php echo $row['subject_id'] ?>">
							</td>
							<td class="text-center">
								<button class="btn btn-sm btn-outline-danger" onclick="removetolist(event)" type="button" ><i class="fa fa-trash"></i></button>
							</td>
						</tr>
					<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</div>
	</form>
</div>
<div class="warnings102"></div>
<script>

		function removetolist(e) {
				let curr_stid = e.currentTarget.parentElement.parentElement.children[1];
	
				let curr_sid = curr_stid.nextElementSibling.dataset.sId;
				let st_id = document.querySelectorAll('[data-cl-id]');
				
		
		
				let array_z = [] ,array_d = [] ;
				let subj_id2 = Array.from(st_id).map(e2=>{
							if (curr_stid.dataset.clId == e2.dataset.clId) {
								
								return e2.nextElementSibling;

							}

						})	

						subj_id2.forEach(e=>{
										if (e != undefined) {

												if (curr_sid == e.dataset.sId){
													array_z.push(e.dataset.sId)
													array_d.push(e)
												} 
											
										}
									})
								

						let vava = array_z.reduce((prev, curr) => (prev[curr] = ++prev[curr] || 1, prev), [])
						vava.forEach((e,i)=>{
							if(e == 2){
								

								array_d.forEach(e1=>{
									if (e1.dataset.sId == i){
										e1.classList.remove('make-it-maroon')
										console.log('remove it',e , e1);
									}

								})
							}
						})


				e.currentTarget.closest('tr').remove();


			}


	$(document).ready(function(){

		VirtualSelect.init({ 
	  ele: '#multi_option',

	  autoSelectFirstOption: true,
	  search:true,
	  disableSelectAll: true,
	  optionsSelectedText: 'Subjects selected',
	  optionSelectedText: 'Subjects selected',
	  dropboxWidth: "450px",
	  optionHeight: "20px",
  position: 'auto',
  maxValues: 1,
  maxWidth: '499px',


	});
	document.querySelector('#multi_option').reset();
	

		$('.select2').select2({
		    placeholder:"Please select here",
		    width: "100%"
		  });
		$('#manage-restriction').submit(function(e){
			e.preventDefault();
			let all_subids= document.querySelectorAll('[data-s-id]')
			let flagy = true
			all_subids.forEach(e=>{
				if(e.classList.contains("make-it-maroon")){
					flagy = false
				}
			})
			
			if(flagy){
					start_load()
				$('#msg').html('')
				$.ajax({
					url:'ajax.php?action=save_restriction',
					method:'POST',
					data:$(this).serialize(),
					success:function(resp){
					
						console.log(resp);
						
						// if (resp == 3) {
						// 	end_load()
						// 	alert("The Restriction table is empty. ");
							
						// 		location.reload()	
							
						// }
						if(resp == 1){
							alert_toast("Data successfully saved.","success");

							setTimeout(function(){
								location.reload()	
							},1750)
						}else if(resp == 2){
							$('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Class already exist.</div>')
							end_load()
						}
					}
				})
			}else{
				const elll = document.createElement('div')
				elll.classList.add('alert','alert-warning','alert-dismissible','fade','show')
				elll.role = 'alert'
				elll.style.csstext = `position:fixed;top:3rem;right:5rem;` 
				elll.innerHTML = `
Cannot save data because duplicates were found in the input. Please remove the duplicate entries and try again.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
`;
			document.querySelector('.warnings102').append(elll)
			setTimeout(() => {
				$('.alert').alert('close')
				document.querySelector('.warnings102').innerHtml = '';
			}, 4000);

			}
		})
		$('#add_to_list').click(function(){
			start_load()
			var frm = $('#manage-restriction')
			var cid = frm.find('#class_id').val()
			var sid = frm.find('#multi_option').val()
		
		
			if (cid != "" && sid != "") {//fid != "" && 
				var f_arr = <?php echo json_encode($f_arr) ?>;
				var fid = f_arr[sid].faid;
			var c_arr = <?php echo json_encode($c_arr) ?>;


			let st_id = document.querySelectorAll('[data-cl-id]');
				let subj_id2 = Array.from(st_id).map(e2=>{
							if (cid == e2.dataset.clId) {
							return e2.nextElementSibling;

							}
						})	
							console.log(subj_id2);
		
					let sub_ins = '';
				let htmll = `<td data-s-id="${sid}"><b>${f_arr[sid].subjname}</b><input type="hidden" name="subject_id[]" value="${sid}"></td>`;

					subj_id2.forEach(e2=>{
						if (e2 != undefined) {
							
								if (e2.dataset.sId == sid){

									e2.classList.add('make-it-maroon');
								htmll = `<td class='make-it-maroon' data-s-id="${sid}"><b>${f_arr[sid].subjname}</b><input type="hidden" name="subject_id[]" value="${sid}"></td>`
								} 
						}
					})
				
					sub_ins += htmll;
			
		


			var tr = $("<tr></tr>")
			tr.append('<td><b>'+f_arr[sid].name+'</b><input type="hidden" name="rid[]" value=""><input type="hidden" name="faculty_id[]" value="'+fid+'"></td>')
			tr.append('<td data-cl-id="'+cid+'"><b>'+c_arr[cid].class+'</b><input type="hidden" name="class_id[]" value="'+cid+'"></td>')
			tr.append(`${sub_ins}`)
			tr.append('<td class="text-center"><span class="btn btn-sm btn-outline-danger" onclick="removetolist(event)" type="button"><i class="fa fa-trash"></i></span></td>')
			$('#r-list tbody').append(tr)
			frm.find('#class_id').val('').trigger('change')
			frm.find('#faculty_id').val('').trigger('change')
			frm.find('#subject_id').val('').trigger('change')
			document.querySelector('#multi_option').reset();

			}else{
$('#msg').html('<div class="alert alert-danger" style="font-size:.8rem;"> <i class="fa fa-exclamation-triangle"></i> Please fill out the fields! <div>')

				setTimeout(function(){
$('#msg').html('')
						},1750)

			}
			end_load()
			
		})


	})

</script>