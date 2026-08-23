<?php

/**
 * @file index.php
 *
 * Copyright (c) 2026 ILS AI-Driven
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @brief Wrapper for the ILS AI-Driven theme plugin.
 *
 * Older releases of OJS/OMP/OPS instantiate plugins through this file rather
 * than through the PSR-4 autoloader, so the class is required explicitly to
 * keep the theme installable on 3.3 through 3.5.
 */

require_once __DIR__ . '/IlsAiDrivenThemePlugin.php';

return new \APP\plugins\themes\ilsAiDriven\IlsAiDrivenThemePlugin();
