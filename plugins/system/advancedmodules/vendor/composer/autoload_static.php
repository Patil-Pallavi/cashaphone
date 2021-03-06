<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0607aff10648ef74c8ea6dd76ad3cc2d
{
	public static $prefixLengthsPsr4 = [
		'R' =>
			[
				'RegularLabs\\Plugin\\System\\AdvancedModules\\' => 42,
			],
	];

	public static $prefixDirsPsr4 = [
		'RegularLabs\\Plugin\\System\\AdvancedModules\\' =>
			[
				0 => __DIR__ . '/../..' . '/src',
			],
	];

	public static function getInitializer(ClassLoader $loader)
	{
		return \Closure::bind(function () use ($loader) {
			$loader->prefixLengthsPsr4 = ComposerStaticInit0607aff10648ef74c8ea6dd76ad3cc2d::$prefixLengthsPsr4;
			$loader->prefixDirsPsr4    = ComposerStaticInit0607aff10648ef74c8ea6dd76ad3cc2d::$prefixDirsPsr4;
		}, null, ClassLoader::class);
	}
}
