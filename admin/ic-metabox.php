<?php
/** 
 * The Class.
 */
class IC_Metabox {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
		$post_types = array('meeting');   //limit meta box to certain post types
		if ( in_array( $post_type, $post_types )) {
			add_meta_box(
				'meeting_meta_box'
				,__( 'Meeting Fields', 'myplugin_textdomain' )
				,array( $this, 'render_meta_box_content' )
				,$post_type
				,'advanced'
				,'high'
			);
		}
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {
	
		if(isset($_POST['post_type'] ) && $_POST['post_type'] == 'meeting')
		{
			update_post_meta( $post_id, 'meeting_start_date', $_POST['meeting_start_date'] );
			update_post_meta( $post_id, 'meeting_end_date', $_POST['meeting_end_date'] );
			update_post_meta( $post_id, 'meeting_participants', $_POST['participants'] );
		}
		
	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {
	
		?>
		<style>
		.metabox_label{font-weight:bold; display:inline-block; width:200px;}
		.form-group{padding: 10px 0;}
		</style>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<div class="form-group">
			<label class="metabox_label" for="myplugin_new_field">Starting Date</label>
			<input type="text" class="regular-text datepicker" name="meeting_start_date" value="<?php echo get_post_meta( $post->ID, 'meeting_start_date', true ); ?>" size="25" />
		</div>
		<div class="form-group">
			<label class="metabox_label" for="myplugin_new_field">Ending Date</label>
			<input type="text" class="regular-text datepicker" name="meeting_end_date" value="<?php echo get_post_meta( $post->ID, 'meeting_end_date', true ); ?>" size="25" />
		</div>
		<h3>Add Participants</h3>
		<div class="add_participants">
			<?php 
				$participants = get_post_meta( $post->ID, 'meeting_participants', true );
				if(is_array($participants)){foreach($participants['name'] as $k=>$val){
			?>
			<div class="form-group">
				<input type="text" class="regular-text" name="participants[name][]" value="<?php echo $participants['name'][$k]; ?>" placeholder="Name" size="10" />
				<input type="text" class="regular-text" name="participants[email][]" value="<?php echo $participants['email'][$k]; ?>" placeholder="Email" size="20" />
				<i class="fa fa-add">Add</i>
				<i class="fa fa-trash">Delete</i>
			</div>
				<?php }}else{?>
			<div class="form-group">
				<input type="text" class="regular-text" name="participants[name][]" placeholder="Name" size="10" />
				<input type="text" class="regular-text" name="participants[email][]" placeholder="Email" size="20" />
				<i class="fa fa-add">Add</i>
				<i class="fa fa-trash">Delete</i>
			</div>
			<?php }?>
		</div>
		<script>
		jQuery(document).ready(function(){
			jQuery(document).on("click", ".fa-add ", function(){
				jQuery(this).parents(".form-group").after(jQuery("#add_new_participant").html());
			});
			jQuery(document).on("click", ".fa-trash ", function(){
				jQuery(this).parents(".form-group").remove();
			});
			jQuery( ".datepicker" ).datepicker();
		});
		</script>
		<script id="add_new_participant" type="text/template">
			<div class="form-group">
				<input type="text" class="regular-text" name="participants[name][]"  placeholder="Name" size="10" />
				<input type="text" class="regular-text" name="participants[email][]" placeholder="Email" size="20" />
				<i class="fa fa-add">Add</i>
				<i class="fa fa-trash">Delete</i>
			</div>
		</script>
		<?php
	}
}