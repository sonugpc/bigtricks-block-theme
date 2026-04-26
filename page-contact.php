<?php
/**
 * Template Name: Contact Us
 * Template Post Type: page
 *
 * @package Bigtricks
 */

declare( strict_types=1 );

get_header();

$bt_telegram  = bigtricks_option( 'bt_telegram_url' );
$bt_whatsapp  = bigtricks_option( 'bt_whatsapp_url' );
?>

<style>
/* ── Contact page scoped styles ─────────────────────── */
.bt-contact-input {
	width: 100%;
	padding: .75rem 1rem;
	border-radius: .75rem;
	border: 1.5px solid #e2e8f0;
	background: #f8fafc;
	color: #0f172a;
	font-size: .9375rem;
	font-weight: 500;
	transition: border-color .18s, box-shadow .18s, background .18s;
	outline: none;
}
.bt-contact-input:focus {
	border-color: #4f46e5;
	background: #fff;
	box-shadow: 0 0 0 3px rgba(79,70,229,.12);
}
.bt-contact-input::placeholder { color: #94a3b8; font-weight: 400; }
.dark .bt-contact-input {
	background: #1e293b;
	border-color: #334155;
	color: #f1f5f9;
}
.dark .bt-contact-input:focus {
	border-color: #6366f1;
	background: #0f172a;
	box-shadow: 0 0 0 3px rgba(99,102,241,.18);
}
.bt-contact-label {
	display: block;
	font-size: .8125rem;
	font-weight: 700;
	color: #475569;
	letter-spacing: .02em;
	margin-bottom: .375rem;
}
.dark .bt-contact-label { color: #94a3b8; }
#bt-contact-feedback {
	display: none;
	padding: 1rem 1.25rem;
	border-radius: .875rem;
	font-weight: 600;
	font-size: .9375rem;
}
#bt-contact-feedback.bt-success {
	background: #f0fdf4; border: 1.5px solid #bbf7d0; color: #15803d;
}
#bt-contact-feedback.bt-error {
	background: #fef2f2; border: 1.5px solid #fecaca; color: #b91c1c;
}
.dark #bt-contact-feedback.bt-success {
	background: #052e16; border-color: #166534; color: #86efac;
}
.dark #bt-contact-feedback.bt-error {
	background: #450a0a; border-color: #991b1b; color: #fca5a5;
}
</style>

<main class="flex-1" id="main-content">

	<!-- ── Hero ─────────────────────────────────────────────── -->
	<section class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-indigo-800 text-white py-16 md:py-24">
		<div class="absolute inset-0 pointer-events-none" style="background-image:radial-gradient(ellipse at 70% 30%,rgba(255,255,255,.08) 0%,transparent 60%);"></div>
		<div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image:radial-gradient(#fff 1px,transparent 1px);background-size:28px 28px;"></div>
		<div class="relative max-w-3xl mx-auto px-4 text-center">
			<div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-full px-5 py-2 text-sm font-bold mb-6 border border-white/20">
				<span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
				<?php esc_html_e( 'We reply within 24 hours', 'bigtricks' ); ?>
			</div>
			<h1 class="text-4xl md:text-5xl font-black leading-tight mb-4"><?php esc_html_e( 'Get in Touch', 'bigtricks' ); ?></h1>
			<p class="text-white/75 text-lg md:text-xl max-w-xl mx-auto">
				<?php esc_html_e( 'Have a question, tip, or deal to share? We\'d love to hear from you.', 'bigtricks' ); ?>
			</p>
		</div>
	</section>

	<!-- ── Quick channel pills ───────────────────────────────── -->
	<div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
		<div class="max-w-[1100px] mx-auto px-4">
			<div class="flex flex-wrap justify-center gap-3 py-5">
				<?php if ( $bt_telegram ) : ?>
				<a href="<?php echo esc_url( $bt_telegram ); ?>" target="_blank" rel="noopener noreferrer"
				   class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-full bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 text-blue-700 dark:text-blue-300 font-bold text-sm hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors">
					<i data-lucide="send" class="w-4 h-4"></i>
					<?php esc_html_e( 'Telegram — Fastest', 'bigtricks' ); ?>
				</a>
				<?php endif; ?>
				<?php if ( $bt_whatsapp ) : ?>
				<a href="<?php echo esc_url( $bt_whatsapp ); ?>" target="_blank" rel="noopener noreferrer"
				   class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-full bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-bold text-sm hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">
					<i data-lucide="message-circle" class="w-4 h-4"></i>
					<?php esc_html_e( 'WhatsApp Group', 'bigtricks' ); ?>
				</a>
				<?php endif; ?>
				<a href="mailto:support@bigtricks.in"
				   class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-full bg-primary-50 dark:bg-primary-900/20 border border-primary-100 dark:border-primary-800 text-primary-700 dark:text-primary-300 font-bold text-sm hover:bg-primary-100 dark:hover:bg-primary-900/40 transition-colors">
					<i data-lucide="mail" class="w-4 h-4"></i>
					support@bigtricks.in
				</a>
			</div>
		</div>
	</div>

	<!-- ── Main layout ───────────────────────────────────────── -->
	<section class="max-w-[1100px] mx-auto px-4 py-12 md:py-16 grid lg:grid-cols-5 gap-10 lg:gap-14 items-start">

		<!-- Left: info + quick links -->
		<div class="lg:col-span-2 space-y-5">

			<!-- Channel cards -->
			<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl p-6 shadow-sm">
				<h2 class="text-base font-black text-slate-900 dark:text-white mb-5"><?php esc_html_e( 'How to Reach Us', 'bigtricks' ); ?></h2>
				<div class="space-y-1">
					<?php
					$channels = [
						[
							'icon'  => 'mail',
							'bg'    => 'bg-primary-100 dark:bg-primary-900/30',
							'color' => 'text-primary-600 dark:text-primary-400',
							'label' => __( 'Email', 'bigtricks' ),
					'value' => 'support@bigtricks.in',
					'href'  => 'mailto:support@bigtricks.in',
							'note'  => __( 'Best for detailed queries', 'bigtricks' ),
						],
						[
							'icon'  => 'send',
							'bg'    => 'bg-blue-100 dark:bg-blue-900/30',
							'color' => 'text-blue-600 dark:text-blue-400',
							'label' => __( 'Telegram', 'bigtricks' ),
					'value' => '@bigtricksbot',
					'href'  => $bt_telegram ?: 'https://t.me/bigtricksbot',
							'note'  => __( 'Fastest response', 'bigtricks' ),
						],
						[
							'icon'  => 'message-circle',
							'bg'    => 'bg-emerald-100 dark:bg-emerald-900/30',
							'color' => 'text-emerald-600 dark:text-emerald-400',
							'label' => __( 'WhatsApp', 'bigtricks' ),
					'value' => '+91 95870 35595',
					'href'  => $bt_whatsapp ?: 'https://wa.me/919587035595',
							'note'  => __( 'Join our deal community', 'bigtricks' ),
						],
					];
					foreach ( $channels as $ch ) :
						if ( empty( $ch['href'] ) ) continue;
					?>
					<a href="<?php echo esc_url( $ch['href'] ); ?>" target="_blank" rel="noopener noreferrer"
					   class="flex items-center gap-4 group -mx-2 px-2 py-2.5 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
						<div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 <?php echo esc_attr( $ch['bg'] ); ?> group-hover:scale-105 transition-transform">
							<i data-lucide="<?php echo esc_attr( $ch['icon'] ); ?>" class="w-5 h-5 <?php echo esc_attr( $ch['color'] ); ?>"></i>
						</div>
						<div class="min-w-0 flex-1">
							<p class="text-xs font-bold text-slate-400 uppercase tracking-wider"><?php echo esc_html( $ch['label'] ); ?></p>
							<p class="font-black text-slate-900 dark:text-white text-sm truncate"><?php echo esc_html( $ch['value'] ); ?></p>
							<p class="text-xs text-slate-400 mt-0.5"><?php echo esc_html( $ch['note'] ); ?></p>
						</div>
						<i data-lucide="arrow-right" class="w-4 h-4 text-slate-300 dark:text-slate-600 shrink-0 group-hover:text-primary-500 group-hover:translate-x-1 transition-all"></i>
					</a>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Response time -->
			<div class="flex items-center gap-4 px-5 py-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm">
				<div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center shrink-0">
					<i data-lucide="clock" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
				</div>
				<div>
					<p class="font-black text-slate-900 dark:text-white text-sm"><?php esc_html_e( 'Typical reply time', 'bigtricks' ); ?></p>
					<p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold"><?php esc_html_e( 'Within 24 hours on working days', 'bigtricks' ); ?></p>
				</div>
			</div>

			<!-- Quick links -->
			<div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-100 dark:border-primary-800/50 rounded-3xl p-6">
				<p class="font-black text-primary-700 dark:text-primary-300 text-sm flex items-center gap-2 mb-4">
					<i data-lucide="zap" class="w-4 h-4"></i>
					<?php esc_html_e( 'Other Ways to Connect', 'bigtricks' ); ?>
				</p>
				<ul class="space-y-3 text-sm">
					<li class="flex items-center justify-between gap-2">
						<span class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
							<i data-lucide="tag" class="w-3.5 h-3.5 text-primary-500 shrink-0"></i>
							<?php esc_html_e( 'Submit a deal', 'bigtricks' ); ?>
						</span>
						<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>" class="text-primary-600 dark:text-primary-400 font-bold hover:underline shrink-0"><?php esc_html_e( 'Submit →', 'bigtricks' ); ?></a>
					</li>
					<li class="flex items-center justify-between gap-2">
						<span class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
							<i data-lucide="megaphone" class="w-3.5 h-3.5 text-primary-500 shrink-0"></i>
							<?php esc_html_e( 'Advertise with us', 'bigtricks' ); ?>
						</span>
						<a href="<?php echo esc_url( home_url( '/advertise-us-bigtricks/' ) ); ?>" class="text-primary-600 dark:text-primary-400 font-bold hover:underline shrink-0"><?php esc_html_e( 'Advertise →', 'bigtricks' ); ?></a>
					</li>
					<li class="flex items-center justify-between gap-2">
						<span class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
							<i data-lucide="square-pen" class="w-3.5 h-3.5 text-primary-500 shrink-0"></i>
							<?php esc_html_e( 'Write for us', 'bigtricks' ); ?>
						</span>
						<a href="<?php echo esc_url( home_url( '/become-an-editor/' ) ); ?>" class="text-primary-600 dark:text-primary-400 font-bold hover:underline shrink-0"><?php esc_html_e( 'Apply →', 'bigtricks' ); ?></a>
					</li>
				</ul>
			</div>

		</div><!-- /left -->

		<!-- Right: form -->
		<div class="lg:col-span-3">
			<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl shadow-sm overflow-hidden">

				<div class="px-7 pt-7 pb-5 border-b border-slate-100 dark:border-slate-800">
					<h2 class="text-xl font-black text-slate-900 dark:text-white"><?php esc_html_e( 'Send Us a Message', 'bigtricks' ); ?></h2>
					<p class="text-slate-500 dark:text-slate-400 text-sm mt-1"><?php esc_html_e( 'All fields marked * are required.', 'bigtricks' ); ?></p>
				</div>

				<form id="bt-contact-page-form" class="px-7 py-7 space-y-5" novalidate>
					<input type="hidden" name="action" value="bigtricks_submit_form">
					<input type="hidden" name="form_type" value="contact">
					<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'bigtricks_contact_form' ) ); ?>">

					<!-- Name + Email -->
					<div class="grid sm:grid-cols-2 gap-5">
						<div>
							<label class="bt-contact-label" for="ct-name">
								<?php esc_html_e( 'Your Name', 'bigtricks' ); ?> <span class="text-red-500">*</span>
							</label>
							<input type="text" id="ct-name" name="contact_name" required autocomplete="name"
							       placeholder="<?php esc_attr_e( 'John Doe', 'bigtricks' ); ?>"
							       class="bt-contact-input">
						</div>
						<div>
							<label class="bt-contact-label" for="ct-email">
								<?php esc_html_e( 'Email Address', 'bigtricks' ); ?> <span class="text-red-500">*</span>
							</label>
							<input type="email" id="ct-email" name="contact_email" required autocomplete="email"
							       placeholder="<?php esc_attr_e( 'you@example.com', 'bigtricks' ); ?>"
							       class="bt-contact-input">
						</div>
					</div>

					<!-- WhatsApp (optional) -->
					<div>
						<label class="bt-contact-label" for="ct-whatsapp">
							<?php esc_html_e( 'WhatsApp Number', 'bigtricks' ); ?>
							<span class="text-slate-400 font-normal">&nbsp;<?php esc_html_e( '(optional)', 'bigtricks' ); ?></span>
						</label>
						<div class="relative">
							<span class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 text-sm font-medium">+91</span>
							<input type="tel" id="ct-whatsapp" name="contact_whatsapp" autocomplete="tel"
							       placeholder="<?php esc_attr_e( '98765 43210', 'bigtricks' ); ?>"
							       class="bt-contact-input pl-12">
						</div>
					</div>

					<!-- Message -->
					<div>
						<label class="bt-contact-label" for="ct-message">
							<?php esc_html_e( 'Message', 'bigtricks' ); ?> <span class="text-red-500">*</span>
						</label>
						<textarea id="ct-message" name="contact_message" required rows="5"
						          placeholder="<?php esc_attr_e( 'Tell us what\'s on your mind…', 'bigtricks' ); ?>"
						          class="bt-contact-input resize-none"></textarea>
					</div>

					<!-- Feedback area -->
					<div id="bt-contact-feedback" role="alert" aria-live="polite"></div>

					<!-- Submit button -->
					<button type="submit" id="bt-contact-submit"
					        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-black py-3.5 px-6 rounded-2xl shadow-md hover:shadow-lg transition-all active:scale-[.98] flex items-center justify-center gap-3 text-base">
						<i data-lucide="send" class="w-5 h-5"></i>
						<span id="bt-contact-submit-label"><?php esc_html_e( 'Send Message', 'bigtricks' ); ?></span>
					</button>

					<p class="text-xs text-slate-400 text-center"><?php esc_html_e( 'We respect your privacy. No spam, ever.', 'bigtricks' ); ?></p>
				</form>

			</div>
		</div><!-- /right -->

	</section>

</main>

<script>
(function () {
	var form     = document.getElementById('bt-contact-page-form');
	var feedback = document.getElementById('bt-contact-feedback');
	var btnLabel = document.getElementById('bt-contact-submit-label');
	var btn      = document.getElementById('bt-contact-submit');
	if ( ! form ) return;

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		var name    = form.querySelector('[name="contact_name"]').value.trim();
		var email   = form.querySelector('[name="contact_email"]').value.trim();
		var message = form.querySelector('[name="contact_message"]').value.trim();
		if ( ! name || ! email || ! message ) {
			show('bt-error', '<?php echo esc_js( __( 'Please fill in all required fields.', 'bigtricks' ) ); ?>');
			return;
		}
		btn.disabled = true;
		if ( btnLabel ) btnLabel.textContent = '<?php echo esc_js( __( 'Sending…', 'bigtricks' ) ); ?>';
		hide();
		fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
			method  : 'POST',
			headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
			body    : new URLSearchParams(new FormData(form)).toString(),
		})
		.then(function (r) { return r.json(); })
		.then(function (d) {
			if ( d.success ) {
				show('bt-success', d.data.message + (d.data.details ? ' ' + d.data.details : ''));
				form.reset();
			} else {
				show('bt-error', d.data && d.data.message ? d.data.message : '<?php echo esc_js( __( 'Something went wrong. Please try again.', 'bigtricks' ) ); ?>');
			}
		})
		.catch(function () {
			show('bt-error', '<?php echo esc_js( __( 'Network error. Please check your connection and try again.', 'bigtricks' ) ); ?>');
		})
		.finally(function () {
			btn.disabled = false;
			if ( btnLabel ) btnLabel.textContent = '<?php echo esc_js( __( 'Send Message', 'bigtricks' ) ); ?>';
		});
	});

	function show(cls, msg) {
		feedback.className = cls;
		feedback.textContent = msg;
		feedback.style.display = 'block';
		feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
	}
	function hide() { feedback.style.display = 'none'; }
})();
</script>

<?php get_footer(); ?>
