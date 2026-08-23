{**
 * templates/frontend/pages/indexJournal.tpl
 *
 * Copyright (c) 2026 ILS AI-Driven
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @brief The journal homepage.
 *
 * Renders a hero, an at-a-glance facts strip, announcements, a grid of the most
 * recent articles (supplied by the theme plugin) and the current issue's table
 * of contents. Every block is optional and degrades to nothing when the journal
 * has not supplied the underlying content.
 *}
{include file="frontend/components/header.tpl" pageTitleTranslated=$displayPageHeaderTitle}

{assign var="ilsLead" value=$journalDescription}
{if !$ilsLead && $currentContext}
	{assign var="ilsLead" value=$currentContext->getLocalizedData('description')}
{/if}
{assign var="ilsLayout" value=$ilsThemeOptions.homepageLayout|default:"magazine"}

<div class="page_index_journal">

	{call_hook name="Templates::Index::journal"}

	{if $ilsLayout == 'magazine'}
		<section class="ils-hero{if !$homepageImage && !$issue} ils-hero--no-figure{/if}" aria-labelledby="ils-hero-title">
			<div class="ils-hero__inner">
				<div class="ils-hero__body">
					<h1 class="ils-hero__title" id="ils-hero-title">{$displayPageHeaderTitle|escape}</h1>

					{if $ilsLead}
						<div class="ils-hero__lead">{$ilsLead}</div>
					{/if}

					<div class="ils-hero__actions">
						{if $issue}
							<a class="ils-button" href="{url page="issue" op="current"}">{translate key="journal.currentIssue"}</a>
						{/if}
						<a class="ils-button ils-button--secondary" href="{url page="issue" op="archive"}">{translate key="navigation.archives"}</a>
						<a class="ils-button ils-button--secondary" href="{url page="about" op="submissions"}">{translate key="about.submissions"}</a>
					</div>
				</div>

				{if $homepageImage && $homepageImage.uploadName}
					<figure class="ils-hero__figure">
						<img src="{$publicFilesDir}/{$homepageImage.uploadName|escape:"url"}" alt="{$homepageImageAltText|escape|default:""}">
					</figure>
				{elseif $issue && $issue->getLocalizedCoverImageUrl()}
					<figure class="ils-hero__figure">
						<a href="{url page="issue" op="current"}">
							<img src="{$issue->getLocalizedCoverImageUrl()|escape}" alt="{$issue->getLocalizedCoverImageAltText()|escape|default:""}">
						</a>
					</figure>
				{/if}
			</div>
		</section>

		{if $currentContext}
			{assign var="ilsIssn" value=$currentContext->getData('onlineIssn')}
			{if !$ilsIssn}{assign var="ilsIssn" value=$currentContext->getData('printIssn')}{/if}
			{assign var="ilsPublisher" value=$currentContext->getData('publisherInstitution')}
			{if $ilsIssn || $ilsPublisher || $issue}
				<div class="ils-facts">
					{if $issue}
						<div class="ils-fact">
							<span class="ils-fact__label">{translate key="journal.currentIssue"}</span>
							<span class="ils-fact__value">{$issue->getIssueIdentification()|strip_tags|escape}</span>
						</div>
					{/if}
					{if $ilsIssn}
						<div class="ils-fact">
							<span class="ils-fact__label">ISSN</span>
							<span class="ils-fact__value">{$ilsIssn|escape}</span>
						</div>
					{/if}
					{if $ilsPublisher}
						<div class="ils-fact">
							<span class="ils-fact__label">{translate key="manager.setup.publisher"}</span>
							<span class="ils-fact__value">{$ilsPublisher|escape}</span>
						</div>
					{/if}
				</div>
			{/if}
		{/if}
	{else}
		<h1 class="ils-page-title">{$displayPageHeaderTitle|escape}</h1>
		{if $ilsLead}
			<div class="homepage_about ils-prose">{$ilsLead}</div>
		{/if}
	{/if}

	{if $announcements && $announcements|@count}
		<section class="ils-section" aria-labelledby="ils-announcements-title">
			<div class="ils-section__head">
				<h2 class="ils-section__title" id="ils-announcements-title">{translate key="announcement.announcements"}</h2>
				<a class="ils-section__link" href="{url page="announcement"}">{translate key="plugins.themes.ilsAiDriven.viewAll"}</a>
			</div>
			<ul class="ils-announcement-list">
				{foreach from=$announcements item=announcement}
					<li>{include file="frontend/objects/announcement_summary.tpl"}</li>
				{/foreach}
			</ul>
		</section>
	{/if}

	{if $ilsLatestArticles && $ilsLatestArticles|@count}
		<section class="ils-section" aria-labelledby="ils-latest-title">
			<div class="ils-section__head">
				<h2 class="ils-section__title" id="ils-latest-title">{translate key="plugins.themes.ilsAiDriven.latestArticles"}</h2>
				<a class="ils-section__link" href="{url page="issue" op="archive"}">{translate key="navigation.archives"}</a>
			</div>
			<ul class="ils-latest-grid">
				{foreach from=$ilsLatestArticles item=ilsSubmission}
					{assign var="ilsPublication" value=$ilsSubmission->getCurrentPublication()}
					{if $ilsPublication}
						<li>
							<article class="ils-latest-card">
								<h3 class="ils-latest-card__title">
									<a href="{url page="article" op="view" path=$ilsSubmission->getBestId()}">
										{$ilsPublication->getLocalizedTitle()|strip_unsafe_html}
									</a>
								</h3>

								{assign var="ilsAuthors" value=$ilsPublication->getData('authors')}
								{if $ilsAuthors}
									<p class="ils-latest-card__authors">
										{strip}
											{foreach from=$ilsAuthors item=ilsAuthor name=ilsAuthorLoop}
												{$ilsAuthor->getFullName()|escape}{if !$smarty.foreach.ilsAuthorLoop.last}, {/if}
											{/foreach}
										{/strip}
									</p>
								{/if}

								{if $ilsPublication->getData('datePublished')}
									<p class="ils-latest-card__footer">
										<time datetime="{$ilsPublication->getData('datePublished')|escape}">
											{$ilsPublication->getData('datePublished')|date_format:$dateFormatShort}
										</time>
									</p>
								{/if}
							</article>
						</li>
					{/if}
				{/foreach}
			</ul>
		</section>
	{/if}

	{if $issue}
		<section class="ils-section" aria-labelledby="ils-current-issue-title">
			<div class="ils-section__head">
				<h2 class="ils-section__title" id="ils-current-issue-title">{translate key="journal.currentIssue"}</h2>
				<a class="ils-section__link" href="{url page="issue" op="current"}">{translate key="issue.toc"}</a>
			</div>
			{include file="frontend/objects/issue_toc.tpl" heading="h3"}
		</section>
	{/if}

	{if $additionalHomeContent}
		<section class="ils-section additional_content ils-prose">
			{$additionalHomeContent}
		</section>
	{/if}

</div>

{include file="frontend/components/footer.tpl"}
