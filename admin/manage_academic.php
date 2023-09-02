<?php
include '../db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM academic_list where id={$_GET['id']}")->fetch_array();
	foreach($qry as $k => $v){
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<form action="" id="manage-academic">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div id="msg" class="form-group"></div>
		
		<!-- <div class="form-group">
		<label for="year" class="control-label">Year:</label>
		<input type="text" class="form-control " id="myYear" name="year" list="listofyear" >
    <datalist id="listofyear" name="myYear" >

   
	</datalist>
	</div> -->

	<div class="form-group"> <!-- Create the input field with auto-suggestion -->
	<label for="year" class="control-label">Year:</label><input id="myYear" name="year" type="text" value='<?php if(isset($year))echo $year?>'class="form-control"autocomplete="off"> 
        <!-- Create a dropdown list for the suggestions -->
        <style>

            li{
                cursor:pointer;
                font-weight:bold;
            }
            li:hover{
                color:hotpink!important;

            }
        </style>
        <div class="dropdown mt-2">
               
            <ul style='list-style:none; margin:0;padding:0;'id="suggestions"></ul>
        </div>
    </div>
	<!-- <div class="form-group">
		<label for="year" class="control-label">Year:</label>
       <select  class="form-control form-control-sm" id="myYear" name="year" >
    
        </select>
	</div> -->
		<div class="form-group">
    <label for="semester" class="control-label">Semester</label>
    <select class="form-control form-control-sm" name="semester" id="semester" required>
        <option value="1" <?php if(isset($semester) && $semester == '1') echo 'selected'; ?>>Semester 1</option>
        <option value="2" <?php if(isset($semester) && $semester == '2') echo 'selected'; ?>>Semester 2</option>
		<option value="Summer" <?php if(isset($semester) && $semester == 'Summer') echo 'selected'; ?>>Summer</option>
    </select>
</div>
           
		<?php if(isset($status)): ?>
		<div class="form-group">
			<label for="" class="control-label">Status</label>
			<select name="status" id="status" class="form-control form-control-sm">
				<option value="0" <?php echo $status == 0 ? "selected" : "" ?>>Pending</option>
				<option value="1" <?php echo $status == 1 ? "selected" : "" ?>>Started</option>
				<option value="2" <?php echo $status == 2 ? "selected" : "" ?>>Closed</option>
			</select>
		</div>
		<a href="index.php?page=faculty_list">To Assign subjects click here!</a>

		<?php endif; ?>
	</form>
</div>
<script>
  if(typeof yearindatabse === 'undefined'){
	let yearindatabase = '';
}
	
	</script>
<?php echo isset($year) ? '<script> yearindatabse = "'.$year.'"</script>' : "<script> yearindatabse = ''</script>" ?>

<script>

	$(document).ready(function(){
		$('#manage-academic').submit(function(e){
			e.preventDefault();
			start_load()
			$('#msg').html('')
			$.ajax({

				url:'ajax.php?action=save_academic',
				method:'POST',
				data:$(this).serialize(),
				success:function(resp){
					if(resp == 1){

						alert_toast("Data successfully saved.","success");
						setTimeout(function(){
							location.reload()	
						},1750)
					}else if(resp == 2){
						$('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> academic Code already exist.</div>')
						end_load()
					}
					else if(resp == 3){
						$('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i>Fill-out the input field</div>')
						end_load()
					}
				}
			})
		})

		

		function suggestYears() {
            // Get the user input
            const input = this.value;

            // Parse the year input as an integer
            const inputYear = parseInt(input);

            // If the input is 1 to 4 digits, add zeroes to make it a valid year
            if (!isNaN(inputYear) && input.length < 4) {
                const yearString = input.padEnd(4, '0');
                const year = parseInt(yearString);
                if (year >= 1900 && year <= 2100) {
                    console.log(year);
                    const label = generateY(year);
                    populateDropdown(label);
                    return;
                }
            }

            // Check if the input is a valid year
            if (isNaN(inputYear) || inputYear < 1900 || inputYear > 2100) {
                clearDropdown();
                return;
            }

            // Filter the year options based on the user input
            const matchingYears = generateY(inputYear).filter(label => label.startsWith(input));

            // Populate the dropdown list with the filtered year options
            populateDropdown(matchingYears);
        }

        // Generate up to 10 year labels based on the input year
        function generateY(inputYear) {
            const filteredYears = [];
            for (let i = 0; i < 5; i++) {
                const year = inputYear + i;
                const label = `${year}-${year + 1}`;
                filteredYears.push(label);
            }
            return filteredYears;
        }

        // Populate the dropdown list with the given year labels
        function populateDropdown(years) {
            // Clear the existing dropdown items
            clearDropdown();

            // Add the year options to the dropdown menu
            const dropdown = document.getElementById('suggestions');
            years.forEach(year => {
             
              
                const item = document.createElement('li');
                item.textContent = year;
                item.classList.add('dropdown-item');
                dropdown.appendChild(item);
                item.addEventListener('click', () => {
                    // Set the input field value to the selected year
                    document.getElementById('myYear').value = year;
                    // Clear the dropdown menu
                    clearDropdown();
                });
                dropdown.appendChild(item);
            });
        }

        // Clear the dropdown list
        function clearDropdown() {
            const dropdown = document.getElementById('suggestions');
            dropdown.innerHTML = '';
        }

        // Attach the suggestion function to the input field
        const input = document.getElementById('myYear');
        input.addEventListener('input', suggestYears);





	})
	
</script>