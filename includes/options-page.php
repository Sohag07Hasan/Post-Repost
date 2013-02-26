<?php 
	$categories = self::get_all_categories();
	//var_dump($categories);
	$scheduled_categories = self::get_scheduled_categories();
	//var_dump($scheduled_categories);
?>

<div class="wrap">
	<h2> Post Scheduler Settings </h2>
	
	<?php 
		if($_POST['postrepostscheuler'] == 'submitted'){
			echo '<div class="updated"><p> Saved </p></div>';			
		}	
	?>
	
	<form action="" method="post">
		
		<input type="hidden" name="postrepostscheuler" value='submitted' />
		
		<table class="form-table">
			<tr>
				<td colspan="2">Choose Categories </td>				
			</tr>
			
			<?php 
				foreach ($categories as $cat){
					$checked = (in_array($cat->term_id, $scheduled_categories)) ? 1 : 0;
					?>
					<tr>
						<td colspan="2">
							<input <?php checked(1, $checked); ?> type="checkbox" id="<?php echo 'scheduler-term-' . $cat->term_id; ?>" value="<?php echo $cat->term_id; ?>" name="scheduler_categories[]" /> <label for="<?php echo 'scheduler-term-' . $cat->term_id; ?>"> <?php echo $cat->name; ?> </label>
						</td>
					</tr>					
					<?php 					
				}			
			?>
			
			<tr>
				<td cospan="2">
					<input type="submit" value="Save" class="button-primary" />
				</td>
			</tr>
			
		</table>
			
	</form>
	

</div>