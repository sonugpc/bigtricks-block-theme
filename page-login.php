<?php
/**
 * Template Name: Login Page
 * Template Post Type: page
 *
 * Custom Login Page – assign via Page Attributes → Template.
 *
 * @package Bigtricks
 */

get_header();

$redirect_to_raw = isset( $_GET['redirect_to'] ) ? wp_unslash( $_GET['redirect_to'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$redirect_to     = $redirect_to_raw ? esc_url_raw( $redirect_to_raw ) : bigtricks_get_dashboard_url();

// Active tab: 'login' (default) or 'register'
$active_tab = ( isset( $_GET['tab'] ) && 'register' === sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) ? 'register' : 'login'; // phpcs:ignore WordPress.Security.NonceVerification

// Registration error
$reg_error          = isset( $_GET['reg_error'] ) ? sanitize_text_field( wp_unslash( $_GET['reg_error'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$reg_error_messages = [
	'nonce_failed'          => __( 'Security check failed. Please try again.', 'bigtricks' ),
	'spam'                  => __( 'Submission rejected. Please try again.', 'bigtricks' ),
	'too_fast'              => __( 'Please take a moment to fill in the form.', 'bigtricks' ),
	'captcha_failed'        => __( 'Wrong answer to the security question. Please try again.', 'bigtricks' ),
	'missing_fields'        => __( 'Please fill in all required fields.', 'bigtricks' ),
	'invalid_email'         => __( 'Please enter a valid email address.', 'bigtricks' ),
	'weak_password'         => __( 'Password must be at least 8 characters long.', 'bigtricks' ),
	'password_mismatch'     => __( 'Passwords do not match. Please try again.', 'bigtricks' ),
	'email_exists'          => __( 'An account with this email already exists. Try signing in.', 'bigtricks' ),
	'registration_disabled' => __( 'Account registration is currently disabled.', 'bigtricks' ),
	'register_failed'       => __( 'Could not create your account. Please try again.', 'bigtricks' ),
];

// Stateless math CAPTCHA — answer verified server-side via HMAC (no session/transient needed)
$captcha_a    = wp_rand( 1, 9 );
$captcha_b    = wp_rand( 1, 9 );
$captcha_sum  = $captcha_a + $captcha_b;
$captcha_hash = hash_hmac( 'sha256', (string) $captcha_sum, wp_salt( 'secure_auth' ) );

// Time-challenge token — renders form timestamp, min 3 s required before submit
$form_ts   = time();
$form_time = hash_hmac( 'sha256', (string) $form_ts, wp_salt( 'auth' ) );

$registration_enabled = (bool) get_option( 'users_can_register' );
?>

<main class="flex-1 flex items-center justify-center px-4 py-16 bg-slate-50 dark:bg-slate-950 min-h-[calc(100vh-5rem)]" id="main-content">
	<div class="w-full max-w-md">

		<!-- Card -->
		<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">

			<!-- Brand header -->
			<div class="bg-gradient-to-br from-primary-600 to-blue-600 px-8 pt-8 pb-6 text-center text-white">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center gap-2 text-2xl font-black text-white no-underline mb-2">
					<div class="bg-white/20 p-2 rounded-xl border border-white/30">
						<i data-lucide="zap" class="w-5 h-5 fill-current"></i>
					</div>
					<?php bloginfo( 'name' ); ?>
				</a>
				<p id="bt-auth-subtitle" class="text-primary-100 text-sm font-medium">
					<?php if ( 'register' === $active_tab ) : ?>
						<?php esc_html_e( 'Create a free account to submit deals & track your savings.', 'bigtricks' ); ?>
					<?php else : ?>
						<?php esc_html_e( 'Sign in to access exclusive deals and track your savings.', 'bigtricks' ); ?>
					<?php endif; ?>
				</p>
			</div>

			<!-- Tab switcher -->
			<div class="flex border-b border-slate-200 dark:border-slate-700" role="tablist">
				<button
					type="button"
					id="bt-tab-login"
					class="bt-auth-tab flex-1 py-3.5 text-sm font-black transition-all <?php echo 'login' === $active_tab ? 'text-primary-600 border-b-2 border-primary-600 bg-white dark:bg-slate-900' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 bg-slate-50 dark:bg-slate-800'; ?>"
					data-tab="login"
					aria-selected="<?php echo 'login' === $active_tab ? 'true' : 'false'; ?>"
					role="tab"
				>
					<?php esc_html_e( 'Sign In', 'bigtricks' ); ?>
				</button>
				<button
					type="button"
					id="bt-tab-register"
					class="bt-auth-tab flex-1 py-3.5 text-sm font-black transition-all <?php echo 'register' === $active_tab ? 'text-primary-600 border-b-2 border-primary-600 bg-white dark:bg-slate-900' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 bg-slate-50 dark:bg-slate-800'; ?>"
					data-tab="register"
					aria-selected="<?php echo 'register' === $active_tab ? 'true' : 'false'; ?>"
					role="tab"
				>
					<?php esc_html_e( 'Create Account', 'bigtricks' ); ?>
				</button>
			</div>

			<div class="px-8 py-7">

				<!-- ══════════════════════════════════
				     LOGIN PANEL
				══════════════════════════════════ -->
				<div id="bt-panel-login" class="bt-auth-panel <?php echo 'login' !== $active_tab ? 'hidden' : ''; ?>" role="tabpanel">

					<!-- Error alert -->
					<?php if ( isset( $_GET['login'] ) && 'failed' === sanitize_text_field( wp_unslash( $_GET['login'] ) ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
					<div class="mb-5 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm font-medium px-4 py-3 rounded-xl flex items-center gap-2" role="alert">
						<i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
						<?php esc_html_e( 'Invalid username or password. Please try again.', 'bigtricks' ); ?>
					</div>
					<?php endif; ?>

					<!-- Social buttons (placeholder) -->
					<div class="mb-6">
						<button
							type="button"
							id="bt-google-login-placeholder"
							class="w-full flex items-center justify-center gap-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 hover:border-primary-200 dark:hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 text-slate-700 dark:text-slate-200 font-bold py-3.5 rounded-2xl transition-all text-sm active:scale-[0.98]"
						>
							<svg class="w-5 h-5" viewBox="0 0 24 24" aria-hidden="true">
								<path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
								<path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
								<path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
								<path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
							</svg>
							<?php esc_html_e( 'Continue with Google', 'bigtricks' ); ?>
						</button>
					</div>

					<!-- Divider -->
					<div class="relative flex items-center gap-4 mb-6">
						<div class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></div>
						<span class="text-xs font-bold text-slate-400 uppercase tracking-wider"><?php esc_html_e( 'or', 'bigtricks' ); ?></span>
						<div class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></div>
					</div>

					<!-- WP Login form -->
					<form method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" class="space-y-4">
						<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>">

						<div>
							<label for="bt-username" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
								<?php esc_html_e( 'Email or Username', 'bigtricks' ); ?>
							</label>
							<input
								id="bt-username"
								type="text"
								name="log"
								autocomplete="username"
								required
								class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-2xl px-5 py-3.5 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-300 transition-all"
								placeholder="<?php esc_attr_e( 'your@email.com', 'bigtricks' ); ?>"
							>
						</div>

						<div>
							<div class="flex items-center justify-between mb-1.5">
								<label for="bt-password" class="text-sm font-bold text-slate-700 dark:text-slate-300">
									<?php esc_html_e( 'Password', 'bigtricks' ); ?>
								</label>
								<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="text-xs font-bold text-primary-600 hover:text-primary-800 transition-colors">
									<?php esc_html_e( 'Forgot password?', 'bigtricks' ); ?>
								</a>
							</div>
							<div class="relative">
								<input
									id="bt-password"
									type="password"
									name="pwd"
									autocomplete="current-password"
									required
									class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-2xl px-5 py-3.5 pr-12 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-300 transition-all"
									placeholder="••••••••"
								>
								<button type="button" class="bt-pw-toggle absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" aria-label="<?php esc_attr_e( 'Toggle password visibility', 'bigtricks' ); ?>" data-target="bt-password">
									<i data-lucide="eye" class="w-4 h-4"></i>
								</button>
							</div>
						</div>

						<div class="flex items-center gap-3">
							<input
								id="bt-remember"
								type="checkbox"
								name="rememberme"
								value="forever"
								class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
							>
							<label for="bt-remember" class="text-sm font-medium text-slate-600 dark:text-slate-400">
								<?php esc_html_e( 'Keep me signed in', 'bigtricks' ); ?>
							</label>
						</div>

						<button
							type="submit"
							name="wp-submit"
							class="w-full bg-primary-600 hover:bg-primary-700 text-white font-black py-4 rounded-2xl text-base shadow-lg shadow-primary-200 dark:shadow-none hover:shadow-xl dark:hover:shadow-none transition-all active:scale-[0.98] flex items-center justify-center gap-2"
						>
							<i data-lucide="log-in" class="w-4 h-4"></i>
							<?php esc_html_e( 'Sign In', 'bigtricks' ); ?>
						</button>
					</form>

					<p class="mt-5 text-center text-sm text-slate-500 dark:text-slate-400 font-medium">
						<?php esc_html_e( "Don't have an account?", 'bigtricks' ); ?>
						<button type="button" class="bt-auth-tab font-bold text-primary-600 hover:text-primary-800 transition-colors ml-1" data-tab="register">
							<?php esc_html_e( 'Create one free →', 'bigtricks' ); ?>
						</button>
					</p>
				</div>

				<!-- ══════════════════════════════════
				     REGISTER PANEL
				══════════════════════════════════ -->
				<div id="bt-panel-register" class="bt-auth-panel <?php echo 'register' !== $active_tab ? 'hidden' : ''; ?>" role="tabpanel">

					<?php if ( ! $registration_enabled ) : ?>
					<div class="rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-8 text-center">
						<i data-lucide="user-x" class="w-12 h-12 mx-auto text-slate-400 mb-4"></i>
						<p class="font-bold text-slate-700 dark:text-slate-200 mb-1"><?php esc_html_e( 'Registrations are currently disabled.', 'bigtricks' ); ?></p>
						<p class="text-sm text-slate-500 dark:text-slate-400"><?php esc_html_e( 'Please contact the site administrator.', 'bigtricks' ); ?></p>
					</div>

					<?php else : ?>

					<!-- Registration error -->
					<?php if ( $reg_error && isset( $reg_error_messages[ $reg_error ] ) ) : ?>
					<div class="mb-5 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm font-medium px-4 py-3 rounded-xl flex items-center gap-2" role="alert">
						<i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
						<?php echo esc_html( $reg_error_messages[ $reg_error ] ); ?>
					</div>
					<?php endif; ?>

					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="space-y-4">
						<input type="hidden" name="action"      value="bigtricks_frontend_register">
						<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>">
						<?php wp_nonce_field( 'bigtricks_frontend_register', 'bigtricks_register_nonce' ); ?>

						<!-- Honeypot: visually hidden, must stay empty; bots will fill it -->
						<div style="display:none;position:absolute;left:-9999px;" aria-hidden="true">
							<label for="bt_website">Website</label>
							<input type="text" id="bt_website" name="bt_website" value="" tabindex="-1" autocomplete="off">
						</div>

						<!-- Time-challenge tokens -->
						<input type="hidden" name="bt_form_ts"   value="<?php echo esc_attr( (string) $form_ts ); ?>">
						<input type="hidden" name="bt_form_time" value="<?php echo esc_attr( $form_time ); ?>">

						<div>
							<label for="bt-reg-name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
								<?php esc_html_e( 'Display Name', 'bigtricks' ); ?> <span class="text-red-500" aria-hidden="true">*</span>
							</label>
							<input
								id="bt-reg-name"
								type="text"
								name="reg_display_name"
								autocomplete="name"
								required
								maxlength="60"
								class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-2xl px-5 py-3.5 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
								placeholder="<?php esc_attr_e( 'Your Name', 'bigtricks' ); ?>"
							>
						</div>

						<div>
							<label for="bt-reg-email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
								<?php esc_html_e( 'Email Address', 'bigtricks' ); ?> <span class="text-red-500" aria-hidden="true">*</span>
							</label>
							<input
								id="bt-reg-email"
								type="email"
								name="reg_email"
								autocomplete="email"
								required
								class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-2xl px-5 py-3.5 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
								placeholder="<?php esc_attr_e( 'your@email.com', 'bigtricks' ); ?>"
							>
						</div>

						<div>
							<label for="bt-reg-pw" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
								<?php esc_html_e( 'Password', 'bigtricks' ); ?> <span class="text-red-500" aria-hidden="true">*</span>
							</label>
							<div class="relative">
								<input
									id="bt-reg-pw"
									type="password"
									name="reg_password"
									autocomplete="new-password"
									required
									minlength="8"
									class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-2xl px-5 py-3.5 pr-12 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
									placeholder="<?php esc_attr_e( 'Min. 8 characters', 'bigtricks' ); ?>"
								>
								<button type="button" class="bt-pw-toggle absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" aria-label="<?php esc_attr_e( 'Toggle password visibility', 'bigtricks' ); ?>" data-target="bt-reg-pw">
									<i data-lucide="eye" class="w-4 h-4"></i>
								</button>
							</div>
						</div>

						<div>
							<label for="bt-reg-pw2" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
								<?php esc_html_e( 'Confirm Password', 'bigtricks' ); ?> <span class="text-red-500" aria-hidden="true">*</span>
							</label>
							<input
								id="bt-reg-pw2"
								type="password"
								name="reg_password2"
								autocomplete="new-password"
								required
								minlength="8"
								class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-2xl px-5 py-3.5 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
								placeholder="<?php esc_attr_e( 'Repeat your password', 'bigtricks' ); ?>"
							>
						</div>

						<!-- Math CAPTCHA (stateless: no DB, no transient, answer verified by HMAC) -->
						<div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 rounded-2xl px-5 py-4">
							<label for="bt-captcha" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
								<i data-lucide="shield" class="w-4 h-4 inline-block mr-1 align-text-bottom text-amber-500"></i>
								<?php
								printf(
									/* translators: 1: first number 2: second number */
									esc_html__( 'Quick check — what is %1$d + %2$d?', 'bigtricks' ),
									$captcha_a,
									$captcha_b
								);
								?>
								<span class="text-red-500" aria-hidden="true"> *</span>
							</label>
							<input
								id="bt-captcha"
								type="number"
								name="bt_captcha_answer"
								required
								min="0"
								max="99"
								autocomplete="off"
								class="w-full bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-800/60 rounded-xl px-4 py-3 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-400 transition-all"
								placeholder="<?php esc_attr_e( 'Type your answer', 'bigtricks' ); ?>"
							>
							<input type="hidden" name="bt_captcha_hash" value="<?php echo esc_attr( $captcha_hash ); ?>">
						</div>

						<button
							type="submit"
							class="w-full bg-primary-600 hover:bg-primary-700 text-white font-black py-4 rounded-2xl text-base shadow-lg shadow-primary-200 dark:shadow-none hover:shadow-xl dark:hover:shadow-none transition-all active:scale-[0.98] flex items-center justify-center gap-2"
						>
							<i data-lucide="user-plus" class="w-4 h-4"></i>
							<?php esc_html_e( 'Create My Account', 'bigtricks' ); ?>
						</button>
					</form>

					<?php endif; // registration_enabled ?>

					<p class="mt-5 text-center text-sm text-slate-500 dark:text-slate-400 font-medium">
						<?php esc_html_e( 'Already have an account?', 'bigtricks' ); ?>
						<button type="button" class="bt-auth-tab font-bold text-primary-600 hover:text-primary-800 transition-colors ml-1" data-tab="login">
							<?php esc_html_e( 'Sign in →', 'bigtricks' ); ?>
						</button>
					</p>
				</div>

			</div>
		</div>

		<!-- Trust badges -->
		<div class="mt-6 flex items-center justify-center gap-6 text-xs text-slate-400 font-medium flex-wrap">
			<span class="flex items-center gap-1">
				<i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-500"></i>
				<?php esc_html_e( 'Secure', 'bigtricks' ); ?>
			</span>
			<span class="flex items-center gap-1">
				<i data-lucide="lock" class="w-3.5 h-3.5 text-slate-400"></i>
				<?php esc_html_e( 'SSL Encrypted', 'bigtricks' ); ?>
			</span>
			<span class="flex items-center gap-1">
				<i data-lucide="eye-off" class="w-3.5 h-3.5 text-slate-400"></i>
				<?php esc_html_e( 'Privacy First', 'bigtricks' ); ?>
			</span>
		</div>
	</div>
</main>

<script>
(function () {
	var subtitles = {
		login:    <?php echo wp_json_encode( __( 'Sign in to access exclusive deals and track your savings.', 'bigtricks' ) ); ?>,
		register: <?php echo wp_json_encode( __( 'Create a free account to submit deals & track your savings.', 'bigtricks' ) ); ?>,
	};

	function activateTab( tab ) {
		document.querySelectorAll( '.bt-auth-panel' ).forEach( function ( panel ) {
			panel.classList.toggle( 'hidden', panel.id !== 'bt-panel-' + tab );
		} );
		document.querySelectorAll( '.bt-auth-tab[role="tab"]' ).forEach( function ( btn ) {
			var active = btn.dataset.tab === tab;
			btn.setAttribute( 'aria-selected', active ? 'true' : 'false' );
			btn.classList.toggle( 'text-primary-600',   active );
			btn.classList.toggle( 'border-b-2',         active );
			btn.classList.toggle( 'border-primary-600', active );
			btn.classList.toggle( 'bg-white',           active );
			btn.classList.toggle( 'dark:bg-slate-900',  active );
			btn.classList.toggle( 'text-slate-500',     ! active );
			btn.classList.toggle( 'bg-slate-50',        ! active );
		} );
		var subtitle = document.getElementById( 'bt-auth-subtitle' );
		if ( subtitle && subtitles[ tab ] ) {
			subtitle.textContent = subtitles[ tab ];
		}
	}

	// Tab buttons + inline CTA links
	document.querySelectorAll( '.bt-auth-tab' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			activateTab( btn.dataset.tab );
		} );
	} );

	// Show / hide password toggle
	document.querySelectorAll( '.bt-pw-toggle' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			var target = document.getElementById( btn.dataset.target );
			if ( ! target ) return;
			target.type = target.type === 'password' ? 'text' : 'password';
		} );
	} );
}());
</script>

<?php get_footer(); ?>
