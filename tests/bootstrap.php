<?php declare(strict_types = 1);

return call_user_func(function (): void {

	require __DIR__ . '/../vendor/autoload.php';

	if ( ! class_exists('Tester\Assert')) {
		echo 'Install Nette Tester using `composer install --dev`';
		exit(1);
	}
	\Tester\Environment::setup();

	return;
});
