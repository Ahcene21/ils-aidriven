{**
 * templates/frontend/pages/issueArchive.tpl
 *
 * Copyright (c) 2026 ILS AI-Driven
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @brief The issue archive, grouped under a heading per year of publication.
 *
 * Pagination is preserved exactly as core builds it, so archives that span many
 * pages keep working; the year headings group whatever is on the current page.
 *
 * @uses $issues array Issue objects on this page
 * @uses $prevPage int|null
 * @uses $nextPage int|null
 * @uses $showingStart int
 * @uses $showingEnd int
 * @uses $total int
 *}
{include file="frontend/components/header.tpl" pageTitle="archive.archives"}

<div class="page page_issue_archive">

	{include file="frontend/components/breadcrumbs.tpl" currentTitleKey="archive.archives"}

	<h1 class="ils-page-title">{translate key="archive.archives"}</h1>

	{if $issues && $issues|@count}
		{assign var="ilsYear" value=""}
		{foreach from=$issues item=issue name=ilsIssueLoop}
			{assign var="ilsIssueYear" value=$issue->getData('year')}

			{if $ilsIssueYear != $ilsYear}
				{if $ilsYear != ""}
					</ul></section>
				{/if}
				<section class="ils-archive-year">
					<h2 class="ils-archive-year__heading">{$ilsIssueYear|escape}</h2>
					<ul class="issues_archive">
				{assign var="ilsYear" value=$ilsIssueYear}
			{/if}

			<li>{include file="frontend/objects/issue_summary.tpl"}</li>

			{if $smarty.foreach.ilsIssueLoop.last}
				</ul></section>
			{/if}
		{/foreach}

		{if $prevPage > 1}
			{capture assign=prevUrl}{url op="archive" path=$prevPage}{/capture}
		{elseif $prevPage === 1}
			{capture assign=prevUrl}{url op="archive"}{/capture}
		{/if}
		{if $nextPage}
			{capture assign=nextUrl}{url op="archive" path=$nextPage}{/capture}
		{/if}
		{include file="frontend/components/pagination.tpl"
			prevUrl=$prevUrl
			nextUrl=$nextUrl
			showingStart=$showingStart
			showingEnd=$showingEnd
			total=$total
		}
	{else}
		<div class="ils-empty-state">
			<p>{translate key="current.noCurrentIssueDesc"}</p>
		</div>
	{/if}

</div>

{include file="frontend/components/footer.tpl"}
