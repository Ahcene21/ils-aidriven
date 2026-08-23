{**
 * templates/frontend/objects/article_summary.tpl
 *
 * Copyright (c) 2026 ILS AI-Driven
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @brief One article in a list: cover thumbnail, title, linked authors, page
 *  range, abstract snippet, DOI, metrics badge and galley buttons.
 *
 * Entity data is read through getData()/getLocalizedData() only, so no
 * accessor that moved between OJS releases is relied upon. The DOI is resolved
 * from the 3.4+ DOI object with a fallback to the 3.3 pub-id setting.
 *
 * @uses $article Submission
 * @uses $heading string Heading level for the title, defaults to h3
 *}
{strip}
	{assign var="publication" value=$article->getCurrentPublication()}
	{assign var="ilsHeading" value=$heading|default:"h3"}
	{assign var="ilsPath" value=$article->getBestId()}
	{assign var="ilsTitle" value=$publication->getLocalizedTitle()}
	{assign var="ilsCover" value=$publication->getLocalizedData('coverImage')}
	{assign var="ilsShowCover" value=($ilsThemeOptions.showCoverImages|default:true) && $ilsCover && $ilsCover.uploadName}
	{assign var="ilsFocus" value=$ilsThemeOptions.focusMode|default:true}
	{assign var="ilsBadge" value=$ilsThemeOptions.citationBadge|default:"none"}

	{* DOI: 3.4+ stores a DOI object, 3.3 stored a pub-id setting. *}
	{assign var="ilsDoi" value=""}
	{assign var="ilsDoiObject" value=$publication->getData('doiObject')}
	{if $ilsDoiObject}{assign var="ilsDoi" value=$ilsDoiObject->getData('doi')}{/if}
	{if !$ilsDoi}{assign var="ilsDoi" value=$publication->getData('pub-id::doi')}{/if}
{/strip}
<article class="obj_article_summary ils-article-card{if $ilsShowCover} has-cover{/if}">

	{if $ilsShowCover}
		{assign var="ilsCoverUrl" value="`$publicFilesDir`/`$ilsCover.uploadName|escape:'url'`"}
		<div class="cover ils-article-card__cover">
			{if $ilsFocus}
				<button
					type="button"
					class="ils-focus-trigger"
					data-ils-focus-src="{$ilsCoverUrl}"
					data-ils-focus-caption="{$ilsTitle|strip_tags|escape}"
				>
					<img src="{$ilsCoverUrl}" alt="{$ilsCover.altText|escape|default:""}" loading="lazy">
					<span class="pkp_screen_reader">{translate key="plugins.themes.ilsAiDriven.enlargeCover"}</span>
				</button>
			{else}
				<a href="{url page="article" op="view" path=$ilsPath}">
					<img src="{$ilsCoverUrl}" alt="{$ilsCover.altText|escape|default:""}" loading="lazy">
				</a>
			{/if}
		</div>
	{/if}

	<div class="ils-article-card__body">

		<{$ilsHeading} class="title">
			<a href="{url page="article" op="view" path=$ilsPath}">{$ilsTitle|strip_unsafe_html}</a>
		</{$ilsHeading}>

		{assign var="ilsAuthors" value=$publication->getData('authors')}
		{if $ilsAuthors}
			<div class="authors ils-article-card__authors">
				{strip}
					{foreach from=$ilsAuthors item=ilsAuthor name=ilsAuthorLoop}
						<a href="{url page="search" op="search" authors=$ilsAuthor->getFullName()}">{$ilsAuthor->getFullName()|escape}</a>{if !$smarty.foreach.ilsAuthorLoop.last}<span class="sep">, </span>{/if}
					{/foreach}
				{/strip}
			</div>
		{/if}

		<div class="meta ils-article-card__meta">
			{if $publication->getData('pages')}
				<span class="pages">{$publication->getData('pages')|escape}</span>
			{/if}
			{if $publication->getData('datePublished')}
				<span class="published">
					<time datetime="{$publication->getData('datePublished')|escape}">{$publication->getData('datePublished')|date_format:$dateFormatShort}</time>
				</span>
			{/if}
			{if $ilsDoi}
				<span class="doi"><a href="https://doi.org/{$ilsDoi|escape}">{$ilsDoi|escape}</a></span>
			{/if}
		</div>

		{if ($ilsThemeOptions.showAbstractInList|default:true) && $publication->getLocalizedData('abstract')}
			<div class="summary ils-article-card__summary">
				{$publication->getLocalizedData('abstract')|strip_tags|truncate:260:"&hellip;"}
			</div>
		{/if}

		<div class="ils-article-card__footer">
			{assign var="ilsGalleys" value=$publication->getData('galleys')}
			{if $ilsGalleys}
				<ul class="galleys_links">
					{foreach from=$ilsGalleys item=galley}
						<li>{include file="frontend/objects/galley_link.tpl" parent=$article}</li>
					{/foreach}
				</ul>
			{/if}

			{if $ilsDoi && $ilsBadge != 'none'}
				<div class="ils-article-card__badge">
					{if $ilsBadge == 'dimensions'}
						<span class="__dimensions_badge_embed__" data-doi="{$ilsDoi|escape}" data-style="small_rectangle"></span>
					{elseif $ilsBadge == 'altmetric'}
						<div class="altmetric-embed" data-badge-type="donut" data-badge-popover="right" data-doi="{$ilsDoi|escape}"></div>
					{elseif $ilsBadge == 'plumx'}
						<a class="plumx-plum-print-popup" href="https://plu.mx/plum/a/?doi={$ilsDoi|escape}" data-popup="right" data-size="medium"></a>
					{/if}
				</div>
			{/if}
		</div>

	</div>
</article>
