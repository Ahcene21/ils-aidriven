{**
 * templates/frontend/components/header.tpl
 *
 * Copyright (c) 2026 ILS AI-Driven
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @brief Site masthead and the opening half of the page scaffolding.
 *
 * Everything opened here is closed in frontend/components/footer.tpl.
 *
 * Only long-standing OJS template variables and helpers are used
 * ({load_menu}, {url}, $displayPageHeaderTitle, $displayPageHeaderLogo,
 * $publicFilesDir, $requestedPage, $currentLocale) so that the theme keeps
 * working across OJS releases.
 *}
{strip}
	{* Fall back to sensible defaults if the plugin hook did not run. *}
	{if !$ilsThemeOptions}
		{assign var="ilsThemeOptions" value=[
			'headerStyle' => 'classic',
			'colourScheme' => 'auto',
			'showThemeToggle' => true,
			'stickyHeader' => true,
			'showSearchInHeader' => true,
			'showReadingProgress' => true,
			'showArticleOutline' => true,
			'announcementBar' => ''
		]}
	{/if}
	{assign var="ilsIsArticle" value=($requestedPage == 'article')}
{/strip}<!DOCTYPE html>
<html lang="{$currentLocale|replace:"_":"-"|escape}" xml:lang="{$currentLocale|replace:"_":"-"|escape}">
{include file="frontend/components/headerHead.tpl"}
<body
	class="pkp_page_{$requestedPage|escape|default:"index"} pkp_op_{$requestedOp|escape|default:"index"} ils-body"
	dir="{$currentLocaleLangDir|escape|default:"ltr"}"
	data-ils-outline="{if $ilsThemeOptions.showArticleOutline}true{else}false{/if}"
>

<div id="ils-i18n" hidden
	data-menu="{translate key="plugins.themes.ilsAiDriven.menu"}"
	data-expand="{translate key="plugins.themes.ilsAiDriven.expandSubmenu"}"
	data-outline="{translate key="plugins.themes.ilsAiDriven.onThisPage"}"
	data-copy-doi="{translate key="plugins.themes.ilsAiDriven.copyDoi"}"
	data-copy-citation="{translate key="plugins.themes.ilsAiDriven.copyCitation"}"
	data-copied="{translate key="plugins.themes.ilsAiDriven.copied"}"
	data-back-to-top="{translate key="plugins.themes.ilsAiDriven.backToTop"}"
></div>

<a href="#ils-main" class="ils-skip-link">{translate key="navigation.skip.main"}</a>

{if $ilsThemeOptions.showReadingProgress && $ilsIsArticle}
	<div class="ils-progress" role="progressbar" aria-label="{translate key="plugins.themes.ilsAiDriven.readingProgress"}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
		<div class="ils-progress__bar"></div>
	</div>
{/if}

<div class="pkp_structure_page ils-page">

	{if $ilsThemeOptions.announcementBar}
		<div class="ils-announcement-bar" role="region" aria-label="{translate key="plugins.themes.ilsAiDriven.noticeBar"}">
			{$ilsThemeOptions.announcementBar|escape}
		</div>
	{/if}

	<header class="ils-header ils-header--{$ilsThemeOptions.headerStyle|escape|default:"classic"}" data-sticky="{if $ilsThemeOptions.stickyHeader}true{else}false{/if}" role="banner">

		<div class="ils-header__masthead">
			<div class="ils-header__masthead-inner">

				<div class="ils-brand">
					<a href="{url page="index"}" class="ils-brand__link">
						{if $displayPageHeaderLogo && $displayPageHeaderLogo.uploadName}
							<img
								class="ils-brand__logo"
								src="{$publicFilesDir}/{$displayPageHeaderLogo.uploadName|escape:"url"}"
								{if $displayPageHeaderLogo.altText}alt="{$displayPageHeaderLogo.altText|escape}"{else}alt=""{/if}
							>
						{/if}
						<span class="ils-brand__text">
							<span class="ils-brand__title">{$displayPageHeaderTitle|escape}</span>
							{if $currentContext && $currentContext->getLocalizedData('abbreviation')}
								<span class="ils-brand__tagline">{$currentContext->getLocalizedData('abbreviation')|escape}</span>
							{/if}
						</span>
					</a>
				</div>

				<div class="ils-header__actions">
					{if $ilsThemeOptions.showSearchInHeader && $currentContext}
						<form class="ils-search" role="search" method="get" action="{url page="search" op="search"}">
							<label class="pkp_screen_reader" for="ils-search-query">{translate key="common.search"}</label>
							<input
								class="ils-search__field"
								id="ils-search-query"
								type="search"
								name="query"
								value="{$searchQuery|escape}"
								placeholder="{translate key="plugins.themes.ilsAiDriven.searchPlaceholder"}"
								autocomplete="off"
							>
							<button class="ils-search__submit" type="submit">
								<span class="pkp_screen_reader">{translate key="common.search"}</span>
								<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="currentColor" d="M10 2a8 8 0 1 0 4.9 14.3l5.4 5.4 1.4-1.4-5.4-5.4A8 8 0 0 0 10 2zm0 2a6 6 0 1 1 0 12 6 6 0 0 1 0-12z"/></svg>
							</button>
						</form>
					{/if}

					{if $ilsThemeOptions.showThemeToggle}
						<button class="ils-icon-button ils-scheme-toggle" type="button" aria-pressed="false">
							<span class="pkp_screen_reader">{translate key="plugins.themes.ilsAiDriven.toggleColourScheme"}</span>
							<svg class="ils-scheme-toggle__moon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21 13a9 9 0 1 1-10-10 7 7 0 0 0 10 10z"/></svg>
							<svg class="ils-scheme-toggle__sun" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 17a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-13V1h0v3zm0 19v-3 3zM4.2 5.6 2.1 3.5l1.4-1.4 2.1 2.1-1.4 1.4zm14.2 14.2-2.1-2.1 1.4-1.4 2.1 2.1-1.4 1.4zM1 13v-2h3v2H1zm19 0v-2h3v2h-3zM5.6 19.8l-1.4-1.4 2.1-2.1 1.4 1.4-2.1 2.1zM18.4 5.6l-1.4-1.4 2.1-2.1 1.4 1.4-2.1 2.1z"/></svg>
						</button>
					{/if}

					<nav class="ils-user-nav" aria-label="{translate key="plugins.themes.ilsAiDriven.userMenu"}">
						{load_menu name="user" id="navigationUser" ulClass="ils-user-nav__list"}
					</nav>
				</div>

			</div>
		</div>

		<div class="ils-header__nav">
			<div class="ils-header__nav-inner">
				<button class="ils-nav-toggle" type="button" aria-expanded="false">
					<svg class="ils-nav-toggle__open" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 6h18v2H3zm0 5h18v2H3zm0 5h18v2H3z"/></svg>
					<svg class="ils-nav-toggle__close" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M18.3 5.7 12 12l6.3 6.3-1.4 1.4L10.6 13.4 4.3 19.7 2.9 18.3 9.2 12 2.9 5.7l1.4-1.4L10.6 10.6l6.3-6.3z"/></svg>
					{translate key="plugins.themes.ilsAiDriven.menu"}
				</button>
				<nav class="ils-primary-nav" aria-label="{translate key="plugins.themes.ilsAiDriven.mainNavigation"}">
					{load_menu name="primary" id="navigationPrimary" ulClass="pkp_navigation_primary"}
				</nav>
			</div>
		</div>

	</header>

	<div class="pkp_structure_content ils-content">
		<main class="pkp_structure_main ils-main" id="ils-main" tabindex="-1" role="main">
