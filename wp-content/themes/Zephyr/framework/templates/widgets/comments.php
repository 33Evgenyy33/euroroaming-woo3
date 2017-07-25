<?php

if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && 'comments.php' == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'Do not load this page directly. Thanks!' );
}

if ( post_password_required() ) {
	return;
}

$comments_number = get_comments_number();
?>
<div id="comments" class="w-comments">
	<?php if ( $comments_number > 0 ) { ?>
		<h4 class="w-comments-title">
			<?php
			$comments_label = '<span>';
			$comments_label .= sprintf( us_translate_n( '%s <span class="screen-reader-text">Comment</span>', '%s <span class="screen-reader-text">Comments</span>', $comments_number ), $comments_number );
			$comments_label .= '.</span> ';
			$comments_label .= '<a href="#respond">' . __( 'Leave new', 'us' ) . '</a>';
			comments_number( us_translate( 'No Comments' ), $comments_label, $comments_label );
			?>
		</h4>

		<div class="w-comments-list">
			<?php wp_list_comments(
				array(
					'callback' => 'us_comment_start',
					'end-callback' => 'us_comment_end',
					'walker' => new Walker_Comments_US(),
				)
			); ?>
		</div>

		<div class="w-comments-pagination">
			<?php previous_comments_link() ?>
			<?php next_comments_link() ?>
		</div>
	<?php } ?>
	<?php if ( comments_open() ) : ?>
		<?php if ( get_option( 'comment_registration' ) AND ! is_user_logged_in() ) { ?>
			<div class="w-comments-form-text"><?php printf( us_translate( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( get_permalink() ) ); ?></div>
		<?php } else {
			$commenter = wp_get_current_commenter();

			$fields = apply_filters(
				'us_comment_form_fields', array(
				'comment' => array(
					'type' => 'textarea',
					'name' => 'comment',
					'placeholder' => us_translate_x( 'Comment', 'noun' ),
					'required' => TRUE,
				),
				'author' => array(
					'type' => 'textfield',
					'name' => 'author',
					'placeholder' => us_translate( 'Name' ),
					'required' => get_option( 'require_name_email' ),
					'value' => $commenter['comment_author'],
				),
				'email' => array(
					'type' => 'email',
					'name' => 'email',
					'placeholder' => us_translate( 'Email' ),
					'required' => get_option( 'require_name_email' ),
					'value' => $commenter['comment_author_email'],
				),
				'url' => array(
					'type' => 'textfield',
					'name' => 'url',
					'placeholder' => us_translate( 'Website' ),
					'value' => $commenter['comment_author_url'],
				),
			)
			);

			$comment_form_args = array( 'fields' => array() );
			foreach ( $fields as $field_name => $field ) {
				if ( $field_name == 'comment' ) {
					$comment_form_args['comment_field'] = us_get_template( 'templates/form/' . $field['type'], $field );
				} else {
					$comment_form_args['fields'][$field_name] = us_get_template( 'templates/form/' . $field['type'], $field );
				}
			}

			$comment_form_args['fields'] = apply_filters( 'comment_form_default_fields', $comment_form_args['fields'] );

			comment_form( $comment_form_args );
		} ?>
	<?php endif; ?>
</div>
