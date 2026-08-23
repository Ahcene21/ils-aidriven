/**
 * @file js/main.js
 *
 * Copyright (c) 2026 ILS AI-Driven
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * Progressive enhancement for the ILS AI-Driven theme.
 *
 * Everything in this file is additive: the theme is fully usable with
 * JavaScript disabled, and each enhancement bails out quietly when the markup
 * it expects is not on the page. That matters because OJS front-end markup
 * varies between releases and between installed plugins.
 */
(function () {
	'use strict';

	// Some OJS releases emit the frontend script bundle in both the document
	// head and the footer; initialising twice would duplicate injected controls.
	if (window.__ilsAiDrivenThemeReady) {
		return;
	}
	window.__ilsAiDrivenThemeReady = true;

	var SCHEME_KEY = 'ils-colour-scheme';
	var root = document.documentElement;

	/** Read the translated UI strings the theme template embedded. */
	function strings() {
		var node = document.getElementById('ils-i18n');
		return (node && node.dataset) || {};
	}

	function t(key, fallback) {
		var value = strings()[key];
		return value ? value : fallback;
	}

	function $(selector, scope) {
		return (scope || document).querySelector(selector);
	}

	function $$(selector, scope) {
		return Array.prototype.slice.call((scope || document).querySelectorAll(selector));
	}

	function el(tag, attrs, html) {
		var node = document.createElement(tag);
		Object.keys(attrs || {}).forEach(function (name) {
			node.setAttribute(name, attrs[name]);
		});
		if (html !== undefined) {
			node.innerHTML = html;
		}
		return node;
	}

	var ICONS = {
		chevron: '<svg viewBox="0 0 12 12" aria-hidden="true" focusable="false"><path d="M6 8.5 1.5 4l1-1L6 6.5 9.5 3l1 1z"/></svg>',
		arrowUp: '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 4l8 8-1.4 1.4L13 7.8V20h-2V7.8L5.4 13.4 4 12z"/></svg>',
		menu: '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 6h18v2H3zm0 5h18v2H3zm0 5h18v2H3z"/></svg>',
		close: '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M18.3 5.7 12 12l6.3 6.3-1.4 1.4L10.6 13.4 4.3 19.7 2.9 18.3 9.2 12 2.9 5.7l1.4-1.4L10.6 10.6l6.3-6.3z"/></svg>'
	};

	// -------------------------------------------------------------------------
	// Colour scheme
	// -------------------------------------------------------------------------

	function preferredScheme() {
		return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
	}

	function applyScheme(scheme) {
		root.setAttribute('data-ils-scheme', scheme);
		$$('.ils-scheme-toggle').forEach(function (button) {
			button.setAttribute('aria-pressed', scheme === 'dark' ? 'true' : 'false');
		});
	}

	function initSchemeToggle() {
		var toggles = $$('.ils-scheme-toggle');
		if (!toggles.length) {
			return;
		}

		var stored = null;
		try {
			stored = localStorage.getItem(SCHEME_KEY);
		} catch (e) {
			stored = null;
		}
		applyScheme(stored || root.getAttribute('data-ils-scheme') || preferredScheme());

		toggles.forEach(function (button) {
			button.addEventListener('click', function () {
				var next = root.getAttribute('data-ils-scheme') === 'dark' ? 'light' : 'dark';
				applyScheme(next);
				try {
					localStorage.setItem(SCHEME_KEY, next);
				} catch (e) {
					// Private browsing modes can refuse writes; the toggle still
					// works for the current page view.
				}
			});
		});

		// Follow the operating system while the reader has not chosen manually.
		if (window.matchMedia) {
			var query = window.matchMedia('(prefers-color-scheme: dark)');
			var onChange = function (event) {
				var stored = null;
				try {
					stored = localStorage.getItem(SCHEME_KEY);
				} catch (e) {
					stored = null;
				}
				if (!stored) {
					applyScheme(event.matches ? 'dark' : 'light');
				}
			};
			if (query.addEventListener) {
				query.addEventListener('change', onChange);
			} else if (query.addListener) {
				query.addListener(onChange);
			}
		}
	}

	// -------------------------------------------------------------------------
	// Header: sticky behaviour and measured height
	// -------------------------------------------------------------------------

	function initHeader() {
		var header = $('.ils-header');
		if (!header) {
			return;
		}

		var measure = function () {
			root.style.setProperty('--ils-header-height', header.offsetHeight + 'px');
		};
		measure();

		if (window.ResizeObserver) {
			new ResizeObserver(measure).observe(header);
		} else {
			window.addEventListener('resize', measure);
		}

		if (header.getAttribute('data-sticky') !== 'true') {
			return;
		}

		var ticking = false;
		var onScroll = function () {
			if (ticking) {
				return;
			}
			ticking = true;
			window.requestAnimationFrame(function () {
				header.classList.toggle('is-pinned', window.pageYOffset > 40);
				ticking = false;
			});
		};
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	// -------------------------------------------------------------------------
	// Navigation
	// -------------------------------------------------------------------------

	function initMobileNav() {
		var nav = $('.ils-primary-nav');
		var toggle = $('.ils-nav-toggle');
		if (!nav || !toggle) {
			return;
		}

		var list = nav.querySelector('ul');
		if (list && !list.id) {
			list.id = 'ils-primary-nav-list';
		}
		toggle.setAttribute('aria-controls', (list && list.id) || '');
		toggle.setAttribute('aria-expanded', 'false');

		toggle.addEventListener('click', function () {
			var open = nav.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	}

	/**
	 * Turn nested <ul> menus produced by {load_menu} into accessible
	 * disclosure widgets. The parent link stays a link, so a menu entry that
	 * points somewhere real remains reachable.
	 */
	function initSubmenus() {
		$$('.ils-primary-nav li, .ils-user-nav li').forEach(function (item) {
			var submenu = item.querySelector(':scope > ul');
			var link = item.querySelector(':scope > a');
			if (!submenu || !link) {
				return;
			}

			item.classList.add('ils-has-children');
			if (!submenu.id) {
				submenu.id = 'ils-submenu-' + Math.random().toString(36).slice(2, 9);
			}

			var button = el('button', {
				type: 'button',
				class: 'ils-nav-expand',
				'aria-expanded': 'false',
				'aria-controls': submenu.id
			}, ICONS.chevron + '<span class="pkp_screen_reader">' + t('expand', 'Show submenu') + '</span>');

			button.addEventListener('click', function (event) {
				event.preventDefault();
				var open = !item.classList.contains('is-open');
				closeAllSubmenus();
				item.classList.toggle('is-open', open);
				button.setAttribute('aria-expanded', open ? 'true' : 'false');
			});

			link.insertAdjacentElement('afterend', button);
		});

		document.addEventListener('click', function (event) {
			if (!event.target || !event.target.closest || !event.target.closest('.ils-has-children')) {
				closeAllSubmenus();
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				closeAllSubmenus();
			}
		});
	}

	function closeAllSubmenus() {
		$$('.ils-has-children.is-open').forEach(function (item) {
			item.classList.remove('is-open');
			var button = item.querySelector(':scope > .ils-nav-expand');
			if (button) {
				button.setAttribute('aria-expanded', 'false');
			}
		});
	}

	// -------------------------------------------------------------------------
	// Reading progress and back-to-top
	// -------------------------------------------------------------------------

	function initReadingProgress() {
		var bar = $('.ils-progress__bar');
		var article = $('.obj_article_details .main_entry') || $('.obj_article_details');
		if (!bar || !article) {
			return;
		}

		var ticking = false;
		var update = function () {
			if (ticking) {
				return;
			}
			ticking = true;
			window.requestAnimationFrame(function () {
				var rect = article.getBoundingClientRect();
				var total = rect.height - window.innerHeight;
				var progress = total <= 0 ? 1 : Math.min(Math.max(-rect.top / total, 0), 1);
				bar.style.width = (progress * 100).toFixed(2) + '%';
				bar.parentNode.setAttribute('aria-valuenow', Math.round(progress * 100));
				ticking = false;
			});
		};

		window.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update);
		update();
	}

	function initBackToTop() {
		var button = el('button', {
			type: 'button',
			class: 'ils-to-top',
			'aria-label': t('backToTop', 'Back to top')
		}, ICONS.arrowUp);

		button.addEventListener('click', function () {
			var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
			// Move focus to the top of the document so keyboard users continue
			// from where the page now starts.
			var target = $('.ils-brand__link');
			if (target) {
				target.focus({ preventScroll: true });
			}
		});

		document.body.appendChild(button);

		var ticking = false;
		window.addEventListener('scroll', function () {
			if (ticking) {
				return;
			}
			ticking = true;
			window.requestAnimationFrame(function () {
				button.classList.toggle('is-visible', window.pageYOffset > 600);
				ticking = false;
			});
		}, { passive: true });
	}

	// -------------------------------------------------------------------------
	// Article outline
	// -------------------------------------------------------------------------

	function initArticleOutline() {
		if (document.body.getAttribute('data-ils-outline') !== 'true') {
			return;
		}

		var main = $('.obj_article_details .main_entry');
		var target = $('.obj_article_details .entry_details');
		if (!main || !target) {
			return;
		}

		var headings = $$('h2, h3', main).filter(function (heading) {
			// Skip screen-reader-only headings: they carry no visible landmark
			// for a reader to jump to.
			return heading.textContent.trim().length > 0
				&& !heading.classList.contains('pkp_screen_reader')
				&& heading.offsetParent !== null;
		});
		if (headings.length < 3) {
			return;
		}

		var list = el('ul', {});
		headings.forEach(function (heading, index) {
			if (!heading.id) {
				heading.id = 'ils-heading-' + index;
			}
			var item = el('li', heading.tagName === 'H3' ? { class: 'is-nested' } : {});
			var link = el('a', { href: '#' + heading.id }, heading.textContent.trim());
			item.appendChild(link);
			list.appendChild(item);
		});

		var outline = el('nav', {
			class: 'ils-outline',
			'aria-labelledby': 'ils-outline-title'
		});
		outline.appendChild(el('h2', { class: 'ils-outline__title', id: 'ils-outline-title' }, t('outline', 'On this page')));
		outline.appendChild(list);
		target.insertBefore(outline, target.firstChild);

		if (!window.IntersectionObserver) {
			return;
		}

		var links = $$('a', list);
		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) {
					return;
				}
				links.forEach(function (link) {
					link.classList.toggle('is-active', link.getAttribute('href') === '#' + entry.target.id);
				});
			});
		}, { rootMargin: '-20% 0px -70% 0px', threshold: 0 });

		headings.forEach(function (heading) {
			observer.observe(heading);
		});
	}

	// -------------------------------------------------------------------------
	// Copy-to-clipboard for DOI and citation
	// -------------------------------------------------------------------------

	function copyText(text) {
		if (navigator.clipboard && navigator.clipboard.writeText) {
			return navigator.clipboard.writeText(text);
		}
		// Fallback for pages not served over HTTPS.
		return new Promise(function (resolve, reject) {
			var field = el('textarea', { 'aria-hidden': 'true', tabindex: '-1' });
			field.value = text;
			field.style.position = 'fixed';
			field.style.opacity = '0';
			document.body.appendChild(field);
			field.select();
			try {
				document.execCommand('copy') ? resolve() : reject();
			} catch (e) {
				reject(e);
			}
			document.body.removeChild(field);
		});
	}

	function addCopyButton(container, getText, label) {
		if (!container) {
			return;
		}
		var button = el('button', {
			type: 'button',
			class: 'ils-button ils-button--secondary ils-button--sm ils-copy-button',
			'data-copied-label': t('copied', 'Copied')
		}, label);

		button.addEventListener('click', function () {
			var text = getText();
			if (!text) {
				return;
			}
			copyText(text).then(function () {
				button.classList.add('is-copied');
				window.setTimeout(function () {
					button.classList.remove('is-copied');
				}, 1800);
			}).catch(function () {
				// Nothing to do: the value stays selectable on the page.
			});
		});

		container.appendChild(button);
	}

	/**
	 * Buttons that carry the text to copy in a data attribute, such as the
	 * "Copy link" entry in the share row rendered by the plugin.
	 */
	function initInlineCopyButtons() {
		$$('[data-ils-copy]').forEach(function (button) {
			button.addEventListener('click', function () {
				copyText(button.getAttribute('data-ils-copy')).then(function () {
					button.classList.add('is-copied');
					window.setTimeout(function () {
						button.classList.remove('is-copied');
					}, 1800);
				}).catch(function () {
					// The URL is still in the address bar; nothing to recover.
				});
			});
		});
	}

	function initCopyButtons() {
		var details = $('.obj_article_details');
		if (!details) {
			return;
		}

		var doiItem = $('.item.doi', details);
		if (doiItem) {
			var toolbar = el('div', { class: 'ils-article-toolbar' });
			addCopyButton(toolbar, function () {
				var link = $('a', doiItem);
				return link ? link.href : doiItem.textContent.trim();
			}, t('copyDoi', 'Copy DOI'));
			doiItem.appendChild(toolbar);
		}

		var citation = document.getElementById('citationOutput');
		if (citation) {
			var citationBar = el('div', { class: 'ils-article-toolbar' });
			addCopyButton(citationBar, function () {
				return citation.textContent.trim();
			}, t('copyCitation', 'Copy citation'));
			citation.parentNode.insertBefore(citationBar, citation.nextSibling);
		}
	}

	// -------------------------------------------------------------------------
	// Content clean-up
	// -------------------------------------------------------------------------

	/** Wide editor-authored tables scroll instead of stretching the page. */
	function initResponsiveTables() {
		$$('.main_entry table, .item.abstract table, .additional_content table, .description table').forEach(function (table) {
			if (table.closest('.ils-table-scroll')) {
				return;
			}
			var wrapper = el('div', { class: 'ils-table-scroll', tabindex: '0', role: 'region' });
			table.parentNode.insertBefore(wrapper, table);
			wrapper.appendChild(table);
		});
	}

	/** Flag links that leave the journal so readers are not surprised. */
	function initExternalLinks() {
		var host = window.location.hostname;
		$$('.main_entry a[href^="http"], .ils-footer__content a[href^="http"], .additional_content a[href^="http"]').forEach(function (link) {
			if (link.hostname && link.hostname !== host && !link.querySelector('img')) {
				link.classList.add('ils-external-link');
				if (!link.hasAttribute('rel')) {
					link.setAttribute('rel', 'noopener');
				}
			}
		});
	}

	// -------------------------------------------------------------------------
	// Focus mode
	// -------------------------------------------------------------------------

	/**
	 * Enlarge an article cover over a dimmed, blurred page. One overlay element
	 * is created lazily and reused for every trigger on the page.
	 */
	function initFocusMode() {
		if (document.body.getAttribute('data-ils-focus-mode') !== 'true') {
			return;
		}

		var triggers = $$('.ils-focus-trigger');
		if (!triggers.length) {
			return;
		}

		var overlay = null;
		var image = null;
		var caption = null;
		var closeButton = null;
		var lastTrigger = null;

		function build() {
			overlay = el('div', {
				class: 'ils-focus-overlay',
				role: 'dialog',
				'aria-modal': 'true',
				hidden: 'hidden'
			});

			closeButton = el('button', {
				type: 'button',
				class: 'ils-focus-overlay__close',
				'aria-label': t('close', 'Close')
			}, ICONS.close);

			var figure = el('figure', { class: 'ils-focus-overlay__figure' });
			image = el('img', { alt: '' });
			caption = el('figcaption', { class: 'ils-focus-overlay__caption' });
			figure.appendChild(image);
			figure.appendChild(caption);

			overlay.appendChild(closeButton);
			overlay.appendChild(figure);
			document.body.appendChild(overlay);

			closeButton.addEventListener('click', close);
			overlay.addEventListener('click', function (event) {
				// Only a click on the backdrop itself dismisses the overlay.
				if (event.target === overlay) {
					close();
				}
			});
			overlay.addEventListener('keydown', function (event) {
				if (event.key === 'Escape') {
					close();
				} else if (event.key === 'Tab') {
					// Single focusable control, so the trap is just "stay here".
					event.preventDefault();
					closeButton.focus();
				}
			});
		}

		function open(trigger) {
			if (!overlay) {
				build();
			}

			var src = trigger.getAttribute('data-ils-focus-src');
			if (!src) {
				return;
			}

			var text = trigger.getAttribute('data-ils-focus-caption') || '';
			lastTrigger = trigger;
			image.setAttribute('src', src);
			image.setAttribute('alt', text);
			caption.textContent = text;
			overlay.setAttribute('aria-label', text || t('enlarge', 'Enlarged cover image'));

			overlay.hidden = false;
			document.body.classList.add('ils-focus-open');
			// Let the element paint hidden before transitioning it in.
			window.requestAnimationFrame(function () {
				overlay.classList.add('is-open');
			});
			closeButton.focus();
		}

		function close() {
			if (!overlay || overlay.hidden) {
				return;
			}
			overlay.classList.remove('is-open');
			document.body.classList.remove('ils-focus-open');
			window.setTimeout(function () {
				overlay.hidden = true;
				image.removeAttribute('src');
			}, 160);
			if (lastTrigger) {
				lastTrigger.focus();
				lastTrigger = null;
			}
		}

		triggers.forEach(function (trigger) {
			trigger.addEventListener('click', function () {
				open(trigger);
			});
		});
	}

	function initFocusVisibleFallback() {
		try {
			document.querySelector(':focus-visible');
		} catch (e) {
			document.body.classList.add('no-focus-visible');
		}
	}

	// -------------------------------------------------------------------------

	function init() {
		root.classList.add('ils-js');
		initFocusVisibleFallback();
		initSchemeToggle();
		initHeader();
		initMobileNav();
		initSubmenus();
		initBackToTop();
		initReadingProgress();
		initArticleOutline();
		initCopyButtons();
		initInlineCopyButtons();
		initFocusMode();
		initResponsiveTables();
		initExternalLinks();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
