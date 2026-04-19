<?php
/**
 * Template Name: Games
 * Description: Self-contained gaming page with Gamezop integration
 */

// Fetch games from Gamezop API (server-side to avoid exposing API)
$games = [];
$cache_key = 'bigtricks_gamezop_games';
$cached_games = get_transient( $cache_key );

if ( false === $cached_games ) {
	$response = wp_remote_get( 'https://pub.gamezop.com/v3/games?id=yN0u4VKTY', [
		'timeout' => 15,
		'headers' => [
			'Accept' => 'application/json',
		],
	] );

	if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );
		
		if ( isset( $data['games'] ) && is_array( $data['games'] ) ) {
			$games = $data['games'];
			set_transient( $cache_key, $games, HOUR_IN_SECONDS ); // Cache for 1 hour
		}
	}
} else {
	$games = $cached_games;
}

// Get categories for filtering
$all_categories = [];
foreach ( $games as $game ) {
	if ( isset( $game['categories']['en'] ) && is_array( $game['categories']['en'] ) ) {
		foreach ( $game['categories']['en'] as $cat ) {
			$all_categories[ $cat ] = true;
		}
	}
}
$all_categories = array_keys( $all_categories );
sort( $all_categories );

get_header();
?>

<style>
/* Reset and base styles for games page */
.games-page * {
	box-sizing: border-box;
}

.games-page {
	background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
	min-height: 100vh;
	color: #ffffff;
	font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
	padding: 0;
	margin: 0;
}

.games-container {
	max-width: 1400px;
	margin: 0 auto;
	padding: 20px;
}

/* Header Section */
.games-header {
	text-align: center;
	padding: 40px 20px;
	background: rgba(0, 0, 0, 0.3);
	border-radius: 20px;
	margin-bottom: 40px;
	border: 1px solid rgba(255, 255, 255, 0.1);
}

.games-header h1 {
	font-size: 3rem;
	font-weight: 800;
	margin: 0 0 10px 0;
	background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
	text-shadow: 0 0 30px rgba(102, 126, 234, 0.5);
}

.games-header p {
	font-size: 1.1rem;
	color: #b8b8d1;
	margin: 0;
}

/* Filter Section */
.games-filter {
	display: flex;
	flex-wrap: wrap;
	gap: 10px;
	margin-bottom: 30px;
	padding: 20px;
	background: rgba(0, 0, 0, 0.2);
	border-radius: 15px;
	border: 1px solid rgba(255, 255, 255, 0.05);
}

.filter-btn {
	padding: 10px 20px;
	background: rgba(102, 126, 234, 0.2);
	border: 1px solid rgba(102, 126, 234, 0.3);
	border-radius: 25px;
	color: #ffffff;
	cursor: pointer;
	transition: all 0.3s ease;
	font-size: 0.9rem;
	font-weight: 600;
	outline: none;
}

.filter-btn:hover {
	background: rgba(102, 126, 234, 0.4);
	transform: translateY(-2px);
	box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.filter-btn.active {
	background: linear-gradient(45deg, #667eea, #764ba2);
	border-color: #667eea;
	box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}

/* Search Box */
.games-search {
	margin-bottom: 30px;
}

.search-input {
	width: 100%;
	padding: 15px 20px;
	background: rgba(0, 0, 0, 0.3);
	border: 1px solid rgba(255, 255, 255, 0.1);
	border-radius: 15px;
	color: #ffffff;
	font-size: 1rem;
	outline: none;
	transition: all 0.3s ease;
}

.search-input::placeholder {
	color: #7a7a9d;
}

.search-input:focus {
	border-color: #667eea;
	box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
}

/* Games Grid */
.games-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
	gap: 25px;
	margin-bottom: 40px;
}

/* Game Card */
.game-card {
	background: rgba(0, 0, 0, 0.4);
	border-radius: 15px;
	overflow: hidden;
	border: 1px solid rgba(255, 255, 255, 0.1);
	transition: all 0.3s ease;
	cursor: pointer;
	position: relative;
}

.game-card:hover {
	transform: translateY(-8px);
	box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
	border-color: rgba(102, 126, 234, 0.5);
}

.game-card-image {
	width: 100%;
	height: 180px;
	object-fit: cover;
	display: block;
}

.game-card-content {
	padding: 15px;
}

.game-card-title {
	font-size: 1.1rem;
	font-weight: 700;
	margin: 0 0 10px 0;
	color: #ffffff;
	line-height: 1.3;
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
	overflow: hidden;
}

.game-card-meta {
	display: flex;
	align-items: center;
	gap: 15px;
	margin-bottom: 10px;
	flex-wrap: wrap;
}

.game-rating {
	display: flex;
	align-items: center;
	gap: 5px;
	font-size: 0.9rem;
	color: #ffd700;
}

.game-plays {
	font-size: 0.85rem;
	color: #b8b8d1;
}

.game-tags {
	display: flex;
	flex-wrap: wrap;
	gap: 6px;
	margin-top: 10px;
}

.game-tag {
	padding: 4px 10px;
	background: rgba(102, 126, 234, 0.2);
	border-radius: 12px;
	font-size: 0.75rem;
	color: #a8a8d1;
	border: 1px solid rgba(102, 126, 234, 0.3);
}

.game-card-badge {
	position: absolute;
	top: 10px;
	right: 10px;
	padding: 5px 12px;
	background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
	border-radius: 20px;
	font-size: 0.75rem;
	font-weight: 700;
	color: #ffffff;
	box-shadow: 0 3px 10px rgba(245, 87, 108, 0.4);
}

/* Portrait badge */
.game-portrait-badge {
	position: absolute;
	top: 10px;
	left: 10px;
	padding: 5px 12px;
	background: rgba(0, 0, 0, 0.7);
	border-radius: 20px;
	font-size: 0.7rem;
	font-weight: 600;
	color: #ffffff;
	border: 1px solid rgba(255, 255, 255, 0.2);
}

/* No results message */
.no-results {
	text-align: center;
	padding: 60px 20px;
	color: #b8b8d1;
}

.no-results h3 {
	font-size: 1.5rem;
	margin-bottom: 10px;
	color: #ffffff;
}

/* Loading state */
.games-loading {
	text-align: center;
	padding: 60px 20px;
	font-size: 1.2rem;
	color: #b8b8d1;
}

/* Stats Section */
.games-stats {
	display: flex;
	justify-content: center;
	gap: 30px;
	flex-wrap: wrap;
	padding: 20px;
	background: rgba(0, 0, 0, 0.2);
	border-radius: 15px;
	margin-bottom: 30px;
}

.stat-item {
	text-align: center;
}

.stat-number {
	font-size: 2rem;
	font-weight: 800;
	background: linear-gradient(45deg, #667eea, #764ba2);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
	display: block;
}

.stat-label {
	font-size: 0.9rem;
	color: #b8b8d1;
	text-transform: uppercase;
	letter-spacing: 1px;
}

/* Responsive Design */
@media (max-width: 768px) {
	.games-header h1 {
		font-size: 2rem;
	}

	.games-header p {
		font-size: 0.95rem;
	}

	.games-grid {
		grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
		gap: 15px;
	}

	.game-card-image {
		height: 140px;
	}

	.game-card-content {
		padding: 12px;
	}

	.game-card-title {
		font-size: 0.95rem;
	}

	.filter-btn {
		padding: 8px 15px;
		font-size: 0.85rem;
	}

	.games-header {
		padding: 30px 15px;
	}

	.stat-number {
		font-size: 1.5rem;
	}
}

@media (max-width: 480px) {
	.games-grid {
		grid-template-columns: repeat(2, 1fr);
		gap: 12px;
	}

	.game-card-image {
		height: 120px;
	}

	.games-container {
		padding: 15px;
	}

	.games-stats {
		gap: 20px;
	}
}

/* Play icon overlay on hover */
.game-card-overlay {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 180px;
	background: rgba(102, 126, 234, 0.9);
	display: flex;
	align-items: center;
	justify-content: center;
	opacity: 0;
	transition: opacity 0.3s ease;
}

.game-card:hover .game-card-overlay {
	opacity: 1;
}

.play-icon {
	width: 60px;
	height: 60px;
	border-radius: 50%;
	background: #ffffff;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 24px;
	color: #667eea;
}

.play-icon::after {
	content: '▶';
	margin-left: 3px;
}
</style>

<div class="games-page">
	<div class="games-container">
		
		<!-- Header -->
		<div class="games-header">
			<h1>🎮 Game Zone</h1>
			<p>Play hundreds of free games instantly - No downloads required!</p>
		</div>

		<?php if ( ! empty( $games ) ) : 
			$total_games = count( $games );
			$total_plays = array_sum( array_column( $games, 'gamePlays' ) );
			$avg_rating = round( array_sum( array_column( $games, 'rating' ) ) / $total_games, 1 );
		?>

		<!-- Stats -->
		<div class="games-stats">
			<div class="stat-item">
				<span class="stat-number"><?php echo number_format( $total_games ); ?></span>
				<span class="stat-label">Games</span>
			</div>
			<div class="stat-item">
				<span class="stat-number"><?php echo number_format( $total_plays ); ?></span>
				<span class="stat-label">Total Plays</span>
			</div>
			<div class="stat-item">
				<span class="stat-number"><?php echo $avg_rating; ?> ★</span>
				<span class="stat-label">Avg Rating</span>
			</div>
		</div>

		<!-- Search -->
		<div class="games-search">
			<input 
				type="text" 
				id="game-search" 
				class="search-input" 
				placeholder="🔍 Search games by name or tag..."
				autocomplete="off"
			>
		</div>

		<!-- Category Filter -->
		<?php if ( ! empty( $all_categories ) ) : ?>
		<div class="games-filter">
			<button class="filter-btn active" data-category="all">All Games</button>
			<?php foreach ( $all_categories as $category ) : ?>
				<button class="filter-btn" data-category="<?php echo esc_attr( strtolower( $category ) ); ?>">
					<?php echo esc_html( $category ); ?>
				</button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<!-- Games Grid -->
		<div class="games-grid" id="games-grid">
			<?php foreach ( $games as $game ) : 
				$game_name = $game['name']['en'] ?? 'Untitled Game';
				$game_url = esc_url( $game['url'] ?? '#' );
				$game_image = esc_url( $game['assets']['cover'] ?? $game['assets']['brick'] ?? '' );
				$game_rating = floatval( $game['rating'] ?? 0 );
				$game_plays = intval( $game['gamePlays'] ?? 0 );
				$is_portrait = (bool) ( $game['isPortrait'] ?? false );
				$categories = $game['categories']['en'] ?? [];
				$tags = $game['tags']['en'] ?? [];
				$is_popular = $game_plays > 50000;
			?>
			<a 
				href="<?php echo $game_url; ?>" 
				class="game-card" 
				target="_blank" 
				rel="noopener noreferrer"
				data-name="<?php echo esc_attr( strtolower( $game_name ) ); ?>"
				data-categories="<?php echo esc_attr( strtolower( implode( ',', $categories ) ) ); ?>"
				data-tags="<?php echo esc_attr( strtolower( implode( ',', $tags ) ) ); ?>"
			>
				<div style="position: relative;">
					<img 
						src="<?php echo $game_image; ?>" 
						alt="<?php echo esc_attr( $game_name ); ?>"
						class="game-card-image"
						loading="lazy"
					>
					<div class="game-card-overlay">
						<div class="play-icon"></div>
					</div>
					<?php if ( $is_popular ) : ?>
						<span class="game-card-badge">🔥 Popular</span>
					<?php endif; ?>
					<?php if ( $is_portrait ) : ?>
						<span class="game-portrait-badge">📱 Mobile</span>
					<?php endif; ?>
				</div>
				
				<div class="game-card-content">
					<h3 class="game-card-title"><?php echo esc_html( $game_name ); ?></h3>
					
					<div class="game-card-meta">
						<?php if ( $game_rating > 0 ) : ?>
							<div class="game-rating">
								<span>★</span>
								<span><?php echo number_format( $game_rating, 1 ); ?></span>
							</div>
						<?php endif; ?>
						
						<?php if ( $game_plays > 0 ) : ?>
							<div class="game-plays">
								<?php 
								if ( $game_plays >= 1000000 ) {
									echo number_format( $game_plays / 1000000, 1 ) . 'M plays';
								} elseif ( $game_plays >= 1000 ) {
									echo number_format( $game_plays / 1000, 1 ) . 'K plays';
								} else {
									echo number_format( $game_plays ) . ' plays';
								}
								?>
							</div>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $tags ) ) : ?>
						<div class="game-tags">
							<?php foreach ( array_slice( $tags, 0, 3 ) as $tag ) : ?>
								<span class="game-tag"><?php echo esc_html( $tag ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</a>
			<?php endforeach; ?>
		</div>

		<div class="no-results" id="no-results" style="display: none;">
			<h3>No games found</h3>
			<p>Try adjusting your search or filter criteria</p>
		</div>

		<?php else : ?>
		<div class="games-loading">
			<p>⏳ Loading games...</p>
			<p style="font-size: 0.9rem; margin-top: 10px;">If games don't load, please refresh the page.</p>
		</div>
		<?php endif; ?>

	</div>
</div>

<script>
(function() {
	'use strict';
	
	const searchInput = document.getElementById('game-search');
	const filterBtns = document.querySelectorAll('.filter-btn');
	const gameCards = document.querySelectorAll('.game-card');
	const gamesGrid = document.getElementById('games-grid');
	const noResults = document.getElementById('no-results');
	
	let activeCategory = 'all';
	let searchTerm = '';
	
	function filterGames() {
		let visibleCount = 0;
		
		gameCards.forEach(card => {
			const cardName = card.dataset.name || '';
			const cardCategories = card.dataset.categories || '';
			const cardTags = card.dataset.tags || '';
			const searchableText = cardName + ' ' + cardCategories + ' ' + cardTags;
			
			// Check category filter
			const categoryMatch = activeCategory === 'all' || cardCategories.includes(activeCategory);
			
			// Check search term
			const searchMatch = searchTerm === '' || searchableText.includes(searchTerm);
			
			if (categoryMatch && searchMatch) {
				card.style.display = '';
				visibleCount++;
			} else {
				card.style.display = 'none';
			}
		});
		
		// Show/hide no results message
		if (visibleCount === 0) {
			gamesGrid.style.display = 'none';
			noResults.style.display = 'block';
		} else {
			gamesGrid.style.display = 'grid';
			noResults.style.display = 'none';
		}
	}
	
	// Category filter buttons
	filterBtns.forEach(btn => {
		btn.addEventListener('click', function() {
			// Remove active class from all buttons
			filterBtns.forEach(b => b.classList.remove('active'));
			
			// Add active class to clicked button
			this.classList.add('active');
			
			// Update active category
			activeCategory = this.dataset.category;
			
			// Filter games
			filterGames();
		});
	});
	
	// Search input
	if (searchInput) {
		searchInput.addEventListener('input', function() {
			searchTerm = this.value.toLowerCase().trim();
			filterGames();
		});
	}
	
	// Prevent card click when clicking the card itself (already handled by href)
	// But we could add analytics here if needed
	gameCards.forEach(card => {
		card.addEventListener('click', function(e) {
			// Track game click (optional)
			const gameName = this.querySelector('.game-card-title').textContent;
			console.log('Playing game:', gameName);
		});
	});
})();
</script>

<?php
get_footer();
?>
