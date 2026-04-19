<?php
/**
 * Share Popover Component
 * Reusable social share popup with copy link
 * 
 * @package Bigtricks
 */
?>

<!-- Share Popover -->
<div id="share-popover" class="fixed hidden z-50 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 p-4 min-w-[280px]" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
	<div class="flex items-center justify-between mb-3 pb-3 border-b border-slate-200 dark:border-slate-700">
		<span class="text-sm font-bold text-slate-700 dark:text-white flex items-center gap-2">
			<i data-lucide="share-2" class="w-4 h-4"></i>
			<?php esc_html_e( 'Share', 'bigtricks' ); ?>
		</span>
		<button onclick="closeSharePopover()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
			<i data-lucide="x" class="w-4 h-4"></i>
		</button>
	</div>
	<div class="grid grid-cols-2 gap-2 mb-3">
		<a href="#" data-share="whatsapp" class="flex items-center gap-2 p-2 bg-[#25D366]/10 rounded-lg hover:bg-[#25D366]/20 transition-colors">
			<i data-lucide="message-circle" class="w-4 h-4 text-[#25D366]"></i>
			<span class="text-xs font-bold text-[#128C7E]">WhatsApp</span>
		</a>
		<a href="#" data-share="twitter" class="flex items-center gap-2 p-2 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
			<i data-lucide="twitter" class="w-4 h-4 text-slate-700 dark:text-slate-300"></i>
			<span class="text-xs font-bold text-slate-700 dark:text-slate-300">Twitter</span>
		</a>
		<a href="#" data-share="telegram" class="flex items-center gap-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
			<i data-lucide="send" class="w-4 h-4 text-blue-500"></i>
			<span class="text-xs font-bold text-blue-600 dark:text-blue-400">Telegram</span>
		</a>
		<a href="#" data-share="facebook" class="flex items-center gap-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
			<i data-lucide="facebook" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
			<span class="text-xs font-bold text-blue-700 dark:text-blue-400">Facebook</span>
		</a>
	</div>
	<button class="bt-share-copy-popover w-full flex items-center justify-center gap-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 px-3 py-2 rounded-lg text-xs font-bold transition-colors" data-url="">
		<i data-lucide="link-2" class="w-4 h-4"></i>
		<?php esc_html_e( 'Copy Link', 'bigtricks' ); ?>
	</button>
</div>

<div id="share-popover-overlay" class="fixed inset-0 bg-black/30 z-40 hidden" onclick="closeSharePopover()"></div>

<script>
// Share Popover functionality
function openSharePopover(event) {
	event.preventDefault();
	const popover = document.getElementById('share-popover');
	const overlay = document.getElementById('share-popover-overlay');
	const url = window.location.href;
	const title = document.title;
	
	if (popover && overlay) {
		// Update share links
		const whatsappLink = popover.querySelector('[data-share="whatsapp"]');
		const twitterLink = popover.querySelector('[data-share="twitter"]');
		const telegramLink = popover.querySelector('[data-share="telegram"]');
		const facebookLink = popover.querySelector('[data-share="facebook"]');
		const copyBtn = popover.querySelector('.bt-share-copy-popover');
		
		if (whatsappLink) {
			whatsappLink.href = 'https://wa.me/?text=' + encodeURIComponent(title + ' ' + url);
		}
		if (twitterLink) {
			twitterLink.href = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(title) + '&url=' + encodeURIComponent(url);
		}
		if (telegramLink) {
			telegramLink.href = 'https://t.me/share/url?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(title);
		}
		if (facebookLink) {
			facebookLink.href = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
		}
		if (copyBtn) {
			copyBtn.dataset.url = url;
		}
		
		// Show popover and overlay
		popover.classList.remove('hidden');
		overlay.classList.remove('hidden');
		
		// Re-initialize Lucide icons (scoped to popover only)
		if (typeof lucide !== 'undefined') {
			lucide.createIcons({ nodes: [popover] });
		}
	}
}

function closeSharePopover() {
	const popover = document.getElementById('share-popover');
	const overlay = document.getElementById('share-popover-overlay');
	
	if (popover) {
		popover.classList.add('hidden');
	}
	if (overlay) {
		overlay.classList.add('hidden');
	}
}

// Copy link functionality
document.addEventListener('DOMContentLoaded', function() {
	const copyBtn = document.querySelector('.bt-share-copy-popover');
	if (copyBtn) {
		copyBtn.addEventListener('click', function() {
			const url = this.dataset.url || window.location.href;
			const originalHTML = this.innerHTML;
			
			navigator.clipboard.writeText(url).then(function() {
				copyBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Copied!';
				if (typeof lucide !== 'undefined') {
					lucide.createIcons({ nodes: [copyBtn] });
				}
				
				setTimeout(function() {
					copyBtn.innerHTML = originalHTML;
					if (typeof lucide !== 'undefined') {
						lucide.createIcons({ nodes: [copyBtn] });
					}
				}, 2000);
			}).catch(function() {
				// Fallback for older browsers
				const textArea = document.createElement('textarea');
				textArea.value = url;
				textArea.style.position = 'fixed';
				textArea.style.left = '-999999px';
				document.body.appendChild(textArea);
				textArea.select();
				
				try {
					document.execCommand('copy');
					copyBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Copied!';
					if (typeof lucide !== 'undefined') {
						lucide.createIcons({ nodes: [copyBtn] });
					}
					
					setTimeout(function() {
						copyBtn.innerHTML = originalHTML;
						if (typeof lucide !== 'undefined') {
							lucide.createIcons({ nodes: [copyBtn] });
						}
					}, 2000);
				} catch (err) {
					console.error('Failed to copy:', err);
				}
				
				document.body.removeChild(textArea);
			});
		});
	}
});

// Close popover on Escape key
document.addEventListener('keydown', function(e) {
	if (e.key === 'Escape') {
		closeSharePopover();
	}
});
</script>
