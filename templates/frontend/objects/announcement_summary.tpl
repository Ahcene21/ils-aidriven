{**
 * templates/frontend/objects/announcement_summary.tpl
 *
 * Copyright (c) 2026 ILS AI-Driven
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @brief A single announcement in a list.
 *
 * Uses DataObject::getLocalizedData()/getData() rather than the generated
 * accessors so the same template works on every OJS 3.x release.
 *
 * @uses $announcement Announcement The announcement to display
 * @uses $heading string Optional heading level, defaults to h3
 *}
{assign var="ilsHeading" value=$heading|default:"h3"}

<article class="obj_announcement_summary">
	{if $announcement->getData('datePosted')}
		<p class="date">
			<time datetime="{$announcement->getData('datePosted')|escape}">
				{$announcement->getData('datePosted')|date_format:$dateFormatShort}
			</time>
		</p>
	{/if}

	<{$ilsHeading} class="title">
		<a href="{url page="announcement" op="view" path=$announcement->getId()}">
			{$announcement->getLocalizedData('title')|escape}
		</a>
	</{$ilsHeading}>

	{if $announcement->getLocalizedData('descriptionShort')}
		<div class="summary">
			{$announcement->getLocalizedData('descriptionShort')|strip_unsafe_html}
		</div>
	{/if}
</article>
