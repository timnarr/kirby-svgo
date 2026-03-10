<?php

namespace tobimori;

use Kirby\Cms\App;
use Kirby\Cms\File;
use Kirby\Cms\FileVersion;
use Kirby\Filesystem\F;
use MathiasReker\PhpSvgOptimizer\Service\Facade\SvgOptimizerFacade;

class SvgOptimizer
{
	/**
	 * Process an SVG file with the given optimization rules
	 *
	 * Called by the thumb component for both create (in-place) and thumbs (media folder) flows
	 */
	public static function process(string $src, string $dst, array $options): string
	{
		$rules = $options['svg'] ?? [];

		// `svg: true` in blueprint means use defaults from config
		if ($rules === true) {
			$rules = static::defaults();
		}

		if (empty($rules)) {
			if ($src !== $dst) {
				F::copy($src, $dst, true);
			}

			return $dst;
		}

		// copy source to destination first if they differ
		if ($src !== $dst) {
			F::copy($src, $dst, true);
		}

		SvgOptimizerFacade::fromFile($dst)
			->withRules(...$rules)
			->optimize()
			->saveToFile($dst);

		return $dst;
	}

	/**
	 * Thumbs-like method
	 *
	 * Returns a FileVersion with the optimized SVG in the media folder.
	 */
	public static function thumb(File $file, array $options = []): FileVersion|File
	{
		if ($file->extension() !== 'svg') {
			return $file;
		}

		if (empty($options)) {
			$options = static::defaults();
		}

		return $file->thumb(['svg' => $options]);
	}

	/**
	 * Returns the default optimization rules from config.
	 */
	public static function defaults(): array
	{
		return App::instance()->option('tobimori.svgo.rules', []);
	}
}
