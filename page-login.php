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
?>

<main class="flex-1 flex items-center justify-center px-4 py-16 bg-slate-50 min-h-[calc(100vh-5rem)]" id="main-content">
	<div class="w-full max-w-md">

		<!-- Card -->
		<div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">

			<!-- Card Header -->
			<div class="bg-gradient-to-br from-indigo-600 to-blue-600 px-8 pt-10 pb-8 text-center text-white">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center gap-2 text-3xl font-black text-white no-underline mb-3">
					<div class="bg-white/20 p-2 rounded-xl border border-white/30">
						<i data-lucide="zap" class="w-6 h-6 fill-current"></i>
					</div>
					<?php bloginfo( 'name' ); ?>
				</a>
				<p class="text-indigo-100 text-sm font-medium"><?php esc_html_e( 'Sign in to access exclusive deals and track your savings.', 'bigtricks' ); ?></p>
			</div>

			<div class="px-8 py-8">

				<!-- Social Login Buttons (mockup) -->
				<div class="space-y-3 mb-6">
					<button
						type="button"
						class="w-full flex items-center justify-center gap-3 bg-white border-2 border-slate-200 hover:border-indigo-200 hover:bg-indigo-50 text-slate-700 font-bold py-3.5 rounded-2xl transition-all text-sm shadow-sm hover:shadow-md active:scale-[0.98]"
						onclick="alert('Google login requires a plugin like Social Login or WooCommerce Social Login.')"
					>
						<svg class="w-5 h-5" viewBox="0 0 24 24" aria-hidden="true">
							<path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
							<path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
							<path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
							<path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
						</svg>
						<?php esc_html_e( 'Continue with Google', 'bigtricks' ); ?>
					</button>

					<button
						type="button"
						class="w-full flex items-center justify-center gap-3 bg-slate-900 hover:bg-slate-800 text-white font-bold py-3.5 rounded-2xl transition-all text-sm shadow-sm hover:shadow-md active:scale-[0.98]"
						onclick="alert('Apple login requires a plugin or custom OAuth implementation.')"
					>
						<svg class="w-5 h-5 fill-white" viewBox="0 0 24 24" aria-hidden="true">
							<path d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701"/>
						</svg>
						<?php esc_html_e( 'Continue with Apple', 'bigtricks' ); ?>
					</button>
				</div>

				<!-- Divider -->
				<div class="relative flex items-center gap-4 mb-6">
					<div class="flex-1 h-px bg-slate-200"></div>
					<span class="text-xs font-bold text-slate-400 uppercase tracking-wider"><?php esc_html_e( 'or', 'bigtricks' ); ?></span>
					<div class="flex-1 h-px bg-slate-200"></div>
				</div>

				<!-- WP Login Form -->
				<?php
				// Display login messages/errors
				if ( isset( $_GET['login'] ) && $_GET['login'] === 'failed' ) : // phpcs:ignore WordPress.Security.NonceVerification
				?>
				<div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm font-medium px-4 py-3 rounded-xl flex items-center gap-2" role="alert">
					<i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
					<?php esc_html_e( 'Invalid username or password. Please try again.', 'bigtricks' ); ?>
				</div>
				<?php endif; ?>

				<form method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" class="space-y-4">
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php wp_nonce_field( 'login_nonce', 'login_nonce_field' ); ?>

					<div>
						<label for="bt-username" class="block text-sm font-bold text-slate-700 mb-1.5">
							<?php esc_html_e( 'Email or Username', 'bigtricks' ); ?>
						</label>
						<input
							id="bt-username"
							type="text"
							name="log"
							autocomplete="username"
							required
							class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-300 transition-all"
							placeholder="<?php esc_attr_e( 'your@email.com', 'bigtricks' ); ?>"
						>
					</div>

					<div>
						<div class="flex items-center justify-between mb-1.5">
							<label for="bt-password" class="text-sm font-bold text-slate-700">
								<?php esc_html_e( 'Password', 'bigtricks' ); ?>
							</label>
							<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
								<?php esc_html_e( 'Forgot password?', 'bigtricks' ); ?>
							</a>
						</div>
						<input
							id="bt-password"
							type="password"
							name="pwd"
							autocomplete="current-password"
							required
							class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-300 transition-all"
							placeholder="••••••••"
						>
					</div>

					<div class="flex items-center gap-3">
						<input
							id="bt-remember"
							type="checkbox"
							name="rememberme"
							value="forever"
							class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
						>
						<label for="bt-remember" class="text-sm font-medium text-slate-600">
							<?php esc_html_e( 'Keep me signed in', 'bigtricks' ); ?>
						</label>
					</div>

					<button
						type="submit"
						name="wp-submit"
						class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-2xl text-base shadow-lg shadow-indigo-200 hover:shadow-xl transition-all active:scale-[0.98] flex items-center justify-center gap-2"
					>
						<i data-lucide="log-in" class="w-5 h-5"></i>
						<?php esc_html_e( 'Sign In', 'bigtricks' ); ?>
					</button>
				</form>

				<!-- Register link -->
				<p class="mt-6 text-center text-sm text-slate-500 font-medium">
					<?php esc_html_e( "Don't have an account?", 'bigtricks' ); ?>
					<a href="<?php echo esc_url( wp_registration_url() ); ?>" class="font-bold text-indigo-600 hover:text-indigo-800 transition-colors ml-1">
						<?php esc_html_e( 'Create one for free', 'bigtricks' ); ?>
					</a>
				</p>
			</div>
		</div>

		<!-- Trust badges -->
		<div class="mt-6 flex items-center justify-center gap-6 text-xs text-slate-400 font-medium">
			<span class="flex items-center gap-1">
				<i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-500"></i>
				<?php esc_html_e( 'Secure Login', 'bigtricks' ); ?>
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

<?php get_footer(); ?>
