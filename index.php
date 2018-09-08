<?php 
/*
Plugin Name: Submit Testimonial
Plugin URI: none
Description: Ability to submit testimonial
Author: Sam Pedraza
Version: 1.0
*/
function myscripts(){
	wp_register_style('mainCSS', '/wp-content/plugins/submit-testimonial/css/main.css');
	wp_register_style('fontawesome', 'https://use.fontawesome.com/releases/v5.1.0/css/all.css');
	wp_register_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
	
	wp_enqueue_style('mainCSS');
	wp_enqueue_style('fontawesome');
	wp_enqueue_style('bootstrap');
}	



	function create_review_database(){
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'omc_review_plugin';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(255),
			review TEXT,
			PRIMARY KEY (id)
		) $charset_collate;";

		require_once(ABSPATH . "wp-admin/includes/upgrade.php");
		dbDelta($sql);
	}

	function insert_into_review_database(){
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'omc_review_plugin';

		$wpdb->insert($table_name, array(
			'name' => sanitize_text_field($_POST['name']),
			'review' => sanitize_text_field($_POST['review'])
		));

		return wp_redirect("/testimonials");
	}

	function delete_review_database(){
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'omc_review_plugin';

		$sql = "DROP DATABASE $table_name;";

		require_once(ABSPATH . "wp-admin/includes/upgrade.php");
		$wpdb->query($sql);	
	}

	function fetch_reviews(){
		global $wpdb;
		$table_name = $wpdb->prefix . "omc_review_plugin";
		$last_five_reviews = $wpdb->get_results("SELECT name, review FROM $table_name;");

		for($x = 0; $x < count($last_five_reviews); $x++){
			echo "<div class='card'>";	
			echo "<div class='card-body'>";
				echo "<h4 class='card-title'>" . $last_five_reviews[$x]->name . "</h4>";
				echo "<p class='card-text'>" . $last_five_reviews[$x]->review . "</p>";
			echo "</div>";
			echo "</div>";
		}
	}


	function leave_review(){

		myscripts();
		?>

		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h3>Leave a Review</h3>
						<form method="POST" action="<?php echo admin_url( 'admin-post.php' ); ?>">
							<input type="hidden" name="action" value="reviewsubmission">
							<div class="form-group">
								<label>
									Name
								</label>

								<input type="text" name="name" class="form-control">
							</div>
							<div class="form-group">
								<label>
									Review
								</label>

								<textarea name="review" class="form-control"></textarea>
							</div>
							<div class="form-group">
								<input type="submit" class="btn btn-primary">
							</div>
						</form>	
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<h1>Testimonials</h1>
					<?php fetch_reviews(); ?>
				</div>
			</div>
		</div>

		<?php
	}

	add_shortcode('leave_review_plugin', 'leave_review');

	add_action('wp_head', 'myscripts');
	add_action( 'admin_post_nopriv_reviewsubmission', 'insert_into_review_database' );
	add_action( 'admin_post_reviewsubmission', 'insert_into_review_database' );
	
	register_activation_hook( __FILE__, 'create_review_database' );
	register_uninstall_hook(__FILE__, 'delete_review_database');

?>