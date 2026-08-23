{**
 * templates/frontend/components/footer.tpl
 *
 * Copyright (c) 2026 ILS AI-Driven
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @brief The right-hand block sidebar, the site footer, and the closing half
 *  of the scaffolding opened in header.tpl.
 *
 * The sidebar is captured first so that an area with no block plugins in it
 * collapses completely instead of leaving an empty column.
 *}
{strip}
	{assign var="ilsLayout" value=$ilsThemeOptions.sidebarLayout|default:"both"}
	{assign var="ilsHasRight" value=($ilsLayout == 'right' || $ilsLayout == 'both')}
{/strip}
		</main><!-- .ils-main -->

{if $ilsHasRight}
	{capture assign="ilsSidebar"}{call_hook name="Templates::Common::Sidebar"}{/capture}
	{if $ilsSidebar|regex_replace:"/\s+/":""}
		<aside class="ils-sidebar pkp_structure_sidebar" role="complementary" aria-label="{translate key="plugins.themes.ilsAiDriven.sidebar"}">
			{$ilsSidebar}
		</aside>
	{/if}
{/if}

	</div><!-- .ils-content -->

	<footer class="ils-footer pkp_structure_footer_wrapper" role="contentinfo">
		<div class="ils-footer__inner">

			<div class="ils-footer__col ils-footer__brand">
				<p class="ils-footer__title">{$displayPageHeaderTitle|escape}</p>

				{if $currentContext}
					{assign var="ilsOnlineIssn" value=$currentContext->getData('onlineIssn')}
					{assign var="ilsPrintIssn" value=$currentContext->getData('printIssn')}
					{if $ilsOnlineIssn || $ilsPrintIssn}
						<p class="ils-footer__issn">
							{if $ilsOnlineIssn}{translate key="plugins.themes.ilsAiDriven.issnOnline" issn=$ilsOnlineIssn|escape}{/if}
							{if $ilsOnlineIssn && $ilsPrintIssn}<br>{/if}
							{if $ilsPrintIssn}{translate key="plugins.themes.ilsAiDriven.issnPrint" issn=$ilsPrintIssn|escape}{/if}
						</p>
					{/if}
					{if $currentContext->getData('publisherInstitution')}
						<p class="ils-footer__publisher">{$currentContext->getData('publisherInstitution')|escape}</p>
					{/if}
				{/if}
			</div>

			{if $currentContext}
				<div class="ils-footer__col">
					<h2>{translate key="about.aboutContext"}</h2>
					<ul class="ils-footer__links">
						<li><a href="{url page="about"}">{translate key="about.aboutContext"}</a></li>
						<li><a href="{url page="about" op="editorialTeam"}">{translate key="about.editorialTeam"}</a></li>
						<li><a href="{url page="issue" op="archive"}">{translate key="navigation.archives"}</a></li>
						<li><a href="{url page="announcement"}">{translate key="announcement.announcements"}</a></li>
					</ul>
				</div>

				<div class="ils-footer__col">
					<h2>{translate key="plugins.themes.ilsAiDriven.forAuthors"}</h2>
					<ul class="ils-footer__links">
						<li><a href="{url page="about" op="submissions"}">{translate key="about.submissions"}</a></li>
						<li><a href="{url page="about" op="privacy"}">{translate key="manager.setup.privacyStatement"}</a></li>
						<li><a href="{url page="about" op="contact"}">{translate key="about.contact"}</a></li>
						<li><a href="{url page="search" op="search"}">{translate key="common.search"}</a></li>
					</ul>
				</div>
			{/if}

			<div class="ils-footer__col ils-footer__content">
				{if $currentContext && $currentContext->getLocalizedData('pageFooter')}
					{$currentContext->getLocalizedData('pageFooter')}
				{/if}
				{call_hook name="Templates::Common::Footer::PageFooter"}
			</div>

		</div>

		<div class="ils-footer__bar">
			<div class="ils-footer__bar-inner">
				<span>&copy; {$ilsCurrentYear|escape} {$displayPageHeaderTitle|escape}</span>
				<a href="{url page="about" op="aboutThisPublishingSystem"}">{translate key="about.aboutThisPublishingSystem"}</a>
			</div>
		</div>
	</footer>

</div><!-- .ils-page -->

{load_script context="frontend"}

</body>
</html>
