<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'min-h-screen bg-slate-50 dark:bg-slate-950 font-sans text-slate-800 dark:text-slate-100 flex flex-col overflow-x-hidden' ); ?>>
<?php wp_body_open(); ?>

<!-- ──────────────────── HEADER ──────────────────── -->
<header class="bg-white sticky top-0 z-50 shadow-sm border-b border-slate-200 w-full" role="banner">
	<div class="max-w-[1400px] mx-auto px-4 flex items-center justify-between h-16 md:h-20 gap-4">

		<!-- Logo & Mobile Menu Toggle -->
		<div class="flex items-center gap-3 shrink-0">
			<button
				id="bt-mobile-menu-toggle"
				class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-full transition-colors"
				aria-label="<?php esc_attr_e( 'Toggle menu', 'bigtricks' ); ?>"
				aria-expanded="false"
				aria-controls="bt-mobile-menu"
			>
				<i data-lucide="more-vertical" class="w-6 h-6"></i>
			</button>

			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-2xl font-black flex items-center gap-2 text-slate-900 no-underline hover:no-underline" aria-label="<?php bloginfo( 'name' ); ?> Home">
				<div class="bg-primary-600 text-white p-1.5 rounded-xl shadow-lg shadow-primary-200">
					<i data-lucide="zap" class="w-5 h-5 fill-current"></i>
				</div>
				<span class="hidden xs:block"><?php bloginfo( 'name' ); ?></span>
			</a>
		</div>

		<!-- Desktop Navigation (WP Menu with icon support via [icon-name] prefix) -->
		<nav class="hidden lg:flex items-center gap-1 xl:gap-2 font-bold text-slate-600 text-sm ml-4 shrink-0" aria-label="<?php esc_attr_e( 'Primary', 'bigtricks' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( [
					'theme_location'  => 'primary',
					'menu_class'      => 'flex items-center gap-1 xl:gap-2 list-none m-0 p-0',
					'container'       => false,
					'depth'           => 1,
					'walker'          => new Bigtricks_Icon_Nav_Walker(),
					'fallback_cb'     => false,
					'items_wrap'      => '<ul class="%2$s">%3$s</ul>',
				] );
			} else {
				// Fallback if no menu assigned
				?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-1.5 hover:text-primary-600 transition-colors font-bold text-sm py-1 px-2 rounded-lg <?php echo is_front_page() ? 'text-primary-600' : ''; ?>">
					<i data-lucide="home" class="w-4 h-4 shrink-0"></i><span><?php esc_html_e( 'Home', 'bigtricks' ); ?></span>
				</a>
				<a href="<?php echo esc_url( home_url( '/store/' ) ); ?>" class="flex items-center gap-1.5 hover:text-primary-600 transition-colors font-bold text-sm py-1 px-2 rounded-lg">
					<i data-lucide="shopping-bag" class="w-4 h-4 shrink-0"></i><span><?php esc_html_e( 'Stores', 'bigtricks' ); ?></span>
				</a>
				<a href="<?php echo esc_url( home_url( '/category/credit-cards/' ) ); ?>" class="flex items-center gap-1.5 hover:text-primary-600 transition-colors font-bold text-sm py-1 px-2 rounded-lg">
					<i data-lucide="credit-card" class="w-4 h-4 shrink-0"></i><span><?php esc_html_e( 'Cards', 'bigtricks' ); ?></span>
				</a>
				<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="flex items-center gap-1.5 hover:text-primary-600 transition-colors font-bold text-sm py-1 px-2 rounded-lg">
					<i data-lucide="book-open" class="w-4 h-4 shrink-0"></i><span><?php esc_html_e( 'Blog', 'bigtricks' ); ?></span>
				</a>
				<?php
			}
			?>
		</nav>

		<!-- Search Bar (Desktop) -->
		<div class="hidden md:flex flex-1 max-w-xl mx-4 lg:mx-8 relative group">
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="w-full relative">
				<label for="bt-search" class="sr-only"><?php esc_html_e( 'Search deals, stores, coupons', 'bigtricks' ); ?></label>
				<input
					id="bt-search"
					type="search"
					name="s"
					placeholder="<?php esc_attr_e( 'Search deals, stores, coupons...', 'bigtricks' ); ?>"
					value="<?php echo esc_attr( get_search_query() ); ?>"
					class="w-full py-2.5 pl-5 pr-12 rounded-full bg-slate-100 text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all border border-transparent focus:border-primary-200 shadow-inner text-sm"
					autocomplete="off"
				>
				<button type="submit" class="absolute right-1.5 top-1.5 bottom-1.5 px-3 bg-primary-600 hover:bg-primary-700 rounded-full text-white shadow-md flex items-center justify-center transition-colors" aria-label="<?php esc_attr_e( 'Search', 'bigtricks' ); ?>">
					<i data-lucide="search" class="w-4 h-4"></i>
				</button>
			</form>
		</div>

		<!-- User Actions -->
		<div class="flex items-center gap-1.5 md:gap-2 shrink-0">

			<!-- Dark Mode Toggle -->
			<button
				id="bt-dark-toggle"
				class="p-2 rounded-full text-slate-500 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
				aria-label="<?php esc_attr_e( 'Toggle dark mode', 'bigtricks' ); ?>"
				title="<?php esc_attr_e( 'Toggle dark mode', 'bigtricks' ); ?>"
			>
				<i data-lucide="moon" class="w-4.5 h-4.5 bt-dark-icon-moon"></i>
				<i data-lucide="sun" class="w-4.5 h-4.5 bt-dark-icon-sun hidden"></i>
			</button>

			<!-- Bell / Notifications -->
			<button
				id="bt-bell-toggle"
				class="relative p-2 rounded-full text-slate-500 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
				aria-label="<?php esc_attr_e( 'Notifications', 'bigtricks' ); ?>"
				aria-expanded="false"
				aria-controls="bt-notification-drawer"
			>
				<i data-lucide="bell" class="w-4.5 h-4.5"></i>
				<span id="bt-notif-badge" class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-red-500 border-2 border-white dark:border-slate-900 rounded-full"></span>
			</button>

			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>" class="hidden sm:flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-primary-600 hover:bg-primary-50 px-4 py-2 rounded-full transition-all border border-slate-200 hover:border-primary-200 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
					<i data-lucide="plus-circle" class="w-4 h-4"></i>
					<span><?php esc_html_e( 'Submit', 'bigtricks' ); ?></span>
				</a>
				<a href="<?php echo esc_url( admin_url() ); ?>" class="flex items-center gap-2 text-sm font-bold bg-slate-900 hover:bg-slate-800 dark:bg-primary-600 dark:hover:bg-primary-700 text-white px-4 py-2.5 rounded-full shadow-md hover:shadow-lg transition-all active:scale-95">
					<i data-lucide="user" class="w-4 h-4"></i>
					<span class="hidden sm:inline"><?php esc_html_e( 'Dashboard', 'bigtricks' ); ?></span>
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/submit-deal/' ) ); ?>" class="hidden sm:flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-primary-600 hover:bg-primary-50 px-4 py-2 rounded-full transition-all border border-slate-200 hover:border-primary-200 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
					<i data-lucide="plus-circle" class="w-4 h-4"></i>
					<span><?php esc_html_e( 'Submit', 'bigtricks' ); ?></span>
				</a>
				<a href="<?php echo esc_url( wp_login_url() ); ?>" class="flex items-center gap-2 text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-full shadow-md shadow-primary-200 hover:shadow-lg transition-all active:scale-95">
					<i data-lucide="user" class="w-4 h-4"></i>
					<span class="hidden sm:inline"><?php esc_html_e( 'Sign In', 'bigtricks' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</header>

<!-- ──────────────────── NOTIFICATION DRAWER ──────────────────── -->
<div
	id="bt-notification-drawer"
	class="fixed top-16 md:top-20 right-2 sm:right-4 z-50 w-80 sm:w-96 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transition-all duration-300 scale-95 opacity-0 pointer-events-none origin-top-right"
	aria-hidden="true"
>
	<div class="bg-slate-50 dark:bg-slate-800 px-5 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
		<h2 class="font-black text-slate-900 dark:text-white flex items-center gap-2 text-sm">
			<i data-lucide="bell" class="w-4 h-4 text-primary-500"></i>
			<?php esc_html_e( 'Latest Deals & Alerts', 'bigtricks' ); ?>
		</h2>
		<button id="bt-notif-close" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors p-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700" aria-label="<?php esc_attr_e( 'Close notifications', 'bigtricks' ); ?>">
			<i data-lucide="x" class="w-4 h-4"></i>
		</button>
	</div>
	<div id="bt-notif-list" class="max-h-[420px] overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
		<!-- Populated by JS from carousel-config.json via bigtricksData.carouselData -->
		<div class="p-8 text-center text-slate-400 text-sm">
			<i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto mb-2"></i>
			<?php esc_html_e( 'Loading…', 'bigtricks' ); ?>
		</div>
	</div>
	<div class="p-3 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 text-center">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-xs font-bold text-primary-600 hover:underline">
			<?php esc_html_e( 'View all deals →', 'bigtricks' ); ?>
		</a>
	</div>
</div>

<!-- ──────────────────── MOBILE MENU ──────────────────── -->
<div
	id="bt-mobile-menu"
	class="lg:hidden bg-white fixed inset-0 top-16 md:top-20 z-40 shadow-2xl overflow-y-auto pb-24 transition-transform duration-300 -translate-x-full"
	role="dialog"
	aria-modal="true"
	aria-label="<?php esc_attr_e( 'Mobile Navigation', 'bigtricks' ); ?>"
	hidden
>
	<!-- Mobile Search -->
	<div class="p-4 border-b border-slate-100 bg-slate-50">
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="relative w-full">
			<label for="bt-mobile-search" class="sr-only"><?php esc_html_e( 'Search', 'bigtricks' ); ?></label>
			<input
				id="bt-mobile-search"
				type="search"
				name="s"
				placeholder="<?php esc_attr_e( 'Search deals...', 'bigtricks' ); ?>"
				value="<?php echo esc_attr( get_search_query() ); ?>"
				class="w-full py-3 pl-4 pr-12 rounded-xl bg-white border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary-500 shadow-sm"
			>
			<button type="submit" class="absolute right-3 top-3 text-primary-600" aria-label="<?php esc_attr_e( 'Search', 'bigtricks' ); ?>">
				<i data-lucide="search" class="w-5 h-5"></i>
			</button>
		</form>
	</div>

	<!-- Quick Links Grid -->
	<div class="grid grid-cols-2 gap-2 p-4 border-b border-slate-100">
		<a href="<?php echo esc_url( home_url( '/store/' ) ); ?>" class="bg-slate-50 p-3 rounded-xl flex flex-col items-center gap-2 font-bold text-slate-700 hover:text-primary-600 hover:bg-primary-50 transition-colors">
			<i data-lucide="shopping-bag" class="w-6 h-6 text-primary-500"></i>
			<?php esc_html_e( 'Stores', 'bigtricks' ); ?>
		</a>
		<a href="<?php echo esc_url( home_url( '/category/credit-cards/' ) ); ?>" class="bg-slate-50 p-3 rounded-xl flex flex-col items-center gap-2 font-bold text-slate-700 hover:text-primary-600 hover:bg-primary-50 transition-colors">
			<i data-lucide="credit-card" class="w-6 h-6 text-primary-500"></i>
			<?php esc_html_e( 'Cards', 'bigtricks' ); ?>
		</a>
		<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="bg-slate-50 p-3 rounded-xl flex flex-col items-center gap-2 font-bold text-slate-700 hover:text-primary-600 hover:bg-primary-50 transition-colors">
			<i data-lucide="book-open" class="w-6 h-6 text-primary-500"></i>
			<?php esc_html_e( 'Blog', 'bigtricks' ); ?>
		</a>
		<a href="<?php echo esc_url( home_url( '/submit-deal/' ) ); ?>" class="bg-slate-50 p-3 rounded-xl flex flex-col items-center gap-2 font-bold text-slate-700 hover:text-primary-600 hover:bg-primary-50 transition-colors">
			<i data-lucide="plus-circle" class="w-6 h-6 text-primary-500"></i>
			<?php esc_html_e( 'Submit', 'bigtricks' ); ?>
		</a>
	</div>

	<!-- Categories List -->
	<div class="px-4 py-2 text-xs font-black text-slate-400 uppercase tracking-wider">
		<?php esc_html_e( 'Categories', 'bigtricks' ); ?>
	</div>
	<ul role="list">
		<li>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="px-6 py-3 flex items-center justify-between font-bold text-slate-600 hover:bg-slate-50 border-l-4 border-transparent hover:border-primary-300 transition-colors">
				<?php esc_html_e( 'All Deals', 'bigtricks' ); ?>
				<i data-lucide="chevron-right" class="w-4 h-4 opacity-50"></i>
			</a>
		</li>
		<?php
		$mobile_cats = bigtricks_get_top_categories( 10 );
		foreach ( $mobile_cats as $cat ) :
			?>
			<li>
				<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="px-6 py-3 flex items-center justify-between font-bold text-slate-600 hover:bg-slate-50 border-l-4 border-transparent hover:border-primary-300 transition-colors">
					<span><?php echo esc_html( $cat->name ); ?></span>
					<i data-lucide="chevron-right" class="w-4 h-4 opacity-50"></i>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
