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
		$post_types = array('page');   //limit meta box to certain post types
		if ( in_array( $post_type, $post_types )) {
			add_meta_box(
				'meeting_meta_box'
				,__( 'Chat Welcome Message', 'myplugin_textdomain' )
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
	
		if(isset($_POST['post_type'] ) && $_POST['post_type'] == 'page')
		{
			update_post_meta( $post_id, 'chat_welcome_message', addslashes($_POST['chat_welcome_message']) );
		}
		
	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {
	
		?>
		<div class="form-group">
			<label class="metabox_label" for="myplugin_new_field">Starting Date</label>
			<textarea name="chat_welcome_message"><?php echo stripslashes(get_post_meta( $post->ID, 'chat_welcome_message', true )); ?></textarea>
		</div>
		<?php
	}
}