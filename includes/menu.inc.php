 <!-- Navigational Menus  Start -->
 <div id="topmenu">
	<table cellspacing="0" cellpadding="0">
	 <tr>
	<?PHP 	  
	  	if($_SESSION["ses_view_vac"] == "1"){
	?>
	  <td><a class="topmenu"  onmouseover="show('21020',0,'NULL','NULL');"  onmouseout="hide('21020',0,'NULL','NULL');" href="<?PHP echo DIR_PATH.'index.php' ?>">Home</a></td>	
	<?PHP				
		}else{
	?>		 
		<td><a class="topmenu" href="<?PHP echo DIR_PATH.'index.php' ?>">Home</a></td>
	<?PHP				
		}
		
	//IF THE USER SUCCESFULLY LOGGED IN DISPLAY THEIR INFORMATION.
	if($_SESSION["ses_login_success"] == 'Y'){		
	?>		 

	  <td><a class="topmenu" href="<?PHP echo DIR_PATH.'vacation_cal.php' ?>">Calendar</a></td>
	  <td><a class="topmenu" onmouseover="show('21010',0,'NULL','NULL');"  onmouseout="hide('21010',0,'NULL','NULL');" href="#">My Account</a></td>
	  <?PHP 
	  //IF THE USER IS AN ADMINISTRATOR. DISPLAY AN ADMINISTRATORS MENU TAB.
	  if($_SESSION["ses_is_admin"] == 1){
	  ?>
	  		<td><a class="topmenu" onmouseover="show('21030',0,'NULL','NULL');hideitem(<?PHP echo $itemcnt; ?>);"  onmouseout="hide('21030',0,'NULL','NULL');showitem(<?PHP echo $itemcnt; ?>);" href="#">Administration</a></td>
	  <?PHP
	  }
	  ?>		
	  <td><a class="topmenu" href="<?PHP echo DIR_PATH.'logout.php' ?>">Logout</a></td>
	<?PHP				
	}
	?>
	      
    </tr>
   </table>
 </div>
  <!-- Navigational Menus End -->


  <!-- Popup Menus -->	
	<ul id="m210100" class="menublock" style="top:96px;left:335px;width:140px;" onmouseover="show('21010',0,'NULL','NULL');" onmouseout="hide('21010',0,'NULL','NULL');">
	  <li><a href="<?PHP echo DIR_PATH.'emp_change_password.php' ?>">Change password</a></li>
	  <li><a href="<?PHP echo DIR_PATH.'emp_change_email.php' ?>">Change email address</a></li>
	</ul> 
	
	<?PHP 	  
	  	if($_SESSION["ses_view_vac"] == "1"){
	?>
				<ul id="m210200" class="menublock" style="top:96px;left:166px;width:240px;" onmouseover="show('21020',0,'NULL','NULL');" onmouseout="hide('21020',0,'NULL','NULL');">	
					<li><a href="<?PHP echo DIR_PATH.'report_user_calendar.php' ?>" target="_blank">Calendar view of department vacations</a></li>
				</ul>
	<?PHP				
		}
	?>		
	<ul id="m210300" class="menublock" style="top:96px;left:400px;width:340px;" onmouseover="show('21030',0,'NULL','NULL');hideitem(<?PHP echo $itemcnt; ?>);" onmouseout="hide('21030',0,'NULL','NULL');showitem(<?PHP echo $itemcnt; ?>);">	
		<li><a href="<?PHP echo DIR_PATH.'admin/employee_select.php' ?>">Work with an employee</a></li>			
		<li><a  class="showsub"  onmouseover="show('21030',1,'NULL','NULL');"  onmouseout="hide('21030',0,'NULL','NULL');"   href="#">Report options</a>
			<ul id="m210301" class="submenublock" style="top:20px;left:-305px;" onmouseover="show('21030',1,'NULL','NULL');" onmouseout="hide('21030',0,'NULL','NULL');">					
				<li><a href="<?PHP echo DIR_PATH.'admin/report_weekly.php' ?>">Weekly Vacations report for your department</a></li>
				<li><a href="<?PHP echo DIR_PATH.'admin/report_weekly_entered_time.php' ?>">Submitted Times report for your department</a></li>
				<li><a href="<?PHP echo DIR_PATH.'admin/report_calendar_style.php' ?>">Monthly Calendar style report for your department</a></li>
				<li><a href="<?PHP echo DIR_PATH.'admin/report_admin_emp_timeoff.php' ?>">View and print weekly vacation request forms</a></li>
				<li><a href="<?PHP echo DIR_PATH.'admin/report_weekly_all.php' ?>">Weekly Vactions report for the company</a></li>
				<li><a href="<?PHP echo DIR_PATH.'admin/vacation_summary_step1.php' ?>">Summary information of time off types</a></li>					
			</ul>
		</li>
		<li><a href="<?PHP echo DIR_PATH.'admin/apprv_pending_vacations.php' ?>">Review and approve pending vacation requests</a></li>		
		<li><a href="<?PHP echo DIR_PATH.'admin/date_restrictions_dept.php' ?>">Add time off restrictions for select calendar dates</a></li>
		<?PHP 
		if($_SESSION["ses_super_admin"] == 1 || $_SESSION["ses_is_admin"] == 1){
		?>		
			<li><a class="showsub" onmouseover="show('21030',2,'NULL','NULL');"  onmouseout="hide('21030',0,'NULL','NULL');" href="<?PHP echo DIR_PATH.'admin/site_setup.php' ?>">Site setup</a></li>	
				<ul id="m210302" class="submenublock" style="top:80px;left:-305px;" onmouseover="show('21030',2,'NULL','NULL');" onmouseout="hide('21030',0,'NULL','NULL');">					
					<?PHP 
					if($_SESSION["ses_super_admin"] == 1){
					?>	
					<li><a href="<?PHP echo DIR_PATH.'admin/employee_category.php' ?>">Add/edit employee categories</a></li>
					<li><a href="<?PHP echo DIR_PATH.'admin/departments.php' ?>">Add/edit company departments</a></li>
					<?PHP 
					}
					?>
					<li><a href="<?PHP echo DIR_PATH.'admin/sub_dept_menu.php' ?>">Work with sub-departments</a></li>
					<li><a href="<?PHP echo DIR_PATH.'admin/config.php' ?>">Configure vacation restrictions for your department</a></li>
					<?PHP 
					if($_SESSION["ses_super_admin"] == 1){
					?>						
					<li><a href="<?PHP echo DIR_PATH.'admin/time_off_types.php' ?>">Add/edit time off types</a></li>
					<li><a href="<?PHP echo DIR_PATH.'admin/fiscal_year.php' ?>">Add/edit fiscal year information</a></li>
					<li><a href="<?PHP echo DIR_PATH.'admin/notable_dates.php' ?>">Add holidays and notable dates</a></li>
					<li><a href="<?PHP echo DIR_PATH.'admin/employee_add.php' ?>">Add a new employee</a></li>
					<li><a href="<?PHP echo DIR_PATH.'admin/employee_edit_step1.php' ?>">Edit an existing employee</a></li>
					<li><a href="<?PHP echo DIR_PATH.'admin/timeoff_step1.php' ?>">Add/update earned time off</a></li>
					<?PHP 
					}
					?>										
				</ul>					
	<!--		<li>&nbsp;&nbsp;&nbsp;<u>Depricated options</u></li>	
			<li><a href="<?PHP echo DIR_PATH.'admin/employee_add.php' ?>">Add a new employee</a></li>
			<li><a href="<?PHP echo DIR_PATH.'admin/employee_edit_step1.php' ?>">Edit an existing employee</a></li>
			<li><a href="<?PHP echo DIR_PATH.'admin/timeoff_step1.php' ?>">Update employee earned time off</a></li> -->
		<?PHP 
		}
		if($_SESSION["ses_super_admin"] == 1)
		{
		?> 
			<li><a href="<?PHP echo DIR_PATH.'admin/admin_change_dept.php' ?>">Change
			Working Department</a></li> 			
		<?PHP 
		}
		?>
	<!--
		<li><a href="<?PHP echo DIR_PATH.'admin/change_vac_type_step1.php' ?>">Change or delete a vacation for an employee</a></li>		
		<li><a href="<?PHP echo DIR_PATH.'admin/user_vacation_add.php' ?>">Add vacation for an employee</a></li>							
		<li><a href="<?PHP echo DIR_PATH.'admin/add_note_step1.php' ?>">Add notes to an employees time off</a></li>
		<li><a href="<?PHP echo DIR_PATH.'admin/sub_dept_menu.php' ?>">Work with Sub-Departments</a></li>
		<li><a href="<?PHP echo DIR_PATH.'admin/config.php' ?>">Configure the vacation restrictions for your department</a></li>		-->
	</ul>	
  <!-- Popup Menus End -->	