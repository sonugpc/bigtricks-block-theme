<?php
/**
 * GitHub Auto-Updater for the Bigtricks theme.
 *
 * Hooks into WordPress's native theme-update machinery.
 * When a new GitHub Release is published the admin will see the standard
 * "Update Available" notice and can install it with one click.
 *
 * Configuration (add to wp-config.php):
 *   define( 'BIGTRICKS_GITHUB_OWNER', 'your-github-username' );
 *   define( 'BIGTRICKS_GITHUB_REPO',  'bigtricks-block' );
 *   // Optional – only needed for private repos:
 *   define( 'BIGTRICKS_GITHUB_TOKEN', 'ghp_xxxxxxxxxxxx' );
 *
 * @package Bigtricks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Bigtricks_GitHub_Updater {

	private string $github_owner;
	private string $github_repo;
	private string $theme_slug   = 'bigtricks-block';
	private string $api_url;
	private string $transient_key = 'bigtricks_github_update';

	public function __construct( string $github_owner, string $github_repo ) {
		$this->github_owner = $github_owner;
		$this->github_repo  = $github_repo;
		$this->api_url      = "https://api.github.com/repos/{$github_owner}/{$github_repo}/releases/latest";

		add_filter( 'pre_set_site_transient_update_themes', [ $this, 'check_for_update' ] );
		add_filter( 'themes_api', [ $this, 'theme_info' ], 20, 3 );
		add_action( 'upgrader_process_complete', [ $this, 'clear_cache' ], 10, 2 );
	}

	// ─────────────────────────────────────────────────────────────────────────
	// GitHub API
	// ─────────────────────────────────────────────────────────────────────────

	/**
	 * Fetch the latest release from GitHub. Result cached for 12 hours.
	 */
	private function get_release_data(): ?object {
		$cached = get_transient( $this->transient_key );

		// 0 means a previous failed request; null means never fetched.
		if ( $cached !== false ) {
			return is_object( $cached ) ? $cached : null;
		}

		$args = [
			'headers' => [
				'Accept'     => 'application/vnd.github+json',
				'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . esc_url( get_bloginfo( 'url' ) ),
			],
			'timeout' => 10,
		];

		// Optional: Bearer token for private repos.
		$token = defined( 'BIGTRICKS_GITHUB_TOKEN' ) ? constant( 'BIGTRICKS_GITHUB_TOKEN' ) : '';
		if ( $token ) {
			$args['headers']['Authorization'] = 'Bearer ' . $token;
		}

		$response = wp_remote_get( esc_url_raw( $this->api_url ), $args );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			// Cache failure for 1 hour to avoid hammering the API.
			set_transient( $this->transient_key, 0, HOUR_IN_SECONDS );
			return null;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );
		if ( ! is_object( $body ) || empty( $body->tag_name ) ) {
			set_transient( $this->transient_key, 0, HOUR_IN_SECONDS );
			return null;
		}

		set_transient( $this->transient_key, $body, 12 * HOUR_IN_SECONDS );
		return $body;
	}

	/**
	 * Extract the ZIP download URL from a release object.
	 * Prefers an uploaded .zip asset; falls back to GitHub's auto-zipball.
	 */
	private function get_zip_url( object $release ): string {
		if ( ! empty( $release->assets ) ) {
			foreach ( $release->assets as $asset ) {
				if ( isset( $asset->name ) && str_ends_with( $asset->name, '.zip' ) ) {
					return $asset->browser_download_url;
				}
			}
		}
		return $release->zipball_url ?? '';
	}

	// ─────────────────────────────────────────────────────────────────────────
	// WordPress hooks
	// ─────────────────────────────────────────────────────────────────────────

	/**
	 * Inject an update entry into WordPress's theme-update transient.
	 *
	 * @param object $transient The update_themes site transient.
	 * @return object
	 */
	public function check_for_update( object $transient ): object {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$release = $this->get_release_data();
		if ( ! $release || empty( $release->tag_name ) ) {
			return $transient;
		}

		$remote_version = ltrim( $release->tag_name, 'v' );
		$local_version  = $transient->checked[ $this->theme_slug ] ?? '';

		if ( ! $local_version || ! version_compare( $remote_version, $local_version, '>' ) ) {
			return $transient;
		}

		$zip_url = $this->get_zip_url( $release );
		if ( ! $zip_url ) {
			return $transient;
		}

		$transient->response[ $this->theme_slug ] = [
			'theme'        => $this->theme_slug,
			'new_version'  => $remote_version,
			'url'          => esc_url( "https://github.com/{$this->github_owner}/{$this->github_repo}" ),
			'package'      => esc_url_raw( $zip_url ),
			'requires'     => '6.4',
			'requires_php' => '8.0',
		];

		return $transient;
	}

	/**
	 * Populate the "View version X.X details" popup in the WordPress admin.
	 *
	 * @param mixed  $result The existing result.
	 * @param string $action The API action being performed.
	 * @param object $args   The API request arguments.
	 * @return mixed
	 */
	public function theme_info( mixed $result, string $action, object $args ): mixed {
		if ( 'theme_information' !== $action || ( $args->slug ?? '' ) !== $this->theme_slug ) {
			return $result;
		}

		$release = $this->get_release_data();
		if ( ! $release ) {
			return $result;
		}

		return (object) [
			'name'          => 'Bigtricks',
			'slug'          => $this->theme_slug,
			'version'       => ltrim( $release->tag_name, 'v' ),
			'author'        => '<a href="https://bigtricks.in">Bigtricks Team</a>',
			'homepage'      => esc_url( "https://github.com/{$this->github_owner}/{$this->github_repo}" ),
			'last_updated'  => $release->published_at ?? '',
			'requires'      => '6.4',
			'requires_php'  => '8.0',
			'sections'      => [
				'changelog' => wpautop( wp_kses_post( $release->body ?? 'See GitHub releases for details.' ) ),
			],
			'download_link' => esc_url_raw( $this->get_zip_url( $release ) ),
		];
	}

	/**
	 * Delete the cached release data after the theme is updated so the next
	 * page load immediately re-checks GitHub.
	 *
	 * @param WP_Upgrader $upgrader Upgrader instance.
	 * @param array       $options  Upgrade options.
	 */
	public function clear_cache( WP_Upgrader $upgrader, array $options ): void {
		if ( ( $options['type'] ?? '' ) === 'theme' ) {
			delete_transient( $this->transient_key );
		}
	}
}
