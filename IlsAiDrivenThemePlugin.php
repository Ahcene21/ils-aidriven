<?php

/**
 * @file IlsAiDrivenThemePlugin.php
 *
 * Copyright (c) 2026 ILS AI-Driven
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class IlsAiDrivenThemePlugin
 *
 * @brief A configurable, accessible, reading-first theme for Open Journal Systems.
 */

namespace APP\plugins\themes\ilsAiDriven;

use APP\core\Application;
use PKP\plugins\Hook;
use PKP\plugins\ThemePlugin;

class IlsAiDrivenThemePlugin extends ThemePlugin
{
    /** Fallback palette used when the corresponding theme option is empty. */
    public const DEFAULT_PRIMARY = '#0F4C81';
    public const DEFAULT_ACCENT = '#C2410C';

    /**
     * Register theme options, assets and hooks.
     *
     * Called by OJS for every request in which the theme is active, so anything
     * expensive belongs behind a hook rather than here.
     */
    public function init(): void
    {
        $this->registerOptions();

        // Navigation menu areas offered to the journal manager.
        $this->addMenuArea(['primary', 'user']);

        $this->addStyle('stylesheet', 'styles/index.less');
        $this->modifyStyle('stylesheet', ['addLessVariables' => $this->getLessVariables()]);

        if ($this->getOption('webFonts')) {
            $this->addStyle(
                'fonts',
                'https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,400;0,600;0,700;1,400&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;1,8..60,400&display=swap',
                ['baseUrl' => '']
            );
        }

        $this->addScript('main', 'js/main.js', ['contexts' => 'frontend']);

        Hook::add('TemplateManager::display', [$this, 'loadTemplateData']);
    }

    public function getDisplayName(): string
    {
        return __('plugins.themes.ilsAiDriven.name');
    }

    public function getDescription(): string
    {
        return __('plugins.themes.ilsAiDriven.description');
    }

    /**
     * Declare every option exposed under Website Settings > Appearance.
     */
    protected function registerOptions(): void
    {
        $this->addOption('primaryColour', 'FieldColor', [
            'label' => __('plugins.themes.ilsAiDriven.option.primaryColour.label'),
            'description' => __('plugins.themes.ilsAiDriven.option.primaryColour.description'),
            'default' => self::DEFAULT_PRIMARY,
        ]);

        $this->addOption('accentColour', 'FieldColor', [
            'label' => __('plugins.themes.ilsAiDriven.option.accentColour.label'),
            'description' => __('plugins.themes.ilsAiDriven.option.accentColour.description'),
            'default' => self::DEFAULT_ACCENT,
        ]);

        $this->addOption('typography', 'FieldOptions', [
            'type' => 'radio',
            'label' => __('plugins.themes.ilsAiDriven.option.typography.label'),
            'description' => __('plugins.themes.ilsAiDriven.option.typography.description'),
            'options' => [
                ['value' => 'sans', 'label' => __('plugins.themes.ilsAiDriven.option.typography.sans')],
                ['value' => 'serif', 'label' => __('plugins.themes.ilsAiDriven.option.typography.serif')],
                ['value' => 'hybrid', 'label' => __('plugins.themes.ilsAiDriven.option.typography.hybrid')],
            ],
            'default' => 'hybrid',
        ]);

        $this->addOption('baseFontSize', 'FieldOptions', [
            'type' => 'radio',
            'label' => __('plugins.themes.ilsAiDriven.option.baseFontSize.label'),
            'description' => __('plugins.themes.ilsAiDriven.option.baseFontSize.description'),
            'options' => [
                ['value' => 'compact', 'label' => __('plugins.themes.ilsAiDriven.option.baseFontSize.compact')],
                ['value' => 'regular', 'label' => __('plugins.themes.ilsAiDriven.option.baseFontSize.regular')],
                ['value' => 'large', 'label' => __('plugins.themes.ilsAiDriven.option.baseFontSize.large')],
            ],
            'default' => 'regular',
        ]);

        $this->addOption('containerWidth', 'FieldOptions', [
            'type' => 'radio',
            'label' => __('plugins.themes.ilsAiDriven.option.containerWidth.label'),
            'description' => __('plugins.themes.ilsAiDriven.option.containerWidth.description'),
            'options' => [
                ['value' => 'narrow', 'label' => __('plugins.themes.ilsAiDriven.option.containerWidth.narrow')],
                ['value' => 'wide', 'label' => __('plugins.themes.ilsAiDriven.option.containerWidth.wide')],
                ['value' => 'fluid', 'label' => __('plugins.themes.ilsAiDriven.option.containerWidth.fluid')],
            ],
            'default' => 'wide',
        ]);

        $this->addOption('cornerStyle', 'FieldOptions', [
            'type' => 'radio',
            'label' => __('plugins.themes.ilsAiDriven.option.cornerStyle.label'),
            'options' => [
                ['value' => 'sharp', 'label' => __('plugins.themes.ilsAiDriven.option.cornerStyle.sharp')],
                ['value' => 'soft', 'label' => __('plugins.themes.ilsAiDriven.option.cornerStyle.soft')],
                ['value' => 'round', 'label' => __('plugins.themes.ilsAiDriven.option.cornerStyle.round')],
            ],
            'default' => 'soft',
        ]);

        $this->addOption('headerStyle', 'FieldOptions', [
            'type' => 'radio',
            'label' => __('plugins.themes.ilsAiDriven.option.headerStyle.label'),
            'description' => __('plugins.themes.ilsAiDriven.option.headerStyle.description'),
            'options' => [
                ['value' => 'classic', 'label' => __('plugins.themes.ilsAiDriven.option.headerStyle.classic')],
                ['value' => 'centered', 'label' => __('plugins.themes.ilsAiDriven.option.headerStyle.centered')],
                ['value' => 'compact', 'label' => __('plugins.themes.ilsAiDriven.option.headerStyle.compact')],
            ],
            'default' => 'classic',
        ]);

        $this->addOption('homepageLayout', 'FieldOptions', [
            'type' => 'radio',
            'label' => __('plugins.themes.ilsAiDriven.option.homepageLayout.label'),
            'description' => __('plugins.themes.ilsAiDriven.option.homepageLayout.description'),
            'options' => [
                ['value' => 'magazine', 'label' => __('plugins.themes.ilsAiDriven.option.homepageLayout.magazine')],
                ['value' => 'classic', 'label' => __('plugins.themes.ilsAiDriven.option.homepageLayout.classic')],
            ],
            'default' => 'magazine',
        ]);

        $this->addOption('colourScheme', 'FieldOptions', [
            'type' => 'radio',
            'label' => __('plugins.themes.ilsAiDriven.option.colourScheme.label'),
            'description' => __('plugins.themes.ilsAiDriven.option.colourScheme.description'),
            'options' => [
                ['value' => 'light', 'label' => __('plugins.themes.ilsAiDriven.option.colourScheme.light')],
                ['value' => 'auto', 'label' => __('plugins.themes.ilsAiDriven.option.colourScheme.auto')],
                ['value' => 'dark', 'label' => __('plugins.themes.ilsAiDriven.option.colourScheme.dark')],
            ],
            'default' => 'auto',
        ]);

        $this->addOption('showThemeToggle', 'FieldOptions', [
            'label' => __('plugins.themes.ilsAiDriven.option.showThemeToggle.label'),
            'options' => [
                ['value' => true, 'label' => __('plugins.themes.ilsAiDriven.option.showThemeToggle.enable')],
            ],
            'default' => true,
        ]);

        $this->addOption('stickyHeader', 'FieldOptions', [
            'label' => __('plugins.themes.ilsAiDriven.option.stickyHeader.label'),
            'options' => [
                ['value' => true, 'label' => __('plugins.themes.ilsAiDriven.option.stickyHeader.enable')],
            ],
            'default' => true,
        ]);

        $this->addOption('showSearchInHeader', 'FieldOptions', [
            'label' => __('plugins.themes.ilsAiDriven.option.showSearchInHeader.label'),
            'options' => [
                ['value' => true, 'label' => __('plugins.themes.ilsAiDriven.option.showSearchInHeader.enable')],
            ],
            'default' => true,
        ]);

        $this->addOption('showReadingProgress', 'FieldOptions', [
            'label' => __('plugins.themes.ilsAiDriven.option.showReadingProgress.label'),
            'description' => __('plugins.themes.ilsAiDriven.option.showReadingProgress.description'),
            'options' => [
                ['value' => true, 'label' => __('plugins.themes.ilsAiDriven.option.showReadingProgress.enable')],
            ],
            'default' => true,
        ]);

        $this->addOption('showArticleOutline', 'FieldOptions', [
            'label' => __('plugins.themes.ilsAiDriven.option.showArticleOutline.label'),
            'description' => __('plugins.themes.ilsAiDriven.option.showArticleOutline.description'),
            'options' => [
                ['value' => true, 'label' => __('plugins.themes.ilsAiDriven.option.showArticleOutline.enable')],
            ],
            'default' => true,
        ]);

        $this->addOption('showLatestArticles', 'FieldOptions', [
            'label' => __('plugins.themes.ilsAiDriven.option.showLatestArticles.label'),
            'description' => __('plugins.themes.ilsAiDriven.option.showLatestArticles.description'),
            'options' => [
                ['value' => true, 'label' => __('plugins.themes.ilsAiDriven.option.showLatestArticles.enable')],
            ],
            'default' => true,
        ]);

        $this->addOption('structuredData', 'FieldOptions', [
            'label' => __('plugins.themes.ilsAiDriven.option.structuredData.label'),
            'description' => __('plugins.themes.ilsAiDriven.option.structuredData.description'),
            'options' => [
                ['value' => true, 'label' => __('plugins.themes.ilsAiDriven.option.structuredData.enable')],
            ],
            'default' => true,
        ]);

        $this->addOption('webFonts', 'FieldOptions', [
            'label' => __('plugins.themes.ilsAiDriven.option.webFonts.label'),
            'description' => __('plugins.themes.ilsAiDriven.option.webFonts.description'),
            'options' => [
                ['value' => true, 'label' => __('plugins.themes.ilsAiDriven.option.webFonts.enable')],
            ],
            'default' => false,
        ]);

        $this->addOption('announcementBar', 'FieldText', [
            'label' => __('plugins.themes.ilsAiDriven.option.announcementBar.label'),
            'description' => __('plugins.themes.ilsAiDriven.option.announcementBar.description'),
            'default' => '',
        ]);
    }

    /**
     * Translate the configurable options into LESS variables consumed by
     * styles/_variables.less.
     */
    protected function getLessVariables(): string
    {
        $primary = $this->sanitiseColour($this->getOption('primaryColour'), self::DEFAULT_PRIMARY);
        $accent = $this->sanitiseColour($this->getOption('accentColour'), self::DEFAULT_ACCENT);

        $fontSizes = ['compact' => '15px', 'regular' => '16px', 'large' => '18px'];
        $widths = ['narrow' => '1080px', 'wide' => '1280px', 'fluid' => '1600px'];
        $radii = ['sharp' => '0', 'soft' => '8px', 'round' => '18px'];

        $typography = (string) $this->getOption('typography');
        $headingFont = $typography === 'sans' ? '@font-sans' : '@font-serif';
        $bodyFont = $typography === 'serif' ? '@font-serif' : '@font-sans';

        $variables = [
            '@primary' => $primary,
            '@accent' => $accent,
            '@base-font-size' => $fontSizes[(string) $this->getOption('baseFontSize')] ?? '16px',
            '@container-max' => $widths[(string) $this->getOption('containerWidth')] ?? '1280px',
            '@radius' => $radii[(string) $this->getOption('cornerStyle')] ?? '8px',
            '@font-heading' => $headingFont,
            '@font-body' => $bodyFont,
        ];

        $less = '';
        foreach ($variables as $name => $value) {
            $less .= "{$name}: {$value};\n";
        }

        return $less;
    }

    /**
     * Accept only a hex colour so that an unexpected option value can never be
     * injected into the compiled stylesheet.
     */
    protected function sanitiseColour(?string $value, string $fallback): string
    {
        $value = trim((string) $value);

        return preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value) ? $value : $fallback;
    }

    /**
     * Expose theme settings to the templates, add the no-flash colour scheme
     * bootstrap and, on article pages, emit Schema.org structured data.
     *
     * @param string $hookName
     * @param array $args [TemplateManager, string $template]
     */
    public function loadTemplateData(string $hookName, array $args): bool
    {
        $templateMgr = $args[0];
        $template = $args[1] ?? '';

        if (!method_exists($templateMgr, 'assign')) {
            return false;
        }

        $scheme = (string) $this->getOption('colourScheme') ?: 'auto';

        $templateMgr->assign([
            'ilsCurrentYear' => date('Y'),
            'ilsThemeOptions' => [
                'primaryColour' => $this->sanitiseColour($this->getOption('primaryColour'), self::DEFAULT_PRIMARY),
                'accentColour' => $this->sanitiseColour($this->getOption('accentColour'), self::DEFAULT_ACCENT),
                'typography' => $this->getOption('typography'),
                'containerWidth' => $this->getOption('containerWidth'),
                'headerStyle' => $this->getOption('headerStyle'),
                'homepageLayout' => $this->getOption('homepageLayout'),
                'colourScheme' => $scheme,
                'showThemeToggle' => (bool) $this->getOption('showThemeToggle'),
                'stickyHeader' => (bool) $this->getOption('stickyHeader'),
                'showSearchInHeader' => (bool) $this->getOption('showSearchInHeader'),
                'showReadingProgress' => (bool) $this->getOption('showReadingProgress'),
                'showArticleOutline' => (bool) $this->getOption('showArticleOutline'),
                'announcementBar' => trim((string) $this->getOption('announcementBar')),
            ],
        ]);

        // Applied before first paint so a dark reader never sees a white flash.
        $templateMgr->addHeader(
            'ilsAiDrivenColourScheme',
            '<script>(function(d){try{var p=localStorage.getItem("ils-colour-scheme");var s=p||'
                . json_encode($scheme)
                . ';if(s==="auto"){s=window.matchMedia&&window.matchMedia("(prefers-color-scheme: dark)").matches?"dark":"light";}'
                . 'd.documentElement.setAttribute("data-ils-scheme",s);d.documentElement.classList.add("ils-js");}catch(e){}})(document);</script>',
            ['contexts' => 'frontend']
        );

        if ($template === 'frontend/pages/indexJournal.tpl') {
            $this->assignLatestArticles($templateMgr);
        }

        if ($this->getOption('structuredData') && $template === 'frontend/pages/article.tpl') {
            $this->addArticleStructuredData($templateMgr);
        }

        return false;
    }

    /**
     * Fetch the most recently published articles for the homepage grid.
     *
     * Wrapped defensively: a repository API change should degrade to "no grid"
     * rather than take the journal's front page down.
     */
    protected function assignLatestArticles($templateMgr): void
    {
        if (!$this->getOption('showLatestArticles')) {
            return;
        }

        $context = Application::get()->getRequest()->getContext();
        if (!$context) {
            return;
        }

        try {
            $repo = \APP\facades\Repo::submission();
            $collector = $repo->getCollector();
            $collector = $collector
                ->filterByContextIds([$context->getId()])
                ->filterByStatus([\APP\submission\Submission::STATUS_PUBLISHED])
                ->orderBy($collector::ORDERBY_DATE_PUBLISHED)
                ->limit(6);

            $submissions = method_exists($collector, 'getMany')
                ? $collector->getMany()
                : $repo->getMany($collector);
            $latest = [];
            foreach ($submissions as $submission) {
                $latest[] = $submission;
            }

            $templateMgr->assign('ilsLatestArticles', $latest);
        } catch (\Throwable $e) {
            error_log('ilsAiDriven theme: unable to load latest articles - ' . $e->getMessage());
        }
    }

    /**
     * Emit a Schema.org ScholarlyArticle graph for the article being viewed.
     *
     * OJS already ships Highwire/Dublin Core meta tags; this adds the JSON-LD
     * that Google Dataset/Scholar-style crawlers and reference managers prefer.
     */
    protected function addArticleStructuredData($templateMgr): void
    {
        try {
            $publication = $templateMgr->getTemplateVars('publication');
            $article = $templateMgr->getTemplateVars('article');
            if (!$publication || !$article) {
                return;
            }

            $request = Application::get()->getRequest();
            $context = $request->getContext();

            $authors = [];
            $authorList = $publication->getData('authors');
            if ($authorList) {
                foreach ($authorList as $author) {
                    $entry = [
                        '@type' => 'Person',
                        'name' => $author->getFullName(),
                    ];
                    if ($orcid = $author->getData('orcid')) {
                        $entry['sameAs'] = $orcid;
                    }
                    if ($affiliation = $author->getLocalizedData('affiliation')) {
                        $entry['affiliation'] = [
                            '@type' => 'Organization',
                            'name' => is_array($affiliation) ? reset($affiliation) : $affiliation,
                        ];
                    }
                    $authors[] = $entry;
                }
            }

            $data = array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'ScholarlyArticle',
                'headline' => trim(strip_tags((string) $publication->getLocalizedTitle())),
                'name' => trim(strip_tags((string) $publication->getLocalizedTitle())),
                'datePublished' => $publication->getData('datePublished'),
                'author' => $authors,
                'url' => $request->getRequestUrl(),
                'abstract' => trim(strip_tags((string) $publication->getLocalizedData('abstract'))) ?: null,
                'inLanguage' => $publication->getData('locale'),
                'license' => $publication->getData('licenseUrl'),
                'isPartOf' => $context ? array_filter([
                    '@type' => 'Periodical',
                    'name' => $context->getLocalizedName(),
                    'issn' => $context->getData('onlineIssn') ?: $context->getData('printIssn'),
                    'publisher' => $context->getData('publisherInstitution') ?: null,
                ]) : null,
            ], fn ($value) => $value !== null && $value !== '' && $value !== []);

            if ($doi = $this->getPublicationDoi($publication)) {
                $data['identifier'] = 'https://doi.org/' . $doi;
                $data['sameAs'] = 'https://doi.org/' . $doi;
            }

            $templateMgr->addHeader(
                'ilsAiDrivenStructuredData',
                '<script type="application/ld+json">'
                    . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP)
                    . '</script>',
                ['contexts' => 'frontend']
            );
        } catch (\Throwable $e) {
            error_log('ilsAiDriven theme: unable to build structured data - ' . $e->getMessage());
        }
    }

    /**
     * Read the DOI across the 3.3 (pub-id) and 3.4+ (DOI object) storage models.
     */
    protected function getPublicationDoi($publication): ?string
    {
        if (method_exists($publication, 'getDoi') && ($doi = $publication->getDoi())) {
            return $doi;
        }
        if (method_exists($publication, 'getStoredPubId') && ($doi = $publication->getStoredPubId('doi'))) {
            return $doi;
        }

        return null;
    }
}

// Retained so that installations resolving the plugin by its short class name
// (as declared in version.xml) keep working.
if (!class_exists('IlsAiDrivenThemePlugin', false)) {
    class_alias(IlsAiDrivenThemePlugin::class, 'IlsAiDrivenThemePlugin');
}
