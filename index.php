<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Kirby\Cms\App;
use Kirby\Image\Image;
use tobimori\SvgOptimizer;

// allow SVGs through the manipulate() pipeline so
// create blueprint options trigger the thumb component
Image::$resizableTypes[] = 'svg';

App::plugin('tobimori/svgo', [
	'components' => [
		'thumb' => function (App $kirby, string $src, string $dst, array $options): string {
			if (pathinfo($src, PATHINFO_EXTENSION) !== 'svg') {
				return $kirby->nativeComponent('thumb')($kirby, $src, $dst, $options);
			}

			return SvgOptimizer::process($src, $dst, $options);
		}
	],
	'fileMethods' => [
		/** @kql-allowed */
		'svgo' => fn (array $options = []) => SvgOptimizer::thumb($this, $options),
	],
	'options' => [
		'rules' => [
			'convertColorsToHex' => true,
			'convertCssClassesToAttributes' => true,
			'convertEmptyTagsToSelfClosing' => true,
			'convertInlineStylesToAttributes' => true,
			'fixAttributeNames' => false,
			'flattenGroups' => true,
			'minifySvgCoordinates' => true,
			'minifyTransformations' => true,
			'removeAriaAndRole' => true,
			'removeComments' => true,
			'removeDataAttributes' => false,
			'removeDefaultAttributes' => true,
			'removeDeprecatedAttributes' => true,
			'removeDoctype' => true,
			'removeDuplicateElements' => true,
			'removeEmptyAttributes' => true,
			'removeEmptyGroups' => true,
			'removeEmptyTextAttributes' => true,
			'removeEnableBackgroundAttribute' => false,
			'removeInkscapeFootprints' => true,
			'removeInvisibleCharacters' => true,
			'removeMetadata' => true,
			'removeNonStandardAttributes' => false,
			'removeNonStandardTags' => false,
			'removeTitleAndDesc' => true,
			'removeUnnecessaryWhitespace' => true,
			'removeUnsafeElements' => false,
			'removeUnusedMasks' => true,
			'removeUnusedNamespaces' => true,
			'removeWidthHeightAttributes' => false,
			'scopeSvgStyles' => false,
			'sortAttributes' => true,
		],
	],
]);
