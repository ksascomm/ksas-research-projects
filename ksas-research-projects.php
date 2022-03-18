<?php
/**
 * KSAS Projects
 *
 * @package     KSAS Projects
 * @author      KSAS Communications
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: KSAS Projects
 * Plugin URI:  http://krieger.jhu.edu/documentation/plugins/job-market/
 * Description: Custom Content Type for Centers/Programs/Institutes to highlight their research projects.
 * Version: 1.0
 * Author: KSAS Communications
 * Author URI:  https://krieger.jhu.edu
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

/** Hook into the init action and call create_book_taxonomies when it fires */
add_action( 'init', 'create_ksasresearchprojects_taxonomies', 0 );

/*************************************
 * Registration code for type taxonomy
 */
function create_ksasresearchprojects_taxonomies() {
	$labels = array(
		'name'               => _x( 'Research Project Types', 'taxonomy general name' ),
		'singular_name'      => _x( 'Research Project Type', 'taxonomy singular name' ),
		'add_new'            => _x( 'Add New Research Project Type', 'Research Project Type' ),
		'add_new_item'       => __( 'Add New Research Project Type' ),
		'edit_item'          => __( 'Edit Research Project Type' ),
		'new_item'           => __( 'New Research Project Type' ),
		'view_item'          => __( 'View Research Project Type' ),
		'search_items'       => __( 'Search Research Project Types' ),
		'not_found'          => __( 'No Research Project Type found' ),
		'not_found_in_trash' => __( 'No Research Project Type found in Trash' ),
	);

		$args = array(
			'labels'            => $labels,
			'singular_label'    => __( 'Research Project Type' ),
			'public'            => true,
			'show_ui'           => true,
			'hierarchical'      => true,
			'show_tagcloud'     => false,
			'show_in_nav_menus' => false,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'project',
				'with_front' => false,
			),
		);
		register_taxonomy( 'project_type', 'ksasresearchprojects', $args );
}

/*************************************
 * Registration code for Research Projects post type.
 */
function register_ksasresearchprojects_posttype() {
	$labels = array(
		'name'               => _x( 'Research Projects', 'post type general name' ),
		'singular_name'      => _x( 'Project', 'post type singular name' ),
		'add_new'            => __( 'Add New Research Projects' ),
		'add_new_item'       => __( 'Add New Research Projects' ),
		'edit_item'          => __( 'Edit Reseach Projects' ),
		'new_item'           => __( 'New Research Projects' ),
		'view_item'          => __( 'View Research Projects' ),
		'search_items'       => __( 'Search Research Projects' ),
		'not_found'          => __( 'No Research Projects found' ),
		'not_found_in_trash' => __( 'No Research Projects found in Trash' ),
		'parent_item_colon'  => __( '' ),
		'menu_name'          => __( 'Research Projects' ),
	);

		// $taxonomies = array( 'project_type' );

		$supports = array( 'title', 'revisions', 'thumbnail', 'editor', 'excerpt' );

		$post_type_args = array(
			'labels'             => $labels,
			'singular_label'     => __( 'Research Projects' ),
			'public'             => true,
			'show_ui'            => true,
			'publicly_queryable' => true,
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => true,
			'rewrite'            => array(
				'slug'       => 'projects',
				'with_front' => false,
			),
			'supports'           => $supports,
			'menu_position'      => 5,
			// 'taxonomies'      => $taxonomies,
			'show_in_nav_menus'  => true,
			'show_in_rest'       => true,
		);
		register_post_type( 'ksasresearchprojects', $post_type_args );
}
add_action( 'init', 'register_ksasresearchprojects_posttype' );

/**
 * Add custom metabox
 */
$projectinformation_5_metabox = array(
	'id'       => 'projectinformation',
	'title'    => 'Project Information',
	'page'     => array( 'ksasresearchprojects' ),
	'context'  => 'normal',
	'priority' => 'default',
	'fields'   => array(

		array(
			'name'        => 'Associate Name',
			'desc'        => '',
			'id'          => 'ecpt_associate_name',
			'class'       => 'ecpt_associate_name',
			'type'        => 'text',
			'rich_editor' => 0,
			'max'         => 0,
			'std'         => '',
		),

		array(
			'name'        => 'Funding Source/Dates',
			'desc'        => 'Please enter dates in MM/DD/YY format',
			'id'          => 'ecpt_dates',
			'class'       => 'ecpt_dates',
			'type'        => 'text',
			'rich_editor' => 0,
			'max'         => 0,
			'std'         => '',
		),
	),
);

add_action( 'admin_menu', 'ecpt_add_projectinformation_5_meta_box' );

/**
 * Function to add meta boxes.
 */
function ecpt_add_projectinformation_5_meta_box() {

	global $projectinformation_5_metabox;

	foreach ( $projectinformation_5_metabox['page'] as $page ) {
		add_meta_box( $projectinformation_5_metabox['id'], $projectinformation_5_metabox['title'], 'ecpt_show_projectinformation_5_box', $page, 'normal', 'default', $projectinformation_5_metabox );
	}
}

/**
 * Function to show meta boxes.
 */
function ecpt_show_projectinformation_5_box() {
	global $post;
	global $projectinformation_5_metabox;
	global $ecpt_prefix;
	global $wp_version;

	// Use nonce for verification.
	echo '<input type="hidden" name="ecpt_projectinformation_5_meta_box_nonce" value="', wp_create_nonce( basename( __FILE__ ) ), '" />';

	echo '<table class="form-table">';

	foreach ( $projectinformation_5_metabox['fields'] as $field ) {
		// get current post meta data.

		$meta = get_post_meta( $post->ID, $field['id'], true );

		echo '<tr>',
				'<th style="width:20%"><label for="', $field['id'], '">', stripslashes( $field['name'] ), '</label></th>',
				'<td class="ecpt_field_type_' . str_replace( ' ', '_', $field['type'] ) . '">';
		switch ( $field['type'] ) {
			case 'text':
				echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" /><br/>', '', stripslashes( $field['desc'] );
				break;
			case 'textarea':
				if ( $field['rich_editor'] == 1 ) {
						echo wp_editor(
							$meta,
							$field['id'],
							array(
								'textarea_name' => $field['id'],
								'wpautop'       => false,
							)
						); } else {
					echo '<div style="width: 100%;"><textarea name="', $field['id'], '" class="', $field['class'], '" id="', $field['id'], '" cols="60" rows="8" style="width:97%">', $meta ? $meta : $field['std'], '</textarea></div>', '', stripslashes( $field['desc'] );
						}

				break;
		}
		echo '<td>',
			'</tr>';
	}

	echo '</table>';
}

// Save data from meta box.
add_action( 'save_post', 'ecpt_projectinformation_5_save' );
function ecpt_projectinformation_5_save( $post_id ) {
	global $post;
	global $projectinformation_5_metabox;

	// verify nonce.
	if ( ! isset( $_POST['ecpt_projectinformation_5_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['ecpt_projectinformation_5_meta_box_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// check autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// check permissions.
	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	foreach ( $projectinformation_5_metabox['fields'] as $field ) {

		$old = get_post_meta( $post_id, $field['id'], true );
		$new = $_POST[ $field['id'] ];

		if ( $new && $new != $old ) {
			if ( $field['type'] == 'date' ) {
				$new = ecpt_format_date( $new );
				update_post_meta( $post_id, $field['id'], $new );
			} else {
				if ( is_string( $new ) ) {
					$new = $new;
				}
				update_post_meta( $post_id, $field['id'], $new );

			}
		} elseif ( '' == $new && $old ) {
			delete_post_meta( $post_id, $field['id'], $old );
		}
	}
}

/**
 * Function to define project type.
 */
function define_project_type_terms() {
	$terms = array(
		'0' => array(
			'name' => 'Grant Sponsored',
			'slug' => 'grant',
		),
		'1' => array(
			'name' => 'Pilot',
			'slug' => 'pilot',
		),
	);
	return $terms;
}

/**
 * Function to check project type.
 */
function check_project_type_terms() {

	// see if we already have populated any terms.
	$terms = get_terms( 'project_type', array( 'hide_empty' => false ) );

	// if no terms then lets add our terms.
	if ( empty( $terms ) ) {
		$terms = array(
			'0' => array(
				'name' => 'Grant Sponsored',
				'slug' => 'grant',
			),
			'1' => array(
				'name' => 'Pilot',
				'slug' => 'pilot',
			),
		);
		foreach ( $terms as $term ) {
			if ( ! term_exists( $term['name'], 'project_type' ) ) {
				wp_insert_term( $term['name'], 'project_type', array( 'slug' => $term['slug'] ) );
			}
		}
	}

}

add_action( 'init', 'check_project_type_terms' );



add_filter( 'manage_edit-ksasresearchprojects_columns', 'my_ksasresearchprojects_columns' );

function my_ksasresearchprojects_columns( $columns ) {

	$columns = array(
		'cb'       => '<input type="checkbox" />',
		'title'    => __( 'Name' ),
		'projects' => __( 'Project Type' ),
		'date'     => __( 'Date' ),
	);

	return $columns;
}

add_action( 'manage_ksasresearchprojects_posts_custom_column', 'my_manage_ksasresearchprojects_columns', 10, 2 );

function my_manage_ksasresearchprojects_columns( $column, $post_id ) {
	global $post;

	switch ( $column ) {

		/* If displaying the 'program_type' column. */

		case 'projects':
			/* Get the program_types for the post. */
			$terms = get_the_terms( $post_id, 'project_type' );

			/* If terms were found. */
			if ( ! empty( $terms ) ) {

				$out = array();

				/* Loop through each term, linking to the 'edit posts' page for the specific term. */
				foreach ( $terms as $term ) {
					$out[] = sprintf(
						'<a href="%s">%s</a>',
						esc_url(
							add_query_arg(
								array(
									'post_type'    => $post->post_type,
									'project_type' => $term->slug,
								),
								'edit.php'
							)
						),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'project_type', 'display' ) )
					);
				}

				/* Join the terms, separating them with a comma. */
				echo join( ', ', $out );
			}
			/* If no terms were found, output a default message. */
			else {
				_e( 'No Projects Assigned' );
			}

			break;
		/* Just break out of the switch statement for everything else. */
		default:
			break;
	}
}
