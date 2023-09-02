
<?php 
function ordinal_suffix($num){
    $num = $num % 100; // protect against large numbers
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return $num.'st';
            case 2: return $num.'nd';
            case 3: return $num.'rd';
        }
    }
    return $num.'th';
}
$rid='';
$faculty_id='';
$subject_id='';
if(isset($_GET['rid']))
$rid = $_GET['rid'];
if(isset($_GET['fid']))
$faculty_id = $_GET['fid'];
if(isset($_GET['sid']))
$subject_id = $_GET['sid'];
$restriction = $conn->query("SELECT r.id,s.id as sid,f.id as fid,concat(f.firstname,' ',f.lastname) 
as faculty,s.code,s.subject FROM restriction_list r inner join faculty_list f on f.id = r.faculty_id 
inner join subject_list s on s.id = r.subject_id where academic_id ={$_SESSION['academic']['id']} 
and class_id = {$_SESSION['login_class_id']} and r.id not in (SELECT restriction_id from evaluation_list 
where academic_id ={$_SESSION['academic']['id']} and student_id = {$_SESSION['login_id']} ) ");
?>
<style>
	    [class*="icheck-"] > input:first-child:checked + label::after,
    [class*="icheck-"] > input:first-child:checked + input[type="hidden"] + label::after {
		transform: translate(7.75px, 8px) rotate(45deg) !important;
        -ms-transform: translate(7.75px, 8px) rotate(45deg) !important;
	}
    .p-1 {
		margin-right: 4rem;
        flex-direction:column  !important;
    }
    @media screen and (max-width: 600px) {
  .p-1 {
  display:inline-block !important;
  padding:0.3rem!important;
       
  }
   .btn {
   float:right !important;
        margin-bottom:20px !important;
}
}
    .btn {

     
   float:right !important;
        margin-bottom:20px !important;
}
   
</style>
<div class="col-lg-12">
	<div class="row">
		<div class="col-md-3">
			<div class="list-group listgropy">
				<?php 
				while($row=$restriction->fetch_array()):
					if(empty($rid)){
							$rid = $row['id'];
							$faculty_id = $row['fid'];
							$subject_id = $row['sid'];

						}
						// echo $rid."  | ".$row['sid'];

				?>
				<a class="list-group-item list-group-item-action <?php echo isset($rid) && $rid == $row['id'] ? 'active' : '' ?>" href="./index.php?page=evaluate&rid=<?php echo $row['id'] ?>&sid=<?php echo $row['sid'] ?>&fid=<?php echo $row['fid'] ?>"><?php echo ucwords($row['faculty']).' - ('.$row["code"].') '.$row['subject'] ?></a>
			<?php endwhile; ?>
			</div>
		</div>	
		<div class="col-md-9">
			<div class="card card-outline card-info">
				<div class="card-header">
					<b>Evaluation Questionnaire for Academic: <?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?> </b>
					
				</div>
				<div class="card-body">
					<fieldset class="border border-info p-2 w-100">
					   <legend  class="w-auto">Rating Legend</legend>
					   <p>5 = Strongly Agree, 4 = Agree, 3 = Neutral, 2 = Disagree, 1 = Strongly Disagree</p>
					</fieldset>
					<form id="manage-evaluation">
						<input type="hidden" name="class_id" value="<?php echo $_SESSION['login_class_id'] ?>">
						<input type="hidden" name="faculty_id" value="<?php echo $faculty_id?>">
						<input type="hidden" name="restriction_id" value="<?php echo $rid ?>">
						<input type="hidden" name="subject_id" value="<?php echo $subject_id ?>">
						<input type="hidden" name="academic_id" value="<?php echo $_SESSION['academic']['id'] ?>">
					<div class="clear-fix mt-2"></div>
					<?php 
							$q_arr = array();
						$criteria = $conn->query("SELECT * FROM criteria_list where id in (SELECT criteria_id FROM question_list where academic_id = {$_SESSION['academic']['id']} ) order by abs(order_by) asc ");
						while($crow = $criteria->fetch_assoc()):
					?>
					
					<table class="table table-condensed" >
						<thead>
							<tr class="bg-gradient-secondary">
								<th class="p-1"><b><?php echo $crow['criteria'] ?></b></th>
							</tr>
						</thead>
						<tbody class="tr-sortable">
							<?php 
							$questions = $conn->query("SELECT * FROM question_list where criteria_id = {$crow['id']} and academic_id = {$_SESSION['academic']['id']} order by abs(order_by) asc ");
							while($row=$questions->fetch_assoc()):
							$q_arr[$row['id']] = $row;
							?>
							<tr class="bg-white">
                            
								<td class="p-1">
								<div style="width:100%;overflow-wrap:break-word; margin-bottom:.5rem !important; "><?php echo $row['question'] ?></div>
									<input type="hidden" name="qid[]" value="<?php echo $row['id'] ?>">
								</div>
						
								<?php for($c=1;$c<=5;$c++): ?>
									<span class="icheck-success d-inline ">
				                        <input type="radio" class="vertical-align: middle;" name="rate[<?php echo $row['id'] ?>]" <?php echo $c == 5 ? "" : '' ?> id="qradio<?php echo $row['id'].'_'.$c ?>" value="<?php echo $c ?>" required> 
                                          
                                       
				                        <label class="p-1" style="" for="qradio<?php echo $row['id'].'_'.$c ?>">
										<?php echo $c ?>
				                        </label>
                                        
			                      </span>
								
								<?php endfor; ?>
								</td>
							</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
					<?php endwhile; ?>
					</form>
                  
				</div>
			</div>
              <div class="card-tools">
            
						<button class="btn btn-sm btn-flat btn-primary bg-gradient-primary mx-1" form="manage-evaluation" >Submit Evaluation</button>
					</div>
		</div>
	</div>

</div>
<script>
	$(document).ready(function(){
		let lengthss = document.querySelector(".listgropy").children.length;

		if('<?php echo $_SESSION['academic']['status'] ?>' == 0){
			uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>not_started.php")
		}else if('<?php echo $_SESSION['academic']['status'] ?>' == 2){
			uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>closed.php")
		}else if(lengthss == 0){
			uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>done.php")

		}
	})
	$('#manage-evaluation').submit(function(e){
		e.preventDefault();
		start_load()
		$.ajax({
			url:'ajax.php?action=save_evaluation',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp == 1){
					alert_toast("Data successfully saved.","success");
					setTimeout(function(){
						location.reload()	
					},1750)
				}
			}
		})
	})
</script>