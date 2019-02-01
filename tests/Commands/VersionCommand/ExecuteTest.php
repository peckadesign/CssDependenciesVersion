<?php declare(strict_types = 1);

namespace PdTests\CssDependenciesVersion\CssDependenciesVersion;

require __DIR__ . '/../../bootstrap.php';

final class ExecuteTest extends \Tester\TestCase
{

	public function testWithoutOptions(): void
	{
		$cb = function (): void {
			$loggerInterface = new \Psr\Log\NullLogger();
			$output = new \Symfony\Component\Console\Output\BufferedOutput();
			$command = new \Pd\CssDependenciesVersion\Commands\VersionCommand($loggerInterface, 'v');

			$input = new \Symfony\Component\Console\Input\ArrayInput([]);

			$command->run($input, $output);
		};
		\Tester\Assert::exception($cb, \Symfony\Component\Console\Exception\RuntimeException::class);
	}


	public function testWithoutArguments(): void
	{
		$cb = function (): void {
			$loggerInterface = new \Psr\Log\NullLogger();
			$output = new \Symfony\Component\Console\Output\BufferedOutput();
			$command = new \Pd\CssDependenciesVersion\Commands\VersionCommand($loggerInterface, 'v');

			$arguments = [
				'--baseDir' => 'path',
			];

			$input = new \Symfony\Component\Console\Input\ArrayInput($arguments);

			$command->run($input, $output);
		};
		\Tester\Assert::exception($cb, \Symfony\Component\Console\Exception\RuntimeException::class, 'Not enough arguments (missing: "file").');
	}


	public function testRealFile(): void
	{
		$cssFileName = 'file.css';
		\Nette\Utils\FileSystem::delete($cssFileName);
		\Nette\Utils\FileSystem::write($cssFileName, 'body {background: url("/file.png") }');

		$loggerInterface = new \Psr\Log\NullLogger();
		$output = new \Symfony\Component\Console\Output\BufferedOutput();
		$command = new \Pd\CssDependenciesVersion\Commands\VersionCommand($loggerInterface, 'v');

		$arguments = [
			'file' => __DIR__ . '/' . $cssFileName,
			'--baseDir' => 'path',
		];
		$input = new \Symfony\Component\Console\Input\ArrayInput($arguments, $command->getDefinition());

		try {
			$command->run($input, $output);
			\Tester\Assert::true(\Tester\Assert::isMatching('~^body {background: url\("/file\.png\?v=[a-z0-9]+"\) \}$~', \file_get_contents($cssFileName), TRUE));
		} finally {
			\Nette\Utils\FileSystem::delete($cssFileName);
		}
	}

}

(new ExecuteTest())->run();
