<?php
/**
 * Comments Template — Chat-style UI
 * No website field. Easy comment form.
 *
 * @package Bigtricks
 */

/**
 * Custom walker: renders comments as chat bubbles.
 */
if ( ! class_exists( 'Bigtricks_Chat_Comment_Walker' ) ) :

class Bigtricks_Chat_Comment_Walker extends Walker_Comment {

	public function start_el( &$output, $data_object, $depth = 0, $args = [], $current_object_id = 0 ) {
		$comment = $data_object;
		$indent  = $depth > 0 ? 'ml-10 sm:ml-14' : '';

		$avatar     = get_avatar( $comment, 40, '', '', [ 'class' => 'w-10 h-10 rounded-full object-cover shrink-0 border-2 border-white shadow-sm' ] );
		$author     = esc_html( get_comment_author( $comment ) );
		$date_str   = esc_html( get_comment_date( 'M j, Y · g:i a', $comment ) );
		$reply_link = get_comment_reply_link( array_merge( $args, [
			'depth'     => $depth,
			'max_depth' => $args['max_depth'] ?? 5,
			'before'    => '',
			'after'     => '',
		] ), $comment );
		$edit_link  = get_edit_comment_link( $comment );

		ob_start();
		?>
		<div <?php comment_class( 'bt-chat-comment group ' . $indent, $comment ); ?> id="comment-<?php echo esc_attr( (string) $comment->comment_ID ); ?>">
			<div class="flex items-start gap-3">
				<!-- Avatar -->
				<div class="shrink-0 mt-0.5"><?php echo wp_kses_post( $avatar ); ?></div>

				<!-- Bubble -->
				<div class="flex-1 min-w-0">
					<!-- Name + Date -->
					<div class="flex flex-wrap items-center gap-2 mb-1.5">
						<span class="font-black text-slate-900 text-sm"><?php echo $author; ?></span>
						<?php if ( $comment->user_id && user_can( (int) $comment->user_id, 'edit_posts' ) ) : ?>
						<span class="bg-primary-100 text-primary-700 text-xs font-black px-2 py-0.5 rounded-full">Team</span>
						<?php endif; ?>
						<time class="text-slate-400 text-xs ml-auto"><?php echo $date_str; ?></time>
					</div>

					<!-- Comment text bubble -->
					<?php if ( '0' === $comment->comment_approved ) : ?>
					<div class="bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold px-3 py-2 rounded-xl mb-2">
						<?php esc_html_e( 'Your comment is awaiting moderation.', 'bigtricks' ); ?>
					</div>
					<?php endif; ?>

					<div class="bg-white border border-slate-200 rounded-2xl rounded-tl-sm px-4 py-3 text-slate-700 text-sm leading-relaxed shadow-sm break-words">
						<?php comment_text( $comment ); ?>
					</div>

					<!-- Actions (reply, edit) -->
					<div class="flex items-center gap-3 mt-2 opacity-0 group-hover:opacity-100 transition-opacity">
						<?php if ( $reply_link ) : ?>
						<span class="text-xs font-bold text-slate-400 hover:text-primary-600 transition-colors cursor-pointer bt-reply-link">
							<?php echo wp_kses_post( $reply_link ); ?>
						</span>
						<?php endif; ?>
						<?php if ( current_user_can( 'edit_comment', $comment->comment_ID ) && $edit_link ) : ?>
						<a href="<?php echo esc_url( $edit_link ); ?>" class="text-xs font-bold text-slate-400 hover:text-primary-600 transition-colors">
							<?php esc_html_e( 'Edit', 'bigtricks' ); ?>
						</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
		$output .= ob_get_clean();
	}

	public function end_el( &$output, $data_object, $depth = 0, $args = [] ) {
		// nothing needed
	}
}

endif; // class_exists

if ( post_password_required() ) {
	echo '<p class="text-slate-500 italic p-6">' . esc_html__( 'This post is password protected. Enter the password to view comments.', 'bigtricks' ) . '</p>';
	return;
}

$comment_count = get_comments_number();
?>

<div class="bt-comments-section mt-10 pt-8 border-t-2 border-slate-100" id="comments">

	<!-- HEADER -->
	<div class="flex items-center justify-between mb-6">
		<h2 class="text-xl font-black text-slate-900 flex items-center gap-2">
			<div class="bg-primary-100 p-1.5 rounded-lg">
				<i data-lucide="message-circle" class="w-5 h-5 text-primary-600"></i>
			</div>
			<?php
			if ( $comment_count > 0 ) {
				printf(
					esc_html( _n( '%s Comment', '%s Comments', $comment_count, 'bigtricks' ) ),
					'<span class="text-primary-600">' . esc_html( number_format_i18n( $comment_count ) ) . '</span>'
				);
			} else {
				esc_html_e( 'Be the first to comment!', 'bigtricks' );
			}
			?>
		</h2>
	</div>

	<!-- COMMENT LIST (chat bubbles) -->
	<?php if ( have_comments() ) : ?>
	<div class="space-y-4 mb-8" id="bt-comment-list">
		<?php
		wp_list_comments( [
			'walker'      => new Bigtricks_Chat_Comment_Walker(),
			'style'       => 'div',
			'short_ping'  => true,
		] );
		?>
	</div>

	<!-- Comment Pagination -->
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav class="bt-comment-nav flex justify-between mb-8 text-sm font-bold">
		<?php
		$prev = get_previous_comments_link( __( '← Older Comments', 'bigtricks' ) );
		$next = get_next_comments_link( __( 'Newer Comments →', 'bigtricks' ) );
		if ( $prev ) echo '<span class="text-primary-600 hover:underline">' . wp_kses_post( $prev ) . '</span>';
		if ( $next ) echo '<span class="text-primary-600 hover:underline ml-auto">' . wp_kses_post( $next ) . '</span>';
		?>
	</nav>
	<?php endif; ?>
	<?php endif; // have_comments() ?>

	<!-- COMMENT FORM -->
	<?php if ( comments_open() ) :
		$commenter = wp_get_current_commenter();
		$aria_req  = ' required aria-required="true"';
		?>
	<div class="bt-comment-form-wrap bg-slate-50 rounded-2xl p-5 sm:p-6 border border-slate-200">
		<div class="flex items-center gap-3 mb-4">
			<!-- Avatar placeholder for form -->
			<div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center shrink-0">
				<?php if ( is_user_logged_in() ) :
					echo get_avatar( get_current_user_id(), 40, '', '', [ 'class' => 'w-10 h-10 rounded-full object-cover' ] );
				else : ?>
				<i data-lucide="user" class="w-5 h-5 text-primary-500"></i>
				<?php endif; ?>
			</div>
			<h3 class="font-black text-slate-900 text-base">
				<?php is_user_logged_in() ?
					esc_html_e( 'Leave a comment', 'bigtricks' ) :
					esc_html_e( 'Join the conversation', 'bigtricks' );
				?>
			</h3>
		</div>

		<?php
		comment_form( [
			'id_form'              => 'bt-comment-form',
			'class_form'           => 'bt-chat-form space-y-3',
			'title_reply'          => '',
			'title_reply_to'       => esc_html__( 'Reply to %s', 'bigtricks' ),
			'cancel_reply_link'    => esc_html__( 'Cancel reply', 'bigtricks' ),
			'label_submit'         => esc_html__( 'Post Comment', 'bigtricks' ),
			'class_submit'         => 'bt-submit-btn bg-primary-600 hover:bg-primary-700 text-white font-black py-3 px-7 rounded-xl transition-colors cursor-pointer border-0 text-sm flex items-center gap-2',
			'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s"><i data-lucide="send" class="w-4 h-4"></i> %4$s</button>',
			'submit_field'         => '<div class="flex items-center justify-between gap-4 pt-1">%1$s %2$s</div>',
			'comment_field'        => '<div class="relative"><textarea id="comment" name="comment" rows="3" placeholder="' . (is_singular('referral-codes') ? esc_attr__( 'Join the discussion...', 'bigtricks' ) : esc_attr__( 'Write a comment or note about your code…', 'bigtricks' )) . '" class="bt-chat-textarea w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent resize-none transition-all"' . $aria_req . '></textarea></div>',
			'fields'               => apply_filters( 'comment_form_default_fields', [
				'author' => '<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3"><div>'
					. '<label for="author" class="block text-xs font-bold text-slate-500 mb-1">' . esc_html__( 'Name', 'bigtricks' ) . ' <span class="text-red-400">*</span></label>'
					. '<input id="author" name="author" type="text" placeholder="' . esc_attr__( 'Your name', 'bigtricks' ) . '" value="' . esc_attr( $commenter['comment_author'] ) . '" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent transition-all"' . $aria_req . '></div>',
				'email'  => '<div>'
					. '<label for="email" class="block text-xs font-bold text-slate-500 mb-1">' . esc_html__( 'Email', 'bigtricks' ) . ' <span class="text-red-400">*</span></label>'
					. '<input id="email" name="email" type="email" placeholder="' . esc_attr__( 'your@email.com', 'bigtricks' ) . '" value="' . esc_attr( $commenter['comment_author_email'] ) . '" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent transition-all"' . $aria_req . '><p class="text-xs text-slate-400 mt-1">' . esc_html__( 'Never shown publicly.', 'bigtricks' ) . '</p></div></div>',
				'url'    => '', // Remove website field
			] ),
			'logged_in_as'         => '',
			'comment_notes_before' => '',
			'comment_notes_after'  => '',
		] );
		?>
	</div>
	<?php else : ?>
	<p class="text-slate-500 text-sm italic p-4 bg-slate-50 rounded-xl">
		<?php esc_html_e( 'Comments are closed.', 'bigtricks' ); ?>
	</p>
	<?php endif; // comments_open() ?>

</div><!-- /.bt-comments-section -->


